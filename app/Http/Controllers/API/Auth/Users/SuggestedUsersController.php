<?php

namespace App\Http\Controllers\API\Auth\Users;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SuggestedUsersController extends Controller
{
    public function index(){
        $ids = User::getUser()->followings()
                            ->orWhere('status', RELATIONSHIP_STATUS_REQUESTED)
                            ->orWhere('status',RELATIONSHIP_STATUS_UNFOLLOWING)
                            ->pluck('friend_id')
                            ->toArray();

        $class_objects = User::suggestedUsers($ids)->with('relationships_as_a_friend')->inRandomOrder()->paginate(15);

        return $this->respondWithSuccess($class_objects);
    }
}
