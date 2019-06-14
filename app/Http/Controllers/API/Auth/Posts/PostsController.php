<?php

namespace App\Http\Controllers\API\Auth\Posts;

use App\Models\Blogs\Category;
use App\Traits\FileManager;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{
    use FileManager;
    public function index(){
        $posts = User::getUser()->posts()->with('category','comments','user')->latest('id')->paginate(10);

        return $this->respondWithSuccess($posts);

    }

    public function store(Request $request){
        $this->validate($request,[
            'title'       => 'required|string|min:3',
            'description' => 'required|string|min:3',
            'category_id' => 'required|integer|exists:categories,id',
            'image'       => 'required|image',
        ]);

        $params = $request->only('title','category_id','description','category_id');
        if ($image = $request->file('image')) {
            try {
                $params['image_url'] = $this->uploadFile($image);
            } catch (\Exception $exception) {
                return $this->respondWithError([],$exception->getMessage());
            }
        }

        $post = User::getUser()->posts()->create($params);

        return $this->respondWithSuccess($post);
    }

    public function show($id){
        $post = User::getUser()->posts()->with('user','category','comments')->find($id);
        if (!$post) {
            return $this->respondWithError([], 'Post does not exists', 404);
        }
        return $this->respondWithSuccess($post);
    }

    public function update(Request $request, $id){
        $this->validate($request,[
            'title'       => 'required|string|min:3',
            'description' => 'required|string|min:3',
            'category_id' => 'required|integer|exists:categories,id',
            'image'       => 'nullable|file|image',
        ]);

        $post = User::getUser()->posts()->find($id);
        if (!$post) {
            return $this->respondWithError([], 'Post does not exists', 404);
        }

        $params = $request->only('title','category_id','description','category_id');
        if ($image = $request->file('image')) {
            try {
                $params['image_url'] = $this->uploadFile($image);
            } catch (\Exception $exception) {
                return $this->respondWithError([],$exception->getMessage());
            }
        }

        $post->update($params);

        return $this->respondWithSuccess();
    }

    public function destroy($id){
        $post = User::getUser()->posts()->find($id);
        if (!$post) {
            return $this->respondWithError([], 'Post does not exists', 404);
        }
        $post->delete();

        return $this->respondWithSuccess();
    }

    public function getCategories(){
        $class_objects = Category::orderBy('name')->paginate();

        return $this->respondWithSuccess($class_objects);
    }
}
