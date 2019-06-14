<?php

namespace App;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'image_url', 'bio'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function opened_posts(){
        return $this->belongsToMany(Post::class,'user_opened_posts','user_id','post_id');
    }

    public function favorited_posts(){
        return $this->belongsToMany(Post::class,'user_favorited_posts','user_id','post_id')->withTimestamps();
    }

    public function comments(){
        return $this->hasMany(PostComment::class,'user_id');
    }


    private static $user;
    public static function getUser() {

        if (Auth::check()) {
            return Auth::user();
        }

        if (User::$user) {
            return User::$user;
        }

        try {
            $user = request()->user('api');
            User::$user = $user;
            return $user;
        }catch (\Exception $exception) {
            return null;
        }
    }
}
