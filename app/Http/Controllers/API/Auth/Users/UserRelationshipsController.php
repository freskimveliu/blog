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

        $user = User::getUser();
        $friend = User::whereUsername($slug)->first();
        if (!$friend) {
            return $this->respondWithError([], 'User not found', 404);
        }

        if ($user->id == $friend->id) {
            return $this->respondWithError([], 'You can not follow yourself!');
        }

        $action = $request->get('action');

        if ($action == RELATIONSHIP_ACTION_FOLLOW) {
            $user->relationships()->updateOrCreate([
                'friend_id' => $friend->id
            ], [
                'friend_id'     => $friend->id,
                'status'        => RELATIONSHIP_STATUS_FOLLOWING,
            ]);
        } elseif($action == RELATIONSHIP_ACTION_UNFOLLOW) {
            $user->relationships()->updateOrCreate([
                'friend_id' => $friend->id
            ], [
                'friend_id'     => $friend->id,
                'status'        => RELATIONSHIP_STATUS_UNFOLLOWING,
            ]);
        }

        return $this->respondWithSuccess();
    }
}
