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
use Illuminate\Support\Facades\Log;


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

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return $this->message->where(['recipient' => 'FARMERS'])
            ->orderby('created_at')
            ->groupBy('headline')
            ->get(['headline', 'content', 'created_at', 'type', 'meta_data', \DB::raw('count(*) as ct')]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
    }

    public function send(Request $request) {
        Log::info('NOTIFICATION: process started by user.');
        Log::info('NOTIFICATION: Message details: ', $request->all());

        $categories = $request->categories;

        $list = [];
        array_walk($categories, function ($category) use (&$list) {
            array_push($list, $category['text']);
        });

        $farmers = [];
        if (in_array('all', $list)) {
            $farmers  = Farmer::all();
        } else {
            //$category = FarmerCategory::find($category_id);
            // $farmers  =  $category->farmers;
            $farmers = [];
            foreach ($list as $category_name) {

                $farmer = new Farmer;
                $db_list = $farmer
                    ->where('categories', 'LIKE', "%{$category_name}%")
                    ->get();

                foreach ($db_list as $f) {
                    if (!in_array($f, $farmers)) {
                        array_push($farmers, $f);
                    }
                }
            }
        }

        foreach ($farmers  as $farmer) {
            Log::info("NOTIFICATION: Preparing to send notification to farmer: ", [
                'farmer_id' => $farmer->id
            ]);
            //Save Message;
            //$message = new Messages;
            $input = $request->toArray();
            $input['user_id'] = $farmer->id;
            //print_r($request->type); exit;
            $input['type'] =  implode(",", $request->type);
            $input['content'] = strip_tags($input['content']);

            if (in_array('EMAIL', $request->type)) {
                \Mail::raw($input['content'], function ($message) use ($input, $farmer) {
                    $message->to($farmer->email)->subject($input['headline']);
                });
            }

            if (in_array('WHATSAPP', $request->type) && $farmer->number) {
                $number = substr($farmer->number, -9);
                $number = "+27" . $number;

                $this->client->messages
                    ->create(
                        "whatsapp:{$number}", // to
                        ["from" => "whatsapp:+14155238886", "body" => strip_tags($input['content'])]
                    );
            }
            if (in_array('SMS', $request->type) && $farmer->number) {
                $number = substr($farmer->number, -9);
                $number = "+27" . $number;
                $this->client->messages
                    ->create(
                        $number, // to
                        ["body" => strip_tags($input['content']), "from" => "+18049448749"]
                    );
            }

            if (in_array('PUSH', $request->type) && $farmer->notify) {
                Log::info("NOTIFICATION: Sending push notification", ['farmer_id' => $farmer->id]);

                $identity = md5($farmer->username);
                $notificationBinding = $this->notificationBinding->find($identity);
                if ($notificationBinding) {
                    $message['to'] = $notificationBinding->sid;
                    $message['notification'] = ["title" => $input['headline'], "body" => $input['content']];
                    $message['data'] = ["title" => $input['headline'], "body" => $input['content']];
                    try {
                        $this->firebaseService->send($message);
                    } catch (\Exception $e) {
                        Log::info("NOTIFICATION: Push notification failed", ['farmer_id' => $farmer->id]);
                    }
                } else {
                    Log::info("NOTIFICATION: No notification binding found", ['farmer_id' => $farmer->id]);
                }
                Log::info("NOTIFICATION: Push notification sent", ['farmer_id' => $farmer->id]);
            }
            if ($request->news_id['id'] != -1) {
                $input['meta_data'] = json_encode($request->news_id);
            }
            $input['recipient'] = "FARMERS";
            Message::create($input);
        }
        return ['success' => 1];
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
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
        $messages = $this->message->where([
            'user_id' =>  $user_id,
            'recipient' => 'FARMERS'
        ])
            ->whereRaw('FIND_IN_SET("PUSH", type)')
            ->where(['views' => 0]);
        return $messages->orderBy('created_at', 'DESC')->limit(10)->get();
    }
    public function deleteNotification($id)
    {
        return ['success' => $this->message->find($id)->delete()];
    }
}
