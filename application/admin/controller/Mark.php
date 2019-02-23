<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2019/2/14
 * Time: 9:57
 */

namespace app\admin\controller;

use app\admin\model\Path;
use app\common\model\MarkinfoModel;
use app\common\model\MarkModel;
use app\common\model\SystemModel;
use think\Controller;
use think\Request;

class Mark extends  Base
{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 报名模板
    */
    public function signup(){
        //页码
        $page=input("param.page");
        //获取列表
        $mark=new MarkModel();
        $list=$mark->get_list($page);

        $this->assign("list",$list);
        return view("mark_list");
    }
    /**
     * 报名模板编辑
    */
    public function edit_sign_up(){
        $id=input('param.id');

        //获取信息
        $mark=new MarkModel();
        $sign_info=$mark->get_sign_info($id);
        if(!$sign_info["status"]){
            $this->error($sign_info["msg"]);
            exit;
        }
        $this->assign("info",$sign_info["info"]);
        return view("edit_sign_up");

    }

    /**
     * 新增/编辑留言字段
    */
    public function add_sign_up_info()
    {
        //判断post
        $request=$this->checkPost();

        $id=$request->post("id");

        //组织数据
        $save["name"]=$request->post("name");//字段名称
        $save["type"]=$request->post("type");//字段类型
        $save["default"]=$request->post("default");//字段默认值
        $save["placeholder"]=$request->post("placeholder");//字段为空时显示
        $save["tips"]=$request->post("tips");//字段标签
        $save["is_mast"]=$request->post("is_mast");//字段是否必填
        $save["mark_id"]=input("param.id");//模板id

        //编辑及保存数据
        $markinfo=new MarkinfoModel();
        $result=$markinfo->save_byid($save,$id);

        //返回数据
        $result["status"]?$this->success($result["msg"]):$this->error($request["msg"]);
    }


    public function edit_temp(){

        $request=$this->checkPost();

        $id=$request->post("id");
        $title=$request->post("title");

        $mark=new MarkModel();
        $result=$mark->edit_mark($id,$title);

        $result["status"]?$this->success($result["msg"],$result["url"]):$this->error($result["msg"]);
    }

    /**
     * 修改模板状态
    */
    public function change_mark_status(){
        $request=$this->checkPost();

        $id=$request->post("id");
        $status=$request->post("status");
        $status=$status==0?1:($status==-1?-1:0);
        $mark=new MarkModel();
        $result=$mark->change_status($id,$status);
        $result["status"]?$this->success($result["msg"]):$this->error($result["msg"]);
    }
}