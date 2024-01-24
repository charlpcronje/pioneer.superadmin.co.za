<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Categories;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = Content::where(['recipient' => 'SALES'])->get()->toArray();
        array_walk($data, [$this, '__']);
        return response()->json($data);
    }

    private function __(&$value, $key)
    {
        $category = Categories::find( $value['categories_id']);
        $value['categories_id'] =  $category->name;
        $value['featured_image'] = asset('images/featured_images/'.$value['featured_image']);

        //$value['intro_text'] =
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if($request->has('id') && $request->id){
            $content = Content::find($request->id);
            $input = $request->toArray();

            if ($request->has('featured_image')) {
                $featured_image = $request->file('featured_image');
                $fileName = strtolower(time() . '_' . $featured_image->getClientOriginalName());
                $featured_image->move(public_path('images/featured_images'), $fileName);
                $input['featured_image'] = $fileName;
            }
            $content->update($input);
            return response()->json(['success' => 1]);
        } else {
            if ($request->has('featured_image')) {
                $featured_image = $request->file('featured_image');
                $fileName = strtolower(time() . '_' . $featured_image->getClientOriginalName());
                $featured_image->move(public_path('images/featured_images'), $fileName);
                $input = $request->toArray();
                $input['featured_image'] = $fileName;
                Content::create($input);
                return response()->json(['success' => 1]);
            }
        }
        return response()->json(['error' => 'An error has occurred']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        return response()->json($content);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        $input = $request->toArray();
      //  print_r($input); exit;

        if($request->has('featured_image')) {
            $featured_image = $request->file('featured_image');
            $fileName = strtolower(time() . '_' . $featured_image->getClientOriginalName());
            $featured_image->move(public_path('images/featured_images'), $fileName);
            $input['featured_image']  = $fileName;

        }
        return response()->json(['success' => $content->update($input)]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Content $content)
    {
        //unlink (public_path('featured_images/'+$content->featured_image));
        return response()->json(['success' => $content->delete()]);

    }
}
