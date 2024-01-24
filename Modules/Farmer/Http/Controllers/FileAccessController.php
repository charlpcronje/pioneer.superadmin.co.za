<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Farmer\Entities\FileAccess;
use Modules\Farmer\Entities\Farmer;



class FileAccessController extends Controller
{
    private $file_access_log_model;
    const RECIPIENT = "FARMERS";

    public function __construct(FileAccess $file_access_log_model)
    {
        $this->file_access_log_model = $file_access_log_model;
    }
    public function index()
    {
        $file_access = $this->file_access_log_model->where('recipient','=', self::RECIPIENT)->orderBy('created_at', 'desc')->get()->toArray();
        array_walk($file_access, [$this, '__']);
        return $file_access;
    }

    public function __(&$value, $key)
    {
        $user_id = $value['user_id'];

        $created_at = $value['created_at'];
        $value['created_at'] = \Carbon\Carbon::createFromTimeString($created_at)
            ->format('Y-m-d H:i:s');

        $value['user_id'] = Farmer::find($user_id);
    }
    public function saveLog(Request $request)
    {
        $post = $request->toArray();
        $post['recipient'] = self::RECIPIENT;
        FileAccess::create($post);
        return ['success' => true];

    }

    public function userDownloads($user_id)
    {
        return \DB::table('file_access_logs')
        ->select('filename', 'created_at')
        ->where('access_type', 'DOWNLOADS')
        ->where('user_id', $user_id)
        ->orderBy('created_at', 'desc','date')
        ->get();

    }
    public function userSharing($user_id)
    {
        return \DB::table('file_access_logs')
        ->select('filename', 'created_at')
        ->where('access_type', 'SHARING')
        ->orderBy('created_at', 'desc',)
        ->where('user_id', $user_id)
        ->get();

    }

//File sharing % Page Access//
    public function count_download()
    {

        return \DB::table('file_access_logs')
            ->select('filename', \DB::raw('count(*) as download_count'))
            ->where('access_type', 'DOWNLOADS')
            ->where('recipient', 'FARMERS')
            ->groupBy('filename',)
            ->orderBy('created_at', 'desc',)
            ->get();
    }

    public function count_download1()
    {

        return \DB::table('file_access_logs')
            ->select('user_id','filename','created_at', \DB::raw('count(*) as total'))
            ->where('access_type', 'DOWNLOADS')
            ->where('recipient', self::RECIPIENT)
           // ->groupBy('filename')
            ->orderBy('total', 'desc')
            ->get();
    }


    public function weeklySharing()
    {


        $rows =  $this->file_access_log_model->select( \DB::raw('count(*) as total'),
        \DB::raw('WEEKDAY(created_at)  as wd'), \DB::raw('DAYNAME(created_at) as dd'))
        ->where('recipient', self::RECIPIENT)
        ->where('access_type', 'SHARING')
        ->where('created_at', '>=', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY'))
        ->where('created_at', '<', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate()) -1 DAY'))
        ->groupBy('dd')
        ->orderBy('wd')->get()->pluck('total', 'wd')->toArray();



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

        return ['days' => $days_of_the_week, 'data' => $data];


    }

    public function weeklyDownload()
    {


        $rows =  $this->file_access_log_model->select( \DB::raw('count(*) as total'),
        \DB::raw('WEEKDAY(created_at)  as wd'), \DB::raw('DAYNAME(created_at) as dd'))
        ->where('recipient', self::RECIPIENT)
        ->where('access_type', 'DOWNLOADS')
        ->where('created_at', '>=', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY'))
        ->where('created_at', '<', \DB::raw('curdate() - INTERVAL DAYOFWEEK(curdate()) -1 DAY'))
        ->groupBy('dd')
        ->orderBy('wd')->get()->pluck('total', 'wd')->toArray();




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

        return ['days' => $days_of_the_week, 'data' => $data];

    }


    public function top_downloads()
    {
        $rows = \DB::table('file_access_logs')
        ->select('user_id', \DB::raw('count(*) as ct'))
        ->where('access_type', 'DOWNLOADS')
        ->where('recipient', self::RECIPIENT)
        ->groupBy('user_id')
        ->orderBy('ct', 'desc')
        ->get()->toArray();

        $data = [];

        foreach($rows as $row)
        {
            $user_id = $row->user_id;
            $farmer = Farmer::find( $user_id);
            array_push($data, ['id' =>  $user_id , 'user' => $farmer->name, 'ct' => $row->ct]);

        }

        return $data;
    }
    public function count_shared()
    {
        return  \DB::table('file_access_logs')
        ->select('filename', 'user_id', 'created_at', \DB::raw('count(*) as ct'))
        ->where('access_type', 'SHARING')
        //->where('recipient', self::RECIPIENT)
        ->groupBy('filename','created_at',)
        ->orderBy('ct', 'desc','created_at',)
        ->get()->toArray();

    }

}
