<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class FileAccessLog extends Model
{
    protected $fillable = ['user_id','doc_path','filename','access_type'];
}

