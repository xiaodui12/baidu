<?php
namespace app\admin\controller;

use app\common\model\aliyun;
use app\common\model\MediaModel;
use think\Request;

/**
 *后台视频管理
 * lin
 * 2019-02-18
 */

class Video extends Base
{
    public function _initialize(){
        parent::_initialize();
    }
    public function lists(){
        $page=input("param.page");
        $media=new MediaModel();

        //当有状态值传入设置状态
        $status=input("param.status");
        $status=$status?$status:10;
        if(!empty($status)){
            $media->setstatus($status);
        }

        $list=$media->get_list($page);
        $this->assign("status",$status);
        $this->assign("list",$list);
        return view("list");
    }

    /**
     * 人工审核提交
    */
    public function send_audit()
    {
        //请求信息
        $request = Request::instance();
        //判断是否是post提交
        if(!$request->isPost()){
            $this->error("请求错误");
        }
        $post=$request->post();//post参数
        $aliyun=new aliyun();
        $return_array=$aliyun->send_audit($post["list"]);
        return $return_array;
    }



    /**
     * 得到上传信息
    */
    public function getupload(){

        $result=$this->checkPost();
        $title=$result->post("title");
        $name=$result->post("name");
        $size=$result->post("size");//尺寸
        if($size>10*1024*1024){
            echo json_encode(array("status"=>0,"msg"=>"请上传10M以下的视频"));
            exit;
        }
        $title="test";
        //阿里云上传信息
        $aliyun=new aliyun();
        $result=$aliyun->send_info($name,$title);
        echo  json_encode($result);
    }

    public function refresh_upload(){
        $result=$this->checkPost();
        $title=$result->post("title");
        $name=$result->post("name");
        $videoId=$result->post("videoId");
        $size=$result->post("size");//尺寸
        if($size>10*1024*1024){
            echo json_encode(array("status"=>0,"msg"=>"请上传10M以下的视频"));
            exit;
        }
        //阿里云上传信息
        $aliyun=new aliyun();
        $result=$aliyun->refresh_send_info($title,$videoId);
        echo  json_encode($result);


    }

}
