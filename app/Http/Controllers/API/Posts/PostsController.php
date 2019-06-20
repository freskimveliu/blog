<?php

namespace App\Http\Controllers\API\Posts;

use App\Filters\PostsFilters;
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{
    public function index(PostsFilters $filters){
        $posts = Post::with('category','user')->filter($filters)->latest()->withCount('open_by_users','favorite_by_users')->paginate($this->per_page);

        return $this->respondWithSuccess($posts);
    }

    public function show($id){
        $post = Post::with('category','user','comments.user','is_my_favorite')->find($id);

        if (!$post) {
            return $this->respondWithError([], 'Post does not exists', 404);
        }

        $user = User::getUser();
        if($user){
            $user->opened_posts()->sync($post->id,false);
        }

        return $this->respondWithSuccess($post);
    }

    public function favoriteAction($id, Request $request){
        $is_favorite = $request->get('is_favorite',false);

        $user = User::getUser();
        if($is_favorite){
            $user->favorited_posts()->sync($id,false);
        }else{
            $user->favorited_posts()->detach($id);
        }

        return $this->respondWithSuccess($is_favorite);
    }

}
