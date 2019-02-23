<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2019/2/14
 * Time: 10:11
 * 系统设置文件
 */
namespace app\admin\controller;
use app\admin\model\Mp;
use app\common\model\MpModel;
use app\common\model\SystemModel;


class System extends Base
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }

    //----------------------系统配置（开始）-------
    /**
     *配置列表
     */
    public function info(){
        $page = input('param.page');
        $page=$page?$page:1;
        $system=new SystemModel();
        $system_info=$system->getBypage($page);
        $this->assign("system",$system_info);
        return view("info");
    }
    /**
     * 修改配置
     */
    public function edit(){
        $id = input('param.id');
        if(!empty($id)){
            $this->assign("id",$id);
            $system=new SystemModel();
            $info=$system->find($id);
            $info=$info->toArray();
        }else{
            $this->assign("id",0);
            $info=array("status"=>1);
        }
        $this->assign("info",$info);
        return view("edit");
    }

    /**
     * 更新状态
     */
    public function change(){
        //判断是否是post，并返回post信息
        $request=$this->checkPost();

        $post=$request->post();//post参数
        $id=$post["id"];
        $status=$post["status"]==0?1:0;

        //更新数据
        $system=new System_model();
        $result=$system->where("id",$id)->data(array("status"=>$status,"update_time"=>time()))->update();

        //返回修改结果
        if($result){
            $this->success("更新成功");
        }else{
            $this->error("更新失败");
        }
    }
    /**
     * 删除配置
     */
    public function del()
    {
        $id = input('param.id');
        //删除数据
        $system=new System_model();
        $result=$system->where('id',$id)->data(array("status"=>-1,"update_time"=>time()))->update();;
        //返回修改结果
        if($result){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

    /**
     * 编辑或新增
     */
    public function save_info(){
        //判断是否是post，并返回post信息
        $request=$this->checkPost();

        //组织数据
        $save=array(
            "key"=>strtoupper($request->post("key")),
            "title"=>$request->post("title"),
            "value"=>$request->post("value"),
            "desc"=>$request->post("desc"),
            "status"=>$request->post("status"),
            "update_time"=>time()
        );

        $id = input('param.id');//得到参数id

        //处理数据
        $System_m= new SystemModel();

        $result=$System_m->saveByid($save,$id);

        if($result["status"])
        {
            $this->success($result["msg"]);
        }else{
            $this->error($result["msg"]);
        }
    }

    //----------------------系统配置（结束）-------



    //----------------公众号小程序设置（开始）——----
    /**
     * 公众号设置页面
     */

    public function mp_setting(){
        $type=1;
        $mp=new MpModel();
        $info=$mp->getByType($type);
        //得到数据

        $this->assign("info",$info);
        $this->assign("type",$type);
        $this->assign("sub_title","公众号设置");
        return view("mp_setting");
    }
    /**
     * 小程序设置页面
     */
    public function xcx_setting(){
        $type=2;
        $mp=new MpModel();
        $info=$mp->getByType($type);   //得到数据

        $this->assign("info",$info);
        $this->assign("type",$type);
        $this->assign("sub_title","小程序设置");
        return view("mp_setting");
    }

    /**
     * 公众号小程序设置提交
    */
    public function save_mp(){

        //判断是否是post，并返回post信息
        $request=$this->checkPost();

        $type = input('param.type');

        //组织需要操作数据
        $save=array(
            "title"=>$request->post("title"),
            "source_id"=>$request->post("source_id"),
            "pub_account"=>$request->post("pub_account"),
            "appid"=>$request->post("appid"),
            "secret"=>$request->post("secret"),
            "mchkey"=>$request->post("mchkey"),
            "mchid"=>$request->post("mchid")
        );

        //提交处理数据
        $mp=new MpModel();
        $result=$mp->saveByType($type,$save);

        //返回处理结果
        if($result["status"]==1){
            $this->success($result["msg"]);
        }else{
            $this->error($result["msg"]);
        }

    }

    //----------------公众号小程序设置（结束）——----



    //————————网站配置————————

    /**
     * 网站配置页面
    */
    public function site_setting()
    {

    }

}