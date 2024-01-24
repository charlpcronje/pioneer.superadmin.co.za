<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Farmer\Entities\FilePermission;
use Modules\Farmer\Entities\FarmerCategory;

class FilePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function categoriesWithFile()
    {
        $full_path = request()->get('full_path');
        if(!$full_path)
        {
            return ['success' => 0, 'message' => 'Parameter full_path missing'];
        }
        $filePermission = new FilePermission;
        $categories =  $filePermission->where('full_path', '=',  $full_path)->get()->toArray();
        array_walk($categories, [$this, '__']);

        return $categories;
    }

    public function savePermissions(Request $request)
    {
        $permissions = new FilePermission;
        $permissions = $permissions->where(['full_path' => $request->path]);
        $permissions->delete();

        foreach($request->selectedCategories as $category_id)
        {
            FilePermission::create(['full_path' => $request->path, 'category_id' => $category_id]);

        }
        return ['success' => 1];
    }

    public function __(&$value, $key)
    {
        $category = FarmerCategory::find($value['category_id']);
        $value['category_name'] = $category->name;



    }

}
