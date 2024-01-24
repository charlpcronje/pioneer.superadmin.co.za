<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileManagerLinkRequest;
use App\Models\FileManagerLink;
use Illuminate\Http\Request;

class FileManagerLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(FileManagerLink::get());
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
     * @param  \App\Http\Requests\FileManagerLinkRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = FileManagerLink::create($request->toArray());
        return response()->json(['success' => $result, 'error' => $result ? '': 'Error while saving.Please try again later']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FileManagerLink  $fileManagerLink
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fileManagerLink = FileManagerLink::find($id);
        return response()->json($fileManagerLink);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FileManagerLink  $fileManagerLink
     * @return \Illuminate\Http\Response
     */
    public function edit(FileManagerLink $fileManagerLink)
    {
        //
    }


    public function update(Request $request,  $id)
    {
        $fileManagerLink = FileManagerLink::find($id);
        $fileManagerLink->name = $request->name;
        $fileManagerLink->link = $request->link;
        $fileManagerLink->path = $request->path;
        $result = $fileManagerLink->save();

       // $result = $fileManagerLink->save($request->toArray());
        return response()->json(['success' => $result, 'error' => $result ? '': 'Error while saving.Please try again later']);

    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $fileManagerLink = FileManagerLink::find($id);
        $result = $fileManagerLink->delete();
        return response()->json(['success' => ($result  && $result != null) , 'error' => ($result  && $result != null)  ? '': 'Error while deleting.Please try again later']);

    }

    public function getDirectoryTree()
    {
        return response()->json(json_decode(file_get_contents(storage_path('app/public/directories.json'))));
    }
}
