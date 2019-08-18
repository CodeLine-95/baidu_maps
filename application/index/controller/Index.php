<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index(){
        //选中城市
        $cityid = input('cityid');
        $citys_table = 'citys';
        $field_county = $this->selectOne($citys_table,['id'=>$cityid]);
        if ($field_county['type'] == 2){
            $field_city = $this->selectOne($citys_table,['id'=>$field_county['pid']]);
            $field_lists = $this->selectAll('chart_list',['county'=>$cityid]);
        }else{
            $field_city = $field_county;
            $field_lists = $this->selectAll('chart_list',['city'=>$cityid]);
        }
        //城市
        $citys = $this->selectAll($citys_table,['type'=>1]);
        //数据列表
        foreach ($field_lists as $k=>$l){
            $type_one = $this->selectOne('chart_cate',['id'=>$l['type']]);
            $field_lists[$k]['cate_name'] = $type_one['name'];
        }
        $this->assign([
            'citys' => $citys,
            'field_city' => $field_city,
            'field_county' => $field_county,
            'field_list' => $field_lists
        ]);
        return $this->fetch();
    }

    public function selectOne($table='',$where=''){
        $res_data = Db::table($table)->where($where)->find();
        return $res_data;
    }

    public function selectAll($table='',$where=''){
        $res_data = Db::table($table)->where($where)->all();
        return $res_data;
    }

    public function citysAjax(){
        if (request()->isAjax()){
            $params = request()->post();
            $countys = $this->selectAll('citys',$params);
            if ($countys){
                return json(['data'=>$countys,'code'=>200]);
            }else{
                return json(['data'=>[],'code'=>200]);
            }
        }else{
            return json(['msg'=>'请求异常错误','code'=>400]);
        }
    }

    public function chartAjax(){
        if (request()->isAjax()){
            $params = request()->post();
            if ($params['type'] == 2){
                $data['county'] = $params['city'];
            }else{
                $data['city'] = $params['city'];
            }
            $chart_list = $this->selectAll('chart_list',$data);
            if ($chart_list){
                return json(['data'=>$chart_list,'code'=>200]);
            }else{
                return json(['data'=>[],'code'=>200]);
            }
        }else{
            return json(['msg'=>'请求异常错误','code'=>400]);
        }
    }

    public function clickAjax(){
        if (request()->isAjax()){
            $params = request()->post();
            $data['longitude'] = number_format($params['longitude'],2,'.','');
            $data['latitude'] = number_format($params['latitude'],2,'.','');
            $field = $this->selectOne('chart_list',$data);
            if ($field){
                return json(['data'=>$field,'code'=>200]);
            }else{
                return json(['data'=>'','code'=>200]);
            }
        }else{
            return json(['msg'=>'请求异常错误','code'=>400]);
        }
    }
}
