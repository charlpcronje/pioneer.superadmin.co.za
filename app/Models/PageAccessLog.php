<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PageAccessLog extends Model
{
    protected $fillable = ['page_link','page_slug'];
}

