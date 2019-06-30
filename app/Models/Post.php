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
        'is_favorite', 'short_description',
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

    public function favorites(){
        return $this->hasMany(UserFavoritePost::class,'post_id');
    }

    public function comments(){
        return $this->hasMany(PostComment::class,'post_id')->latest();
    }

    public function is_my_favorite(){
        return $this->hasOne(UserFavoritePost::class,'post_id')->where('user_id',(User::getUser()->id ?? 0));
    }

    public function getIsFavoriteAttribute(){
        if(!$this->relationLoaded('is_my_favorite')) return null;
        return $this->is_my_favorite()->exists();
    }

    public function getShortDescriptionAttribute(){
        return strip_tags($this->attributes['description']);
    }

    public function scopeFilter($query,Filter $filters){
        return $filters->apply($query);
    }

}
