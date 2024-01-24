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
