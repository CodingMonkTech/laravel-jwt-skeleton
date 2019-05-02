<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Config;
use \DB;
use Closure;

class DatabaseSwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $database=$request->getHost();
       
         // Erase the tenant connection, thus making Laravel get the default values all over again.
        DB::purge('mysql');
        // Make sure to use the database name we want to establish a connection.
        Config::set('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        Config::set('database.connections.mysql.database', $database);
        Config::set('database.connections.mysql.username', env('DB_USERNAME', 'forge'));
        Config::set('database.connections.mysql.password', env('DB_PASSWORD', ''));
        Config::set('database.connections.mysql.driver', 'mysql');
       
        DB::reconnect('mysql');

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            abort(404);
            return $next($request);
        }

        return $next($request);
    }
}
