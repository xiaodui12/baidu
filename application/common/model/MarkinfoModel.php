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

class MarkinfoModel extends Model
{
    protected $table = 'mother_mark_info';

    protected $rule=[
        "type"=>"require|number",
        "name"=>"require|max:255",
        "default"=>"max:255",
        "placeholder"=>"max:255",
        "tips"=>"max:255",
        "is_mast"=>"number|max:2",
        "mark_id"=>"require|number|max:10",
        "create_time"=>"number|max:10",
        "update_time"=>"number|max:10",
    ];
    protected $message=[
        "type.require"=>"请选择类型",
        "name.require"=>"请输入名称",
        "mark_id.require"=>"信息错误",
    ];

    public function initialize(){
        parent::initialize();
    }

    /**
     * 根据id保存
    */
    public function save_byid($save,$id)
    {

        $save["update_time"]=time();
        $validate=new Validate($this->rule,$this->message);
        //如果存在错误，返回数据
        if( !$validate->check($save)){
            return array("status"=>0,"msg"=>$validate->getError());
        }

        if(empty($id)){
            //id不存在创建
            $save["create_time"]=time();
            $result=$this->insertGetId($save);
            $msg="新增";
        }else{
            //id存在编辑
            $result=$this->where("id",$id)->data($save)->update();
            $msg="编辑";
        }

        //返回数据
        return $result?array("status"=>1,"msg"=>$msg."成功"):array("status"=>0,"msg"=>$msg."失败");

    }


}