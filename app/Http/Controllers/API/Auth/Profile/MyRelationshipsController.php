<?php

namespace App\Http\Controllers\API\Auth\Profile;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class MyRelationshipsController extends Controller
{
    public function index(Request $request){
        $user = User::getUser();

        $status = $request->get('status');

        if($status == 'followers'){
            $relationships = $user->followers()
                                  ->latest()
                                  ->with(['user'=>function($q){
                                      $q->full();
                                  }])
                                  ->paginate($this->per_page);
        }else if($status == 'followings'){
            $relationships = $user->followings()
                                  ->latest()
                                  ->with(['friend'=>function($q){
                                    $q->full();
                                  }])
                                  ->paginate($this->per_page);
        }

        return $this->respondWithSuccess($relationships ?? []);
    }


    public function update(Request $request, $slug){
        $this->validate($request, [
            'action' => 'required'
        ]);

        $user   = User::getUser();
        $friend = User::whereUsername($slug)->first();
        if (!$friend) {
            return $this->respondWithError([], 'User not found', 404);
        }

        if ($user->id == $friend->id) {
            return $this->respondWithError([], 'You can not follow yourself!');
        }

        $action = $request->get('action');

        switch ($action){
            case RELATIONSHIP_ACTION_FOLLOW:
                try{
                    $this->follow($friend);
                }catch (\Exception $exception){
                    return $this->respondWithError([],$exception->getMessage());
                }
                break;
            case RELATIONSHIP_ACTION_UNFOLLOW:
                try{
                    $this->unFollow($friend);
                }catch (\Exception $exception){
                    return $this->respondWithError([],$exception->getMessage());
                }
                break;
            default:
                return $this->respondWithError([],'Action is not supported');
        }

        $friend = User::full()->find($friend->id);
        return $this->respondWithSuccess($friend);
    }

    private function follow($friend){
        $status = RELATIONSHIP_STATUS_FOLLOWING;
        if($friend->is_private_account){
            $status = RELATIONSHIP_STATUS_REQUESTED;
        }

        $relation = User::getUser()->relationships_as_a_user()->friend($friend->id)->notUnFollowing()->first();

        if($relation){
            throw new \Exception("You have a relation with this user");
        }

        User::getUser()->relationships_as_a_user()->updateOrCreate([
            'friend_id' => $friend->id
        ], [
            'friend_id' => $friend->id,
            'status'    => $status,
        ]);

        return $status;
    }

    private function unFollow($friend){
        $user = User::getUser();

        $relation = $user->relationships_as_a_user()->where('friend_id',$friend->id)->first();
        if(!$relation){
            throw new \Exception("You don't have relation with this user");
        }

        if($relation->status == RELATIONSHIP_STATUS_UNFOLLOWING){
            throw new \Exception("You cannot un follow this user.");
        }

        $user->relationships_as_a_user()->updateOrCreate([
            'friend_id' => $friend->id
        ], [
            'friend_id' => $friend->id,
            'status'    => RELATIONSHIP_STATUS_UNFOLLOWING,
        ]);

        return RELATIONSHIP_STATUS_UNFOLLOWING;
    }
}
