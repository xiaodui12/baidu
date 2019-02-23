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

class ClassModel extends Model
{
    protected $table = 'mother_class';
    protected $type="";
    protected $page="";



    //验证规则
    protected $rule = [
        "parent_id"=> 'require|number',//上级分类id
        "type"=> 'require|number',//分类类型
        "class_name"=> 'require|max:255',//分类名称
        "icon"=> 'max:255',//分类图标
        "status"=> 'number|max:1',//状态：1：启用2禁用
        "sort"=> 'number|max:6',//状态：1：启用2禁用
        "create_time"=> 'number|max:10',//
        "update_time"=> 'number|max:10',//
    ];
    protected $message=[
        "class_name.require"=>"分类名称不可为空",
    ];


    public function initialize(){
        parent::initialize();
        $this->type="goods";

    }
    /**
     * 设置页码
    */
    public function setpage($page){
        $this->page=$page;
    }
    /**
     * 设置类型
    */
    public function settype($type){
        $this->type=$type;
    }

    /**
     * 获得分类列表
     * $parent 上级id 默认为0，顶级
     * $page 页码
    */
    public function getlistByparent($parent=0)
    {
        $page=$this->page;
        if($page!==""){
            //当页码不为空时，查询当前页码数据并返回
           $list= $this->where(array("type"=>$this->type,"status"=>1,"parent_id"=>$parent))->order('sort', 'desc')->paginate(10,false,array("page"=>$page));
        }else{
            //当页码为空时，查询所有数据并返回
            $list= $this
                ->where(array("type"=>$this->type,"status"=>1,"parent_id"=>$parent))
                ->order('sort', 'desc')
                ->field("icon,id,class_name")->select();
        }
        return $list;
    }

}