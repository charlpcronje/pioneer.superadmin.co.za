<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class AclRulesRole extends Model
{
    use HasFactory;
    protected $fillable = ['role_id','path','access','disk'];


}
