<?php

namespace App\Http\Controllers\API\Posts;

use App\Models\Post;
use App\Models\PostComment;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostCommentsController extends Controller
{
    public function store($id,Request $request){
        $rules = [
            'message'   => 'required|string',
            'name'      => User::getUser() ?'': 'required|string|max:191',
        ];

        $this->validate($request,$rules);

        $message = $request->get('message');

        $post = Post::find($id);

        if(!$post)
            return $this->respondWithError([],'Post not found',404);

        $comment = $post->comments()->create([
            'user_id'       => User::getUser()->id ?? null,
            'user_name'     => User::getUser()->name ?? $request->get('name'),
            'message'       => $message,
        ]);

        $comment = PostComment::with('user')->find($comment->id);

        return $this->respondWithSuccess($comment);
    }


    public function destroy($id, $comment_id){
        $user = User::getUser();

        if(!$user)
            return $this->respondWithError([],'You can not delete this comment');

        $comment = $user->comments()->find($comment_id);

        if(!$comment)
            return $this->respondWithError([],'You can not delete this comment');

        $comment->delete();

        return $this->respondWithSuccess();
    }
}
