<?php

namespace App\Http\Controllers\Api;

use App\Exports\UserLoginsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ModelHasRoles;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;


class UserController extends Controller
{

    public function farmer_admin_auth(Request $request){

        

        $user = User::where(['email' => $request->email])->get();
        if(!$user->count())
        {
            return ['success' => 0, 'error' => 'User not found'];

        }
        $user = $user->first();

        $model_has_role = ModelHasRoles::where(['model_type' => User::class,
            'model_id' => $user->id])->get()->first();
        $role = Role::findById($model_has_role->role_id);
        if($role->name != "Admin") {
            return ['success' => 0, 'error' => 'you do not have permission to access this portal.
                 Check with your system administrator and try again later'];
        }

        if (\Hash::check( $request->password,$user->password))
        {
            return ['success' => 1, 'user' => $user];
        }
        return ['success' => 0, 'error' => 'Password incorrect for '.$user->name];


    }
    //
    public function auth(Request $request)
    {

        $user = User::where(['email' => $request->username])->get()->first();
        if($user)
        {
            if (\Hash::check( $request->password,$user->password))
            {
               // $user->role = $user->roles()->get()->first();
                if($user->email_verified_at == null) {
                    return ['success' => 0, 'error' => 'You need to verifiy your email'];
                }

                $user->login_date = Carbon::now()->format('Y-m-d H:i:s');
                $user->save();

                return ['success' => 1, 'user' => $user];
            }
            return ['success' => 0, 'error' => 'Password incorrect for '.$user->name];
        }
        return ['success' => 0, 'error' => 'User not found'];
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $request = $request->toArray();


        if($user->profile_pic && isset($request['profile_pic'])){
            unlink(public_path('images/profiles/'.$user->profile_pic));

        }
        if(isset($request['profile_pic'])) {
            file_put_contents(public_path('images/profiles/' . $request['profile_pic']['filename']),
                base64_decode($request['profile_pic']['fileData']));
          //  Storage::put(public_path('images/profiles/' . $request['profile_pic']['filename']),
            //    $request['profile_pic']['fileData']);
            $request['profile_pic'] = $request['profile_pic']['filename'];
        }
        try {
            return response()->json(['success' => $user->update($request)]);
        } catch(Exception $e){
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }

    }


    public function getUserLogins()
    {
        return User::whereNotNull("login_date")->orderBy('login_date','desc')->get();
    }

    public function downloadUserLogins(Request $request)
    {
        return Excel::download(new UserLoginsExport, 'user_logins.xlsx');

    }

    public function forgot_password(Request $request)
    {

        $user = User::where(['email' => $request->email])->first();
        if($user === null)
        {
            return ['success' => 0, 'message' => 'User with email '.$request->email." was not found in the system"];
        }

        $remember_token = sha1(uniqid(time()));
        $user->remember_token = $remember_token;
        $user->save();

        $link = env('APP_URL') . '/reset_password?token=' . $remember_token;
        $user = $user->toArray();
        Mail::send("emails.reset_password", ['link' => $link, 'user' => $user], function ($message) use ($user) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->to($user['email'], $user['name'] . " " . $user['surname'])
                ->cc("sergi@id8collab.com", "Sergi Passos")->subject("Reset Password");
        });
        return ['success' => 1];


    }

    public function registerUser(Request $request)
    {
        $request = $request->toArray();
        /*
       if(isset($request['profile_pic'])) {
           file_put_contents(public_path('images/profiles/' . $request['profile_pic']['filename']),
               base64_decode($request['profile_pic']['fileData']));
           //  Storage::put(public_path('images/profiles/' . $request['profile_pic']['filename']),
           //    $request['profile_pic']['fileData']);
           $request['profile_pic'] = $request['profile_pic']['filename'];
       }

       $request['password'] = \Hash::make($request['password']);
       $remember_token = sha1(uniqid(time()));
       $request['remember_token'] = $remember_token;

       try {
           $user = User::create($request);

           $role = Role::findByName('Internal Staff');

           $permissions = Permission::pluck('id', 'id')->all();
           $role->syncPermissions($permissions);
           $user->assignRole([$role->id]);

           $link = env('APP_URL') . '/verify_email?token=' . $remember_token;


           Mail::send("emails.confirmation", ['link' => $link, 'user' => $user], function ($message) use ($user) {
               $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
               $message->to($user['email'], $user['name'] . " " . $user['surname'])->subject("Email Confirmation");
           });

           return ['success' => 1, 'message' => 'You registration process has been successful. Please check your email for verification'];
       } catch(\Exception $ex)
       {
           return ['success' => 0, 'message' => 'There has been an error while saving your details please try again later'];

       }*/

        return ['success' => 0, 'message' => 'You will need admin permission toArray been able to register to this application'];

    }




}
