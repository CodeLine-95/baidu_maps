<?php
namespace app\admin\controller;

use think\Controller;

class Login extends Controller
{
    public function index(){
        if (request()->isPost()){
            $params = request()->post();
            $params['token'] = request()->token('__token__','sha1');
            $field = db('admin')->where(['username'=>$params['username']])->find();
            if ($field){
                if (pwd_verify($params['password'],$field['password'])){
                    $update = ['logintime'=>time(),'loginip'=>request()->ip(),'loginstatus'=>1];
                    $updateID = db('admin')->where(['username'=>$field['username']])->update($update);
                    if ($updateID){
                        session('user',['username'=>$field['username'],'token'=>$params['token']]);
                        return json(['msg'=>'登录成功，信息已更新！','code'=>200]);
                    }else{
                        return json(['msg'=>'登录成功，信息未更新！','code'=>210]);
                    }
                }else{
                    return json(['msg'=>'您输入的密码不正确！','code'=>400]);
                }
            }else{
                return json(['msg'=>'您输入的用户不存在！','code'=>400]);
            }
        }else{
            return $this->fetch();
        }
    }
}