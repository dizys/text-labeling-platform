<?php


namespace app\api\controller;


use think\Controller;

class User extends Controller
{
    protected $middleware = [
        'APIAuth' => ['except' => ['login']],
    ];

    public function login()
    {
        $username = input("post.username");
        $password = input("post.password");

        if (!$username || !$password) {
            return failure('用户名或者密码不能为空');
        }

        $user = \app\base\model\User::where('username', $username)->where('password', md5($password))->find();

        if (!$user) {
            return failure('用户名或者密码不正确');
        }

        session('user_id', $user->id);
        return success();
    }

    public function logout()
    {
        session('user_id', null);
        return success();
    }
}