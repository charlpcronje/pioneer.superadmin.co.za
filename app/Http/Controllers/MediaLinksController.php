<?php

namespace App\Http\Controllers;

use App\Models\MediaLinks;
use App\Models\Medias;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MediaLinksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $data = MediaLinks::Where(['recipient' => 'SALES'])->get()->toArray();
        array_walk($data, [$this, '__']);
        return response()->json($data);
    }


    private function __(&$value, $key)
    {
        $value['media_id'] = Medias::find($value['media_id'])->name;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->toArray();
        MediaLinks::create($input);
        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
        return MediaLinks::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MediaLinks  $mediaLinks
     * @return \Illuminate\Http\Response
     */
    public function edit(MediaLinks $mediaLinks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $mediaLinks = MediaLinks::find($id);
        $mediaLinks->fill($request->toArray());


        try {
            return ['success' => $mediaLinks->save()];
        } catch(\Exception $ex){}

        return ['success' => 0, 'There has been an error saving the link'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MediaLinks  $mediaLinks
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //
        try {
            $mediaLinks = MediaLinks::find($id);
            $mediaLinks->delete();
             return  response()->json(['success' => 1]);
        } catch(\Exception $e){
             return  response()->json(['error' => $e->getMessage()]);
        }

    }
    public function latest()
    {
        $media_links = MediaLinks::orderBy('created_at', 'DESC')->limit(5)->get()->toArray();
        array_walk($media_links, [$this, 'populate_media']);
        return response()->json($media_links);
    }

    private function populate_media(&$value, $key){
        $media_id = $value['media_id'];
        $created_at = $value['created_at'];
        $created_at = new Carbon($created_at);
        $value['media_name'] = \App\Models\Medias::find($media_id)->name;
        $value['created_at'] = $created_at->format('Y-m-d H:i:s');
    }
}
