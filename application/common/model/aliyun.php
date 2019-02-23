<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2019/2/14
 * Time: 11:21
 *
 * 阿里云视频操作
 */

namespace app\common\model;

require_once VENDOR_PATH."/aliyun-php-sdk/aliyun-php-sdk-core/Config.php";
require_once VENDOR_PATH."/aliyun-php-sdk/aliyun-oss-php-sdk-2.3.0/autoload.php";


use http\Client\Response;
use OSS\OssClient;
use think\Db;
use think\Exception;
use vod\Request\V20170321 as vod;




class aliyun
{


    public function __construct(){

        $this->load_config();
    }
    protected $secret="";
    protected $keyid="";
    protected $GroupId="";

    protected $bucket= "testoss4";

    public function load_config(){
        $keyid="LTAIBAEnBvRq2BO9";
        $secret="CoSL9ajYInczE0hPs39fkjrYhdLo89";
        $temp="ffd90fb755821baa57539e11d7c00d0f";

        $this->setkeyid($keyid);
        $this->setsecret($secret);
        $this->setTemplateGroupId($temp);
    }

    /**
     * 设置keyid
     */
    public function setkeyid($keyid){
        $this->keyid=$keyid;
    }
    /**
     * 设置secret
     */
    public function setsecret($secret){
        $this->secret=$secret;
    }
    public function setTemplateGroupId($temp){
        $this->GroupId=$temp;
    }

    /**
     * 加载初始
     */
    function initVodClient($accessKeyId, $accessKeySecret) {
        $regionId = 'cn-shanghai';  // 点播服务接入区域
        $profile = \DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
        return new \DefaultAcsClient($profile);
    }

    /**
     * 上传前视频获得路径
     */
    function send_info($temp_name,$title){

        $request=new vod\CreateUploadVideoRequest();
        $request->setTitle($title);
        $request->setFileName($temp_name);
        $request->setTemplateGroupId($this->GroupId);//转码默认模板


        $return_array=$this->aliyun_send($request);


        if($return_array["status"]){

            $media=new MediaModel();
            $info=$return_array["info"];

            $save=array(
                "mediaid"=>$info->VideoId,
                "title"=>$title,
//                "UploadAuth"=>$info->UploadAuth,
//                "UploadAddress"=>$info->UploadAddress,
                "RequestId"=>$info->RequestId,
            );

//            dump($save);

            $return_array=$media->create_one($save);
            if($return_array["status"]){
                $return_array["info"]=$info;
            }
        }
        return $return_array;
    }

    /**
     * 刷新上传证书
     */
    function refresh_send_info($title,$videoId){


        //刷新证书
        $request=new vod\RefreshUploadVideoRequest();
        $request->setVideoId($videoId);

        $return_array=$this->aliyun_send($request);
        if($return_array["status"]){
            $media=new MediaModel();
            $info=$return_array["info"];
            $save=array(
                "mediaid"=>$videoId,
                "title"=>$title,
//                "UploadAuth"=>$info["UploadAuth"],
//                "UploadAddress"=>$info["UploadAddress"],
                "RequestId"=>$info["RequestId"],
            );
            $return_array=$media->create_one($save);
            if($return_array["status"]){
                $return_array["info"]=$info;
            }
        }
        return $return_array;

    }

    /**
     * 提交审核
     */
    protected function submit_ai($mediaid,$temp_id="")
    {

        //提交多媒体审核
        $request= new vod\SubmitAIMediaAuditJobRequest();
        $request->setMediaId($mediaid);
        //审核模板id是否存在，存在就设置模板id
        if(!empty($temp_id)){
            $request->setTemplateId($temp_id);
        }

        $info=$this->aliyun_send($request);
        return $info;
    }

    /**
     * 视频回调
     */
    public function videoback()
    {
        $EventType=input('param.EventType');//返回类型
        $mediaid=input('param.VideoId');//媒体id

        $media=new MediaModel();
        $result=false;

        switch ($EventType){
            //上传视频完成
            case "FileUploadComplete":
                /**
                 * 视频上传完成添加进入数据表
                 */
                $msg="视频上传完成添加进入数据表";
                $result=$media->update_save($mediaid,"status");
                break;
            //视频分析完成
            case "VideoAnalysisComplete":
                $msg="视频分析完成";
                $result=$media->update_save($mediaid,"analysis_status");
                break;
            //视频单个清晰度解析完成
            case "StreamTranscodeComplete":
                $result=true;
                $msg="视频单个清晰度解析完成";
                break;
            //视频全部清晰度解析完成
            case "TranscodeComplete":
                $result=$media->update_save($mediaid,"transcode_status");
                $msg="视频全部清晰度解析完成";
                /**
                 * 提交ai审核
                 */
                $this->submit_ai($mediaid);
                break;
            //视频截图完成
            case "SnapshotComplete":
                $msg="视频截图完成";
                $result=$media->update_save($mediaid,"shot_status");
                break;
            //智能审核完成
            case "AIVideoCensorComplete":

                $status=input('param.Status');
                $status=$status=="Success"?1:-1;
                $result=$media->update_save($mediaid,"ai_status",$status);
                if($status==1){
                    $msg="智能审核完成";
                }else{
                    $msg="智能审核失败，错误码：".input('param.Code')."，错误原因：".input('param.Message');
                }
                break;
            //人工审核完成
            case "CreateAuditComplete":
                $status=input('param.Status');
                $status=$status=="Success"?1:-1;
                $result=$media->update_save($mediaid,"preson_status",$status);
                if($status==1){
                    $msg="智能审核完成";
                }else{
                    $msg="智能审核失败，错误码：".input('param.Code')."，错误原因：".input('param.Message');
                }
                break;
        }
        if($result){
            /***
             * 添加日志
             */
            $save=array(
                "media_id"=>$mediaid,
                "type"=>$EventType,
                "mark"=>$msg,
                "create_time"=>time()

            );
            Db::table('mother_media_log')->insert($save);
        }
    }
    /**
     * 获得播放信息路径及封面
     */
    public function getplay($mediaid="00cdcf9ad446474bbc7bac5bf1c8e3f0")
    {

        $request=new vod\GetPlayInfoRequest();
        $request->setVideoId($mediaid);//媒体id
        $request->setFormats("mp4");//获得媒体格式
        $request->setStreamType("video");//类型
        $request->setResultType("Single");
        $return_array=$this->aliyun_send($request);
        return $return_array;
    }


    /**
     * 提交人工审核
     */
    public function send_audit($list)
    {
        $request=new vod\CreateAuditRequest();
        $request->setAuditContent(json_encode($list));
        $return_array=$this->aliyun_send($request);
        return $return_array;
    }

    /**
     * 阿里云提交
     */
    public function aliyun_send($request){
        $return_array=array();
        //初始化
        $DefaultAcsClient=$this->initVodClient($this->keyid,$this->secret);
        try{
            $info=$DefaultAcsClient->getAcsResponse($request);
            $return_array["status"]=1;
            $return_array["info"]=$info;
        }
        catch (Exception $e)
        {
            $return_array["status"]=0;
            $return_array["msg"]=$e->getMessage();
        }
        return $return_array;
    }

    /**
     * 根据url拉取上传
     */
    public function upload_by_url($url,$title){
        $request=new vod\UploadMediaByURLRequest();
        //拉去url上传数据
        $upload_media=array(
            array(
                "SourceURL"=>$url,
                "Title"=>$title,
                "Tags"=>"短视频",
                "TemplateGroupId"=>$this->GroupId
            )
        );
        $request->setUploadURLs($url);//设置上传连接
        $request->setTemplateGroupId($this->GroupId);//设置编码模板
        $request->setUploadMetadatas(json_encode($upload_media));//设置上传内容
        $info=$this->aliyun_send($request);//上传
        return $info;
    }




    public function upload_image($url){

//        //刷新证书
//        $request=new vod\CreateUploadImageRequest();
//
//        $request->setImageType("default");
//        $request->setTags("商品图片");
//        $request->setDescription("商品图片");
//        $request->setStorageLocation($_SERVER['DOCUMENT_ROOT'].$url);
//
//        dump($_SERVER['DOCUMENT_ROOT'].$url);
//        $return_array=$this->aliyun_send($request);
//        dump($return_array);
//        return $return_array;
//
//
       $endpoint ="http://oss-cn-hangzhou.aliyuncs.com";


        try{
            $ossClient = new OssClient($this->keyid, $this->secret, $endpoint);

            $result=$ossClient->uploadFile($this->bucket, "test", $_SERVER['DOCUMENT_ROOT'].$url);
            dump(1);
        } catch(OssException $e) {
            dump(2);
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }


        https://testoss4.oss-cn-hangzhou.aliyuncs.com/test?
        //Expires=1550891041&
        //OSSAccessKeyId=TMP.AQFH1aWsSz6JAnxgqwZOWwkxIpkyVjou27gU5g3bNLrOlclXMaAXo1YbRkkTMC4CFQCpomAiexId4XX6FTeKUt13y6uSCAIVAIX92CUouwYhPPPgIFsPIMcUJzc8&
        //Signature=PUxO%2BpLuRBrCp1%2B3rbnaEyDmock%3D

        return $result;
    }
}