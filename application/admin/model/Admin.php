<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/14
 * Time: 11:21
 */
namespace app\admin\model;
use think\Model;
class Admin extends Model
{
    protected $table = 'mother_admin';


     //验证规则
    protected $rule = [
        "account"=> 'require|max:255',
        "password"=> 'require|max:50',
        "type"=> 'max:20',
        "status"=> 'number|max:2',
        "create_time"=> 'number|max:10',
        "update_time"=> 'number|max:10',
    ];


    public function initialize(){
        parent::initialize();
    }
    /**
     * 用户登录
     * $account：账号
     * $password：密码
    */
    public function login($account,$password){
        //根据条件查询用户信息
        $info=$this::where(array("account"=>$account,"password"=>$password,"status"=>array("gt",-1)))->field("id,password,type,status")->find();

        //用户信息为空，返回错误信息
        if(empty($info)){
            return array("status"=>false,"msg"=>"账号或密码错误");
        }
        $admin=json_decode($info->toJson(),1);
        //判断用户状态
        if($admin["status"]==0){
            return array("status"=>false,"msg"=>"用户已经被禁用");
        }
        else{
            return array("status"=>true,"msg"=>"登陆成功","id"=>$admin["id"]);
        }
    }
}