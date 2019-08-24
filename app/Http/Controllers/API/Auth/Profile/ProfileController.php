<?php

namespace App\Http\Controllers\API\Auth\Profile;

use App\Http\Controllers\Controller;
use App\Traits\FileManager;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    use FileManager;

    public function index(){
        $user = User::getUser();
        return $this->respondWithSuccess($user);
    }

    public function update(Request $request){
        $rules = [
            'name'                  => 'required|string|min:3',
            'image'                 => 'nullable|file|image',
            'is_private_account'    => 'boolean',
        ];

        $this->validate($request, $rules);

        $params = $request->only('image','name','bio','is_private_account');

        if ($image = $request->file('image')) {
            try {
                $params['image_url'] = $this->uploadFile($image);
            } catch (\Exception $exception) {
                return $this->respondWithError([],$exception->getMessage());
            }
        }

        //TODO change this slug generator;
        $params['username'] = Str::slug($request->get('name'));

        $user = User::getUser();
        $user->update($params);

        $user = User::find($user->id);
        return $this->respondWithSuccess($user);
    }
}
