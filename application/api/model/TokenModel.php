<?php
/**
 * 用户openid
*/

namespace app\api\model;


use app\common\model\xcx;
use think\Model;
use think\Validate;

class TokenModel extends Model
{
    protected $table = 'mother_token';


    //验证规则
    protected $rule = [
        "token"=> 'require|max:50',//token
        "appid"=> 'max:50',//appid
        "openid"=> 'require|max:50',//openid
        "uid"=> 'max:10',//用户id
        "create_time"=> 'number|max:10',
        "update_time"=> 'number|max:10',
        "expire_time"=> 'number|max:10',//过期时间

    ];

    /**
     * 创建用户token
     * $openid 用户openid
     * $uid 用户id
    */
    public function create_token($openid,$uid=0)
    {
        $xcx=new xcx();
        $appid=$xcx->getappid();//得到appid

        $token=$this->get_new_token($openid,$appid);//得到生成token

        //token记录表数据
        $save=array();
        $save["token"]=$token;
        $save["appid"]=$appid;
        $save["openid"]=$openid;
        $save["uid"]=$uid;
        $save["create_time"]=time();
        $save["update_time"]=time();
        $save["expire_time"]=time()+7200;
        $result=$this->insertGetId($save);//保存token
        if($result){
            //保存成功返回数据
            return array("token"=>$token,"expire_time"=>$save["expire_time"]);
        }else{
            return $this->create_token($openid,$uid);
        }


    }

    //生成token值
    protected function get_new_token($openid,$appid)
    {
        $token_string=$appid.$openid.date("Y-m-d H:i").rand(100,200);
        return md5($token_string);
    }


    /**
     * 根据token查询uid
    */
    public function getuserid($token){
        $uid=$this->where("token",$token)->value("uid");
        return $uid;
    }
}