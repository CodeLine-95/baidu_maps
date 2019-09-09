<?php
namespace app\admin\controller;

use app\admin\model\ChartList;

class Index extends Base
{
    public function index(){
        return $this->fetch();
    }

    public function edit(){
        if (request()->isPost()){
            $params = request()->post();
            $updateID = (new ChartList())->update($params);
            if ($updateID){
                return json(['msg'=>'修改成功','code'=>200]);
            }else{
                return json(['msg'=>'修改失败','code'=>400]);
            }
        }else{
            $params = request()->get();
            $field = db('chart_list')->where(['id'=>$params['id']])->find();

            $citys = db('citys')->where('type',1)->select();

            $type = db('chart_cate')->where('status',1)->select();

            $this->assign([
                'field'=>$field,
                'citys'=>$citys,
                'type'=>$type
            ]);
            return $this->fetch();
        }
    }

    public function add(){
        if (request()->isPost()){
            $params = request()->post();
            $updateID = (new ChartList())->save($params);
            if ($updateID){
                return json(['msg'=>'添加成功','code'=>200]);
            }else{
                return json(['msg'=>'添加失败','code'=>400]);
            }
        }else {
            $citys = db('citys')->where('type', 1)->select();

            $type = db('chart_cate')->where('status', 1)->select();

            $this->assign([
                'citys' => $citys,
                'type' => $type
            ]);
            return $this->fetch();
        }
    }

    public function lagout(){
        session('user',null);
        session(null);
    }
}