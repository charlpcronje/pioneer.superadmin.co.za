<?php

namespace Modules\Farmer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Media extends Model
{
    use HasFactory;

    use HasFactory;
    protected $fillable = ['name','slug'];
    protected $table = 'medias';
    

    public  function links()
    {
        return  $this->hasMany(MediaLink::class);
    }
}
