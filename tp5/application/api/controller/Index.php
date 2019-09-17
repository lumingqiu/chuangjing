<?php
namespace app\api\controller;

use think\Loader;
use think\Db;
use think\Log;
class Index extends \app\common\controller
{
    public function _initialize(){
        parent::_initialize();
        
    }
    public function index(){
        
        return "请访问正确的地址！";
    }

    public function nodify()
    {
        
        $params = $this->request->param();
        $arr = [];
        Log::record($this->request->request());
        if($params && $params['app_id'] &&  $params['id']){
            $err = Db::table('cj_ad_click_log')
                ->where('id', $params['id'])
                ->update([
                    'activate_state'  => 1,
                    'activate_time'  => Db::raw('now()')
                    ]);
            if($err){
                $json  = ["err"=>1004,"message"=>"激活数据同步成功"];
            }else{
                $json  = ["err"=>1005,"message"=>"激活数据同步失败"];
            }
            if($params['callback']){
                $res = file_get_contents($params['callback']);
            }
            Log::record($res);
        }else{
            $json  = ["err"=>1002,"message"=>"请传参"];
        }
        return json_encode($json) ;       
    }

    

    public function click()
    {
        $params = $this->request->param();
        $arr = [];
        Log::record($this->request->request());
        if($params){
            $arr['user_ip'] = $params['ip'] ?? $this->request->ip() ;
            $arr['user_idfa'] = $params['idfa'];

            $arr['user_ua'] = $this->request->header('user-agent');      
            $arr['app_id'] = intval($params['app_id']);
            if(count($arr)<4){
                return ["err"=>1001,"message"=>"参数缺少"];
            }
        }else{
            return ["err"=>1002,"message"=>"请传参"];
        }
        $id = Db::table('cj_ad_click_log')->insertGetId($arr);    
        if($id) {
            $callbackurl = $params['callback'];
            $callback = $this->request->domain();
            $callback = $callback."/index/nodify?id=".$id."&app_id=".$arr['app_id']."&callback=".$callbackurl;
            $app_info = Db::table('cj_ad_app')->where('app_id',$arr['app_id'] )->select();
            $ad_call_url = $app_info[0]['ad_call_url'];
            $redirect_url =  $ad_call_url."&idfa=".$arr['user_idfa']."&ip=".$arr['user_ip']."&callback=".\urlencode($callback);
            $this->redirect($redirect_url,302);
        }else{
            return ["err"=>1003,"message"=>"数据库保存失败，请重新请求"];
        }
    }
}