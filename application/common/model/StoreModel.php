<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2019/2/14
 * Time: 11:21
 *
 * 小程序配置操作
 */
namespace app\common\model;
use think\Model;
use think\Validate;


class StoreModel extends Model
{
    protected $table = 'mother_store';

    //自动验证规则
    protected $rule=[
        "store_name"=>"require|max:255",//店铺名称
        "contacts"=>"max:255",//联系人
        "contact_number"=>"max:255",//联系电话
        "email"=>"email|max:255",//邮箱
        "logo"=>"max:255",//店铺logo
        "province"=>"max:255",//省份
        "city"=>"max:255",//城市
        "area"=>"max:255",//区
        "province_code"=>"number|max:10",//省编码
        "city_code"=>"number|max:10",//城市编码
        "area_code"=>"number|max:10",//区域编码
        "address"=>"max:255",//地址
        "longitude"=>"max:50",//经度
        "latitude"=>"max:50",//纬度
        "door"=>"max:50",//门牌号
        "create_time"=>"number|max:10",
        "update_time"=>"number|max:10",
        "status"=>"number|max:2",
    ];

    //验证提示
    protected $message=[
        "store_name.require"=>"请输入店铺名称",
        "email.email"=>"请输入正确的邮箱",
    ];

    public function initialize(){
        parent::initialize();
    }

    /**
     * 店铺列表
     * 参数：$page  页码
    */
    public function getlist($page=1){
        $list=$this->where(array("status"=>array("egt",0)))->order('id', 'desc')->paginate(10,false,array("page"=>$page));
        return $list;
    }

    /**
     * 得到店铺名称列表
    */
    public function get_store_namelist()
    {
        $list=$this->where(array("status"=>1))->order('id', 'desc')->field("id,store_name")->select();
        return $list;
    }

    /**
     * 店铺信息
     * 参数：$store_id  店铺id
    */
    public function getinfo($store_id){
        $store=$this->where("id",$store_id)->find();
        return $store;
    }

    /**
     * 编辑或新增添店铺
    */
    public function setstore($id,$save)
    {
        $validate=new Validate($this->rule,$this->message);
        //如果存在错误，返回数据
        if( !$validate->check($save)){
            return array("status"=>0,"msg"=>$validate->getError());
        }

        $save["update_time"]=time();

        if(empty($id)){
            //新增店铺
            $save["create_time"]=time();
           $result=$this->insertGetId($save);
           $msg="新增店铺";
        }else{
            //编辑店铺
            $result=$this->where("id",$id)->data($save)->update();
            $msg="编辑店铺";
        }
        //返回信息
        return $result?array("status"=>1,"msg"=>$msg."成功"):array("status"=>0,"msg"=>$msg."失败");
    }
    /**
     * 更新用户状态 0：禁用，1：启用，-1：删除
    */
    public function change_status($id,$status)
    {
        $result=$this->where("id",$id)->data(array("status"=>$status,"update_time"=>time()))->update();
        $msg=$status==-1?"删除":"更新";
        return $result?array("status"=>1,"msg"=>$msg."成功"):array("status"=>0,"msg"=>$msg."失败");
    }




}