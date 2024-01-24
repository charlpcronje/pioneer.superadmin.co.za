<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Farmer\Entities\MediaLink;
use Modules\Farmer\Entities\Media;

class MediaLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $links = MediaLink::Where(['recipient' => 'FARMER'])->get()->toArray();
        array_walk($links, function(&$value, $key){
            $media_id = $value['media_id'];
            $value['media'] = Media::find($media_id);
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
        $post = $request->toArray();
        $post['recipient'] = 'FARMER';
        $post['media_id'] = $post['media']['id'];

        return ['success' => MediaLink::create($post)];

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $mediaLink = MediaLink::find($id);
        $media = Media::find($mediaLink->media_id);
        $media->text = $media->name; 

        $mediaLink->media = $media;
        return $mediaLink;
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
        $mediaLink = MediaLink::find($id);
        $post = $request->toArray();
        $post['media_id'] = $post['media']['id'];
        $mediaLink->fill($post);
        return ['success' => $mediaLink->save()];

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $mediaLink = MediaLink::find($id);
        return ['success' => $mediaLink->delete()];

    }
}
