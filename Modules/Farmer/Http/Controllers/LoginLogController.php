<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Farmer\Entities\LoginLog;

class LoginLogController extends Controller
{
    
    public function logs($user_id)
    {
        return LoginLog::where(['user_id' => $user_id])->orderBy('created_at', 'desc')->get();
    }
}
