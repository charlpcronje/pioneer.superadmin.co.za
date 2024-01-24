Here is what I have:
./.env
```env
FIREBASE_SERVICE_KEY=AAAAbutTKf4:APA91bHijciodk6tmCBMenvl1py5Oqpg7e7aP0YnstZK1ZVwUZNnd7hPzqQxwMbZ3NN8HTl2Xk4nGB-0sDF-TECq2iYToMsnPhiXzAdq8pLNjzF29cd5iTap9rnoK3QffhZI9JNM9dUy
FIREBASE_SERVICE_ENDPOINT=https://fcm.googleapis.com/fcm/send
```
./config/app.php

```php
Modules\Farmer\Providers\FirebaseServiceProvider::class,
```
./Modules/Farmer/Providers/FirebaseServiceProvider.php

```php
<?php
namespace Modules\Farmer\Providers;
use Illuminate\Support\ServiceProvider;
use Modules\Farmer\Services\Firebase;
class FirebaseServiceProvider extends ServiceProvider
{
        public function register()
    {
        $this->app->singleton(Firebase::class, function ($app) {
            return new Firebase(config('firebase'));
        });
    }
        public function provides()
    {
        return [];
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\config\services.php


```php
...
'firebase' => [
        'endpoint' => env('FIREBASE_SERVICE_ENDPOINT'),
        'server_key' => env('FIREBASE_SERVICE_KEY')
    ] 
];
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Config\firebase.php

```php
<?php
return [
    'endpoint' => env('FIREBASE_SERVICE_ENDPOINT'),
    'server_key' => env('FIREBASE_SERVICE_KEY')
];
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Http\Controllers\MessageController.php

```php
<?php
namespace Modules\Farmer\Http\Controllers;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Twilio\Rest\Client;
use Modules\Farmer\Entities\Message;
use Modules\Farmer\Entities\Farmer;
use Modules\Farmer\Entities\FarmerCategory;
use Modules\Farmer\Services\Firebase;
use App\Models\NotificationBinding;
class MessageController extends Controller
{
    private $client;
    private $message;
    private $firebaseService;
    private $notificationBinding;
    public function __construct(Message $message, Client $client,  NotificationBinding $notificationBinding)
    {
        $this->message = $message;
        $this->client = $client;
        $this->firebaseService = new Firebase(config('services.firebase'));
        $this->notificationBinding = $notificationBinding;
    }
    public function index()
    {
        return $this->message->where(['recipient' => 'FARMERS'])
            ->orderby('created_at')
            ->groupBy('headline')
            ->get(['headline', 'content','created_at', 'type', 'meta_data', \DB::raw('count(*) as ct')]);
    }
    public function create()
    {
    }
    public function send(Request $request)
    {
        $categories = $request->categories;
        $list = [];
        array_walk($categories, function($category) use (&$list){
            array_push($list, $category['text']);
        });
        $farmers = [];
        if( in_array('all', $list))
        {
            $farmers  = Farmer::all();
        } else
        {
            $farmers = [];
            foreach($list as $category_name)
            {
                $farmer = new Farmer;
                $db_list = $farmer
                    ->where('categories', 'LIKE', "%{$category_name}%")
                    ->get();
                foreach( $db_list as $f)
                {
                    if(!in_array($f, $farmers))
                    {
                        array_push($farmers, $f);
                    }
                }
            }
        }
        foreach($farmers  as $farmer) {
            //Save Message;
            //$message = new Messages;
            $input = $request->toArray();
            $input['user_id'] = $farmer->id;
            //print_r($request->type); exit;
            $input['type'] =  implode(",", $request->type);
            $input['content'] = strip_tags($input['content']);
            if(in_array('EMAIL', $request->type))
            {
                \Mail::raw($input['content'], function ($message) use ($input, $farmer) {
                    $message->to($farmer->email)->subject($input['headline']);
                });
            }
            if(in_array('WHATSAPP', $request->type) && $farmer->number)
            {
                $number = substr($farmer->number, -9);
                $number = "+27".$number;
                $this->client->messages
                    ->create("whatsapp:{$number}", // to
                       ["from" => "whatsapp:+14155238886", "body" => strip_tags($input['content'])]);
            }
            if(in_array('SMS', $request->type) && $farmer->number)
            {
                $number = substr($farmer->number, -9);
                $number = "+27".$number;
                $this->client->messages
                    ->create($number, // to
                        ["body" => strip_tags($input['content']), "from" => "+18049448749"]
                    );
               // print($message->sid);
            }
            if(in_array('PUSH', $request->type) && $farmer->notify)
            {
                $identity = md5($farmer->username);
                $notificationBinding = $this->notificationBinding->find($identity);
                if($notificationBinding)
                {
                    $message['to'] = $notificationBinding->sid;
                    $message['notification'] = ["title"=>$input['headline'], "body"=>$input['content']];
                    $message['data'] = ["title"=>$input['headline'], "body"=>$input['content']];
                    $this->firebaseService->send($message);
                }
            }
            if( $request->news_id['id'] != -1 )
            {
                    $input['meta_data'] = json_encode($request->news_id);
            }
            $input['recipient'] = "FARMERS";
            Message::create($input);
        }
        return ['success' => 1];
    }
    public function store(Request $request)
    {
    }
    public function show($id)
    {
    }
    public function edit($id)
    {
    }
    public function update(Request $request, $id)
    {
        //
    }
    public function destroy($id)
    {
        //
    }
    public function setViewed($id)
    {
        $message = $this->message->find($id);
        $message->views = 1;
        $message->save();
        return ['success' => 1];
    }
    public function getUserPushNotifications($user_id)
    {
        $messages = $this->message->where(['user_id' =>  $user_id,
        'recipient' => 'FARMERS'])
        ->whereRaw('FIND_IN_SET("PUSH", type)')
        ->where(['views' => 0]);
        return $messages->orderBy('created_at', 'DESC')->limit(10)->get();
    }
    public function deleteNotification($id)
    {
        return ['success' => $this->message->find($id)->delete()];
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Notifications\MessageSent.php

```php
<?php
namespace Modules\Farmer\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\NotificationBinding;
use Modules\Farmer\Services\Firebase;
class MessageSent extends Notification
{
    use Queueable;
    private $notificationData;
    private $firebaseService;
    private $notificationBinding;
    public function __construct($notificationData)
    {
        $this->notificationData = $notificationData;
        $this->firebaseService = new Firebase(config('firebase'));
        $this->notificationBinding = new NotificationBinding();
    }
    public function via($notifiable)
    {
        return ['mail','sms', 'push', 'array','whatsapp'];
    }
    public function toPush($notifiable)
    {
        $identity = md5($notifiable->username);
        $notificationbinding = $this->notificationbinding->find($identity);
        $to = $notificationbinding->sid;
        $message['notification'] = [
            "body"  => $this->notificationData['content'],
            "title" => $this->notificationData['headline']
        ];
        if( $this->notificationData['data'] = 1) {
            $message['data'] = [
                "body"  => $this->notificationData['content'],
                "title" => $this->notificationData['headline']
            ];
        }
        $this->firebaseService->send($message);
    }
    public function toWhatsapp()
    {
        $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $number = substr($notifiable->phone_number, -9);
        $number = "+27".$number;
        $this->client->messages
        ->create("whatsapp:{$number}", // to
           ["from" => "whatsapp:+14155238886", "body" => $this->notificationData['content']]);
    }
    public function toSms($notifiable)
    {
        $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $number = substr($notifiable->phone_number, -9);
        $number = "+27".$number;
        $this->client->messages
                    ->create($number, // to
                        ["body" => $this->notificationData['content'], "from" => "+18049448749"]
                    );
    }
        public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line($this->notificationData['headline'])
                    ->line($this->notificationData['content']);
    }
        public function toArray($notifiable)
    {
        return $this->notificationData;
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Providers\FirebaseServiceProvider.php

```php
<?php
namespace Modules\Farmer\Providers;
use Illuminate\Support\ServiceProvider;
use Modules\Farmer\Services\Firebase;
class FirebaseServiceProvider extends ServiceProvider
{
        public function register()
    {
        $this->app->singleton(Firebase::class, function ($app) {
            return new Firebase(config('firebase'));
        });
    }
        public function provides()
    {
        return [];
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Services\Firebase.php

```php
<?php
namespace Modules\Farmer\Services;
class Firebase
{
    private  $config;
    public function __construct( array $config) {
        if(!isset($config['endpoint'])){
            throw  new \Exception("Endpoint needed");
        }
        if(!isset($config['server_key'])){
            throw  new \Exception("Server Key needed");
        }
        $this->config = $config;
    }
    public function send(array $notification) : string {
        if($this->validate($notification)){
            $response = 
            \Http::withToken($this->config['server_key'])
            ->post($this->config['endpoint'], $notification);
            return $response->body();
        }
    }
        public function validate($notification){
        if(!isset($notification['to']) ){
            throw  new \Exception("who is receiving the message?");
        }
        if(!isset($notification['notification']) && !isset($config['data'])){
            throw  new \Exception("No message");
        }
        return true;
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\app\Console\Commands\TwilioPushNotification.php

```php
<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;
use Twilio\Rest\Chat\V2\Service\Channel;
class TwilioPushNotification extends Command
{
    protected $signature = 'twilio:push';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        try {
            $client = new Client(config('services.twilio.account_sid'), config('services.twilio.auth_key'));
           // $channel = new Channel\MessageInstance();
            //$channel->
          //services.twilio.farmer_push_service
            $notification = $client->notify->v1->services(config("services.twilio.farmer_push_service"))
                ->notifications
                ->create([
                        //"body" => "Hello Bob",
                        "identity" => ["9e247c6d97b6e6bc9777b0a8ddc68961"],
                        'data' => ["title"=>"hey", "content"=>"I am testing from program"],
                        "apn" => ["aps" => ["alert" => ["body" => "I am testing from program.", "title"=>"hey"],
                            'data' => ["title"=>"hey", "content"=>"I am testing from program"]]
                        ],
                        'fcm' => [
                            'notification' => [
                                'title' =>  'hey',
                                'body' => "I am testing from program",
                                'data' => ["title"=>"hey", "content"=>"I am testing from program"],
                            ]
                        ]
                    ]
                );
            print_r($notification->toArray());
        }  catch(ConfigurationException  $ex){
            print($ex->getMessage());
        }
        return 0;
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\app\Http\Controllers\MessageController.php

```php
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Messages;
use Carbon\Carbon;
use App\Models\ModelHasRoles;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
class MessageController extends Controller
{
    //
    private $messages;
    private $client;
    public function __construct(Messages $messages, Client $client)
    {
        $this->messages = $messages;
        $this->client = $client;
    }
    public function getMessages()
    {
        return response()->json($this->messages());
    }
    public function postMessages(Request $request)
    {
        $role_id = $request->role_id;
        $recipients = ModelHasRoles::where(['role_id' => $role_id, 'model_type' => User::class])
            ->get(['model_id'])->pluck('model_id');
        foreach($recipients as $recipient) {
            //Save Message;
            //$message = new Messages;
            $input = $request->toArray();
            $input['user_id'] = $recipient;
            //print_r($request->type); exit;
            $input['type'] =  implode(",", $request->type);
            $user = User::find( $input['user_id'] );
            if(in_array('EMAIL', $request->type))
            {
                Mail::raw($input['content'], function ($message) use ($input, $user) {
                    $message->to($user->email)->subject($input['headline']);
                });
            }
            if(in_array('WHATSAPP', $request->type) && $user->phone_number)
            {
                $number = substr($user->phone_number, -9);
                $number = "+27".$number;
                $this->client->messages
                    ->create("whatsapp:{$number}", // to
                       ["from" => "whatsapp:+14155238886", "body" => $input['content']]);
            }
            if(in_array('SMS', $request->type) && $user->phone_number)
            {
                $number = substr($user->phone_number, -9);
                $number = "+27".$number;
                $this->client->messages
                    ->create($number, // to
                        ["body" => $input['content'], "from" => "+18049448749"]
                    );
               // print($message->sid);
            }
            if(in_array('PUSH', $request->type))
            {
                $notification_id  = md5($user->email);
                Log::info('notification', [
                    'body' => $input['content'],
                    'identity' => [$notification_id],
                    'data' => ["title"=>$input['headline'], "content"=>$input['content']],
                    'fcm' => [
                        'notification' => [
                            'title' =>  $input['headline'],
                            'body' => $input['content'],
                            'data' => ["title"=>$input['headline'], "content"=>$input['content']]
                        ]
                    ],
                    'apn' => [
                        'aps' => [
                            'alert' => [
                                'title' =>  $input['headline'],
                                'body' => $input['content'],
                                'data' => ["title"=>$input['headline'], "content"=>$input['content']]
                            ]
                        ]
                    ]
                ]);
                $response = $this->client->notify->v1->services(config("services.twilio.push_service"))
                    ->notifications
                    ->create([
                            'body' => $input['content'],
                            'identity' => [$notification_id],
                            'data' => ["title"=>$input['headline'], "content"=>$input['content']],
                            'fcm' => [
                                'notification' => [
                                    'title' =>  $input['headline'],
                                    'body' => $input['content'],
                                    'data' => ["title"=>$input['headline'], "content"=>$input['content']]
                                ]
                            ],
                            'apn' => [
                                'aps' => [
                                    'alert' => [
                                        'title' =>  $input['headline'],
                                        'body' => $input['content'],
                                        'data' => ["title"=>$input['headline'], "content"=>$input['content']]
                                    ]
                                ]
                            ]
                        ]
                    );
                    Log::info('response',  $response->toArray());
                }
                Messages::create($input);
        }
        return response()->json(['success' => 1]);
    }
    private function messages(): array
    {
       return $this->messages->get()->toArray();
    }
    public function scheduled()
    {
        $schedule_messages = $this->messages->whereNotNull('scheduled_at')->where('scheduled_at','>=', Carbon::now())
            ->groupby('name','content','scheduled_at')
            ->get(['content', \DB::raw('COUNT(DISTINCT(user_id)) as recipients'),\DB::raw('SUM(DISTINCT(views)) as views'), 'scheduled_at'])
            ->toArray();
        return response()->json($schedule_messages);
    }
    public function past_messages()
    {
        $messages = $this->messages->whereNull('scheduled_at')
            ->groupby('name','content','created_at')
            ->get(['content', \DB::raw('COUNT(DISTINCT(user_id)) as recipients'),\DB::raw('SUM(DISTINCT(views)) as views'), 'created_at'])
            ->toArray();
        return response()->json($messages);
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\app\Http\Controllers\Api\TwilioController.php

```php
<?php
namespace App\Http\Controllers\Api;
use App\Providers\TwilioServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use NotificationChannels\Twilio\Twilio;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;
use App\Models\NotificationBinding;
class TwilioController
{
    protected $client;
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    public function registerDevice(Request $request)
    {
        $notification_binding = NotificationBinding::find($request->identity);
        if($notification_binding) {
            try {
                $this->client->notify->v1->services(config("services.twilio.push_service"))
                    ->bindings($notification_binding->sid)
                    ->delete();
            } catch(\Exception $e){
                print_r($e->getMessage());
            }
            $notification_binding->delete();
        }
       // dd($request->toArray());
        $request = $request->toArray();
        try {
            // print_r($request);
            // exit;
            $binding = $this->client->notify->v1->services(config("services.twilio.push_service"))
                ->bindings
                ->create($request['identity'], $request['binding_type'], $request['address']);
            return $binding->toArray();
        } catch(\Exception $ex) {
            return $ex;
        }
    }
    public function getBindingForDeviceId($device_id) {
        return NotificationBinding::Where('device_id', $device_id)->first();
    }
    public function createMember(Request $request)
    {
        try {
            $channel_sid = $request->channel_id;
            $username = $request->username;
            return $this->client->chat->v2->services(config('services.twilio.chat_service'))
                ->channels($channel_sid)
                ->members
                ->create(md5($username));
        }
        catch (RestException $exception)
        {
            return ['success' => false, 'error' => $exception->getMessage()];
        }
    }
    public function sendEmail(Request $request)
    {
        return ['success' => Mail::raw($request->message, function ($message) use ($request) {
            $message->to($request->to)->subject($request->title);
        })];
    }
    public function sendSMS(Request $request)
    {
        $message = $this->client->messages
            ->create($request->to, // to
                ["body" => $request->message, "from" => config('services.twilio.phone_number')]
            );
        return $message->toArray();
    }
    public function sendWHATSAPP(Request $request)
    {
        $message = $this->client->messages
            ->create("whatsapp:{$request->to}", // to
                ["from" => "whatsapp:".config('services.twilio.whatsapp_number'), "body" => $request->message]);
        return $message->toArray();
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\app\Models\User.php

```php
<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $fillable = [
        'name',
        'email',
        'password',
        'surname',
        'profile_pic',
        'phone_number',
        'remember_token'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function fileAccessLogs()
    {
        return  $this->hasMany(FileAccessLog::class);
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\app\Notifications\InspireUser.php

```php
<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\Twilio;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
class InspireUser extends Notification
{
    use Queueable;
    public $quote;
    public function __construct($quote)
    {
        $this->quote = $quote;
    }
    public function via($notifiable)
    {
        return [TwilioChannel::class];
    }
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Entities\Farmer.php

```php
<?php
namespace Modules\Farmer\Entities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class Farmer extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['name','surname','email','number', 'field', 'username', 'password', 'categories', 'auto',
        'field', 'location','active', 'notify', 'profile_pic', 'dob'];
    public function category()
    {
        return $this->belongsTo('Modules\Farmer\Entities\FarmerCategory');
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Http\Controllers\MessageController.php

```php
<?php
namespace Modules\Farmer\Http\Controllers;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Twilio\Rest\Client;
use Modules\Farmer\Entities\Message;
use Modules\Farmer\Entities\Farmer;
use Modules\Farmer\Entities\FarmerCategory;
use Modules\Farmer\Services\Firebase;
use App\Models\NotificationBinding;
class MessageController extends Controller
{
    private $client;
    private $message;
    private $firebaseService;
    private $notificationBinding;
    public function __construct(Message $message, Client $client,  NotificationBinding $notificationBinding)
    {
        $this->message = $message;
        $this->client = $client;
        $this->firebaseService = new Firebase(config('services.firebase'));
        $this->notificationBinding = $notificationBinding;
    }
        public function index()
    {
        return $this->message->where(['recipient' => 'FARMERS'])
            ->orderby('created_at')
            ->groupBy('headline')
            ->get(['headline', 'content','created_at', 'type', 'meta_data', \DB::raw('count(*) as ct')]);
    }
    public function send(Request $request)
    {
        $categories = $request->categories;
        $list = [];
        array_walk($categories, function($category) use (&$list){
            array_push($list, $category['text']);
        });
        $farmers = [];
        if( in_array('all', $list))
        {
            $farmers  = Farmer::all();
        } else
        {
            //$category = FarmerCategory::find($category_id);
           // $farmers  =  $category->farmers;
            $farmers = [];
            foreach($list as $category_name)
            {
                $farmer = new Farmer;
                $db_list = $farmer
                    ->where('categories', 'LIKE', "%{$category_name}%")
                    ->get();
                foreach( $db_list as $f)
                {
                    if(!in_array($f, $farmers))
                    {
                        array_push($farmers, $f);
                    }
                }
            }
        }
        foreach($farmers  as $farmer) {
            //Save Message;
            //$message = new Messages;
            $input = $request->toArray();
            $input['user_id'] = $farmer->id;
            //print_r($request->type); exit;
            $input['type'] =  implode(",", $request->type);
            $input['content'] = strip_tags($input['content']);
            if(in_array('EMAIL', $request->type))
            {
                \Mail::raw($input['content'], function ($message) use ($input, $farmer) {
                    $message->to($farmer->email)->subject($input['headline']);
                });
            }
            if(in_array('WHATSAPP', $request->type) && $farmer->number)
            {
                $number = substr($farmer->number, -9);
                $number = "+27".$number;
                $this->client->messages
                    ->create("whatsapp:{$number}", // to
                       ["from" => "whatsapp:+14155238886", "body" => strip_tags($input['content'])]);
            }
            if(in_array('SMS', $request->type) && $farmer->number)
            {
                $number = substr($farmer->number, -9);
                $number = "+27".$number;
                $this->client->messages
                    ->create($number, // to
                        ["body" => strip_tags($input['content']), "from" => "+18049448749"]
                    );
               // print($message->sid);
            }
            if(in_array('PUSH', $request->type) && $farmer->notify)
            {
                Log::info("Sending push notification", ['farmer_id' => $farmer->id]);
                $identity = md5($farmer->username);
                $notificationBinding = $this->notificationBinding->find($identity);
                if($notificationBinding)
                {
                    $message['to'] = $notificationBinding->sid;
                    $message['notification'] = ["title"=>$input['headline'], "body"=>$input['content']];
                    $message['data'] = ["title"=>$input['headline'], "body"=>$input['content']];
                    $this->firebaseService->send($message);
                }
            }
            if( $request->news_id['id'] != -1 )
            {
                    $input['meta_data'] = json_encode($request->news_id);
            }
            $input['recipient'] = "FARMERS";
            Message::create($input);
        }
        return ['success' => 1];
    }
    public function setViewed($id)
    {
        $message = $this->message->find($id);
        $message->views = 1;
        $message->save();
        return ['success' => 1];
    }
    public function getUserPushNotifications($user_id)
    {
        $messages = $this->message->where(['user_id' =>  $user_id,
        'recipient' => 'FARMERS'])
        ->whereRaw('FIND_IN_SET("PUSH", type)')
        ->where(['views' => 0]);
        return $messages->orderBy('created_at', 'DESC')->limit(10)->get();
    }
    public function deleteNotification($id)
    {
        return ['success' => $this->message->find($id)->delete()];
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Http\Controllers\NotificationController.php

```php
<?php
namespace Modules\Farmer\Http\Controllers;
use Twilio\Rest\Client;
use App\Models\NotificationBinding;
use Illuminate\Http\Request;
class NotificationController 
{
    private $notificationBinding;
    public function __construct(NotificationBinding $notificationBinding)
    {
        $this->notificationBinding = $notificationBinding;
    }
    public function registerDevice(Request $request)
    {
        $notification_binding = NotificationBinding::find($request->identity);
        $request = $request->toArray();
        \Log::debug($request);
        if(!$notification_binding) 
        {
            $notification_binding = new NotificationBinding;
        }
        $notification_binding->identity = $request['identity'];
        $notification_binding->type = $request['binding_type'];
        $notification_binding->device_id = $request['device_id'];
        $notification_binding->sid = $request['address'];
        $save = $notification_binding->save();
        \Log::debug([$save]);
        return ['success' => $save];
    }
    public function getBindingForDeviceId($device_id) 
    {
        return NotificationBinding::Where('device_id', $device_id)->first();
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Notifications\MessageSent.php

```php
<?php
namespace Modules\Farmer\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\NotificationBinding;
use Modules\Farmer\Services\Firebase;
class MessageSent extends Notification
{
    use Queueable;
    private $notificationData;
    private $firebaseService;
    private $notificationBinding;
        public function __construct($notificationData)
    {
        $this->notificationData = $notificationData;
        $this->firebaseService = new Firebase(config('firebase'));
        $this->notificationBinding = new NotificationBinding();
    }
        public function via($notifiable)
    {
        return ['mail','sms', 'push', 'array','whatsapp'];
    }
    public function toPush($notifiable)
    {
        $identity = md5($notifiable->username);
        $notificationbinding = $this->notificationbinding->find($identity);
        $to = $notificationbinding->sid;
        $message['notification'] = [
            "body"  => $this->notificationData['content'],
            "title" => $this->notificationData['headline']
        ];
        if( $this->notificationData['data'] = 1) {
            $message['data'] = [
                "body"  => $this->notificationData['content'],
                "title" => $this->notificationData['headline']
            ];
        }
        $this->firebaseService->send($message);
    }
    public function toWhatsapp()
    {
        $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $number = substr($notifiable->phone_number, -9);
        $number = "+27".$number;
        $this->client->messages
        ->create("whatsapp:{$number}", // to
           ["from" => "whatsapp:+14155238886", "body" => $this->notificationData['content']]);
    }
        public function toSms($notifiable)
    {
        $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $number = substr($notifiable->phone_number, -9);
        $number = "+27".$number;
        $this->client->messages
                    ->create($number, // to
                        ["body" => $this->notificationData['content'], "from" => "+18049448749"]
                    );
    }
        public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line($this->notificationData['headline'])
                    ->line($this->notificationData['content']);
    }
        public function toArray($notifiable)
    {
        return $this->notificationData;
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Routes\api.php

```php
// other routes above
Route::post('notification/registerDevice', [Modules\Farmer\Http\Controllers\NotificationController::class, 'registerDevice']);
   Route::get('notification/deviceBinding/{device_id}', [Modules\Farmer\Http\Controllers\NotificationController::class, 'getBindingForDeviceId']);
   Route::get('notification/messages/{user_id}', [Modules\Farmer\Http\Controllers\MessageController::class, 'getUserPushNotifications']);
   Route::put('notification/messages/{id}', [Modules\Farmer\Http\Controllers\MessageController::class, 'setViewed']);
   Route::delete('notification/messages/{id}', [Modules\Farmer\Http\Controllers\MessageController::class, 'deleteNotification']);
// More routes below
```
C:\dev\www\pioneer.superadmin.co.za\Modules\Farmer\Services\Firebase.php

```php
<?php
namespace Modules\Farmer\Services;
class Firebase
{
    private  $config;
    public function __construct( array $config) {
        if(!isset($config['endpoint'])){
            throw  new \Exception("Endpoint needed");
        }
        if(!isset($config['server_key'])){
            throw  new \Exception("Server Key needed");
        }
        $this->config = $config;
    }
    public function send(array $notification) : string {
        if($this->validate($notification)){
            $response = 
            \Http::withToken($this->config['server_key'])
            ->post($this->config['endpoint'], $notification);
            return $response->body();
        }
    }
        public function validate($notification){
        if(!isset($notification['to']) ){
            throw  new \Exception("who is receiving the message?");
        }
        if(!isset($notification['notification']) && !isset($config['data'])){
            throw  new \Exception("No message");
        }
        return true;
    }
}
```
C:\dev\www\pioneer.superadmin.co.za\resources\js\components\blocks\Notification.vue
```vue
<template>
        <div class="fm-notification">
            <transition-group name="notify">
                <div class="fm-notification-item" role="alert"
                     v-for="(notification, index) in notifications"
                     v-bind:class="`fm-${notification.status}`"
                     v-bind:key="`notify-${index}`">
                    {{ notification.message }}
                </div>
            </transition-group>
        </div>
</template>
<script>
import EventBus from '../../eventBus';
export default {
  name: 'notification',
  data() {
    return {
      notifications: [],
    };
  },
  mounted() {
        EventBus.$on('addNotification', ({ status, message }) => this.addNotification(status, message));
  },
  methods: {
        addNotification(status, message) {
      this.notifications.push({
        status, message,
      });
      // timeout for closing
      setTimeout(() => {
        this.notifications.shift();
      }, 3000);
    },
  },
};
</script>
<style lang="scss">
    .fm-notification {
        position: absolute;
        right: 1rem;
        bottom: 0;
        z-index: 9999;
        width: 350px;
        display: block;
        transition: opacity .4s ease;
        overflow: auto;
        .fm-notification-item {
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid;
            border-radius: .25rem;
        }
        .notify-enter-active {
            transition: all .3s ease;
        }
        .notify-leave-active {
            transition: all .8s ease;
        }
        .notify-enter, .notify-leave-to {
            opacity: 0;
        }
    }
</style>
```
Okay I made a mental note of that... "Do not switch off critical systems" ✅

I'm positive that everything will be fine.

I am in the process of adding a testing application to the server, it run on the server and as a chrome extension. It will test every button and form field and validation and check for correct error messages and status codes etc.

It will also give us some real time feedback, there are a few commercial systems like this available but they are not always testing the frontend or app like a human could use it, most of them do static analysis of the code on the sever (That means it runs through the code and according to the structures it makes the best estimate of what the code could do but not always having them libraries of the 3rd party things we used like firebase it's not always very accurate) The one I created actually runs on the site as a user and can actually see their website like a human does and it will create screenshots of where it finds mistakes and send it through me and tell me exactly how it how it got to making that. It's basically an AI testing assistant that can see what we do and tries to use the website by actually clicking with the mouse and using the keyboard input for completing form fields. So it will give us a nice breakdown of what is working and what could be better and where there are problems from the view point of a person