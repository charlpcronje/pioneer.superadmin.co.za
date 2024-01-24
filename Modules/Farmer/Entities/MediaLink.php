<?php

namespace Modules\Farmer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaLink extends Model
{
    use HasFactory;
    protected $fillable = ['media_id', 'name', 'link', 'recipient'];

    public function media()
    {
        return $this->belongsTo(Media::class);

    }
}
