<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Farmer\Entities\Farmer;
use Modules\Farmer\Entities\FarmerCategory;
use Modules\Farmer\Entities\LoginLog;
use  Modules\Farmer\Http\Requests\FarmerRequest;
use Twilio\Rest\Client;



class FarmerController extends Controller
{
    private $client;


    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $farmers = Farmer::all()->toArray();
        //array_walk($farmers, [$this, '__']);

        return $farmers;
    }


    private function  __(&$value, $key)
    {
        $categories = $value['categories'];
        //$category = FarmerCategory::find($category_id);
        $value['category'] = $categories;
    }



    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {

    }

    public function getUserLogins()
    {
        return Farmer::whereNotNull("login_date")->orderBy('login_date','desc')->get();
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(FarmerRequest $request)
    {
        $post = $request->toArray();
        $farmer  = Farmer::where(['username' => $post['username']])->get()->first();
        if($farmer)
        {
            return ['success' => 0, 'message' => "The username {$post['username']} is already"];
        }


        if(isset($post['auto']) && $post['auto']) {
            $username = substr(sha1($request['email']), 0, 6);
            $password = substr(uniqid(md5($request['email']), true), 0, 6);
            $post['password'] = \Hash::make($password);
            $post['username'] = $username;

            // $cc_emails[];

            Mail::send("emails.farmer_set", ['name' => $request->name . " " . $request->surname, 'password' => $password,
                'username' => $username], function ($message) use ($post) {
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->to($post['email'], $post['name'])->subject("New User registration");

            });
        } else {
            $post['auto'] = 0;
            $post['password'] = \Hash::make( $post['password']);
        }


        $post['notify'] = $post['notify'] ? 1: 0;
        $post['categories'] = $post['category_id'];
      

        unset($post['category_id']);

        if($request->hasFile('profile_pic')) {
            $profile_pic = $request->file('profile_pic');
            $filename = uniqid(md5(time())).".".$profile_pic->getClientOriginalExtension();

            $profile_pic->move(public_path("images/profiles"), $filename);
            $post['profile_pic'] = $filename;
        }

        if(isset($post['display_date']))
        {
            unset($post['display_date']);
        }
        $post['number'] = "0".$post['number'];


        unset($post['id']);
        Log::debug($post);
        $success = Farmer::create($post);
        if($success && !isset($request->auto) && isset($post['number']))
        {
            $number = substr($post['number'], -9);
                $number = "+27".$number;
                $this->client->messages
                    ->create($number, // to
                        ["body" => "Dear {$post['name']} {$post['surname']}, thank you for your registration to the Pioneer Farmer App." , "from" => "+18049448749"]
                    );


        }

        return ['success' => $success ];
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $farmer = Farmer::find($id);
        if($farmer->profile_pic) {
            
            $farmer->profile_pic = "https://pioneer.superadmin.co.za/images/profiles/".$farmer->profile_pic;
        }
        return  $farmer;
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('farmer::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $farmer = Farmer::find($id);
        $post = $request->toArray();
       


        unset($post['id']);
        if(isset($post['category_id'])) {
            $post['categories'] = $post['category_id'];
            unset($post['category_id']);
        } else {
            $categories = [];
           // dd($post['categories'] );
            
            foreach( $post['categories'] as $category) {
                array_push($categories, $category['text']);
                //$categories[] = $category['text'];
            }

            //$categories = $categories.join(',');

            $post['categories'] = join(",",$categories);
        }

        if($request->hasFile('profile_pic')) {
            $profile_pic = $request->file('profile_pic');
            $filename = uniqid(md5(time())).".".$profile_pic->getClientOriginalExtension();

            $profile_pic->move(public_path("images/profiles"), $filename);
            $post['profile_pic'] = $filename;
        }

        if(isset($post['display_date']))
        {
            unset($post['display_date']);
        }
        $farmer->fill($post);

        return ['success' => $farmer->save()];
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        return ['success' =>Farmer::findorfail($id)->delete()];
    }

    public function forgot_password(Request $request)
    {

        $farmer = Farmer::where(['email' => $request->email])->first();
        if(!$farmer)
        {
            return ['success' => 0, 'message' => 'User with email '.$request->email." was not found in the system"];
        }
        $remember_token = sha1(uniqid(time()));
        $farmer->remember_token = $remember_token;
        $farmer->save();

        $link = env('APP_URL') . '/reset_password?tenant=1&web=1&token=' . $remember_token;
        $farmer = $farmer->toArray();
        Mail::send("emails.reset_password", ['link' => $link, 'user' => $farmer], function ($message) use ($farmer) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->to($farmer['email'], $farmer['name'] . " " . $farmer['surname'])
                ->cc("sergi@id8collab.com", "Sergi Passos")->subject("Reset Password");
        });
        return ['success' => 1];


    }


    public function auth(Request $request)
    {
        \Log::debug($request->toArray());

        $user = Farmer::where(['username' => $request->username])->get();
        if(!$user->count())
        {
            return ['success' => 0, 'error' => 'User not found'];

        }
        $user = $user->first();



        if (\Hash::check( $request->password,$user->password))
        {
            //Log Logins and Update last login

            LoginLog::create(['user_id' => $user->id]);
            $user->login_date =  \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $user->save();
            return ['success' => 1, 'user' => $user];
        }
        return ['success' => 0, 'error' => 'Password incorrect for '.$user->name];


    }
}
