<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2019/2/14
 * Time: 9:57
 */

namespace app\api\controller;

use app\admin\model\Path;
use app\api\model\TokenModel;
use app\common\model\SystemModel;
use think\Controller;
use think\Request;

class Base extends  Controller
{

    protected  $uid="";
    protected  $request="";
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        //判断是否是post提交
        if(!$request->isPost()){
            $this->error("请求错误");
            exit;
        }
        $this->request=$request;


        //根据token获得用户id
        $token=$request->post("token");
        $tokenmodel=new TokenModel();
        $uid=$tokenmodel->getuserinfo($token);
        $this->uid=$uid;
    }


    /**
     * 成功返回数据
     * $data  返回数据
     * $msg 返回提示
    */
    public function successReturn($data,$msg=""){
        $return_array=array(
            "status"=>1,
            "data"=>$data,
            "msg"=>$msg
        );
        echo json_encode($return_array);
    }
    /**
     * 失败返回数据
     * $msg 返回提示
     */
    public function errorReturn($msg="")
    {
        $return_array=array(
            "status"=>0,
            "msg"=>$msg
        );
        echo json_encode($return_array);
    }
}