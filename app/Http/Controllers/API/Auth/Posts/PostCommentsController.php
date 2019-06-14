<?php

namespace App\Http\Controllers\API\Auth\Posts;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostCommentsController extends Controller
{
    public function destroy($id, $comment_id){
        $user = User::getUser();

        if(!$user)
            return $this->respondWithError([],'You can not delete this comment');

        $post = $user->posts()->find($id);

        if(!$post)
            return $this->respondWithError([],'Post does not exists or is deleted',404);

        $comment = $post->comments()->find($comment_id);

        if(!$comment)
            return $this->respondWithError([],'You can not delete this comment');

        $comment->delete();

        return $this->respondWithSuccess();
    }
}
