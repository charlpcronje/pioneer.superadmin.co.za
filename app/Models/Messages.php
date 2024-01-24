<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;
    protected $dates = ['created_at','updated_at'];
    protected $fillable = ['name', 'content','headline','type', 'user_id', 'scheduled_at'];
}
