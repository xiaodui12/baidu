<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/14
 * Time: 13:33
 */
function getmenu(){
    $info=array();
    $info[0]=array("title"=>"返回首页","url"=>url('index/index'),"select"=>true);
    $info[1]=array("title"=>"菜单","list"=>array(array("title"=>"总后台菜单","url"=>url('Menu/admin_list')),array("title"=>"店铺菜单","url"=>url('Menu/store_list'))));
    $info[2]=array("title"=>"设置","list"=>array(array("title"=>"系统设置","url"=>url('System/info'))));
    return $info;
}