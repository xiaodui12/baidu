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

class MarkModel extends Model
{
    protected $table = 'mother_mark';



    public function initialize(){
        parent::initialize();

    }

    /**
     * 关联分类
     */
    public function markinfo(){
        return $this->hasMany('MarkinfoModel','mark_id',"id");
    }

    /**
     * 获得列表
    */
    public function get_list($page){
        $page=$page?$page:1;
        $list=$this->where(array("status"=>array("egt",0)))->order("create_time","desc")->paginate(10,false,array("page"=>$page));
        return $list;
    }

    /***
     * 得到报名信息
    */
    public function get_sign_info($id){
        if(empty($id)){return array("status"=>1,"info"=>array("title"=>"新增模板"));};
        $info=self::with("markinfo")//关联商品分类
        ->where(array("id"=>$id))
            ->find();
        return $info? array("status"=>1,"info"=>$info):array("status"=>0,"msg"=>"信息错误");
    }

    /**
     * 编辑模板信息
    */
    public function edit_mark($id,$title)
    {
        $save=array("title"=>$title,"update_time"=>time());
        if(empty($id)){
            $save["create_time"]=time();
            $save["status"]=1;
            $result=$this->insertGetId($save);
            $url=url('Mark/edit_sign_up',array("id"=>$result));
        }else{
            $result=$this->where("id",$id)->data($save)->update();

            $url=url('Mark/edit_sign_up',array("id"=>$id));
        }
        return $result?array("status"=>1,"msg"=>"编辑成功","url"=>$url):array("status"=>0,"msg"=>"编辑失败","url"=>$url);

    }

    public function change_status($id,$status)
    {
        $save=array(
            "status"=>$status,
            "update_time"=>time()
        );
        $result=$this->where("id",$id)->data($save)->update();
        $msg=$status==-1?"删除":"更新";
        return $result?array("status"=>1,"msg"=>$msg."成功"):array("status"=>0,"msg"=>$msg."失败");
    }

}