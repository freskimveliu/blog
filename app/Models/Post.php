<?php

namespace App\Models;

use App\Filters\Filter;
use App\Models\Blogs\Category;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'user_id', 'category_id', 'title', 'description', 'image_url'
    ];

    protected $appends = [
        'is_favorite'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function open_by_users(){
        return $this->belongsToMany(User::class,'user_opened_posts','post_id','user_id');
    }

    public function favorite_by_users(){
        return $this->belongsToMany(User::class,'user_favorited_posts','post_id','user_id');
    }

    public function comments(){
        return $this->hasMany(PostComment::class,'post_id')->latest();
    }

    public function scopeFilter($query,Filter $filters){
        return $filters->apply($query);
    }

    public function getIsFavoriteAttribute(){
        $user = User::getUser();

        if($user){
            return $user->favorited_posts()->where('post_id',$this->id)->exists();

        }

        return false;
    }
}
