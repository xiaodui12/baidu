<?php
/***
 * 小程序接口
 *
 */
namespace app\common\model;

class xcx{
    protected $xcx_config=array();
    public function __construct()
    {
        $this->set_xcx_config();
    }
    /**
     * 得到小程序appid
    */
    public function getappid(){
        return $this->xcx_config["appid"];
    }
    /**
     * 设置小程序配置
     */
    public function set_xcx_config(){
        $mp=new MpModel();
        $info=$mp->getByType(2,0,"appid,secret,mchkey,mchid");
        //小程序参数
        $xcx_config=array(
            "appid"=>$info->appid,
            "secret"=>$info->secret,
            "mchkey"=>$info->mchkey,
            "mchid"=>$info->mchid,
        );
        $this->xcx_config=$xcx_config;
    }

    /**
     * 微信小程序登录
     */
    public function getopenid($code){

        //判断小程序参数
        if(empty($this->xcx_config["appid"])||empty($this->xcx_config["secret"])){
            return array("status"=>0,"msg"=>"小程序配置设置错误，请联系管理员！");
        }
        //小程序换取openid
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=".$this->xcx_config["appid"]."&secret=".$this->xcx_config["secret"]."&js_code=".$code."&grant_type=authorization_code";
        return curlGet($url);
    }

}