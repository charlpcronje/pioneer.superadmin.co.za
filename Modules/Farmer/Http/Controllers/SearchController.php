<?php
namespace Modules\Farmer\Http\Controllers;
use Illuminate\Http\Request;
use Modules\Farmer\Entities\Content;
use Modules\Farmer\Entities\SearchTags;


class SearchController 
{
    private $content;
    private $search_tags;

    public function __construct(Content $content, SearchTags $search_tags)
    {
        $this->content = $content;
        $this->search_tags = $search_tags;
    }

    public function search($terms)
    {
        $news = $this->content->where(['recipient' => 'FARMER', 'categories_id' =>1 ])
        ->where('title', 'LIKE', '%'.$terms.'%')
        ->orWhere('intro_text', 'LIKE', '%'.$terms.'%')
        ->orWhere('full_text', 'LIKE', '%'.$terms.'%')
        ->get();
       
        
        $results = [];

        if($news)
        {
            foreach($news as $new)
            {
                $result = [
                    'category' => 'news',
                    'id' => $new->id,
                    'title' => $new->title,
                    'content' => $new->intro_text,
                    'featuredImage' => $new->featured_image
                ];

                array_push( $results, $result);
            }
        }

        $files = $this->search_tags->whereRaw("FIND_IN_SET('$terms', tags)")
        ->get();
        if($files)
        {
            foreach($files as $file)
            {
                $result = [
                    'category' => 'file',
                    'id' => 0,
                    'title' => $file->display_name,
                    'content' => $file->filename,
                    'featuredImage' => ''
                ];
                array_push( $results, $result);

            }

        }
      return $results;  
    }

}
