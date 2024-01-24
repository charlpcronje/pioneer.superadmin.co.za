<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationBinding extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $primaryKey = 'identity';
    protected $fillable = ['sid','type','device_id','identity'];

}
