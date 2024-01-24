<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medias extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug'];

    public  function links()
    {
        return  $this->hasMany(MediaLinks::class,'media_id');
    }
}
