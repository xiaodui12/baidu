<?php
namespace app\index\controller;

use app\common\model\aliyun;
use app\common\model\MediaModel;
use think\Db;
use think\Exception;

class Index
{




    public function index()
    {
//        $aliyun=new aliyun();
//        $aliyun->send_info("1.mp4");




//        return View('index');
        return View('upload');
    }
    public function getupload(){
        $name ="1.mp4";
        $aliyun=new aliyun();
         $result=$aliyun->send_info($name,$name);
        echo  json_encode($result);
    }
    public function videoback()
    {
        $aliyun=new aliyun();
        $aliyun->videoback();
    }

    public function getplay(){
        $aliyun=new aliyun();
        $aliyun->getplay();
    }


    public function upload(){
        $media=new MediaModel();
        $media->upload_media();
    }
}
