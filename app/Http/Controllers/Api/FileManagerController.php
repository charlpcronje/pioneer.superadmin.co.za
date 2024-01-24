<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FileManagerLink;
use App\Models\PageAccessLog;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    //
    protected $http_path;
    protected $api_path;

    public function __construct()
    {
        $this->http_path = env('APP_URL');
        $this->api_path =  $this->http_path."/file-manager/%s?disk=storage&path=%s";
    }

    public function tree()
    {
        //echo $this->http_path."/file-manager/tree?disk=storage"; exit;
        PageAccessLog::create(["page_link" => "file-manager/tree", "page_slug" => 'tree']);
        return  $this->api_response(Http::get($this->http_path."/file-manager/tree?disk=storage")->body());

    }

    public function content(Request $request)
    {
        $path = $request->input('path');
        $url = sprintf($this->api_path,'content',$path);
        $data = json_decode(Http::get($url)->body());
        $links =  FileManagerLink::where(['path' => $path])->pluck('name', 'link');
        if($data != null) {
            $data->links = $links;
        }
        PageAccessLog::create(["page_link" => "file-manager/{$path}", "page_slug" =>$url]);
        return  response()->json($data);
    }
    public function url(Request $request)
    {
        $path = $request->input('path');
        $url = sprintf($this->api_path,'url',$path);
        PageAccessLog::create(["page_link" => "file-manager/{$path}", "page_slug" =>$url]);
        return  $this->api_response(Http::get($url)->body());
    }
    public function download(Request $request)
    {
        $path = $request->input('path');
        $file = explode("/", $path);
        $name = $file[count($file) -1 ];

        if(File::exists(storage_path('tmp/'.$name))){
            //return response()->download(public_path('tmp/'.$name), $name);
            unlink(storage_path('tmp/'.$name));
        }
        //File::
        touch(storage_path('tmp/'.$name));
        $url = sprintf($this->api_path,'download',$path);
        $body = Http::get($url)->body();
        File::put(storage_path('tmp/'.$name), $body);
        PageAccessLog::create(["page_link" => "file-manager/download", "page_slug" =>$url]);

        return response()->download(storage_path('tmp/'.$name), $name);
    }

    private function  api_response(string $json)
    {
        return response()->json(json_decode($json));
    }


    public function getDirectories()
    {
        return file_get_contents(storage_path('app/public/directories.json'));
    }



}
