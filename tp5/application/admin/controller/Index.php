<?php
namespace app\admin\controller;

use think\Loader;
use think\Db;
use function GuzzleHttp\json_encode;

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

        $page = 1;
        $page_size = 20;
        $where = " where 1=1  ";
        if($this->request->param("page")){
            $page = $this->request->param("page");
        }
        
        if (\json_decode(session('userinfo'),true)['user_id'] != 1 )
        {
            $where .= ' and b.add_channel_id='.\json_decode(session('userinfo'),true)['user_id'];   
        }else{
            if($this->request->param('add_channel_id')){
                $where .= ' and b.add_channel_id='.$this->request->param('add_channel_id');     
            }
        }   
        if($this->request->param('app_id')){
            $where .= ' and a.app_id='.$this->request->param('app_id');
        }
        if($this->request->param('ad_name')){
            $where .= ' and b.ad_name="'.$this->request->param('ad_name').'"';
        }
        if($this->request->param('app_name')){
            $where .= ' and b.app_name="'.$this->request->param('app_name').'"';
        }
        if($this->request->param('create_time')){
            $where .= ' and TO_DAYS(a.create_time)= TO_DAYS("'.$this->request->param('create_time').'")';
        }
        if($this->request->param('activate_time')){
            $where .= ' and TO_DAYS(a.activate_time)= TO_DAYS("'.$this->request->param('activate_time').'")';
        }
        $limit = " limit ".($page-1)*$page_size.",".$page_size;
        $page_count = count(Db::query("select count(a.id) as idn,SUM(a.activate_state) as asn ,SUM(a.is_call_next) as asn_y ,CONCAT(DATE_FORMAT(a.create_time,'%Y') ,'-',DATE_FORMAT(a.create_time,'%m'),'-',DATE_FORMAT(a.create_time,'%d')) AS data_time from cj_ad_click_log as a left join cj_ad_app as b on a.app_id=b.app_id $where GROUP BY data_time DESC order by data_time DESC"));
        $loglist = Db::query("select count(a.id) as idn,SUM(a.activate_state) as asn ,SUM(a.is_call_next) as asn_y ,CONCAT(DATE_FORMAT(a.create_time,'%Y') ,'-',DATE_FORMAT(a.create_time,'%m'),'-',DATE_FORMAT(a.create_time,'%d')) AS data_time from cj_ad_click_log as a left join cj_ad_app as b on a.app_id=b.app_id $where GROUP BY data_time DESC order by data_time DESC $limit");
        // $applist = Db::table('cj_ad_click_log')
        //     ->alias('a')
        //     ->join('cj_ad_app b','a.app_id = b.app_id','LEFT')
        //     ->order('a.id', 'desc');
        // if($this->request->param()){  
        //     // if($this->request->get("name")){
        //     //     $name = $this->request->get("name");
        //     //     $applist = $applist->where('app_name','like',"%".$name."%");
        //     // }
        //     // // if($this->request->get("chaname")){
        //     // //     $chaname = $this->request->get("chaname");
        //     // //     $applist = $applist->where('ad_name','like',"%".$des."%");
        //     // // }
        //     // if($this->request->get("des")){
        //     //     $des = $this->request->get("des");
        //     //     $applist = $applist->where('ad_name','like',"%".$des."%");
        //     // }
        //     // if($this->request->get("id")){
        //     //     $id = $this->request->get("id");
        //     //     $applist = $applist->where("app_id",$id);
        //     // }
        //     if($this->request->param("page")){
        //         $page = $this->request->param("page");
        //     }
            
        // }
        // $applist = $applist->limit(($page-1)*$page_size,$page_size);
        // $applist = $applist->select();
        // if($applist){
        //     $applist = $applist;
        // }else{
        //     $applist = [];
        // }
        $pageinfo["page_count"] = $page_count;
        $pageinfo["page_size"] = $page_size;
        $pageinfo["page_index"] = $page;
        $pageinfo["page_num"] = \ceil($page_count/$page_size);
        $userlist = Db::table('cj_user')
                           ->select();
        return $this->fetch("",['loglist'=>$loglist,"pageinfo"=>$pageinfo,"userlist"=>$userlist]);
    }

    public function app_list()
    {
        $page = 1;
        $page_size = 20;
        if (\json_decode(session('userinfo'),true)['user_id'] != 1 ){
            $page_count = count(Db::table('cj_ad_click_log')->select());
        }else{
            $page_count = count(Db::table('cj_ad_click_log')->select());
        } 
        $applist = Db::table('cj_ad_click_log')
            ->field('b.ad_name,b.app_name,b.ad_platform_id,b.add_channel_id,b.app_id,b.in_price,b.out_price,a.user_idfa,a.create_time,a.activate_state,a.activate_time,a.id')
            ->alias('a')
            ->join('cj_ad_app b','a.app_id = b.app_id','LEFT')
            ->order('a.id', 'desc');
     
            if($this->request->param('create_time')){
                $applist = $applist->where("TO_DAYS(a.create_time)",'$TO_DAYS("'.$this->request->param('create_time').'")');
            }
            if($this->request->param('activate_time')){
                $applist = $applist->where("TO_DAYS(a.activate_time)",'$TO_DAYS("'.$this->request->param('activate_time').'")');
            }
            if(\json_decode(session('userinfo'),true)['user_id'] != 1){
                $add_channel_id = \json_decode(session('userinfo'),true)['user_id'];
                $applist = $applist->where("b.add_channel_id",$add_channel_id);
            }else{
                if($this->request->param("add_channel_id")){
                    $add_channel_id = $this->request->param("add_channel_id");
                    $applist = $applist->where("b.add_channel_id",$add_channel_id);      
                }
            }
            if($this->request->param("app_id")){
                $app_id = $this->request->param("app_id");
                $applist = $applist->where("b.app_id",$app_id);
            }
            if($this->request->param("activate_state")){
                $activate_state = $this->request->param("activate_state");
                $applist = $applist->where("a.activate_state",$activate_state);
            }
            if($this->request->param("user_idfa")){
                $user_idfa = $this->request->param("user_idfa");
                $applist = $applist->where("a.user_idfa",$user_idfa);
            }
            if($this->request->param("page")){
                $page = $this->request->param("page");
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

    public function app_list_del(){
        if(\json_decode(session('userinfo'),true)['user_id'] != 1){
            return "没有权限";
        }
        $id = $this->request->param("id");
        if(Db::table('cj_ad_click_log')->delete($id))
        {
            return ["成功"];
        }else{
            return ["失败"];
        }
    }

    public function app_upload()
    {
        if(\json_decode(session('userinfo'),true)['user_id'] != 1){
            return "没有权限";
        }
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
        if(\json_decode(session('userinfo'),true)['user_id'] != 1){
            return "没有权限";
        }
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
        if(\json_decode(session('userinfo'),true)['user_id'] != 1){
            return "没有权限";
        }
        $page = 1;
        $page_size = 20;
        $page_count = count(Db::table('cj_user')->select());
        $userlist = Db::table('cj_user')
            ->order('user_id', 'desc');
        if($this->request->param()){  
            if($this->request->param("kw")){
                $kw = $this->request->param("kw");
                $userlist = $userlist->where("user_account","like","%$kw%")->whereOr("user_name","like","%$kw%");
            }
            if($this->request->param("page")){
                $page = $this->request->param("page");
            }
            
        }
        $userlist = $userlist->limit(($page-1)*20,20);
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
        if(\json_decode(session('userinfo'),true)['user_id'] != 1){
            return "没有权限";
        }
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
                unset($data['do']);
                $data['user_account'] = trim($data['user_account']);
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
