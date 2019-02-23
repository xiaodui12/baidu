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

class SystemModel extends Model
{
    protected $table = 'mother_system';


    //验证规则
    protected $rule = [
        "key"=> 'require|max:255',//配置标识
        "title"=> 'require|max:255',//配置标题
        "value"=> 'require|max:255',//配置值
        "desc"=> 'max:255',//配置描述
        "status"=> 'number|max:1',//状态：1：启用2禁用
        "create_time"=> 'number|max:10',//
        "update_time"=> 'number|max:10',//
    ];
    protected $message=[
        "key.require"=>"标识不可为空",
        "title.require"=>"标题不可为空",
        "value.require"=>"配置值不可为空",
    ];

    public function initialize(){
        parent::initialize();
    }
    /**
     * 根据页码查询
     * lin
    */
    public function getBypage($page=1)
    {
        $info=$this->where(array("status"=>array("gt",-1)))->field("id,key,title,value,desc,status,update_time")->order('id', 'desc')->paginate(10,false,array("page"=>$page));
        return $info;
    }
    /**
     * 设置配置
     * lin
    */
    public function setconfig()
    {
        $system=$this->where(array("status"=>1))->cache('config',60)->select();

        foreach ($system as $key=>$value){
            config($value->key,$value->value);
        }
    }


    /**
     * 根据id保存数据，id不存在新增
     * $save 更新数据
     *
     * $id 数据对应id
    */
    public function saveByid($save,$id){

        //自动验证数据是否正确
        $validate = new Validate($this->rule, $this->message);

        //如果存在错误，返回数据
        if( !$validate->check($save)){
            return array("status"=>0,"msg"=>$validate->getError());
        }

        //验证当前标识是否已经存在
        $where=array("key"=>$save["key"]);
        if(!empty($id)){
            $where["id"]=array("neq",$id);
        }
        $has_key=$this->where($where)->field("id")->find();
        if($has_key){
            return array("status"=>0,"msg"=>"标识已存在，不可重复");
        }
        //处理数据
        if(!empty($id)){
            $result=$this->where("id",$id)->data($save)->update();
        }else{
            $save["create_time"]=time();
            $result=$this->insertGetId($save);
        }
        //返回处理结果
        if($result){
            return array("status"=>1,"msg"=>"保存成功");
        }else{
            return array("status"=>0,"msg"=>"保存失败");
        }
    }
}