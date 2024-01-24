<?php

namespace Modules\Farmer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FarmerCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function farmers()
    {
        return $this->hasMany('Modules\Farmer\Entities\Farmer','category_id','id');
    }
    public  function filePermissions()
    {
        return $this->hasMany(' Modules\Farmer\Entities\FilePermission');
    }


}
