<?php

namespace App\Filters;

class PostsFilters extends Filter
{

    public function tag($value){
        switch ($value) {
            case 'most_popular':
                return $this->builder->orderBy('open_by_users_count','desc');

            case 'most_favorite':
                return $this->builder->orderBy('favorite_by_users_count','desc');

            default :
                return $this->builder;
        }
    }

    public function category($value){
        if(is_array($value)){
            return $this->builder->whereIn('category_id',$value);
        }
        return $this->builder->where('category_id',$value);
    }

    public function favorites($value){
        if(isset($value) && $value == 'true'){
            return $this->builder->whereHas('favorites');
        }
        return $this->builder;
    }

    public function q($value){
        $value = str_replace(' ','%',$value);
        return $this->builder->where('title', 'LIKE', "%$value%")
            ->orWhereHas('user',function ($q) use ($value){
                $q->where('name','LIKE',"%$value%");
            })
            ->orWhereHas('category',function($q) use ($value){
                $q->where('name','LIKE',"%$value%");
            });
    }
}
