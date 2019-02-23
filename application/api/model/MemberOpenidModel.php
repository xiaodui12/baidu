<?php
/**
 * 用户openid
*/

namespace app\api\model;


use app\common\model\xcx;
use think\Model;
use think\Validate;

class MemberOpenidModel extends Model
{
    protected $table = 'mother_member_openid';


    //验证规则
    protected $rule = [
        "openid"=> 'require|max:50',
        "unionid"=> 'max:100',
        "uid"=> 'max:10',
        "appid"=> 'max:50',
        "create_time"=> 'number|max:10',
        "update_time"=> 'number|max:10',

    ];



    /**
     * 新增用户
    */
    protected function add_member_openid($openid,$unionid)
    {
        $save=array();
        $save["openid"]=$openid;
        $save["unionid"]=$unionid;
        $save["uid"]=0;
        $xcx=new xcx();
        $save["appid"]=$xcx->getappid();
        $save["create_time"]=time();
        $save["update_time"]=time();
        return $this->insertGetId($save);
    }

    /**
     * 判断用户是否存在，存在生成token
     *
    */
    public function check_login($data){

        //得到用户信息
        $openid=$data["openid"];
        $getuser=$this->where("openid",$openid)->find();

        //用户不存在创建用户
        if(empty($getuser)){
            //创建用户
            $result=$this->add_member_openid($openid,$data["unionid"]);
            if(!$result){//用户创建失败
                return array("code"=>-1,"errmsg"=>"用户创建失败");
            }
            $getuser=array("openid"=>$openid,"uid"=>0);
        }
        //创建token
        $token=new TokenModel();
        $result=$token->create_token($getuser["openid"],$getuser["uid"]);

        return array("code"=>0,"data"=>$result);

    }
}