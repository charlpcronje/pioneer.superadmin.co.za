<?php

namespace App\Http\Controllers;

use App\Models\Medias;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response()->json(Medias::whereRaw('FIND_IN_SET("SALES",recipient)')->get());
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
        //
        $input = $request->toArray();
        $input['slug'] = strtolower(preg_replace('/\s/','_',  $input['name'] ));
        Medias::create($input);
        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Medias  $medias
     * @return \Illuminate\Http\Response
     */
    public function show(Medias $medias)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Medias  $medias
     * @return \Illuminate\Http\Response
     */
    public function edit(Medias $medias)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Medias  $medias
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medias $medias)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Medias  $medias
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medias $medias)
    {
        //
    }
}
