<?php

namespace Modules\Farmer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageAcess extends Model
{
    use HasFactory;

    protected $table = "page_access_logs";
    protected $fillable = ['page_link','page_slug','user_id','recipient','created_at'];

}

