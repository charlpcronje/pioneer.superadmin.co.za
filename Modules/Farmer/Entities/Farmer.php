<?php

namespace Modules\Farmer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Farmer extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','surname','email','number', 'field', 'username', 'password', 'categories', 'auto',
        'field', 'location','active', 'notify', 'profile_pic', 'dob'];

    public function category()
    {
        return $this->belongsTo('Modules\Farmer\Entities\FarmerCategory');
    }
}
