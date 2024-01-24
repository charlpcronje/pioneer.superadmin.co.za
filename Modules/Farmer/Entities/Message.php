<?php

namespace Modules\Farmer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $dates = ['created_at','updated_at'];
    protected $fillable = ['name', 'content','headline','type', 'user_id', 'recipient'];
    
    protected static function newFactory()
    {
        return \Modules\Farmer\Database\factories\MessageFactory::new();
    }
}
