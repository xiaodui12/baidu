<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2019/2/14
 * Time: 9:57
 */

namespace app\admin\controller;

use app\admin\model\Path;
use app\common\model\SystemModel;
use think\Controller;
use think\Request;

class Base extends  Controller
{
    public function _initialize(){
        //设置config配置
        $system=new SystemModel();
        $system->setconfig();
        //判断登录
        $this->checklogin();
    }
    /**
     * 判断用户是否登录，已经登录不作处理，未登录，跳转到登录页面
     */
    function checklogin()
    {
        $check_login=session("check_login");//得到登录状态
        if(empty($check_login))//判断登录状态
        {
            $this->assign("title",config("PAGE_TITLE"));
            $this->redirect("Login/login");
        }
        else{
            //获得当前显示路径
            $path=new Path();
            $load_path=$path->load_path(1);
            $this->assign("menu",$load_path["list"]);

            //设置显示标题
            if(empty($load_path["title"])){
                $title=config("PAGE_TITLE");
            }else{
                $title=$load_path["title"]."_".config("PAGE_TITLE");
            }
            $this->assign("title",$title);
        }
    }

    /**
     * 判断是否是post
    */
    function checkPost(){
        //请求信息
        $request = Request::instance();
        //判断是否是post提交
        if(!$request->isPost()){
            $this->error("请求错误");
            exit;
        }
        return $request;
    }
}