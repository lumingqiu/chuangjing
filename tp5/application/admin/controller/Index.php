<?php
namespace app\admin\controller;

use think\Loader;
use think\Db;

class Index extends \app\common\controller
{

    protected $beforeActionList = [
       'check',
        //'check' =>  ['except'=>'login'],
       // 'three'  =>  ['only'=>'hello,data'],
    ];


    public function _initialize(){
        parent::_initialize();
        
    }
    public function check(){
        
        if(!$this->userinfo && $this->request->action() != 'login'){
            $this->redirect('login');
        }

        if($this->userinfo && $this->request->action() == 'login'){
            $this->redirect('total_day');
        }
    }

    public function index()
    {
        return $this->fetch('login');
    }


    public function login()
    {

        if($this->request->isPost()){
            $username = $this->request->param('username');     
            $pwd = $this->request->post('pwd');
            if($username && $pwd ){
                $user = model('User');
                // save方法第二个参数为更新条件
                   $user_info = $user->where(['user_account'=>$username,'user_password'=> $pwd])->find();
                   if($user_info){
                        session('userinfo',\json_encode($user_info->toArray()));
                        return ["error"=>0,"message"=>'ok'];
                   }
           
                return ["error"=>1001,"message"=>'账号或者密码错误'];
            }  
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

    public function userlist()
    {
        $page = 1;
        $page_size = 20;
        $page_count = count(Db::table('cj_user')->select());
        $userlist = Db::table('cj_user')
            ->order('user_id', 'desc');
        if($this->request->param()){  
            if($this->request->param("kw")){
                $kw = $this->request->param("kw");
                $userlis = $userlist->where("user_account ='".$kw."' or user_name= '".$kw."'");
            }
            if($this->request->param("page")){
                $page = $this->request->param("page");
            }
            
        }
        $userlis = $userlist->limit(($page-1)*20,20);
        $userlist = $userlist->select();
        if($userlist){
            $userlist = $userlist;
        }else{
            $userlist = [];
        }
        $pageinfo["page_count"] = $page_count;
        $pageinfo["page_size"] = $page_size;
        $pageinfo["page_index"] = $page;
        $pageinfo["page_num"] = \ceil($page_count/$page_size);
        return $this->fetch("",['userlist'=>$userlist,"pageinfo"=>$pageinfo]);
    }

    public function user(){
        $arr = [];

        if($this->request->param()){
            $do = $this->request->param("do");
            
            $user_id = $this->request->param("user_id");
            $this->request->get(["do"=>$do,"user_id"=>$user_id]);
            $user = "";
            if($do == 'edit'){
                $user = Db::table('cj_user')
                            ->where("user_id",$user_id)->select()[0];

            }elseif($do == 'add'){
                $data = $this->request->post();

                if($data['user_password']){
                    $data['user_password'] = md5($data['user_password']);
                }else{
                    $data['user_password'] = $data['user_password1'];
                }
                unset($data['user_password1']);   
                if(isset($data['user_id'])){
                    Db::table('cj_user')->update($data);
                } else{
                    $user_id = Db::table('cj_user')->insertGetId($data);
                }          
          
                return $this->redirect('user',["do"=>"edit","user_id"=>$user_id,"par_up"=>1] );
                
            }elseif($do=='del'){
                Db::table('cj_user')->where("user_id",$user_id)->update(["user_state"=>2]);
                return  $this->fetch('',["par_up"=>1]);
            }
            $arr = ["user"=>$user];
            
        }
        return $this->fetch('',$arr);
    }


    public function loginout()
    {
        session('userinfo', null);
        return $this->fetch('login');
    }

}
