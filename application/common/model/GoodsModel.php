<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/14
 * Time: 11:21
 */
namespace app\common\model;
use think\Model;
use think\Validate;

class GoodsModel extends Model
{
    protected $table = 'mother_goods';

    protected $store_id=array("egt",0);
    //验证规则
    protected $rule = [
        "goods_name"=> 'require|max:255',//商品名称
        "goods_class"=> 'require|number|max:10',//商品分类
        "goods_type"=> 'require|number|max:3',//商品类型  1：视频，2：活动，3:线下课程，4：线下实物，5：旅游
        "goods_desc"=> 'max:255',//配置描述
        "store_id"=> 'number|max:10',//状态：1：启用2禁用
        "old_price"=> 'float',//
        "new_price"=> 'float',//
        "basic"=> 'number',//
        "sales"=> 'number',//
        "disseminate"=> 'max:255',//
        "cover"=> 'max:255',//
        "pic"=> 'max:1000',//
        "freight_tpl_id"=> 'number',//
        "freight_price"=> 'float',//
        "content"=> 'max:64000',//
        "status"=> 'number',//
        "stock"=> 'number',//
        "create_time"=> 'number|max:10',//
        "update_time"=> 'number|max:10',//
    ];
    protected $message=[
        "goods_name.require"=>"商品名称不得为空",
        "goods_class.require"=>"请选择商品分类",
        "goods_type.require"=>"请选择商品类型"
    ];


    public function initialize(){
        parent::initialize();
    }
    /**
     * 关联分类
    */
    public function Classm(){
        return $this->hasOne('ClassModel','id',"goods_class");
    }
    //设置店铺id
    public function setStore($store_id){
        $this->store_id=$store_id;
    }
    /**
     * 根据页码查询数据
    */
    public function getlistbypage($page=1)
    {
        $list=self::with("Classm")//关联商品分类
            ->where(array("store_id"=>$this->store_id,"status"=>array("egt",0)))
            ->order('create_time', 'desc')
            ->paginate(10,false,array("page"=>$page));
        return $list;
    }

    /**
     * 更新商品
    */
    public function edit_goods($save,$id){

        $save["update_time"]=time();
        $validate=new Validate($this->rule,$this->message);
        //如果存在错误，返回数据
        if( !$validate->check($save)){
            return array("status"=>0,"msg"=>$validate->getError());
        }

        if($id){//id存在，更新商品
            $result=$this->where("id",$id)->date($save)->update();
            $msg="更新商品";
        }else{
            $save["status"]=1;
            $save["update_time"]=time();
            //id不存在，新增数据
            $result=$this->insertGetId($save);
            $msg="新增商品";
        }
        return $result?array("status"=>1,"msg"=>$msg."成功"):array("status"=>0,"msg"=>$msg."失败");
    }






}