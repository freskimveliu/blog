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
}
