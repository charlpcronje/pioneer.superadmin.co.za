<?php

use Illuminate\Support\Facades\Http;

if(!function_exists('folders_tree'))
{
    function folders_tree(&$folder_name = [], $path='')
    {
        $link =  env('APP_URL')."/file-manager/tree?disk=storage";


        $response = Http::get($link)->body();
        $tree = json_decode($response);
        $directories = $tree->directories;

        foreach($directories as $directory)
        {
            if(!preg_match("/Farmers/", $directory->path)) {
                array_push($folder_name, $directory->path);
                if ($directory->props->hasSubdirectories) {
                    tree_content($directory, $folder_name);
                }
            }
        }

        sort($folder_name);
    }
}

function tree_content($directory, &$folder_name){
    $link = env('APP_URL')."/file-manager/tree?disk=storage&path=$directory->path";
    $response = Http::get($link)->body();
    $tree = json_decode($response);
    $directories = $tree->directories;
    foreach($directories as $directory)
    {
        array_push($folder_name, $directory->path);
        if($directory->props->hasSubdirectories)
        {
           tree_content($directory, $folder_name);
        }
    }

}


