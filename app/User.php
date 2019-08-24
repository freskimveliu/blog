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
        'has_blocked_relationships', 'next_relationship_action_as_a_friend_with_auth_user',
        'next_relationship_action_as_a_user_with_auth_friend',
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

    public function following_auth_user(){
        return $this->hasOne(UserRelationship::class,'user_id')->where('status',RELATIONSHIP_STATUS_FOLLOWING)->where('friend_id',(User::getUser()->id ?? 0))->latest();
    }

    public function followers(){
        return $this->hasMany(UserRelationship::class,'friend_id')->where('status',RELATIONSHIP_STATUS_FOLLOWING);
    }

    public function follower_auth_user(){
        return $this->hasOne(UserRelationship::class,'friend_id')->where('status',RELATIONSHIP_STATUS_FOLLOWING)->where('user_id',(User::getUser()->id ?? 0))->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getImFollowingAttribute(){
        if(!($this->relationLoaded('follower_auth_user'))) return null;
        return $this->follower_auth_user()->exists();
    }

    public function getIsFollowingMeAttribute(){
        if(!$this->relationLoaded('following_auth_user')) return null;
        return $this->following_auth_user()->exists();
    }

    public function getRelationshipStatusAsAFriendWithAuthUserAttribute(){
        if(!$this->relationLoaded('relationship_as_a_friend_with_auth_user')) return null;

        return $this->relationship_as_a_friend_with_auth_user->status ?? RELATIONSHIP_STATUS_UNFOLLOWING;
    }

    public function getRelationshipStatusAsAUserWithAuthFriendAttribute(){
        if(!$this->relationLoaded('relationship_as_a_user_with_auth_friend')) return null;

        return $this->relationship_as_a_user_with_auth_friend->status ?? RELATIONSHIP_STATUS_UNFOLLOWING;
    }

    public function getNextRelationshipActionAsAFriendWithAuthUserAttribute(){
        if(!$this->relationLoaded('relationship_as_a_friend_with_auth_user')) return null;

        $status  = $this->relationship_as_a_friend_with_auth_user->status ?? RELATIONSHIP_STATUS_UNFOLLOWING;
        $action  = null;
        switch($status){
            case RELATIONSHIP_STATUS_UNFOLLOWING:
                $action = RELATIONSHIP_ACTION_FOLLOW;
                break;
            case RELATIONSHIP_STATUS_REQUESTED:
            case RELATIONSHIP_STATUS_FOLLOWING:
                $action = RELATIONSHIP_ACTION_UNFOLLOW;
                break;
            default:
                return null;
        }

        return $action;
    }

    public function getNextRelationshipActionAsAUserWithAuthFriendAttribute(){
        if(!$this->relationLoaded('relationship_as_a_user_with_auth_friend')) return null;

        $status  = $this->relationship_as_a_user_with_auth_friend->status ?? RELATIONSHIP_STATUS_UNFOLLOWING;
        $action  = null;
        switch($status){
            case RELATIONSHIP_STATUS_UNFOLLOWING:
                $action = RELATIONSHIP_ACTION_FOLLOW;
                break;
            case RELATIONSHIP_STATUS_REQUESTED:
            case RELATIONSHIP_STATUS_FOLLOWING:
                $action = RELATIONSHIP_ACTION_UNFOLLOW;
                break;
            default:
                return null;
        }

        return $action;
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
        if(!$this->relationLoaded('relationship_as_a_user_with_auth_friend')) return false;

        return $this->relationship_as_a_user_with_auth_friend()
                     ->where('status',RELATIONSHIP_STATUS_REQUESTED)
                     ->exists();
    }

    public function getImageAttribute(){
        return $this->image_url ?? asset('/images/defaults/profile.png');
    }

    public function getHasBlockedRelationshipsAttribute(){
        if($this->is_my_profile){
            return false;
        }

        if(!$this->is_private_account){
            return false;
        }

        if($this->im_following){
            return false;
        }

        return true;
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
        return $query->with(['relationship_as_a_friend_with_auth_user', 'following_auth_user','follower_auth_user',
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
