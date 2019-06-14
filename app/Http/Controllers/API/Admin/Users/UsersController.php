<?php

namespace App\Http\Controllers\API\Admin\Users;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function index(){
        $class_objects = User::latest()->withCount('posts')->paginate();

        return $this->respondWithSuccess($class_objects);
    }

    public function show($id){
        $object = User::withCount('posts')->find($id);

        if(!$object){
            return $this->respondWithError([],'User not found',404);
        }

        return $this->respondWithSuccess($object);
    }

    public function update(Request $request,$id){
        $object = User::find($id);

        if(!$object){
            return $this->respondWithError([],'User not found',404);
        }

        $params = $request->only('name','role','bio','image');

        $object->update($params);

        return $this->respondWithSuccess($object);
    }

    public function destroy($id){
        $object = User::find($id);

        if(!$object){
            return $this->respondWithError([],'User not found',404);
        }

        $object->delete();

        return $this->respondWithSuccess();
    }
}
