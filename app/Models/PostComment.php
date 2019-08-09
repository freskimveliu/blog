<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable = [
        'user_id', 'post_id', 'user_name', 'message', 'parent_id'
    ];

    protected $appends = [
        'is_my_comment'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function getIsMyCommentAttribute(){
        if($this->user_id == (User::getUser()->id ?? 0)){
            return true;
        }

        return false;
    }
}
