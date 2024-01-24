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


   /* protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    public function registerDevice(Request $request)
    {
        $notification_binding = NotificationBinding::find($request->identity);
        if($notification_binding) {
            try {
                $this->client->notify->v1->services(config("services.twilio.farmer_push_service"))
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
            $address = (string) $request['address'];
            print_r($address); exit;
        
            // exit;
            $binding = $this->client->notify->v1->services(config("services.twilio.farmer_push_service"))
                ->bindings
                ->create($request['identity'], $request['binding_type'], $address);
            return $binding->toArray();
        } catch(\Exception $ex) {
            return $ex;
        }
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

    }*/

}