<?php

namespace App\Http\Controllers\API\Auth\Users;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserRelationshipsController extends Controller
{
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
                    $status = $this->follow($friend);
                }catch (\Exception $exception){
                    return $this->respondWithError([],$exception->getMessage());
                }
                break;
            case RELATIONSHIP_ACTION_UNFOLLOW:
                try{
                    $status = $this->unFollow($friend);
                }catch (\Exception $exception){
                    return $this->respondWithError([],$exception->getMessage());
                }
                break;
            default:
                return $this->respondWithError([],'Action is not supported');
        }

        return $this->respondWithSuccess(['status'=>$status]);
    }

    private function follow($friend){
        $status = RELATIONSHIP_STATUS_FOLLOWING;
        if($friend->is_private_account){
            $status = RELATIONSHIP_STATUS_REQUESTED;
        }

        $relation = User::getUser()->relationships()->friend($friend->id)->notUnFollowing()->first();

        if($relation){
            throw new \Exception("You have a relation with this user");
        }

        User::getUser()->relationships()->updateOrCreate([
            'friend_id' => $friend->id
        ], [
            'friend_id' => $friend->id,
            'status'    => $status,
        ]);

        return $status;
    }

    private function unFollow($friend){
        $user = User::getUser();

        $relation = $user->relationships()->where('friend_id',$friend->id)->first();
        if(!$relation){
            throw new \Exception("You don't have relation with this user");
        }

        if($relation->status == RELATIONSHIP_STATUS_UNFOLLOWING){
            throw new \Exception("You cannot un follow this user.");
        }

        $user->relationships()->updateOrCreate([
            'friend_id' => $friend->id
        ], [
            'friend_id' => $friend->id,
            'status'    => RELATIONSHIP_STATUS_UNFOLLOWING,
        ]);

        return RELATIONSHIP_STATUS_UNFOLLOWING;
    }
}
