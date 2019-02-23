<?php
namespace app\admin\controller;

use app\common\model\aliyun;
use app\common\model\MediaModel;
use app\common\model\StoreModel;
use think\Request;

/**
 *后台店铺管理
 * lin
 * 2019-02-18
 */

class Store extends Base
{
    public function _initialize(){
        parent::_initialize();
    }

    //----------------店铺信息（开始）--------------

    /**
     * 店铺列表
     * 参数 page 页码
     */
    public function lists()
    {
        $page=input("param.page");
        $page=$page?$page:1;

        //获取店铺列表
        $store=new StoreModel();
        $store_list=$store->getlist($page);
        $this->assign("store",$store_list);
        return view("lists");
    }

    /**
     * 编辑/新增 店铺
     * store_id  店铺id
     */
    public function edit_store(){
        $store_id=input("param.store_id");//店铺id

        $this->assign("id",$store_id);

        $map_url="http://apis.map.qq.com/tools/locpicker?search=1&type=1&key=".config("ADDRESS_KEY")."&referer=myapp&policy=1";
        $this->assign("map_url",$map_url);

        //默认参数
        $store_info=array("status"=>1);

        //店铺id不为空的时候查询信息
        if(!empty($store_id)){
            //获取店铺信息
            $store=new StoreModel();
            $store_info=$store->getinfo($store_id);
        }

        $this->assign("store_info",$store_info);
        return view("edit_store");
    }
    /**
     * 新增/编辑店铺提交
     */
    public function edit_store_submit()
    {

        $id=input("param.id");

        $request=$this->checkPost();
        $save=array();
        $save["store_name"]=$request->post("store_name");
        $save["contacts"]=$request->post("contacts");
        $save["contacts"]=$request->post("contacts");
        $save["contact_number"]=$request->post("contact_number");
        $save["email"]=$request->post("email");
        $save["desc"]=$request->post("desc");
        $save["address"]=$request->post("address");
        $save["longitude"]=$request->post("longitude");
        $save["latitude"]=$request->post("latitude");
        $save["city"]=$request->post("city");
        $save["door"]=$request->post("door");
        $save["status"]=$request->post("status")=="on"?1:0;

        //保存数据
        $store=new StoreModel();
        $result=$store->setstore($id,$save);

        //返回结果
        $result["status"]? $this->success($result["msg"]): $this->error($result["msg"]);
    }

    /**
     * 修改店铺状态
     * status  -1:删除 0 禁用 1 启用
    */
    public function change_store_status(){

        $result=$this->checkPost();
        $id=$result->post("store_id");
        $status=$result->post("status");
        $status=$status==0?1:($status==-1?-1:0);

        //修改状态
        $store=new StoreModel();
       $result= $store->change_status($id,$status);

        //返回结果
        $result["status"]? $this->success($result["msg"]): $this->error($result["msg"]);
    }

}
