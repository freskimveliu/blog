<?php

namespace App;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\UserFavoritePost;
use App\Models\UserRelationship;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;


    protected $fillable = [
        'name', 'email', 'password', 'role', 'image_url', 'bio', 'username'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'im_following', 'is_my_profile', 'is_following_me'
    ];

    /*
   |--------------------------------------------------------------------------
   | RELATIONS
   |--------------------------------------------------------------------------
   */

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

    public function relationships(){
        return $this->hasMany(UserRelationship::class,'user_id');
    }

    public function followings(){
        return $this->hasMany(UserRelationship::class,'user_id')->where('status','following');
    }

    public function followers(){
        return $this->hasMany(UserRelationship::class,'friend_id')->where('status','following');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */
    public function getImfollowingAttribute(){
        if(!$this->relationLoaded('followers')) return null;
        return $this->followers()->where('user_id',(User::getUser()->id ?? 0))->exists();
    }

    public function getIsFollowingMeAttribute(){
        if(!$this->relationLoaded('followings')) return null;
        return $this->followings()->where('friend_id',(User::getUser()->id ?? 0))->exists();
    }

    public function getIsMyProfileAttribute(){
        if(!User::getUser()){
            return null;
        }
        return $this->getKey() == User::getUser()->id;
    }

    /*
   |--------------------------------------------------------------------------
   | SCOPES
   |--------------------------------------------------------------------------
   */



    /*
   |--------------------------------------------------------------------------
   | METHODS
   |--------------------------------------------------------------------------
   */

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

    public function routeNotificationForSlack($notification){
        return env('SLACK_HOOK');
    }

}
