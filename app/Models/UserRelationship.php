<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserRelationship extends Model
{
    protected $fillable = [
        'user_id', 'friend_id', 'status', 'old_status'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function friend(){
        return $this->belongsTo(User::class,'friend_id');
    }

    public function scopeUnFollowing($query){
        return $query->where('status',RELATIONSHIP_STATUS_UNFOLLOWING);
    }

    public function scopeNotUnFollowing($query){
        return $query->where('status','<>',RELATIONSHIP_STATUS_UNFOLLOWING);
    }

    public function scopeRequested($query){
        return $query->where('status',RELATIONSHIP_STATUS_REQUESTED);
    }

    public function scopeFriend($query,$friend_id){
        return $query->where('friend_id',$friend_id);
    }
}
