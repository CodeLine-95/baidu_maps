<?php
namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    public function initialize(){
        $user = session('user');
        if (!$user){
            $this->redirect('login/index');
        }
    }
}