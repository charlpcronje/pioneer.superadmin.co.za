<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medias;
use App\Models\PageAccessLog;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index()
    {
        return Medias::Where(['marketing_content' => 0])->whereRaw('FIND_IN_SET("SALES",recipient)')->get();
    }


    public function marketing_content()
    {
        PageAccessLog::create(["page_link" => "marketing_content", "page_slug" => ""]);
        return Medias::where(['marketing_content' => 1])
        ->whereRaw('FIND_IN_SET("SALES",recipient)')->get()->first()->links()->get()->toArray();
    }

    public function links($slug)
    {
        PageAccessLog::create(["page_link" => "marketing_content/{$slug}", "page_slug" => $slug]);
        return Medias::where(['slug' => $slug])->whereRaw('FIND_IN_SET("SALES",recipient)')->get()->first()
            ->links()->whereRaw('FIND_IN_SET("SALES",recipient)')->get()->toArray();
    }
}
