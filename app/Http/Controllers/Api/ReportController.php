<?php


namespace App\Http\Controllers\Api;


use App\Exports\FileAccessExport;
use App\Exports\PageAccessExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController
{

    public function page_access_log()
    {
        return DB::table('page_access_logs')
            ->select('page_link', DB::raw('count(*) as total'))
            ->groupBy('page_link')
            ->orderBy('total', 'desc')
            ->get();;
    }

    public function pageAccessDownload()
    {
        return Excel::download(new PageAccessExport(),
            'page_access.xlsx');
    }

}
