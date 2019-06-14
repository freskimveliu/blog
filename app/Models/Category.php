<?php

namespace App\Models\Blogs;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name','description'
    ];

    public function posts(){
        return $this->hasMany(Post::class);
    }
}
