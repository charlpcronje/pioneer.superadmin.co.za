<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected  $categories;

    public function __construct(Categories  $categories)
    {
        $this->categories = $categories;
    }
    //
    public function index()
    {
        return response()->json( $this->categories->select()->orderby('name')->get());
    }
}
