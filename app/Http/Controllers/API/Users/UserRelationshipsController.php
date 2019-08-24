<?php

namespace App\Http\Controllers\API\Users;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserRelationshipsController extends Controller
{
    public function index($slug,Request $request){
        $user = User::whereUsername($slug)->full()->first();

        if(!$user){
            return $this->respondWithError([],'User not found!');
        }

        if($user->has_blocked_relationships){
            return $this->respondWithError([],'You cannot get relationships for this user.');
        }

        $status = $request->get('status');

        if($status == 'followers'){
            $relationships = $this->getFollowers($user);
        }else if($status == 'followings'){
            $relationships = $this->getFollowings($user);
        }

        return $this->respondWithSuccess($relationships ?? []);
    }

    private function getFollowers(User $user){
        return $user->followers()
            ->latest()
            ->with(['user'=>function($q){
                $q->full();
            }])
            ->paginate($this->per_page);
    }

    private function getFollowings(User $user){
        return $user->followings()
            ->latest()
            ->with(['friend'=>function($q){
                $q->full();
            }])
            ->paginate($this->per_page);
    }
}
