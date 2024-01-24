<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //$passord =
        //print_r($row);

        if(isset($row['name'])) {

            if(!User::where('email', $row['email'])->first())
            {
                $password = substr(md5(time()), 0, 6) . random_int(111, 999);
                Mail::send("emails.password_set", ['name' => $row['name'], 'password' => $password], function ($message) use ($row) {
                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    $message->to($row['email'], $row['name'])->subject("Password  set");
                });
                return new User([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => \Hash::make($password),
                    'phone_number' => $row['cell']
                ]);

            }

        }
    }
}
