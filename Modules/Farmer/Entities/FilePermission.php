<?php

namespace Modules\Farmer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FilePermission extends Model
{
    use HasFactory;

    protected $fillable = ['full_path', 'category_id'];

    public  function category()
    {
        return $this->belongsTo(' Modules\Farmer\Entities\FarmerCategory');
    }


}
