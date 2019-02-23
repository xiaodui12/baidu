<?php
namespace app\api\controller;


use app\api\model\MemberOpenidModel;
use app\common\model\xcx;
use think\Request;

class Index extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function _initialize(){
        parent::_initialize();
    }
    public function index()
    {
        $this->successReturn("","123");
    }

    /**
     * 用户登录
     * /api.php/index/login.html
     * 参数：code
    */
    public function login(){
        $code=$this->request->post("code");
        //使用code换openid
        $xcx=new xcx();
        $result=$xcx->getopenid($code);
        if($result["code"]!=0){
            //curl错误返回
            $this->errorReturn($result["errmsg"]);
        }elseif ($result["data"]["errcode"]!=0){
            $this->errorReturn($result["data"]["errmsg"]);
        }else{
            //生成token
            $memberopenid=new MemberOpenidModel();
            $result=$memberopenid->check_login($result["data"]);

            //返回结果
            if($result["code"]!=0) {
                $this->errorReturn($result["errmsg"]);
            }
            else{
                $this->successReturn($result["data"],"");
            }
        }
    }




}
