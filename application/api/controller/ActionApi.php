<?php
namespace app\api\controller;

use app\admin\model\ChartList;
use think\Controller;

class ActionApi extends Controller
{
    /**
     * 数据列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function table_action(){
        $params = request()->get();
        $totalCount = db('chart_list')->count();
        if (isset($params['limit']) && !empty($params['limit'])){
            $pageSize = $params['limit'];
        }else{
            $pageSize = 10;
        }
        $lastPage = ceil($totalCount/$pageSize);
        if (isset($params['page']) && !empty($params['page'])){
            $page = $params['page'];
            if ($page > $lastPage){
                $page = $lastPage;
            }
        }else{
            $page = 1;
        }
        $list = (new ChartList)
            ->alias('l')
            ->field('l.*,c.name as cityname,t.name as countyname')
            ->leftJoin(['citys'=>'c'],'l.city = c.id')
            ->leftJoin(['citys'=>'t'],'l.county = t.id')
            ->order('l.id','desc')
            ->limit(($page-1)*$pageSize,$pageSize)->select();
        $json_data = [
            'code' => '0',
            'currPage'=>$page,
            'count'=>strval($totalCount),
            'pageSzie' =>$pageSize,
            'data' => $list,
            'msg'=>'获取数据成功'
        ];
        return json($json_data);
    }

    /**
     * 数据列表删除
     * @return \think\response\Json
     */
    public function table_del(){
        if (request()->isPost()){
            $params = request()->post();
            $delID = ChartList::destroy($params['id']);
            if ($delID){
                return json(['msg'=>'删除成功','code'=>200]);
            }else{
                return json(['msg'=>'删除失败','code'=>400]);
            }
        }else{
            return json(['msg'=>'请求异常','code'=>410]);
        }
    }

    /**
     * 登录请求
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function login_action(){
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
            return json(['msg'=>'请求异常','code'=>410]);
        }
    }

    /**
     * 城市区域获取
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function county(){
        $params = request()->get();
        if (isset($params['cityid']) && !empty($params['cityid'])){
            $county = db('citys')->where(['pid'=>$params['cityid'],'type'=>2])->select();
            if ($county){
                return json(['msg'=>'数据获取成功','data'=>$county,'code'=>200]);
            }else{
                return json(['msg'=>'数据获取失败','data'=>[],'code'=>200]);
            }
        }else{
            return json(['msg'=>'参数cityid必填！','code'=>410]);
        }
    }
}