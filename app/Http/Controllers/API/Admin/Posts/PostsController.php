<?php

namespace App\Http\Controllers\API\Admin\Posts;

use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{
    public function index(){
        $posts = Post::with('category','user')->latest('id')->paginate(10);

        return $this->respondWithSuccess($posts);

    }

    public function store(Request $request){
        $this->validate($request,[
            'title'       => 'required|string|min:3',
            'description' => 'required|string|min:3',
            'category_id' => 'required|integer|exists:categories,id'
        ]);

        $params = $request->all();
        $params['user_id']= User::getUser()->id;
        $post = Post::create($params);

        return $this->respondWithSuccess($post);
    }

    public function show($id){
        $post = Post::with('user','category')->find($id);
        if (!$post) {
            return $this->respondWithError([], 'Post does not exists', 404);
        }
        return $this->respondWithSuccess($post);
    }

    public function update(Request $request, $id){
        $this->validate($request,[
            'title'       => 'required|string|min:3',
            'description' => 'required|string|min:3',
            'category_id' => 'required|integer|exists:categories,id'
        ]);

        $params = $request->all();
        $post = Post::find($id);
        if (!$post) {
            return $this->respondWithError([], 'Post does not exists', 404);
        }
        $post->update($params);

        return $this->respondWithSuccess();
    }

    public function destroy($id){
        $post = Post::find($id);
        if (!$post) {
            return $this->respondWithError([], 'Post does not exists', 404);
        }
        $post->delete();

        return $this->respondWithSuccess();
    }
}
