<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;
use Twilio\Rest\Chat\V2\Service\Channel;

class TwilioPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        try {

            $client = new Client(config('services.twilio.account_sid'), config('services.twilio.auth_key'));
           // $channel = new Channel\MessageInstance();
            //$channel->


          /* $service = $client->chat->v2->services(config('services.twilio.chat_service'))
                ->update(array(
                        "notificationsAddedToChannelEnabled" => True,
                        "notificationsAddedToChannelSound" => "default",
                       // "notificationsAddedToChannelTemplate" => "A New message in ${CHANNEL} from ${USER}: ${MESSAGE}"
                    )
                );*/

           /* $client->chat->v2->services(config('services.twilio.chat_service'))
                ->channels("CHcede350779114c1aaaa07d66c7cc5832")->delete();

            $channel = $client->chat->v2->services(config('services.twilio.chat_service'))
                ->channels
                ->create(['friendlyName' => "Farmers", 'uniqueName' => md5(time())]);

           // $channel->friendlyName = "Farmers";
            //$channel->update();


            print_r($channel);
           */
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
