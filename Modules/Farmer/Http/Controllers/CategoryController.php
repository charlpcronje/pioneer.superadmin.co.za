<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Farmer\Entities\FarmerCategory;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return FarmerCategory::where('visible', 1)->get();
    }

    public function all()
    {
        return FarmerCategory::all();
    }



    /**
     * Store a newly created resource in storage.
     * @param Request $request
     */
    public function store(Request $request)
    {
        return ['success' => FarmerCategory::create($request->toArray())];

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
       return FarmerCategory::find($id);
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $category = FarmerCategory::find($id);
        $category->fill($request->toArray());
        return ['success' => $category->save()];
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     */
    public function destroy($id)
    {
        return ['success' =>FarmerCategory::findorfail($id)->delete()];
    }

    public function  filesInCategory($categoryId)
    {
        $category = FarmerCategory::find($categoryId);
        $filePermissions = $category->filePermissions();
        $path = [];
        foreach($filePermissions as $filePermission)
        {
            array_push($path, $filePermission->full_path);
        }
        return $path;
    }
}
