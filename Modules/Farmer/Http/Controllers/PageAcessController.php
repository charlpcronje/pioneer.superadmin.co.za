<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Farmer\Entities\PageAcess;

class PageAcessController extends Controller
{

    private $page_access_log_model;
    const RECIPIENT = "FARMERS";

    public function __construct(PageAcess $page_access_log_model)
    {
        $this->page_access_log_model = $page_access_log_model;
    }
    public function index()
    {
        $page_access = $this->page_access_log_model->where('recipient','=', self::RECIPIENT)->orderBy('created_at', 'desc')->get()->toArray();
        return $page_access;
    }

    public function weeklyAccess()
    {
        $rows = $this->page_access_log_model->select( \DB::raw('count(*) as total'),
        \DB::raw('WEEKDAY(created_at)  as wd'), \DB::raw('DAYNAME(created_at) as dd'))
        ->where('recipient', self::RECIPIENT)
        ->where('created_at', '>=', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY'))
        ->where('created_at', '<', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate()) -1 DAY'))
        ->groupBy('dd')
        ->orderBy('wd')->get()->pluck('total', 'wd')->toArray();


        $total_of_last_week =  $this->page_access_log_model->select( \DB::raw('count(*) as total'))
        ->where('recipient', self::RECIPIENT)
        ->where('created_at', '>=', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY'))
        ->where('created_at', '<', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate()) -1 DAY'))
        ->get()->first();

        $total_of_last_week =  $total_of_last_week->total;


        $total_of_previous_week =  $this->page_access_log_model->select( \DB::raw('count(*) as total'))
        ->where('recipient', self::RECIPIENT)
        ->where('created_at', '>=', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate())+12 DAY'))
        ->where('created_at', '<', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate()) -7 DAY'))
        ->get()->first();

        $total_of_previous_week =  $total_of_previous_week->total;

        $percent = round(($total_of_last_week -  $total_of_previous_week)/ $total_of_previous_week, 2);






        $days_of_the_week = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        $data = [];
        for($i = 0; $i < count($days_of_the_week); $i++)
        {
            if(!isset($rows[$i]))
            {
                $data[$i] = 0;
            } else
            {
                $data[$i] = $rows[$i];
            }
        }

        return ['days' => $days_of_the_week, 'data' => $data, 'percent' => $percent];


    }


    public function saveLog(Request $request)
    {
        $post = $request->toArray();
        $post['recipient'] = self::RECIPIENT;
        $post['created_at']  = \Carbon\Carbon::now()->format('Y-m-d');
        PageAcess::create($post);
        return ['success' => PageAcess::create($post)];

    }
    public function count_access()
    {

        return \DB::table('page_access_logs')
            ->select('page_link', 'user_id', 'created_at',)
            ->where('recipient', self::RECIPIENT)
            //->groupBy('page_link')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function userAccess($user_id)
    {
        return \DB::table('page_access_logs')
        ->select('page_link', 'created_at')
        ->where('user_id', $user_id)
        ->orderBy('created_at', 'desc')
        ->get();

    }


    public function lastWeekAccess()
    {
        return $this->page_access_log_model->select(\DB::raw('count(*) as total'))
        ->where('recipient', self::RECIPIENT)
        ->where('created_at', '>=', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY'))
        ->where('created_at', '<', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate()) -1 DAY'))
        ->get()->first();
    }




}
