<?php
namespace app\admin\model;


class User extends \app\common\model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'cj_user';
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
}