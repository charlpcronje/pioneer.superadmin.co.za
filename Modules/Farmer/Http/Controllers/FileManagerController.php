<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use \Alexusmai\LaravelFileManager\Controllers\FileManagerController as DropBoxController;
use Spatie\Dropbox\Exceptions\BadRequest;
use  Modules\Farmer\Entities\PageAcess;
use  Modules\Farmer\Entities\FilePermission;

use Illuminate\Support\Facades\Storage;

class FileManagerController extends DropBoxController
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getTree()
    {
        return  $this->fm->content('storage', 'Farmers');

    }
    public function getContent(Request $request)
    {
        $path = $request->input('path');
        $path =  "Farmers/".$path;
        $data  = $this->fm->content('storage', $path);
        PageAcess::create(["page_link" => "file-manager/{$path}", "page_slug" =>$path,
        'user_id' => $request->input('user_id')]);

       // print_r($data);

        array_walk($data, [$this, 'populate_filter']);
        return  response()->json($data);
    }

    public function getUrl(Request $request)
    {
        $path = $request->input('path');
        $path =  "Farmers/".$path;
        return   $this->fm->url('storage', $path);
    }



    public function getDownload(Request $request)
    {
        $path = $request->input('path');
        $path =  "Farmers/".$path;
        return  $this->fm->download('storage',$path);
    }

    private function populate_filter(&$value, $key)
    {


        if($key === 'files'){
            array_walk($value, static function(&$v){
                $filePermission = new FilePermission;
                $filePermission = $filePermission
                ->join('farmer_categories', 'farmer_categories.id', 'file_permissions.category_id')
                ->where('full_path', '=', $v['path'])
                ->get(['farmer_categories.name'])->pluck(['name'])->toArray();

                $v['permission'] = implode(',',  $filePermission);

            });
        }
    }






}
