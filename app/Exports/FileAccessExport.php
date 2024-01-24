<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\FileAccessLog;
use App\Models\User;


class FileAccessExport implements FromCollection,WithHeadings
{


    private $file_access_log_model;

    public function __construct(FileAccessLog $file_access_log_model)
    {
        $this->file_access_log_model = $file_access_log_model;
    }



    public function __(&$value, $key)
    {
        $user_id = $value['user_id'];
        $created_at = $value['created_at'];
        $value['created_at'] = \Carbon\Carbon::createFromTimeString($created_at)
            ->format('Y-m-d H:i:s');
        $value['name'] = User::find($user_id)->name;
        unset($value['user_id']);
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $file_access = $this->file_access_log_model->orderBy('created_at', 'desc')->get(['filename','access_type','user_id','created_at'])->toArray();
        array_walk($file_access, [$this, '__']);
        return collect($file_access);
    }

    public function headings(): array
    {
        return ["Filename", "Access", "Date", "User"];
    }
}
