<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2019/2/14
 * Time: 10:11
 * 系统设置文件
 */

namespace app\admin\controller;

use app\admin\model\Path;



class Menu extends Base
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }
    //————————————后台目录设置————————————


    /*********************
     * 一级导航操作（开始）
    ****************/
    /**
     *总后台目录列表
    */
    public function admin_list()
    {

        $type=1;
        $page = input('param.page');
        $page=$page?$page:1;
        $path=new Path();
        $list=$path->getpageBytype($type,$page);
        $this->assign("list",$list);
        $this->assign("type",$type);
        return view("admin_list");
    }
    /**
     *店家后台目录列表
    */
    public function store_list()
    {
        $type=2;
        $page = input('param.page');
        $page=$page?$page:1;
        $path=new Path();
        $list=$path->getpageBytype($type,$page);
        $this->assign("list",$list);
        $this->assign("type",$type);
        return view("admin_list");
    }


    /**
     * 编辑一级目录
    */
    public function edit_first(){
        $type = input('param.type');//类型
        $id = input('param.id');//路径id
        $this->assign("type",$type);
        $this->assign("id",$id);
        $info=array();
        //如果路径id存在，查询信息
        if(!empty($id)){
            $path=new Path();
            $info=$path->find($id)->toArray();
        }
        $this->assign("info",$info);
        return view("edit_first");
    }

    /**
     * 添加或编辑一级导航提交
    */
    public function edit_first_submit(){
        $type = input('param.type');//类型
        $id = input('param.id');//路径id

        //判断是否是post，并返回post信息
        $request=$this->checkPost();

        $name=$request->post('name');//导航名称
        $pyth=$request->post('pyth');//路径
        $sort=$request->post('sort');//排序
        $show_status=$request->post('show_status');//显示状态
        if(empty($name)){
            $this->error("请填写导航名称");
        }
        //需要保存的数据
        $save=array(
            "type"=>$type,
            "name"=>$name,
            "parent"=>0,
            "pyth"=>$pyth,
            "sort"=>$sort,
            "show_status"=>$show_status,
            "update_time"=>time(),
        );



        $path=new Path();
        $result=$path->saveByid($save,$id);

        //返回结果
        if($result["status"]){
            $this->success($result["msg"]);
        }else{
            $this->error($result["msg"]);
        }
    }

    /**
     * 删除一级导航
    */
    public function del_menu()
    {

        //判断是否是post，并返回post信息
        $request=$this->checkPost();

        $type = $request->post('type');//类型
        $id =  $request->post('id');//路径id

        //判断参数不为空
        if(empty($type)||empty($id)){
            $this->error("参数错误");
        }

        //根据条件更新数据
        $path=new Path();
        $result=$path->where(array("id"=>$id,"type"=>$type,"status"=>1))->data(array("status"=>-1))->update();

        //返回结果
        if($result){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }

    }

    /**
     * 修改导航显示状态
    */
    public function change_status_menu(){
        //判断是否是post，并返回post信息
        $request=$this->checkPost();

        $type = $request->post('type');//类型
        $id =  $request->post('id');//路径id
        $status =  $request->post('status');//路径id
        $status=$status==0?1:0;

        //判断参数不为空
        if(empty($type)||empty($id)){
            $this->error("参数错误");
        }

        //根据条件更新数据
        $path=new Path();
        $result=$path->where(array("id"=>$id,"type"=>$type,"status"=>1))->data(array("show_status"=>$status))->update();

        //返回结果
        if($result){
            $this->success("状态更新成功");
        }else{
            $this->error("状态更新失败");
        }
    }

    /*********************
     * 一级导航操作（结束）
     ****************/



    /*********************
     * 二级导航操作（开始）
     ****************/
    /**
     *二级目录列表
     */
    public function two_list()
    {
        $type = input('param.type');
        $parent = input('param.parent');
        $page = input('param.page');
        $page=$page?$page:1;
        $path=new Path();
        $list=$path->getpageBytype($type,$page,$parent);
        $this->assign("list",$list);
        $this->assign("type",$type);
        $this->assign("parent",$parent);
        return view("two_list");
    }

    /**
     * 二级导航编辑页面
    */
    public function edit_secound()
    {
        $type = input('param.type');//类型
        $id = input('param.id');//路径id
        $parent = input('param.parent');//路径id
        $this->assign("type",$type);
        $this->assign("id",$id);

        $path=new Path();
        $first=$path->where(array("parent"=>0,"status"=>1,"type"=>$type))->order("sort","desc")->field("id,name")->select();
        $this->assign("first",$first);

        $info=array();
        //如果路径id存在，查询信息
        if(!empty($id)){
            $path=new Path();
            $info=$path->find($id)->toArray();
            $parent=$info["parent"];
        }

        $this->assign("parent",$parent);
        $this->assign("info",$info);
        return view("edit_secound");
    }
    /**
     * 二级导航编辑提交
    */
    public function edit_secound_submit(){
        $type = input('param.type');//类型
        $id = input('param.id');//路径id

        $request=$this->checkPost();
        //需要保存的数据
        $save=array(
            "type"=>$type,
            "name"=>$request->post('name'),//导航名称
            "parent"=>$request->post('parent')?$request->post('parent'):0,//上一级
            "pyth"=>$request->post('pyth'),//路径
            "sort"=>$request->post('sort'),//排序
            "show_status"=>$request->post('show_status'),//显示状态
            "update_time"=>time(),
        );

        $path=new Path();
        $result=$path->saveByid($save,$id);

        //返回结果
        if($result["status"]){
            $this->success($result["msg"]);
        }else{
            $this->error($result["msg"]);
        }
    }

    /**
     * 删除二级导航
     */
    public function del_secound()
    {

        //判断是否是post，并返回post信息
        $request=$this->checkPost();

        $parent = $request->post('parent');//类型
        $id =  $request->post('id');//路径id

        //判断参数不为空
        if(empty($parent)||empty($id)){
            $this->error("参数错误");
        }

        //根据条件更新数据
        $path=new Path();
        $result=$path->where(array("id"=>$id,"parent"=>$parent,"status"=>1))->data(array("status"=>-1))->update();

        //返回结果
        if($result){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }

    }

}