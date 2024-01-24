<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FileManagerController extends Controller
{
    //
    public function countFiles()
    {
        //$endpoint = env('APP_URL');
       // echo $endpoint."/file-manager/tree?disk=storage"; exit;

        $response = Http::get(env('APP_URL')."/file-manager/tree?disk=storage")->body();
        $tree = json_decode($response);
        $directories = $tree->directories;
        $folders = count($directories);
        $files = 0;
        foreach($directories as $directory)
        {
            $this->loop_count($directory->path, $folders, $files);
        }

        return response()->json(['folders' => $folders, 'files' => $files]);
    }

    public function latest_files()
    {
        $response = Http::get(env('APP_URL')."/file-manager/tree?disk=storage")->body();
        $tree = json_decode($response);
        $directories = $tree->directories;
        print_r($directories);

    }

    private function loop_count($directory, &$folders, &$files)
    {
        //Get Content of the directory
        $response = Http::get(env('APP_URL')."/file-manager/content?disk=storage&path={$directory}")->body();
        $response = json_decode($response);
        $remote_files = $response->files;
        if(count($remote_files)){
            $files += count($remote_files);
        } else {
            $folders += count($response->directories);
            foreach($response->directories as $dir){
                $this->loop_count($dir->path, $folders, $files);
            }
        }

    }

}
