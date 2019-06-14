<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        User::create([
            'name'      => $request->get('name'),
            'email'     => $request->get('email'),
            'password'  => bcrypt($request->get('password'))
        ]);

        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials))
            return $this->respondWithError([],'Unauthorized',401);

        $user        = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->token;

        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addMonths(6);

        $token->save();

        return $this->respondWithSuccess([
            'access_token'  => $tokenResult->accessToken,
            'token_type'    => 'Bearer',
            'expires_at'    => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'user'          => $user,
        ]);
    }


    public function login(LoginRequest $request){
        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials))
            return $this->respondWithError(['email'=>['Wrong credentials']],'Wrong credentials',400);

        $user        = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->token;

        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addMonths(6);

        $token->save();

        return $this->respondWithSuccess([
            'access_token'  => $tokenResult->accessToken,
            'token_type'    => 'Bearer',
            'expires_at'    => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'user'          => $user,
        ]);
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();
        return $this->respondWithSuccess([],'Successfully logged out');
    }
}
