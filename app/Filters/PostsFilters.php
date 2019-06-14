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
}
