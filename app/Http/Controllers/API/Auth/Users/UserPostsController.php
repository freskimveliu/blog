<?php

namespace App\Http\Controllers\API\Auth\Users;

use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserPostsController extends Controller
{
    public function index($slug){
        $object = User::whereUsername($slug)->withCount('posts')->with('followers')->first();

        if (!$object) {
            return $this->respondWithError([], 'User not found', 404);
        }

        if(!$object->can_i_show_posts){
            return $this->respondWithError([],"This Account is Private. Follow to see their photos and videos.");
        }

        $posts = Post::where('user_id',$object->id)
                     ->with('is_my_favorite')
                     ->withCount('comments','favorites')
                     ->latest()
                     ->paginate($this->per_page);

        return $this->respondWithSuccess($posts);
    }
}
