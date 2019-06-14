<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavoritePost extends Model
{
    protected $table = 'user_favorite_posts';

    protected $fillable = [
        'user_id', 'post_id'
    ];
}
