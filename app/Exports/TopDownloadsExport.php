<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TopDownloadsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table('file_access_logs')
            ->select('filename', DB::raw('count(*) as total'))
            ->where('access_type', 'DOWNLOADS')
            ->groupBy('filename')
            ->orderBy('total', 'desc')
            ->get();
    }
    public function headings(): array
    {
        return ["Filename", "Count"];
    }
}
