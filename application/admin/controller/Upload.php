<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2019/2/14
 * Time: 9:57
 */

namespace app\admin\controller;

use app\admin\model\Path;
use app\common\model\aliyun;
use app\common\model\SystemModel;
use think\Controller;
use think\Request;

class Upload extends  Base
{
    public function _initialize(){

    }
    /**
     * 上传图片
    */
    public function upload_image(){

        // 获取表单上传文件 例如上传了001.jpg
        $file = \request()->file('upfile');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(["size"=>1024*1024*2,"ext"=>"jpg,png,gif"])->move( './uploads');
        if($info){
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $image_url="http://".$_SERVER['SERVER_NAME']."/uploads/" .$info->getSaveName();
            $image_code="/uploads/" .$info->getSaveName();

            $aliyun=new aliyun();
            $aliyun->upload_image($image_code);

            return array("status"=>1,"image_url"=>$image_url,"image_code"=>$image_code);
        }else{
            // 上传失败获取错误信息
            return array("status"=>0,"msg"=>$file->getError());
        }
    }
}