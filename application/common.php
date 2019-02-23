<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * curl post 提交
 * 参数  $url 提交连接
 *       $post_data  提交内容
 */
function curlPost($url,$post_data){
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 1);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);

    return json_decode($data,1);
}
/**
 * curl get 提交
 * 参数  $url 提交连接
 *
 */
function curlGet($url){
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, false);

    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //执行命令
    $data = curl_exec($curl);
    $errorno=curl_errno($curl);
    $error=curl_error($curl);

    //关闭URL请求
    curl_close($curl);

    //curl错误，重新组织返回信息
    if($errorno!=0){
        $data=array("code"=>$errorno,"errmsg"=>$error);
    }else{
        $data=array("code"=>$errorno,"data"=>json_decode($data,1));
    }
    return $data;
}



function check_mark_type($type){
    $type_array=array(
        1=>"标题",
        2=>"单行文本",
        3=>"日期选择",
        4=>"下拉",
        5=>"单选",
        6=>"多选",
    );
    return $type_array[$type];
}