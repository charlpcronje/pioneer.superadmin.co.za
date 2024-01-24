<?php
namespace Modules\Farmer\Services;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
class Firebase {

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
        Log::info('Sending notification to Firebase', $notification);
        if($this->validate($notification)){
            $response = Http::withToken($this->config['server_key'])->post($this->config['endpoint'], $notification);
            Log::info('Firebase response', ['response' => $response->body()]);
            return $response->body();
        } else {
            Log::info('Invalid notification', [$notification]);
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