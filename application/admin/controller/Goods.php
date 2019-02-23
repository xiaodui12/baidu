<?php
namespace app\admin\controller;
use app\common\model\Class_m;
use app\common\model\ClassModel;
use app\common\model\Goods_model;
use app\common\model\GoodsModel;
use app\common\model\StoreModel;

/**
 * 商品
 * lin
 * 2019-02-18
 */

class Goods extends Base
{
    public function _initialize(){
        parent::_initialize();
    }

    //--------商品分类（开始）------
    /**
     * 商品分类
     */
    public function goods_class(){
        $class= new ClassModel();
        $class->setpage(1);
        $list=$class->getlistByparent();
        $this->assign("class",$list);

        return view("class");
    }
    //--------商品分类（结束）------

    /***
     *商品列表
     */
    public function goods_list(){
        $goods=new GoodsModel();
        $list=$goods->getlistbypage(1);
        $this->assign("list",$list);
        return view("list");
    }

    /**
     * 新增或编辑商品页面
     */
    public function add_goods(){

        $aliyunuserid=config("ALIYUNUSERID");
        $this->assign("aliyunuserid",$aliyunuserid);

        //得到店铺名称列表
        $store=new StoreModel();
        $store_list=$store->get_store_namelist();
        $this->assign("store",$store_list);

        //得到分类名称
        $class=new ClassModel();
        $class_list=$class->getlistByparent();
        $this->assign("class",$class_list);

        return view("add_goods");
    }

    /***
     * 新增商品提交
     *
     */
    public function add_goods_submit(){
        $goods_id=input("param.goods_id");
        $request= $this->checkPost();
        $post=$request->post();

        //新增、编辑商品内容
        $goods=array(
            "goods_name"=>$post["goods_name"],
            "goods_class"=>$post["class"],
            "goods_type"=>$post["type"],
            "goods_desc"=>$post["goods_desc"],
            "store_id"=>$post["store"],
            "old_price"=>$post["old_price"],
            "new_price"=>$post["new_price"],
            "basic"=>$post["basic"],
            "disseminate"=>$post["videoid"],
            "cover"=>$post["image_code"][0],
            "pic"=>json_encode($post["image_code"]),
            "content"=>$post["content"]
        );

        //提交内容
        $goodsmodel=new GoodsModel();
        $result=$goodsmodel->edit_goods($goods,$goods_id);

        //返回信息
        $result["status"]?$this->success($result['msg']):$this->error($result['msg']);

    }
}
