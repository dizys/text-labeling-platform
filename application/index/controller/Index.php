<?php

namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        if (!$userId) {
            $this->redirect(url('index/user/login'));
        } else {
            $this->redirect(url('index/label/home'));
        }
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
