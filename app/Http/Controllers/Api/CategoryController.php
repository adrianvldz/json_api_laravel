<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryController extends Controller
{
    public function show($category): JsonResource
    {
       $category = Category::where('slug', $category)->firstOrFail();

       return CategoryResource::make($category);
    }

    public function index(): AnonymousResourceCollection
    {
        $categories = Category::jsonPaginate();

        return CategoryResource::collection($categories);
    }
}
