<?php
namespace app\admin\controller;


class Index extends Base
{
    public function _initialize(){
        parent::_initialize();
    }
    public function index()
    {
       return view("index");
    }
}
