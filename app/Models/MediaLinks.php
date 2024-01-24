<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Twilio\TwiML\Messaging\Media;

class MediaLinks extends Model
{
    use HasFactory;
    protected $fillable = ['media_id', 'name', 'link'];
    protected $table = 'media_links';

    public function media(){
        return $this->belongsTo(Media::class);
    }
}
