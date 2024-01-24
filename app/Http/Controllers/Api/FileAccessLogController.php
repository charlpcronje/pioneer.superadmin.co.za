<?php


namespace App\Http\Controllers\Api;


use App\Exports\FileAccessExport;
use App\Exports\TopDownloadsExport;
use App\Exports\UserLoginsExport;
use Illuminate\Http\Request;
use App\Models\FileAccessLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FileAccessLogController
{

    private $file_access_log_model;

    public function __construct(FileAccessLog $file_access_log_model)
    {
        $this->file_access_log_model = $file_access_log_model;
    }

    public function index()
    {
        $file_access = $this->file_access_log_model->orderBy('created_at', 'desc')->get()->toArray();
        array_walk($file_access, [$this, '__']);
        return $file_access;
    }

    public function __(&$value, $key)
    {
        $user_id = $value['user_id'];
        $created_at = $value['created_at'];
        $value['created_at'] = \Carbon\Carbon::createFromTimeString($created_at)
            ->format('Y-m-d H:i:s');
        $value['user_id'] = User::find($user_id);
    }

    public function saveLog(Request $request)
    {
        FileAccessLog::create($request->toArray());
        return ['success' => true];

    }

    public function topDownloads(Request $request)
    {
        return Excel::download(new TopDownloadsExport(), 'top_download.xlsx');

    }
    public function downloadlogs()
    {
        return Excel::download(new FileAccessExport( $this->file_access_log_model),
            'file_access.xlsx');
    }
    public function count_download()
    {

        return DB::table('file_access_logs')
            ->select('filename', DB::raw('count(*) as total'))
            ->where('access_type', 'DOWNLOADS')
            ->groupBy('filename')
            ->orderBy('total', 'desc')
            ->get();;
    }



}
