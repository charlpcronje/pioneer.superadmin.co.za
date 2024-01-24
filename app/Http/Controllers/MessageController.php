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
    private $messages;
    private $client;

    public function __construct(Messages $messages, Client $client)
    {
        $this->messages = $messages;
        $this->client = $client;
    }

    private function messages(): array {
        return $this->messages->get()->toArray();
    }
 

    public function getMessages() {
        return response()->json($this->messages());
    }


    /**
     * Handle the process of sending messages through various channels.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postMessages(Request $request)
    {
        $role_id = $request->role_id;
    
        // Fetching recipients based on role_id
        $recipients = ModelHasRoles::where(['role_id' => $role_id, 'model_type' => User::class])
            ->get(['model_id'])->pluck('model_id');
    
        foreach ($recipients as $recipient) {
            // Initialize input array
            $input = $request->toArray();
            $input['user_id'] = $recipient;
            $input['type'] = implode(",", $request->type);
    
            try {
                $user = User::find($input['user_id']);
                if (!$user) {
                    throw new \Exception("User not found for ID: {$input['user_id']}");
                }
    
                $this->processMessageTypes($input, $user);
                Messages::create($input);
            } catch (\Exception $e) {
                Log::error('Error in sending message to user: ' . $e->getMessage());
                // You might want to return an error response or continue processing other recipients
            }
        }
        return response()->json(['success' => 1]);
    }

    /**
     * Process and send message based on type.
     *
     * @param array $input
     * @param User $user
     * @return void
     */
    private function processMessageTypes(array $input, User $user)
    {
        if (in_array('EMAIL', $input['type'])) {
            $this->sendEmail($input, $user);
        }

        if (in_array('WHATSAPP', $input['type']) && $user->phone_number) {
            $this->sendWhatsApp($input, $user);
        }

        if (in_array('SMS', $input['type']) && $user->phone_number) {
            $this->sendSMS($input, $user);
        }

        if (in_array('PUSH', $input['type'])) {
            $this->sendPushNotification($input, $user);
        }
    }


     /**
     * Send an email message to the user.
     *
     * @param array $input
     * @param User $user
     * @return void
     */
    private function sendEmail(array $input, User $user) {
        try {
            Mail::raw($input['content'], function ($message) use ($input, $user) {
                $message->to($user->email)->subject($input['headline']);
            });
            Log::info("Email sent successfully to {$user->email}");
        } catch (\Exception $e) {
            Log::error("Error sending email: " . $e->getMessage());
        }
    }

    /**
     * Send a WhatsApp message to the user.
     *
     * @param array $input
     * @param User $user
     * @return void
     */
    private function sendWhatsApp(array $input, User $user)
    {
        try {
            $number = $this->formatPhoneNumber($user->phone_number);
            $this->client->messages->create(
                "whatsapp:{$number}",
                ["from" => "whatsapp:+14155238886", "body" => $input['content']]
            );
            Log::info("WhatsApp message sent to {$number}");
        } catch (\Exception $e) {
            Log::error("Error sending WhatsApp message: " . $e->getMessage());
        }
    }

    /**
     * Format the phone number for WhatsApp.
     *
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber($phoneNumber)
    {
        $number = substr($phoneNumber, -9);
        return "+27" . $number;
    }

    /**
     * Send an SMS message to the user.
     *
     * @param array $input
     * @param User $user
     * @return void
     */
    private function sendSMS(array $input, User $user)
    {
        try {
            $number = $this->formatPhoneNumber($user->phone_number);
            $this->client->messages->create(
                $number,
                ["body" => $input['content'], "from" => "+18049448749"]
            );
            Log::info("SMS sent to {$number}");
        } catch (\Exception $e) {
            Log::error("Error sending SMS: " . $e->getMessage());
        }
    }


    
    /**
     * Send a push notification to the user.
     *
     * @param array $input
     * @param User $user
     * @return void
     */
    private function sendPushNotification(array $input, User $user) {
        try {
            $notification_id = md5($user->email);

            // Construct the payload
            $payload = [
                'body' => $input['content'],
                'identity' => [$notification_id],
                'data' => ["title" => $input['headline'], "content" => $input['content']],
                'fcm' => [
                    'notification' => [
                        'title' => $input['headline'],
                        'body' => $input['content'],
                        'data' => ["title" => $input['headline'], "content" => $input['content']]
                    ]
                ],
                'apn' => [
                    'aps' => [
                        'alert' => [
                            'title' => $input['headline'],
                            'body' => $input['content'],
                            'data' => ["title" => $input['headline'], "content" => $input['content']]
                        ]
                    ]
                ]
            ];

            // Log the payload before sending
            Log::info('notification', $payload);

            // Send the notification
            $response = $this->client->notify->v1->services(config("services.twilio.push_service"))
                ->notifications->create($payload);

            // Log the response
            Log::info('response',  $response->toArray());
        } catch (\Exception $e) {
            Log::error("Error sending push notification: " . $e->getMessage());
        }
    }


   

    public function scheduled() {
        $schedule_messages = $this->messages->whereNotNull('scheduled_at')->where('scheduled_at','>=', Carbon::now())
            ->groupby('name','content','scheduled_at')
            ->get(['content', \DB::raw('COUNT(DISTINCT(user_id)) as recipients'),\DB::raw('SUM(DISTINCT(views)) as views'), 'scheduled_at'])
            ->toArray();

        return response()->json($schedule_messages);
    }
    
    public function past_messages(){
        $messages = $this->messages->whereNull('scheduled_at')
            ->groupby('name','content','created_at')
            ->get(['content', \DB::raw('COUNT(DISTINCT(user_id)) as recipients'),\DB::raw('SUM(DISTINCT(views)) as views'), 'created_at'])
            ->toArray();
        return response()->json($messages);
    }

}
