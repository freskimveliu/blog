<?php
namespace App\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request as Request;

abstract class Filter{

    protected $request;

    /** @var Builder */
    protected $builder;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function apply(Builder $builder){
        $this->builder = $builder;
        foreach ($this->filters() as $name => $value){
            if(method_exists($this,$name) && !empty($value)){
                call_user_func_array([$this,$name],array_filter([$value]));
            }
        }
        return $this->builder;
    }

    public function filters(){
        return $this->request->all() ?? [];
    }
}
