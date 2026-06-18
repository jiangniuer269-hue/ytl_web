<?php
namespace app\index\controller;

use app\index\common;
use think\Config;
use think\Db;
use think\Request;
use think\Session;
use app\index\model\BetsMerge;
use app\index\model\Agents;
use app\index\model\User;

class Wxauth
{
    
    /**
     *  访问当前方法，就会获取到code，并且弹出维系授权页面
     */
    public function getCode()
    {
        $request = Request::instance();
        $uid = $request->get('uid');
        $gameUrl = $request->get('gameUrl');
        $wechat_config = Config::get('wechat');
        $appid = $wechat_config['appid'];
        $back_url = 'http://authrun.qqyimfq.cn/v1/wxauth/callback?uid='.$uid.'&gameUrl='.$gameUrl;
        $redirect_uri = urlencode($back_url);
        $url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        header("Location:".$url);exit;
    }
    
    
    /**
     *  微信授权回调，回调地址会传回一个code，则我们根据code去获取openid和授权获取到的access_token
     */
    public function callback()
    {
        $request = Request::instance();
        $uid = $request->get('uid');
        $code = $request->get('code');
        $tid = $request->get('tid');
        $gameUrl = $request->get('gameUrl');
        
        $data =  Db::name('wxopen')->field('id,appid,appsecret,authdomain1')->where('type',1)->find();
        if (!empty($data)) {
            
            $appid = $data['appid'];
            $secret = $data['appsecret'];
            
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";  
            $reswx = common::http_request($url);
            $res = json_decode($reswx,TRUE);
            $access_token = $res['access_token'];
            $openid = $res['openid'];
            $unionid = $res['unionid'].'_'.$tid;
            User::where('uid',$uid)->update(['unionid2'=>$unionid]);
            //获取用户授权信息
           // $urltoc = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
           // $resinfos = common::http_request($urltoc);
          //print_r($resinfos);exit;
            header("Location:".$gameUrl);exit;
        }
    }
    
    /**
     * @function 判断是否需要获取unionid2
     */
    public function getUnionid2(){
        $isweixin = common::is_weixin();
        if ($isweixin) {        
            $uid = Session::get('uid');
            $tid= Session::get('tid');
            if (!empty($uid) &&!empty($tid) ) {
            
              $user = User::where('uid',$uid)->field('unionid2')->find();
              if (empty($user['unionid2'])) {
                  $data =  Db::name('wxopen')->field('id,appid,appsecret,authdomain1')->where('type',1)->find();
              
                  if (!empty($data)) {  
                      $appid =$data['appid'];
                      $authurl = $data['authdomain1'];
                      $r = mt_rand(100,999);
                      $gameUrl =urlencode('http://'. $_SERVER['HTTP_HOST'].'/v1/login/wx_login_quick?r='.$r.'&clearUser=0&agent_id='.$tid);     
                      $back_url =  $authurl.'/v1/wxauth/callback?uid='.$uid.'&gameUrl='.$gameUrl.'&tid='.$tid;
                      $redirect_uri = urlencode($back_url);
                      $url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
                      return ['code'=>200,'msg'=>'需要获取unionid2','data'=>['url'=>$url]];
                }
              }
            }
        }
    }
}

