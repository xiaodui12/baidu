<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/14
 * Time: 11:21
 * 用户表
 */
namespace app\admin\model;
use think\Model;
use think\Validate;

class UserModel extends Model
{
    protected $table = 'mother_member';

    public function initialize(){
        parent::initialize();
    }


}