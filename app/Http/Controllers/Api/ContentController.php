<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Content;
use App\Models\PageAccessLog;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    //
    public function  category($slug)
    {
        PageAccessLog::create(["page_link" => "content/{$slug}", "page_slug" => $slug]);

        $data = Categories::where(['slug' => $slug])->get()->first()->contents()->where(['recipient' => 'SALES'])->get()->toArray();
        array_walk($data, [$this, '__']);
        return $data;
    }

    public function content($id)
    {
        PageAccessLog::create(["page_link" => "content/{$id}", "page_slug" => $id]);
        $content =  Content::find($id);
        PageAccessLog::create(["page_link" => "content/{$content->title}", "page_slug" => $content]);
        return $content;
    }
    public function __(&$value, $key)
    {
        $value['featured_image'] = env('APP_URL')."/images/featured_images/".$value['featured_image'];
    }
}
