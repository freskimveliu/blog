<?php

namespace App\Http\Controllers\API\Admin\Posts;

use App\Http\Requests\API\Posts\CategoryRequest;
use App\Models\Blogs\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function index(){
        $class_objects = Category::latest()->paginate();

        return $this->respondWithSuccess($class_objects);
    }

    public function show($id){
        $object = Category::find($id);

        if(!$object){
            return $this->respondWithError([],'Category not found');
        }

        return $this->respondWithSuccess($object);
    }

    public function store(CategoryRequest $request){
        $params = $request->all();
        $object   = Category::create($params);

        return $this->respondWithSuccess($object);
    }

    public function update(CategoryRequest $request,$id){
        $object = Category::find($id);

        if(!$object){
            return $this->respondWithError([],'Category not found');
        }

        $params   = $request->all();
        $object->update($params);

        return $this->respondWithSuccess($object);
    }

    public function destroy($id){
        $object = Category::find($id);

        if(!$object){
            return $this->respondWithError([],'Category not found');
        }

        $object->delete();

        return $this->respondWithSuccess();
    }
}
