<?php

namespace Modules\Farmer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileAccess extends Model
{
    use HasFactory;

    protected $table = "file_access_logs";

    protected $fillable = ['user_id','doc_path','filename','access_type', 'recipient'];
    
}
