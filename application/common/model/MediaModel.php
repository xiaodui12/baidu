<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/14
 * Time: 11:21
 */
namespace app\common\model;
use think\Model;
use think\Validate;

class MediaModel extends Model
{
    protected $table = 'mother_media';
    protected $rule = [
        'mediaid' => 'require|unique:MediaModel',
        'title' => 'require',
//        "UploadAuth"=>"max:255",//请求上传认证
//        "UploadAddress"=>"max:255",//请求上传地址
        "RequestId"=>"max:255",//请求上传id
        "size"=>"max:30",
        "fileurl"=>"max:255",
        "eventtime"=>"date",
        "type"=>"max:2",//文件类型1：视频，2：图片
        "status"=>"max:3",

    ];
    public $message=[
        "mediaid.unique"=>"媒体id必须是唯一",
        "title.require"=>"请填写标题"
    ];


   protected $type="1";
   protected $status=array("gt",0);


   public function settype($type){
       $this->type=$type;
   }
   public function setstatus($status){
       $this->status=$status;
   }

    public function initialize(){
        parent::initialize();
    }
    /**
     * 创建媒体表
    */
    public function create_one($save)
    {
        //自动验证数据是否正确
        $validate = new Validate($this->rule, $this->message);

        //如果存在错误，返回数据
        if( !$validate->check($save)){
            return array("status"=>0,"msg"=>$validate->getError());
        }
        $save["create_time"]=time();
        $save["update_time"]=time();
        $save["status"]=0;
        $result= $this->insertGetId($save);
        if( $result){
            return array("status"=>1,);
        }else{
            return array("status"=>0,"msg"=>"上传失败");
        }
    }
    /**
     * 更新信息状态
    */
    public function update_save($media,$field,$value=1)
    {
        $save=array($field=>$value,"update_time"=>time());
        if($field=="ai_status")
        {
            if($value==-1){
                $save["status"]=-1;
            }
            else{
                $this_media=$this->where("mediaid",$media)->field("preson_status")->find();
                if($this_media["preson_status"]==1){
                    $save["status"]=10;
                }
            }
        }
        if($field=="preson_status"){
            if($value==-1){
                $save["status"]=-1;
            }
            else{
                $this_media=$this->where("mediaid",$media)->field("ai_status")->find();
                if($this_media["ai_status"]==1){
                    $save["status"]=10;
                }
            }
        }
        return $this->where("mediaid",$media)->data($save)->update();
    }
    /**
     * 根据页码查询视频
    */
    public function get_list($page=1)
    {
        $where=array("type"=>$this->type,"status"=>$this->status);
        $list=$this->where($where)->order("create_time","desc")->paginate(10,false,array("page"=>$page));
        return $list;
    }




    public function upload_media($file_name="video")
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($file_name);

        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            //上传视频
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS .$file_name);
            //上传成功
            if($info){
                $aliyun=new aliyun();
                $url="http://".$_SERVER['SERVER_NAME']."/uploads/".$info->getSaveName();
                dump($aliyun->upload_by_url($url,$info->getFilename()));

            }else{
                // 上传失败获取错误信息
                echo $file->getError();
                $return=array("status"=>0,"msg"=> $file->getError());
            }
        };
    }

}