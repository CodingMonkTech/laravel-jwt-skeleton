<?php 


// This is an extended Middleware to Throw Custom Error when Unauthorised role user tries to access Route.

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class EntrustRole {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param null $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        $user=JWTAuth::user();
        if($user){
            if ($role != null && !$user->hasRole(explode('|', $role))) {
                return response()->json(['status'=>false,'message' => 'unauthorised']);
            }    
        }
        

        return $next($request);
    }

}