<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class ApiAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $user = User::getUser();
        if ($user == null) {
            return response()->json(['errors' => [], 'message' => 'Unauthorized Request', 'success' => false], 401);
        }

        if($user->role != 'admin'){
            return response()->json(['errors' => [], 'message' => 'Unauthorized Request', 'success' => false], 403);
        }

        return $next($request);
    }
}
