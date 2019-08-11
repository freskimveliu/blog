<?php

namespace App;

use App\Filters\Filter;
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
        'name', 'email', 'password', 'role', 'image_url', 'bio', 'username', 'is_private_account'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_private_account'=> 'boolean',
    ];

    protected $appends = [
        'im_following', 'is_my_profile', 'is_following_me', 'image',
        'is_following_me_and_im_not_following_him', 'can_i_show_posts', 'has_requested_to_follow_me',
        'relationship_status_as_a_user_with_auth_friend', 'relationship_status_as_a_friend_with_auth_user',
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

    public function relationships_as_a_user(){
        return $this->hasMany(UserRelationship::class,'user_id');
    }

    public function relationship_as_a_user_with_auth_friend(){
        return $this->hasOne(UserRelationship::class,'user_id')->where('friend_id',(User::getUser()->id ?? 0));
    }

    public function relationships_as_a_friend(){
        return $this->hasMany(UserRelationship::class,'friend_id');
    }

    public function relationship_as_a_friend_with_auth_user(){
        return $this->hasOne(UserRelationship::class,'friend_id')->where('user_id',(User::getUser()->id ?? 0));
    }

    public function followings(){
        return $this->hasMany(UserRelationship::class,'user_id')->where('status',RELATIONSHIP_STATUS_FOLLOWING);
    }

    public function followers(){
        return $this->hasMany(UserRelationship::class,'friend_id')->where('status',RELATIONSHIP_STATUS_FOLLOWING);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getImFollowingAttribute(){
        if(!($this->relationLoaded('followers'))) return null;
        return $this->followers()->where('user_id',(User::getUser()->id ?? 0))->exists();
    }

    public function getIsFollowingMeAttribute(){
        if(!$this->relationLoaded('followings')) return null;
        return $this->followings()->where('friend_id',(User::getUser()->id ?? 0))->exists();
    }

    public function getRelationshipStatusAsAFriendWithAuthUserAttribute(){
        if(!$this->relationLoaded('relationship_as_a_friend_with_auth_user')) return null;

        return $this->relationship_as_a_friend_with_auth_user()->first()->status ?? null;
    }

    public function getRelationshipStatusAsAUserWithAuthFriendAttribute(){
        if(!$this->relationLoaded('relationship_as_a_user_with_auth_friend')) return null;

        return $this->relationship_as_a_user_with_auth_friend()->first()->status ?? null;
    }

    public function getIsMyProfileAttribute(){
        if(!User::getUser()){
            return null;
        }
        return $this->getKey() == User::getUser()->id;
    }

    public function getIsFollowingMeAndImNotFollowingHimAttribute(){
        if($this->getKey() == (User::getUser()->id ?? 0)){
            return false;
        }

        if(!$this->is_following_me){
            return false;
        }

        if(!$this->relationLoaded('relationship_as_a_friend_with_auth_user')) return null;

        $am_i_following = $this->relationship_as_a_friend_with_auth_user()
            ->whereIn('status',[RELATIONSHIP_STATUS_REQUESTED,RELATIONSHIP_STATUS_FOLLOWING])->exists();

        if($am_i_following){
            return false;
        }

        return true;
    }

    public function getCanIShowPostsAttribute(){
        if((User::getUser()->id ?? 0) == $this->getKey()){
            return true;
        }

        if($this->im_following){
            return true;
        }

        if(!$this->is_private_account){
            return true;
        }

        return false;
    }

    public function getHasRequestedToFollowMeAttribute(){
        if(!$this->relationLoaded('relationships_as_a_user')) return false;

        return $this->relationship_as_a_user_with_auth_friend()
                     ->where('status',RELATIONSHIP_STATUS_REQUESTED)
                     ->exists();
    }

    public function getImageAttribute(){
        return $this->image_url ?? asset('/images/defaults/profile.png');
    }

    /*
   |--------------------------------------------------------------------------
   | SCOPES
   |--------------------------------------------------------------------------
   */

    public function scopeSuggestedUsers($query,$ids){
        return $query->whereNotIn('id',$ids)->where('id','<>',(User::getUser()->id ?? 0));
    }

    public function scopeFull($query){
        return $query->with(['relationships_as_a_user','relationship_as_a_friend_with_auth_user', 'followings','followers',
                            'relationship_as_a_user_with_auth_friend'])
                    ->withCount('followings','followers','posts');
    }

    public function scopeFilter($query,Filter $filters){
        return $filters->apply($query);
    }


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
