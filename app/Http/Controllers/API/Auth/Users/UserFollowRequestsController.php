<?php

namespace App\Http\Controllers\API\Auth\Users;

use App\Http\Controllers\Controller;
use App\User;

class UserFollowRequestsController extends Controller
{
    public function index(){
        $user = User::getUser();

        $class_objects = $user->my_relationships()->requested()->with('user')->latest()->paginate($this->per_page);

        return $this->respondWithSuccess($class_objects);
    }

    public function confirm($id){
        $user = User::getUser();

        $relationship = $user->my_relationships()->find($id);

        if(!$relationship){
            return $this->respondWithError([],'You don"t have relationship with this user.');
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
                return $this->respondWithSuccess();
            default:
                return $this->respondWithError([],'Not supported relationship status');
        }
    }

    public function cancel($id){
        $user = User::getUser();

        $relationship = $user->my_relationships()->find($id);

        if(!$relationship){
            return $this->respondWithError([],'You don"t have relationship with this user.');
        }

        $status = $relationship->status;

        switch ($status){
            case RELATIONSHIP_STATUS_FOLLOWING:
            case RELATIONSHIP_STATUS_REQUESTED:
                $relationship->update([
                    'status'     => RELATIONSHIP_STATUS_UNFOLLOWING,
                    'old_status' => $status,
                ]);
                return $this->respondWithSuccess();
            case RELATIONSHIP_STATUS_UNFOLLOWING:
                return $this->respondWithError([],'Already you are not following this user.');
            default:
                return $this->respondWithError([],'Not supported relationship status');
        }

    }
}
