<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserLoginsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::whereNotNull("login_date")->orderBy('login_date','desc')->get(['name','email', 'login_date']);
    }
    public function headings(): array
    {
        return ["Name", "Email", "Login Date"];
    }
}
