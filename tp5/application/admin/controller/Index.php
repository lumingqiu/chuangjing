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
        $page = 1;
        $page_size = 20;
        $page_count = count(Db::table('cj_ad_app')->select());
        $applist = Db::table('cj_ad_app')
            ->order('app_id', 'desc');
        if($this->request->param()){  
            if($this->request->get("name")){
                $name = $this->request->get("name");
                $applist = $applist->where('app_name','like',"%".$name."%");
            }
            // if($this->request->get("chaname")){
            //     $chaname = $this->request->get("chaname");
            //     $applist = $applist->where('ad_name','like',"%".$des."%");
            // }
            if($this->request->get("des")){
                $des = $this->request->get("des");
                $applist = $applist->where('ad_name','like',"%".$des."%");
            }
            if($this->request->get("id")){
                $id = $this->request->get("id");
                $applist = $applist->where("app_id",$id);
            }
            if($this->request->param("page")){
                $page = $this->request->param("page");
            }
            
        }
        $applist = $applist->limit(($page-1)*$page_size,$page_size);
        $applist = $applist->select();
        if($applist){
            $applist = $applist;
        }else{
            $applist = [];
        }
        $pageinfo["page_count"] = $page_count;
        $pageinfo["page_size"] = $page_size;
        $pageinfo["page_index"] = $page;
        $pageinfo["page_num"] = \ceil($page_count/$page_size);
        $userlist = Db::table('cj_user')
                           ->select();
        return $this->fetch("",['applist'=>$applist,"pageinfo"=>$pageinfo,"userlist"=>$userlist]);
        
    }

    public function app_info()
    {
        $arr = [];
           
        if($this->request->param()){
            $do = $this->request->param("do");
            $channel_activate = $this->request->param("channel_activate");
            $app_id = $this->request->param("app_id");
            $this->request->get(["do"=>$do,"app_id"=>$app_id]);
            $app = "";
            $userlist = "";
            if($do == 'edit'){
                if($app_id){
                    $app = Db::table('cj_ad_app')
                            ->where("app_id",$app_id)->select()[0];
                }
                
                

            }elseif($channel_activate){
                $data = $this->request->post();
                Db::table('cj_ad_app')->update($data);
                return ["error"=>0,"message"=>"保存成功"];
            }elseif($do == 'add'){
                $data = $this->request->post();
                if(isset($data['app_id'])){
                    Db::table('cj_ad_app')->update($data);
                } else{
                    $app_id = Db::table('cj_ad_app')->insertGetId($data);
                }          
                return $this->redirect('app_info',["do"=>"edit","app_id"=>$app_id,"par_up"=>1] );
                
            }elseif($do=='del'){
                Db::table('cj_ad_app')->where("app_id",$app_id)->update(["user_state"=>2]);
                return  $this->fetch('',["par_up"=>1]);
            }
            $userlist = Db::table('cj_user')
                           ->select();
            $arr = ["appinfo"=>$app,"userlist"=>$userlist];
            
        }
        return $this->fetch('',$arr);
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
            
            $user_id = intval($this->request->param("user_id"));
            $this->request->get(["do"=>$do,"user_id"=>$user_id]);
            $user = "";
            if($do == 'edit'){
                if($user_id){
                    $user = Db::table('cj_user')
                    ->where("user_id",$user_id)->select()[0];

                }
               
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
