<?php


namespace app\api\middleware;


use app\base\model\User;
use think\Request;

class AuthMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $userId = session('user_id');

        if (!$userId) {
            return failure('无权限访问');
        }

        $user = User::get($userId);

        if (!$user) {
            return failure('无权限访问');
        }

        bind('app\base\model\User', $user);

        return $next($request);
    }
}