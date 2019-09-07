<?php
namespace app\admin\controller;


class Index extends \app\common\controller
{

    protected $beforeActionList = [
       'check',
        //'check' =>  ['except'=>'login'],
       // 'three'  =>  ['only'=>'hello,data'],
    ];


    public function _initialize(){
        
    }
    public function check(){
        // if(!$this->userinfo && $this->request->action() != 'login'){
        //     $this->redirect('login');
        // }

        // if($this->userinfo && $this->request->action() == 'login'){
        //     $this->redirect('total_day');
        // }
    }

    public function index()
    {
        return $this->fetch('index');
    }


    public function login()
    {

        if($this->request->isPost()){
            return ["error"=>0,"message"=>'ok'];
        }
        return $this->fetch('login');
    }

    public function total_day()
    {
        return $this->fetch();
    }

    public function app_list()
    {
        return $this->fetch();
    }

    public function app_upload()
    {
        return $this->fetch();
    }

    public function user()
    {
        return $this->fetch();
    }

}
