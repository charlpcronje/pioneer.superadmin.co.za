<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PageAccessExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table('page_access_logs')
        ->select('page_link', DB::raw('count(*) as total'))
        ->groupBy('page_link')
        ->orderBy('total', 'desc')
        ->get();;
    }

    public function headings(): array
    {
        return ["Link", "Total"];
    }
}
