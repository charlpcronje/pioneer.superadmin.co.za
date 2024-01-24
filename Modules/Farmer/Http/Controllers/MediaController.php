<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Farmer\Entities\Media;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return Media::Where(['marketing_content' => 0])->whereRaw('FIND_IN_SET("FARMER",recipient)')->orderby('name')->get();
    }


    public function mediaList($marketing_content)
    {
        return Media::where(['marketing_content' => $marketing_content])
        ->whereRaw('FIND_IN_SET("FARMER",recipient)')->get()->toArray();

    }

    public function marketing_content()
    {

        return Media::where(['marketing_content' => 1])
        ->whereRaw('FIND_IN_SET("FARMER",recipient)')->get()->first()->links()->get()->toArray();
    }

    public function links($slug)
    {
        $media = Media::where('slug', '=', $slug)->whereRaw('FIND_IN_SET("FARMER",recipient)')->get()->first();
        $links = $media->links()->whereRaw('FIND_IN_SET("FARMER",recipient)')->get()->toArray();
        usort($links, function ($link1, $link2){
           return strtotime($link1['created_at']) > strtotime($link2['created_at']) ? -1: 1;
        });

        return $links;

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('farmer::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
