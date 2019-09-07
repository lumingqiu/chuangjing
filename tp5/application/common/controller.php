<?php
namespace app\common;

class controller extends \think\Controller
{
    public $userinfo = "";

    protected  function _initialize(){
        parent::_initialize();
        $this->getUserInfo();
    }

    public function getuserinfo(){
        $this->userinfo = session('userinfo');
    }

    
}
