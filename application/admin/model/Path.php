<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/14
 * Time: 11:21
 */
namespace app\admin\model;
use think\Model;
use think\Validate;

class Path extends Model
{
    protected $table = 'mother_path';




    //验证规则
    protected $rule = [
        "type"=> 'require|number|max:3',//路径分类（1：总后台，2：店铺）
        "name"=> 'require|max:255',//路径名称
        "parent"=> 'max:10',//上级分类id（顶级为0）
        "pyth"=> 'max:255',//路径
        "sort"=> 'number|max:10',//排序
        "show_status"=> 'number|max:2',//是否显示（0：隐藏,1:显示）
        "create_time"=> 'number|max:10',//
        "update_time"=> 'number|max:10',//
        "status"=> 'number|max:5',//状态1：正常 -1:删除

    ];
    protected  $message=[
        "type.require"=>"类型必填",
        "name.require"=>"请填写导航名称",
    ];



    public function initialize(){
        parent::initialize();
    }

    /**
     * 根据类型页码获得路径
     * $type 类型（1：总后台，2：店铺后台）
     * $page 页码：默认为1；
     * $parent  上级id 默认为0
    */
    public function getpageBytype($type,$page=1,$parent=0)
    {
        $list=$this->where(array("type"=>$type,"parent"=>$parent,"status"=>1))->order('sort ', 'desc')->paginate(10,false,array("page"=>$page));
        return $list;
    }

    /**
     * 加载路径
    */
    public function load_path($type=1)
    {
        $title="";
        //获得所有可显示路径
        $list=$this->where(array("type"=>$type,"show_status"=>1,"status"=>1))->order("parent asc,sort desc")->select();
        $object_list=array();//组织数组
        //循环所有路径
        foreach ($list as $key=>$value){
            //每一个路径共有参数
            $one_info=array("title"=>$value['name'],"pyth"=>$value->pyth,"id"=>$value->id);
            //当路径是顶级路径时
            if($value["parent"]==0){
                //设置顶级路径数组
                $object_list[$value->id]=$one_info;
            }else{
                //不是顶级路径
                //判断当前路径路径是否存在，存在生成正式连接
                if(!empty($value->pyth)){
                    $one_info["url"]=url($value->pyth);
                }
                //二级路径数组存不存在，不存在生成一个
                if(!isset($object_list[$value["parent"]]["list"]))
                {
                    $object_list[$value["parent"]]["list"]=array();
                }
                //插入二级路径
                array_push($object_list[$value["parent"]]["list"],$one_info);
            }
        }

        $return_array=array();//定义返回数组
        $has_select=false;//是否有选中连接
        $controller = request()->controller();//当前控制器
        $action = request()->action();//当前方法
        $this_pyth=$controller."/".$action;//拼接当前路径

        //循环组织数组二次处理
        foreach ($object_list as $key=>$value){
            //一级路径没有二级，且有路径参数
            if(!isset($value["list"])&&!empty($value["pyth"])){
                //生成连接
                $value["url"]=url($value["pyth"]);
                //判断当前路径是否被选中
                if(!$has_select&&trim(strtolower($this_pyth))==trim(strtolower($value["pyth"]))){
                    $has_select=true;
                    $title=$value["title"];
                    $value["select"]=true;
                }
            }
            //没有找到选中连接且有二级路径
            if(!$has_select&&isset($value["list"])){
                //循环路径，判断二级连接
                foreach ($value["list"] as $k=>$v){
                    if(trim(strtolower($this_pyth))==trim(strtolower($v["pyth"]))){
                        $has_select=true;
                        $title=$v["title"];
                        $value["select"]=true;
                    }
                    break;
                }
            }
            //加入返回数组中
            array_push($return_array,$value);
        }

        return array("list"=>$return_array,"title"=>$title);
    }

    /**
     * 根据id修改，没有id，则添加
    */
    public function saveByid($save,$id)
    {


        //自动验证数据是否正确
        $validate = new Validate($this->rule, $this->message);


        //如果存在错误，返回数据
        if( !$validate->check($save)){
            return array("status"=>0,"msg"=>$validate->getError());
        }

        if(empty($id)){
            //id不存在新增数据
            $save["create_time"]=time();
            $result= $this->insertGetId($save);
        }else{
            //id存在更新数据
            $result= $this->where("id",$id)->data($save)->update();
        }
        if($result){
            return array("status"=>1,"msg"=>"更新成功！");
        }else{
            return array("status"=>0,"msg"=>"更新失败！");
        }
    }
}