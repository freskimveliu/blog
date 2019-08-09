<?php

namespace App\Http\Controllers\API\Auth\Users;

use App\Http\Controllers\Controller;
use App\User;

class UserFollowRequestsController extends Controller
{
    public function index(){
        $user = User::getUser();

        $class_objects = $user->relationships_as_a_friend()->requested()->with('user')->latest()->paginate($this->per_page);

        return $this->respondWithSuccess($class_objects);
    }

    public function confirm($id){
        $user = User::getUser();

        $relationship = $user->relationships_as_a_friend()->find($id);

        if(!$relationship){
            return $this->respondWithError([],'You don"t have relationship with this user.');
        }

        $relation_user = User::find($relationship->user_id);

        if(!$relation_user){
            return $this->respondWithError([],'User does not exists, or is deleted');
        }

        $status = $relationship->status;

        switch ($status){
            case RELATIONSHIP_STATUS_FOLLOWING:
                return $this->respondWithError([],'You are already following this user');
            case RELATIONSHIP_STATUS_UNFOLLOWING:
                return $this->respondWithError([],'First you have to send follow request to this user.');
            case RELATIONSHIP_STATUS_REQUESTED:
                $relationship->update([
                    'status'     => RELATIONSHIP_STATUS_FOLLOWING,
                    'old_status' => $status,
                ]);
                break;
            default:
                return $this->respondWithError([],'Not supported relationship status');
        }
        $relation_user = User::full()->find($relation_user->id);
        return $this->respondWithSuccess($relation_user);
    }

    public function cancel($id){
        $user = User::getUser();

        $relationship = $user->relationships_as_a_friend()->find($id);

        if(!$relationship){
            return $this->respondWithError([],'You don"t have relationship with this user.');
        }

        $relation_user = User::find($relationship->user_id);

        if(!$relation_user){
            return $this->respondWithError([],'User does not exists, or is deleted');
        }

        $status = $relationship->status;

        switch ($status){
            case RELATIONSHIP_STATUS_FOLLOWING:
            case RELATIONSHIP_STATUS_REQUESTED:
                $relationship->update([
                    'status'     => RELATIONSHIP_STATUS_UNFOLLOWING,
                    'old_status' => $status,
                ]);
                break;
            case RELATIONSHIP_STATUS_UNFOLLOWING:
                return $this->respondWithError([],'Already you are not following this user.');
            default:
                return $this->respondWithError([],'Not supported relationship status');
        }
        $relation_user = User::full()->find($relation_user->id);
        return $this->respondWithSuccess($relation_user);
    }
}
