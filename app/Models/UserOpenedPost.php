<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserOpenedPost extends Model
{
    protected $table = 'user_opened_posts';

    protected $fillable = [
        'user_id', 'post_id'
    ];

}
