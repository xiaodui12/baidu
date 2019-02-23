<?php
/**
 * 媒体文件
*/
namespace app\api\controller;


use app\common\model\MediaModel;
use think\Request;

class Media extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function upload()
    {
        $nedia=new MediaModel();
//        $nedia
    }
}