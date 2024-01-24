<?php

namespace App\Http\Controllers;

use App\Models\ModelHasRoles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Modules\Farmer\Entities\Farmer;


class UserController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        $role = null;
        if($user != null)
        {
            $model_has_role = ModelHasRoles::where(['model_type' => User::class,
                'model_id' => $user->getAuthIdentifier()])->get()->first();
            if($model_has_role)
            {
                $role = Role::findById($model_has_role->role_id);
            }
        }
        $users = [];

        if($role->name == "Admin")
        {
            $users = User::all()->toArray();
        }
        else
        {
           // $admin = Role::findByName('Admin');
            //$users = User::whereNotIn('id', ModelHasRoles::where('role_id', '=', $admin->id)->pluck('id','id')->all())
            //->get()->toArray();
            $users = User::all()->toArray();
            $users = array_filter($users, function ($user){
                $u  = User::find($user['id']);
                if($u->roles()->first()->name != 'Admin'){
                    return $user;
                }
            });
        }

        array_walk($users, [$this, 'addRole']);
        return response()->json($users);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $user->role = $user->roles()->first();
        return response()->json($user);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(User $user)
    {
        return response()->json($user);
    }

    /**
     * @param Request $request
     * @param User $user
     * @throws \Exception
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->notes = $request->notes;
        $role = Role::findById($request->role_id);

        if($role->id != $user->roles()->first()->id){
            $user->removeRole($user->roles()->first());
            $user->assignRole($role);
        }





        if($user->send_password)
        {
            $admin_users = User::all()->toArray();
            $admin_users = array_filter($admin_users, function ($user){
                $u  = User::find($user['id']);
                if($u->roles()->first()->name == 'Admin'){
                    return $user;
                }
            });
           // $cc_emails = ["sergi@id8collab.com", "anthony@fgx.co.za"];
            foreach($admin_users as $admin_user)
            {
               // array_push($cc_emails, $admin_user['email']);
            }

            $password = substr(md5(time()), 0, 6).random_int(111,999);
            $user->password = bcrypt($password);
            Mail::send("emails.password_set", ['name' => $request->name, 'password' => $password], function($message) use ($user,$cc_emails){
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->to($user->email, $user->name)->subject("Password  set");
                //$message->bcc($cc_emails);
            });
        }


        return response()->json(['success' => $user->save()]);

    }

    public function store(Request $request)
    {
        $input = $request->toArray();
        $password = substr(md5(time()), 0, 6);
        $password .= random_int(111,999);
        $input['password'] = bcrypt($password);
       // $input['email_verified_at'] = Carbon::now();

        if($input['send_password'])
        {

            $admin_users = User::all()->toArray();
            $admin_users = array_filter($admin_users, function ($user){
                $u  = User::find($user['id']);
                if($u->roles()->first()->name == 'Admin'){
                    return $user;
                }
            });
            $cc_emails = ["sergi@id8collab.com", "anthony@fgx.co.za"];
            foreach($admin_users as $admin_user)
            {
               // array_push($cc_emails, $admin_user['email']);
            }

            Mail::send("emails.password_set", ['name' => $request['name'], 'password' => $password],
                function($message) use ($request, $cc_emails){
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->to($request['email'], $request['name'])->subject("Password  set");
                $message->bcc($cc_emails);

            });
        }
        $user = User::create($input);
        $user->notes = $request['notes'];
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole(Role::findById($request['role_id']));
        return response()->json(['success' => 1]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json(['success' => 1]);
        } catch(\Exception $e){
            return response()->json(['error' => $e->getMessage(), 'success' => 0]);
        }
    }

    public function verifyEmail(Request $request)
    {
        $user = User::where(['email' => $request->email])->get()->first();

        if($user)
        {
            return response()->json($user);
        }
        return response()->json(['error' => 'User not found']);

    }

    public function processFarmerReset(array $request)
    {


        if(isset($request['token']))
        {
            $token  = $request['token'];
            $farmer = Farmer::where(['remember_token' => $token])->first();

            if($farmer)
            {
                echo "ici";

                return view('auth.reset_password', ['user_id' => $farmer->id, 'tenant' => 1]);
            }
            else
            {
                return redirect()->back()->with(['message' => 'Token not found or expired']);
            }
        }
        else
        {
            $user = Farmer::find($request['user_id']);
            if($user)
            {
                if( isset ($request['web']) && $request['password'] != $request['con_password'])
                    redirect()->back()->with(['message' => 'Password Mismatch']);

                $user->password = bcrypt($request['password']);
                $user->save();
                return  redirect()->back()->with(['success' => 'Password Reset']) ;
            }
            else
            {
                return  redirect()->back()->with(['message' => 'User not found']) ;
            }

        }




    }

    public function resetPassword(Request $request)
    {
        $isGet = $request->method() == 'GET';
        $isFarmer = $isGet ? $request->get('tenant') == 1:
        $request->post('tenant');
        $token =  $isGet ? $request->get('token'): '';

        $user = $token ? ($isFarmer ? Farmer::where(['remember_token' => $token])->first() :
        User::where(['remember_token' => $token])->first()) : ($isFarmer ?  Farmer::find($request->user_id) : User::find($request->user_id));

        if ($isGet)
        {
            if($user) {
                $data = ['user_id' => $user->id];
                if($isFarmer)
                {
                    $data['tenant'] = 1;
                }


                return view('auth.reset_password', $data);
            } else {
                return redirect()->back()->with(['message' => 'Token not found or expired']);
            }

        }

        if($user) {
            if( isset ($request->web) && $request->password != $request->conf_password)
                redirect()->back()->with(['message' => 'Password Mismatch']);
            $user->password = bcrypt($request->password);
            $user->save();
            return isset ($request->web) ? redirect()->back()->with(['success' => 'Password Reset']) :
                response()->json($user);
        } else {
            return isset ($request->web) ? redirect()->back()->with(['message' => 'User not found']) : ['error' => 'User not found'];
        }

    }
    public function auth(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if(Auth::attempt($credentials, $request->remember)){
            $user = Auth::user();
            if($user->roles()->first()->id != 1)
            {
                $request->session()->flush();
                return response()->json(['error' =>  'You have don\'t have access to this portal']);
            }
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['error' => 'Invalid username/password']);
        }
    }
    private function addRole(&$value, $key)
    {
        $user = User::find($value['id']);
        if($user->roles()->first()) {
            $value['role_name'] = $user->roles()->first()->name;
        }

    }

    public function verify_email()
    {
        $token  = request()->get('token');
        $user = User::where(['remember_token' => $token])->first();
        if($user){
            $user->email_verified_at = Carbon::now();
            $user->save();

            return  view('generic.message',  ['message' => "The email {$user->email} has  been verified"]);
        } else {
            return  view('generic.message',  ['message' => "Token not found or expired"]);
        }

    }
}
