<?php
namespace Modules\Farmer\Http\Controllers;
use Illuminate\Http\Request;
use Modules\Farmer\Entities\SearchTags;


class SearchTagsController 
{
    private $search_tags;

    public function __construct(SearchTags $search_tags)
    {
        $this->search_tags = $search_tags;
    }

    public function addTag(Request $request)
    {
        $request = $request->toArray();
        $filename = $request['filename'];
        $path = $request['path'];
        $rows =  $this->search_tags->where(['filename' =>  $request['path']]);
        $row =  $rows->first();
        if( $row != null){
            $tags = explode(",", $row->tags);
            if(!in_array( $request['tag'], $tags)){
                array_push($tags, $request['tag']);
            }
            $row->tags = implode(",", $tags);
            $row->save();
        } else {
            $tags = implode(",", [$request['tag']]);
            $search_tags = new SearchTags;
            $search_tags->filename =  $path;
            $search_tags->display_name =  $filename;
            $search_tags->tags =  $tags;
            $search_tags->save();

        }
        return ['success' => true];
        

    }
    public function allTags()
    {   
        $search_tags = $this->search_tags->all()->pluck('tags', 'filename');
        $results = [];
        foreach($search_tags as $key => $value)
        {
            $results[$key] = explode(",", $value);

        }
        return  $results;

    }
   
    public function removeTag(Request $request)
    {
        $request = $request->toArray();
        $filename = $request['filename'];
        $rows =  $this->search_tags->where(['filename' =>  $request['filename']]);
        $row =  $rows->first();
       
        if( $row != null){
            $tags = explode(",", $row->tags);
          
            $index = array_search( $request['tag'], $tags);
            //dd($index);
            //$tags = array_slice( $tags, $index, true);
            unset($tags[$index]);
            $count = count($tags);
            if($count > 0 ){
                $row->tags = implode(",", $tags);
                $row->save();
            } else {
                $row->delete();
            }
        }
        return ['success' => true];
    }
}
