<?php


namespace app\index\middleware;


use app\base\model\User;
use think\Request;

class AuthMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect(url('index/user/login'));
        }

        $user = User::get($userId);

        if (!$user) {
            dump('hello');
            return redirect(url("index/user/login"));
        }

        bind('app\base\model\User', $user);

        return $next($request);
    }
}