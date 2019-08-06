<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\User;

class UsersController extends Controller
{
    public function show($slug){
        $object = User::whereUsername($slug)
                        ->withCount('posts','followers','followings')
                        ->with('followings','followers','my_relationship_with_this_user')
                        ->first();

        if (!$object) {
            return $this->respondWithError([], 'User not found', 404);
        }

        return $this->respondWithSuccess($object);
    }
}
