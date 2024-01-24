<?php

namespace Modules\Farmer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Farmer\Entities\Content;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $news = Content::where(['recipient' => 'FARMER', 'categories_id' =>1 ])->orderBy('created_at', 'DESC')->get()->toArray();
        array_walk($news, [$this, '__']);

        return $news;
    }



    /**
     * Store a newly created resource in storage.
     * @param Request $request
     */
    public function store(Request $request)
    {
        
            
        if($file = $request->file('featured_image'))
        {
            $filename = time().".".$file->getClientOriginalExtension();
            if($file->move(public_path("images/featured_images"), $filename))
            {
                $post = $request->toArray();
                $post["recipient"] = 'FARMER';
                $post["categories_id"] = 1;
                $post['featured_image'] = $filename;
                Content::create($post);
                return ['success' => 1];

            }
            return ['success' => 0, 'message' => 'error while processing'];
        }
        return['success' => 0, 'no featured image supplied'];

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $content =  Content::find($id);
        $content->featured_image = env('APP_URL')."/images/featured_images/".$content->featured_image;
        return $content;
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

        $post = $request->toArray();
        unset($post['featured_image']);
        unset($post['id']);

        if($file = $request->file('featured_image'))
        {
            $filename = time().".".$file->getClientOriginalExtension();
            $uploaded = $file->move(public_path("images/featured_images"), $filename);
            if($uploaded)
                $post['featured_image'] = $filename;
        }


        $content = Content::find($id);
        $content->fill($post);
        

        return ['success' => $content->save()];
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     */
    public function destroy($id)
    {
        return ['success' =>Content::findorfail($id)->delete()];
    }
    public function __(&$value, $key)
    {
        $value['featured_image'] = env('APP_URL')."/images/featured_images/".$value['featured_image'];
    }
}
