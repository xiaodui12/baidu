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


class MpModel extends Model
{
    protected $table = 'mother_mp';

    //验证规则
    protected $rule = [
        "title"=> 'require|max:255',//名称
        "source_id"=> 'max:50',//原始id
        "pub_account"=> 'max:255',//微信号
        "appid"=> 'require|max:70',//公众号或小程序appid
        "secret"=> 'require|max:70',//公众号或小程序secret
        "mchkey"=> 'max:70',//商户号mchkey
        "mchid"=> 'max:30',//商户号
        "type"=> 'number|max:2',//类型1：公众号，2：小程序
        "store_id"=> 'number|max:10',//店铺id，为0时总店铺设置
        "access_token"=> 'max:255',//公众号access_token
        "token_over"=> 'number|max:10',//token过期时间
        "ticket"=> 'max:255',//公众号ticket
        "ticket_over"=> 'number|max:10',//ticket过期时间
        "status"=> 'number|max:2',//状态
        "create_time"=> 'number|max:10',
        "update_time"=> 'number|max:10',
    ];
    protected $message  =   [
        'title.require' => '请填写标题',
        'appid.require'     => '请填写appid',
        'secret.require'   => '请填写secret',
        'pub_account.require'   => '请填写微信号',
    ];


    public function initialize(){
        parent::initialize();
    }
    /**
     * 根据type和店铺id获得信息
     * $type  类型
     * $store_id 店铺id
     * $field 得到参数
    */
    public function getByType($type,$store_id=0,$field="*")
    {
       $mp_setting=$this->where(array("store_id"=>$store_id,"type"=>$type))->field($field)->find();
       return $mp_setting;
    }

    /**
     * 根据类型保存小程序或公众号信息
     * $type 类型
     * $save 保存信息
     * $store_id 店铺id
     *
    */
    public function saveByType($type,$save,$store_id=0){


        //数据中添加类型，店铺id，更新时间
        $save["type"]=$type;
        $save["store_id"]=$store_id;
        $save["update_time"]=time();

        //自动验证数据是否正确
        $validate = new Validate($this->rule, $this->message);

        //如果存在错误，返回数据
        if( !$validate->check($save)){
            return array("status"=>0,"msg"=>$validate->getError());
        }

        //数据库中是否有这条数据有更新，没有新增
        $info=$this->getByType($type,$store_id,"id");
       if($info)
       {
           //更新数据
           $result=$this->where("id",$info->id)->data($save)->update();
       }else{
           //新增数据
           $save["create_time"]=time();
           $save["status"]=1;
           $result=$this->insertGetId($save);
       }
       //返回处理结果
       if($result){
           return array("status"=>1,"msg"=>"更新成功");
       }else{
           return array("status"=>0,"msg"=>"更新失败");
       }
    }
}