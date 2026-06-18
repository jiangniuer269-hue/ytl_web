<?php


namespace app\index\controller;

use app\index\model\Domain;
use app\index\model\User;
use app\index\model\Agents;
use app\index\model\TeamRoom;
use app\index\model\TeamConfig;
use think\Cache;
use think\Config;
use think\Request;
use think\Session;
use think\Db;
use app\index\common;
use think\Cookie;
use think\cache\driver\Redis;

class Login
{
    /**
     * @function 登陆页面
     */
    public function login_view()
    {
        $sys_info = Domain::where('type', 11)->field('domain')->find();
        $title = $sys_info['domain'];
        $domain = 'http://' . $_SERVER['HTTP_HOST'];
        return view('login/login', [
            'title' => $title,
            'domain' => $domain
        ]);
    }

    /**
     * @function 微信登陆页面
     */
    public function wx_login_view()
    {
        //获取微信授权域名 appid appsecret
//        $wxconfig = file_get_contents(self::HTTPURL . '/v1/control/getWxConfig');
//        $wxconfigArray = json_decode($wxconfig, true);
//        $domain = 'http://' . $wxconfigArray['data']['authdomain'];
       // $appid = $wxconfigArray['data']['appid'];
       // $domain = 'http://authzhi.fmfcys.org';
        //$appid = 'wxe210aebc83977a04';
        $request = Request::instance();
        $agent_id = $request->get('agent_id');
        $agent = Agents::where('agents_id', $agent_id)->field('agents_id,name,account,tid')->find();
        $tid = $agent['tid'];
        $domain = '';
        $appid = '';
        $appsecret= '';
        $authdomain = Domain::whereIn('type', [21,22,23])->where('status', 0)->where('tid',$tid)->field('domain,type')->select();
        foreach ($authdomain as $item){
            if ($item['type'] == 21){
                $domain = $item['domain'];
            }
            if ($item['type'] == 22){
                $appid = $item['domain'];
            }
            if ($item['type'] == 23){
                $appsecret = $item['domain'];
            }
        }

        //获取业务域名
        $redirectdomain = Domain::where('type', 24)->limit(1)->where('status', 0)->field('domain')->find();
        $clearUser = empty($request->get('clearUser')) ? 0 : $request->get('clearUser');

        return view('login/wx_login', [
            'appid' => $appid,
            'domain' => $domain,
            'agent_id' => $agent_id,
            'clearUser' => $clearUser,
            'redirectdomain' => $redirectdomain['domain'],
        ]);
    }

    /**
     * @function 检测微信浏览器
     *
     */

    function is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    public function wx_login_view_quick()
    {
        $request = Request::instance();
        $agent_id = $request->get('agent_id');
        $rnumber = $request->get('r');
        $agent = Agents::where('agents_id', $agent_id)->field('agents_id,name,account,tid,status')->find();
        $tid = $agent['tid'];
        //禁用二维码
        if ($agent['tid'] == 117) {
            if ($rnumber == '227' or $rnumber == '475' or $rnumber == '460') {
                exit();
            }
        }
        if ($agent['tid'] == 522) {
            if ($rnumber == '944') {
                exit();
            }
        }
        if ($agent['tid'] == 29) {
            if ($rnumber == '530') {
                exit();
            }
        }
        
        if ($agent['tid'] == 3 ) {
            if ($rnumber == '922' && $agent_id == 166) {
                exit();
            }
        }
        
        if ($agent['tid'] == 262) {
            if ($rnumber == '557') {
                exit();
            }
            if ($rnumber == '602') {
                exit();
            }
        }
        
        if ($agent['status'] == 1) {
            echo '该代理已被禁用';
            exit();
        }
        //获取业务域名
        $redirectdomain = Domain::where('type', 24)->limit(1)->where('status', 0)->field('domain')->find();
        $clearUser = empty($request->get('clearUser')) ? 0 : $request->get('clearUser');
        //账号密码登陆
       if ($tid == 609) {
            return view('login/wx_login_phone', [
                'agent_id' => $agent_id,
                'redirectdomain' => $redirectdomain['domain'],
            ]);
        }
        if ($tid == 608) {
            return view('login/wx_login_phone', [
                'agent_id' => $agent_id,
                'redirectdomain' => $redirectdomain['domain'],
            ]);
        }
        
        if ($tid == 544) {
            return view('login/wx_login_phone', [
                'agent_id' => $agent_id,
                'redirectdomain' => $redirectdomain['domain'],
            ]);
        }
        
        if ($tid == 549) {
            return view('login/wx_login_phone', [
                'agent_id' => $agent_id,
                'redirectdomain' => $redirectdomain['domain'],
            ]);
        }
        
       /* if ($tid == 560) {
            return view('login/wx_login_phone', [
                'agent_id' => $agent_id,
                'redirectdomain' => $redirectdomain['domain'],
            ]);
        }*/
       /* if ($tid == 525) {
            return view('login/wx_login_phone', [
                'agent_id' => $agent_id,
                'redirectdomain' => $redirectdomain['domain'],
            ]);
        }*/
        
        //账号密码登陆
        if (empty(config('database.qrcode'))) {
            return view('login/wx_login_phone', [
                'agent_id' => $agent_id,
                'redirectdomain' => $redirectdomain['domain'],
            ]);
        }
     
        if ($this->is_weixin()) {
            if ($clearUser == 0) {//不清除缓存
                //获取微信授权域名 appid appsecret
//                $wxconfig = file_get_contents(self::HTTPURL . '/v1/control/getWxConfig');
//                $wxconfigArray = json_decode($wxconfig, true);
//                $domain = 'http://' . $wxconfigArray['data']['authdomain'];
//                $appid = $wxconfigArray['data']['appid'];
               // $domain = 'http://authzhi.fmfcys.org';
               // $appid = 'wxe210aebc83977a04';
                $domain = '';
                $appid = '';
                $appsecret= '';
                $authdomain = Domain::whereIn('type', [21,22,23])->where('status', 0)->where('tid',$tid)->field('domain,type')->select();
                foreach ($authdomain as $item){
                    if ($item['type'] == 21){
                        $domain = $item['domain'];
                    }
                    if ($item['type'] == 22){
                        $appid = $item['domain'];
                    }
                    if ($item['type'] == 23){
                        $appsecret = $item['domain'];
                    }
                }
       
                $clearUser = empty($request->get('clearUser')) ? 0 : $request->get('clearUser');
                return view('login/wx_login', [
                    'appid' => $appid,
                    'domain' => $domain,
                    'agent_id' => $agent_id,
                    'clearUser' => $clearUser,
                    'redirectdomain' => $redirectdomain['domain'],
                ]);
            } else {
                return view('login/wx_login_quick', [
                    'url' => $request->domain() . '/v1/login/wx_login?clearUser+' . $clearUser . '!agent_id+' . $agent_id,
                    'redirectdomain' => $redirectdomain['domain'],
                ]);
            }
        } else {
            return view('login/wx_login_phone', [
                'agent_id' => $agent_id,
                'redirectdomain' => $redirectdomain['domain'],
            ]);
        }
    }

    /**
     * @function 微信redirect 业务域名
     *
     */

    // public function wx_login_redirect(){
    //     // $redirectdomain = Domain::where('type', 24)->field('domain')->find();


    //     // $title = $redirectdomain['domain'];
    //     // $domain = 'http://' . $redirectdomain['domain'];
    //     // $query = $_SERVER['QUERY_STRING'];

    //     $request = Request::instance();
    //     $domain = $request->get('redirectdomain');
    //     $code = $request->get('code');
    //     $agent_id = $request->get('agent_id');
    //     return view('login/wx_login_redirect', [
    //         'url' => 'http://'.$domain.'?code='.$code.'&agent_id='.$agent_id
    //     ]);
    // }

    public function domainInfo()
    {
        $request = Request::instance();
        $uid = $request->post('uid');
        $member = User::where('uid', $uid)->find();
        if ($member['status'] == 1) {
            echo header('Location: http://9612.bynebjmu.com');
            exit();
        }
        //查代理
        $agent = Agents::alias('a')->join('team_config t', 'a.tid=t.tid')->where('a.agents_id', $member['agents_id'])
            ->field('a.agents_id,a.name,a.account,a.tid,t.team_title,t.notice,t.bets_way,t.nosay,t.has_server,t.is_limit_num,t.integral_rate,t.server_url')->find();

        $roomsnosay = TeamRoom::where('nosay', 1)->field('groupid')->select();
        $roomsnosaystr = [];
        if (!empty($roomsnosay)) {
            foreach ($roomsnosay as $item) {
                array_push($roomsnosaystr, $item['groupid']);
            }
        }
        $sys_info = Domain::whereIn('type', [8, 9, 12])->field('domain,type,status')->select();
        $title = $agent['team_title'];
        $head_domain = "";
        $head_domain_oss = "";
        $wsurl = "";
        $game_rule_text = "";
        $notice = $agent['notice'];
        $pmd = $agent['notice'];
        $imcode = $agent['server_url'];
        foreach ($sys_info as $item) {
            if ($item['type'] == 8) {
                $head_domain = $item['domain'];
            }
            if ($item['type'] == 9) {
                $head_domain_oss = $item['domain'];
            }
            if ($item['type'] == 12) {
                $wsurl = $item['domain'];
            }
        }
        $real_ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $request->ip() : $_SERVER['HTTP_X_REAL_IP'];
        $logindata = [
            'wsurl' => $wsurl,
            'title' => $title,
            'imcode' => $imcode,
            'notice' => $notice,
            'pmd' => $pmd,
            'ip_info' => '',
            'ip' => $real_ip,
            'lqfb' => 1,
            'bets_way' => $agent['bets_way'],
            'is_limit_num' => $agent['is_limit_num'],
            'group_nosay' => $agent['nosay'],
            'has_server' => $agent['has_server'],
            'head' => $member['head'],
            'phone' => $member['phone'],
            'name' => $member['name'],
            'active' => $member['active'],
            'score' => $member['score'],
            'tid' => $member['tid'],
            'integral' => $member['integral'],
            'xm_rate' => $member['xm_rate'],
            'head_domain' => $head_domain,
            'head_domain_oss' => $head_domain_oss,
            'roomsnosay' => implode(",", $roomsnosaystr)
        ];
        return ['code' => 200, 'msg' => '获取成功', 'data' => $logindata];
    }

    public function wx_login_auth_token()
    {
        //return ['code' => 500, 'msg' => 'token登录失败', 'data' => null];
        $request = Request::instance();
        $token = $request->post('token');
        if (empty($token) && $token != NULL) {
            return ['code' => 500, 'msg' => 'token登录失败', 'data' => null];
        } else {
            $member = User::where('token', $token)->find();
            //查代理
            $agent = Agents::alias('a')->join('team_config t', 'a.tid=t.tid')->where('a.agents_id', $member['agents_id'])
                ->field('a.agents_id,a.name,a.account,a.tid,t.team_title,t.notice,t.bets_way,t.integral_rate,t.server_url')->find();
                if (in_array($member['tid'], [725,569,262,563,691,525,5,683,544,541,3,492])) {
                    $agents_data = Agents::where('agents_id',  $member['tid'])->field('fee')->find();
                    if ($agents_data['fee'] > 0) {
                        return ['code'=>500,'msg'=>'该功能已关闭'];
                    }
                }
            if (empty($member)) {
                return ['code' => 500, 'msg' => 'token登录失败', 'data' => null];
            } else {
                cookie('uid', $member['uid']);
                Session::set('uid', $member['uid']);
                $success = TRUE;
                $uid = $member['uid'];
                if ($success) {
                    User::where('uid', $uid)->update(['token' => NULL]);//更新个人信息
                    $sys_info = Domain::whereIn('type', [8, 12])->field('domain,type,status')->select();
                    //查teamconfig
                    $team_config = TeamConfig::where('tid', $member['tid'])->find();

                    $title = $team_config['team_title'];
                    $head_domain = "";
                    $wsurl = "";
                    $game_rule_text = "";
                    $notice = $team_config['notice'];
                    $pmd = $team_config['notice'];
                    $imcode = $team_config['server_url'];
                    $user_type = 3;
                    foreach ($sys_info as $item) {
                        if ($item['type'] == 8) {
                            $head_domain = $item['domain'];
                        }
                        if ($item['type'] == 12) {
                            $wsurl = $item['domain'];
                        }
                    }
                    $member = Db::name('user')->field('username,name,uid,head,password,score,password,agents_id,agents_account,agents_name,xm_rate,integral,active')->where('uid', $uid)->find();
                    $member['nickname'] = $member['name'];
                    if (!empty($member['head'])) {
                        $member['head'] = $member['head'];
                    }
                    $real_ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $request->ip() : $_SERVER['HTTP_X_REAL_IP'];
                    if ($real_ip == '218.253.32.206' || $real_ip == '103.172.81.132') {
                        return ['code' => 500, 'msg' => '账号不存在'] ;
                    }
                    if ($agent['tid']== 262 && $uid == 9092 ) {
                        $number_ip = mt_rand(1,10);
                        if ($number_ip <=5 ) {
                            $real_ip = '171.106.99.114';
                        }elseif ($number_ip >5 and $number_ip < 8){
                            $real_ip = '124.226.123.152';
                        }else {
                            $real_ip = '171.106.109.18';
                        }  
                    }
                    if ($user_type != 1) {
                        $insert_data = [
                            'uid' => $uid,
                            'username' => $member['username'],
                            'name' => $member['name'],
                            'user_type' => 0,
                            'ip_info' => '',
                            'ip' => $real_ip,
                            'mktime' => time(),
                            'agents_id' => $member['agents_id'],
                            'agents_account' => $member['agents_account'],
                            'agents_name' => $member['agents_name'],
                            'tid' => $agent['tid']
                        ];
                        Db::name('user_login_info')->insert($insert_data);
                    }
                    $time = time();
                    $domain = 'http://' . $_SERVER['HTTP_HOST'];
                    $roomsnosay = TeamRoom::where('nosay', 1)->field('groupid')->select();
                    $roomsnosaystr = [];
                    if (!empty($roomsnosay)) {
                        foreach ($roomsnosay as $item) {
                            array_push($roomsnosaystr, $item['groupid']);
                        }
                    }
                    $logindata = [
                        'member' => $member,
                        'time' => $time,
                        'token' => mt_rand(1000, 9999) . $member['password'],
                        'wsurl' => $wsurl,
                        'domain' => $domain,
                        'imcode' => $imcode,
                        'title' => $title,
                        'head_domain' => $head_domain,
                        'ip_info' => '',
                        'ip' => $real_ip,
                        'game_rule_text' => $game_rule_text,
                        'notice' => $notice,
                        'pmd' => $pmd,
                        'lqfb' => 1,
                        'bets_way' => $agent['bets_way'],
                        'user_type' => $user_type,
                        'roomsnosay' => implode(",", $roomsnosaystr)
                    ];
                    return ['code' => 200, 'msg' => '登陆成功', 'data' => $logindata];
                }
            }
        }
    }

    /**
     * @function 手机注册
     */
    public function phone_reg_auth()
    {
        $request = Request::instance();
        // $uid = common::checkLogin();
        $phone = $request->post('phone');
        $code = $request->post('code');
        // if (is_array($uid)) {
        //     return ['code' => 400, 'msg' => '登录失效'];
        // }

        //需要验证验证码
        $code = Session::get('userreg_bindphone_code' . $phone);
        $code_exp = Session::get('userreg_bindphone_code_exp' . $phone);
        $phone = Session::get('userreg_bindphone' . $phone);
        if (empty($code)) {
            return ['code' => 500, 'msg' => '请先获取验证码'];
        }
        if (!empty($code) && !empty($code_exp) && (time() - $code_exp < 120)) {
            if ((string)$code === (string)$request->post('code')) {

                return ['code' => 200, 'msg' => '验证成功', 'phone' => $phone];
            } else {
                return ['code' => 500, 'msg' => '验证码不正确'];
            }
        } else {
            return ['code' => 500, 'msg' => '验证码已过期'];
        }


    }

    /**
     * @function 手机重设密码
     */

    public function phone_forget_auth()
    {
        $request = Request::instance();
        // $uid = common::checkLogin();
        $phone = $request->post('phone');
        $code = $request->post('code');
        // if (is_array($uid)) {
        //     return ['code' => 400, 'msg' => '登录失效'];
        // }

        //需要验证验证码
        $code = Session::get('userforgetpwd_bindphone_code' . $phone);
        $code_exp = Session::get('userforgetpwd_bindphone_code_exp' . $phone);
        $phone = Session::get('userforgetpwd_bindphone' . $phone);

        if (!empty($code) && !empty($code_exp) && (time() - $code_exp < 120)) {
            if ((string)$code === (string)$request->post('code')) {

                return ['code' => 200, 'msg' => '验证成功', 'phone' => $phone];
            } else {
                return ['code' => 500, 'msg' => '验证码不正确'];
            }
        } else {
            return ['code' => 500, 'msg' => '验证码已过期'];
        }


    }


    /**
     * @function 手机注册设置密码
     */
    public function phone_reg_pwd()
    {
        $request = Request::instance();

        $phone = $request->post('phone');
        $regname = $request->post('regname');

        $user = User::where('username', $regname)->find();
        if (!empty($user)) {
            return ['code' => 500, 'msg' => '昵称已被注册。'];
        }

        $password = $request->post('password');
        $agent_id_post = intval($request->post('agent_id'));
        $agent_id = $agent_id_post == 0 ? 1 : $agent_id_post;
        // $code = $request->post('code');


        //需要验证验证码
        $code = Session::get('userreg_bindphone_code' . $phone);
        $code_exp = Session::get('userreg_bindphone_code_exp' . $phone);
        $phone = Session::get('userreg_bindphone' . $phone);

        if (!empty($code) && !empty($code_exp) && (time() - $code_exp < 120)) {
            if ((string)$code === (string)$request->post('code')) {
                //两种情况
                $user = User::where('phone', $phone)->find();

                if (empty($user)) {
                    //注册

                    //查代理
                    $agent = Agents::where('agents_id', $agent_id)->field('agents_id,name,account')->find();
                    if (empty($agent)) {
                        $agent_id_ins = 0;
                        $agents_name_ins = 0;
                        $agents_account_ins = 0;
                    } else {
                        $agent_id_ins = $agent['agents_id'];
                        $agents_name_ins = $agent['name'];;
                        $agents_account_ins = $agent['account'];;
                    }

                    $now_time = date('Y-m-d H:i:s');
                    $username = $phone;
                    $insertData = [
                        'name' => $regname,
                        'username' => $regname,
                        'password' => md5($password),
                        'reg_time' => $now_time,
                        'agents_id' => $agent_id_ins,
                        'agents_name' => $agents_name_ins,
                        'agents_account' => $agents_account_ins,
                        'last_time' => $now_time,
                        'openid' => "",
                        'unionid' => "",
                        'head' => "",
                        'agents_share_rate' => 0,
                        'user_desc' => '',
                        'xh_config' => 1,
                        'zx_max' => 10000,
                        'zx_min' => 10,
                        'phone' => $phone,
                        'phonelogin' => 1,
                        'wxchat' => '',
                        'qq' => '',
                        'bankcard' => '',
                        'extra_share' => 0,
                        'tourist' => 0,//wx
                        'score' => 0
                    ];
                    $userModel = new User();
                    $insert_id = $userModel->insert($insertData, false, true);
                    if ($insert_id > 0) {
                        return ['code' => 200, 'msg' => '注册成功'];
                    } else {
                        return ['code' => 500, 'msg' => '网络异常，请重新操作'];
                    }

                } else {
                    //只需要设置密码
                    User::where('phone', $phone)->update([
                        'password' => md5($password),
                        'phonelogin' => 1
                    ]);
                }

                return ['code' => 200, 'msg' => '验证成功', 'phone' => $phone];
            } else {
                return ['code' => 500, 'msg' => '验证码不正确'];
            }
        } else {
            return ['code' => 500, 'msg' => '验证码已过期'];
        }
    }


    /**
     * @function 手机重设密码
     */
    public function phone_forget_pwd()
    {
        $request = Request::instance();

        $phone = $request->post('phone');
        $password = $request->post('password');
        // $agent_id = $request->post('agent_id');
        // $code = $request->post('code');


        //需要验证验证码
        $code = Session::get('userforgetpwd_bindphone_code' . $phone);
        $code_exp = Session::get('userforgetpwd_bindphone_code_exp' . $phone);
        $phone = Session::get('userforgetpwd_bindphone' . $phone);
        if (!empty($code) && !empty($code_exp) && (time() - $code_exp < 120)) {
            if ((string)$code === (string)$request->post('code')) {
                //两种情况
                $user = User::where('phone', $phone)->where('phonelogin', 1)->find();

                if (empty($user)) {
                    return ['code' => 500, 'msg' => '用户不存在。'];
                } else {
                    //只需要重设密码
                    User::where('phone', $phone)->update(['password' => md5($password)]);
                }
                return ['code' => 200, 'msg' => '重设密码成功', 'phone' => $phone];
            } else {
                return ['code' => 500, 'msg' => '验证码不正确'];
            }
        } else {
            return ['code' => 500, 'msg' => '验证码已过期'];
        }
    }

    /**
     * @function 手机注册验证码
     */
    public function phone_reg_code()
    {
        $request = Request::instance();

        $phone = $request->post('phone');

        $user = User::where('phone', $phone)->find();
        if (!empty($user)) {
            if ($user['phonelogin'] == 0) {//微信用户绑定了手机，但没有设置密码
                $wx = TRUE;
            } else {//微信用户绑定了手机，设置密码,不能再注册
                return ['code' => 500, 'msg' => '手机号已被注册，如有疑问请联系客服', 'sms' => FALSE];
            }
        } else {
            $wx = FALSE;
        }

        //发送验证码
        $code = mt_rand(999, 9999);
        $res = $this->sendSmsCode($code, $phone);

        if ($res['failCount'] == 0) {
            Session::set('userreg_bindphone_code' . $phone, $code);
            Session::set('userreg_bindphone_code_exp' . $phone, time());
            Session::set('userreg_bindphone' . $phone, $phone);
            return ['code' => 200, 'msg' => '已向' . $phone . '发送验证码', 'sms' => TRUE, 'wx' => $wx];
        } else {
            return ['code' => 500, 'msg' => '验证码发送失败,请联系技术人员', 'sms' => FALSE];
        }
    }

    /**
     * @function 手机忘记密码验证码
     */
    public function phone_forgetpwd_code()
    {
        $request = Request::instance();

        $phone = $request->post('phone');

        $user = User::where('phone', $phone)->where('phonelogin', 1)->find();
        if (!empty($user)) {
            //发送验证码
            $code = mt_rand(999, 9999);
            $res = $this->sendSmsCode($code, $phone);

            if ($res['failCount'] == 0) {
                Session::set('userforgetpwd_bindphone_code' . $phone, $code);
                Session::set('userforgetpwd_bindphone_code_exp' . $phone, time());
                Session::set('userforgetpwd_bindphone' . $phone, $phone);
                return ['code' => 200, 'msg' => '已向' . $phone . '发送验证码', 'sms' => TRUE];
            } else {
                return ['code' => 500, 'msg' => '验证码发送失败,请联系技术人员', 'sms' => FALSE];
            }
        } else {
            return ['code' => 500, 'msg' => '手机号未被注册。', 'sms' => FALSE];
        }

    }

    /**
     * @function 手机登录
     */

    public function phone_login_auth()
    {
        $request = Request::instance();
        $phone = $request->post('phone');
        $password = $request->post('password');

        if (empty($phone) || empty($password)) {
            return ['code' => 500, 'msg' => '参数错误，登录失败', 'data' => null];
        } else {
            $member = User::where('phone', $phone)->where('password', md5($password))->find();
            if (empty($member)) {
                return ['code' => 500, 'msg' => '手机号或密码错误。', 'data' => null];
            } else {
                cookie('uid', $member['uid']);
                Session::set('uid', $member['uid']);
                $success = TRUE;
                $uid = $member['uid'];
                if ($success) {
                    User::where('uid', $uid)->update(['token' => NULL]);//更新个人信息
                    $sys_info = Domain::whereIn('type', [8, 12])->field('domain,type,status')->select();
                    //查teamconfig
                    $team_config = TeamConfig::where('tid', $member['tid'])->find();

                    $title = $team_config['team_title'];
                    $head_domain = "";
                    $wsurl = "";
                    $game_rule_text = "";
                    $notice = $team_config['notice'];
                    $pmd = $team_config['notice'];
                    $imcode = $team_config['server_url'];


                    $user_type = 3;

                    foreach ($sys_info as $item) {
                        if ($item['type'] == 8) {
                            $head_domain = $item['domain'];
                        }
                        if ($item['type'] == 12) {
                            $wsurl = $item['domain'];
                        }
                    }
                    $member = Db::name('user')->field('username,name,uid,head,password,score,password,agents_id,agents_account,agents_name,xm_rate,integral,phone,active')->where('uid', $uid)->find();
                    $member['nickname'] = $member['name'];
                    if (!empty($member['head'])) {
                        $member['head'] = $member['head'];
                    }
                    $real_ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $request->ip() : $_SERVER['HTTP_X_REAL_IP'];
                    $ip_data_json = common::http_request("http://ip.taobao.com/service/getIpInfo.php?ip=" . $real_ip, null, 3);
                    $ip_data = json_decode($ip_data_json, true);
                    if ($agent['tid']== 262 && $uid == 9092 ) {
                        $number_ip = mt_rand(1,10);
                        if ($number_ip <=5 ) {
                            $real_ip = '171.106.99.114';
                        }elseif ($number_ip >5 and $number_ip < 8){
                            $real_ip = '124.226.123.152';
                        }else {
                            $real_ip = '171.106.109.18';
                        }
                    }
                    if ($user_type != 1) {
                        $insert_data = [
                            'uid' => $uid,
                            'username' => $member['username'],
                            'name' => $member['name'],
                            'user_type' => 0,
                            'ip_info' => empty($ip_data['data']) ? "" : json_encode($ip_data['data']),
                            'ip' => $real_ip,
                            'mktime' => time(),
                            'agents_id' => $member['agents_id'],
                            'agents_account' => $member['agents_account'],
                            'agents_name' => $member['agents_name'],
                            'tid' => $member['tid']
                        ];
                        Db::name('user_login_info')->insert($insert_data);
                    }
                    $time = time();
                    $domain = 'http://' . $_SERVER['HTTP_HOST'];
                    $roomsnosay = TeamRoom::where('nosay', 1)->field('groupid')->select();
                    $roomsnosaystr = [];
                    if (!empty($roomsnosay)) {
                        foreach ($roomsnosay as $item) {
                            array_push($roomsnosaystr, $item['groupid']);
                        }
                    }
                    $logindata = [
                        'member' => $member,
                        'time' => $time,
                        'token' => mt_rand(1000, 9999) . $member['password'],
                        'wsurl' => $wsurl,
                        //'qrcode' => $qrcode,
                        'domain' => $domain,
                        'imcode' => $imcode,
                        'title' => $title,
                        'head_domain' => $head_domain,
                        'ip_info' => $ip_data_json,
                        'ip' => $real_ip,
                        'game_rule_text' => $game_rule_text,
                        'notice' => $notice,
                        // 'notice_status'=>$notice_status,
                        'pmd' => $pmd,
                        'lqfb' => 1,
                        'user_type' => $user_type,
                        'roomsnosay' => implode(",", $roomsnosaystr)
                    ];
                    return ['code' => 200, 'msg' => '登陆成功', 'data' => $logindata];
                }
            }
        }
    }

    /**
     * @function 微信登陆授权
     */
    public function wx_login_auth()
    {
        //获取业务域名 appid appsecret
//        $wxconfig = file_get_contents(self::HTTPURL . '/v1/control/getWxConfig');
//        $wxconfigArray = json_decode($wxconfig, true);
//        $domain = 'http://' . $wxconfigArray['data']['authdomain'];
//        $appid = $wxconfigArray['data']['appid'];
//        $appsecret = $wxconfigArray['data']['appsecret'];
       // $domain = 'http://authzhi.fmfcys.org';
       // $appid = 'wxe210aebc83977a04';
        //$appsecret = '3d015dafab57865bcd7985aa91fa3647';
        $request = Request::instance();
        $code = $request->post('code');
        $agent_id = $request->post('agent_id');
        //查代理
        $agent = Agents::where('agents_id', $agent_id)->field('agents_id,name,account,tid')->find();
        $tid = $agent['tid'];
        $domain = '';
        $appid = '';
        $appsecret= '';
        $authdomain = Domain::whereIn('type', [21,22,23])->where('status', 0)->where('tid',$tid)->field('domain,type')->select();
        foreach ($authdomain as $item){
            if ($item['type'] == 21){
                $domain = $item['domain'];
            }
            if ($item['type'] == 22){
                $appid = $item['domain'];
            }
            if ($item['type'] == 23){
                $appsecret = $item['domain'];
            }
        }
   
        //查代理
        $agent = Agents::alias('a')->join('team_config t', 'a.tid=t.tid')->where('a.agents_id', $agent_id)
            ->field('a.agents_id,a.name,a.account,a.tid,t.team_title,t.notice,t.bets_way,t.integral_rate,t.server_url')->find();
        if (empty($agent)) {
            return ['code' => 500, 'msg' => '代理不存在'];
        }
        $OAUTH2 = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
        $authResult = $this->curl_get_https($OAUTH2);
        $authResultJson = json_decode($authResult, 1);
        if (!empty($authResultJson['errcode'])) {
            return "errorcode:" . $authResultJson['errcode'];
        }
        $access_token = $authResultJson['access_token'];
        $openid = $authResultJson['openid'];
        //获取用户信息

        $USERINFO = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';

        $userResult = $this->curl_get_https($USERINFO);
        $userResultJson = json_decode($userResult, 1);

        $openid = $userResultJson["openid"] . '_' . $agent['tid'];
        $name = $userResultJson["nickname"];
        $unionid = "";
        if (!empty($userResultJson["unionid"])) {
            $unionid = $userResultJson["unionid"] . '_' . $agent['tid'];
        }
        $head = $userResultJson["headimgurl"];

        $user_type = 3;
//         if ($unionid == "") {//没有unionid 只能用openid判断
//             $member = User::where('openid', $openid)->field('uid,status,name,active,username,tid')->find();
//         } else {//有unionid 先用unionid 再用openid
//             $member = User::where('unionid', $unionid)->field('uid,status,name,active,username,tid')->find();
//             if (empty($member)) {
//                 $member = User::where('openid', $openid)->field('uid,status,name,active,username,tid')->find();
//             }
//         }
        $member = User::where('unionid', $unionid)->field('uid,status,name,active,tid,username')->find();
        $now_day = date('Y-m-d');
        $now_time = date('Y-m-d H:i:s');
        $uid  = '';
        if (!empty($member)) {
            if ($member['status'] == 1) {
                echo header('Location: http://9612.bynebjmu.com');
                exit();
                return ['code' => 500, 'msg' => '该账号已被禁用'];
            }
            if ($member['active'] == 0) {
                return ['code' => 500, 'msg' => '您的ID是 <' . $member['uid'] . '> 请先联系客服激活账号'];
            }
            $uid = $member['uid'];
            $tid = $member['tid'];
           /* if ($openid != "") {
                User::where('uid', $uid)->update(['openid' => $openid]);//更新个人信息
            }*/
           /* if ($unionid != "") {
                User::where('uid', $uid)->update(['unionid' => $unionid]);//更新个人信息
            }*/
            if (empty($member['username'])) {
                $username = 'ab'.$uid.mt_rand(1,99);
                User::where('uid', $uid)->update(['last_time' => $now_time,'openid'=>'','username'=>$username]);
            }else {
                User::where('uid', $uid)->update(['last_time' => $now_time,'openid'=>'']);
            }
           
            $success = TRUE;      
        } else {
            //先判断是否username重名
            $u = User::where('username', $name)->field('uid')->find();
         
            if (!empty($u)) {
                //重命名$name
                $uniqid = md5($name . '_' . $now_time);
                $suffix = substr($uniqid, 1, 3);
                $name = $name . '_' . $suffix;
            }
            $active = 1;
            if ($agent['tid'] == 584) {
                $agents_data = Agents::where('agents_id', $agent['tid'])->field('smstime')->find();
                if ($agents_data['smstime'] > 0) {
                    $active = 0;
                }  
            }
            if ($agent['tid']== 914) {
                $agents_data = Agents::where('agents_id', $agent['tid'])->field('smstime')->find();
                if ($agents_data['smstime'] > 0) {
                    $active = 0;
                }  
            }
            if ($agent['tid'] == 5) {
                $agents_data = Agents::where('agents_id', $agent['tid'])->field('smstime')->find();
                if ($agents_data['smstime'] > 0) {
                    $active = 0;
                }
            }
            $agents_share_rate = 0;
            if ($agent['tid'] == 549) {
                $agents_share_rate = 3;
            }
            if ($agent['tid'] == 525) {
                $active = 1;
            }
            $insertData = [
                'name' => $name,
                'username' => $name,
                'nickname' => $name,
                'password' => md5($name . $now_day),
                'reg_time' => $now_time,
                'agents_id' => $agent['agents_id'],
                'agents_name' => $agent['name'],
                'agents_account' => $agent['account'],
                'last_time' => $now_time,
                'openid' => $openid,
                'unionid' => $unionid,
                'head' => $head,
                'agents_share_rate' => $agents_share_rate,
                'user_desc' => '',
                'xh_config' => 1,
                'zx_max' => 10000,
                'zx_min' => 10,
                'phone' => '',
                'wxchat' => '',
                'qq' => '',
                'bankcard' => '',
                'extra_share' => 0,
                'tourist' => 0,//wx
                'score' => 0,
                'active' => $active,
                'tid' => $agent['tid'],
                'inte_rate' => $agent['integral_rate'],
                'xm_rate' => $agent['integral_rate']
            ];
            $userModel = new User();
            $insert_id = $userModel->insert($insertData, false, true);
            if ($insert_id > 0) {
                $username = 'ab'.$insert_id.mt_rand(1,99);
                User::where('uid',$insert_id)->update(['username'=>$username]);
                if ($active == 0) {
                    return ['code' => 500, 'msg' => '您的ID是 <' . $insert_id . '> 请先联系客服激活账号'];
                }
                cookie('uid', $insert_id);
                Session::set('uid', $insert_id);
                $success = TRUE;
                $uid = $insert_id;
                $tid =  $agent['tid'];
            } else {
                return ['code' => 500, 'msg' => '网络异常，请重新操作'];
            }
        }
        
        
        //走登陆
        if ($success) {
            Session::set('uid', $uid);
            Session::set('tid',$agent['tid']);
            cookie('uid', $uid);
            $sys_info = Domain::whereIn('type', [8,9, 12])->field('domain,type,status')->select();

            $title = $agent['team_title'];
            $head_domain = "";
            $head_domain_oss = "";
            $wsurl = "";
            $notice = $agent['notice'];
            $pmd = $agent['notice'];
            $imcode = $agent['server_url'];

            foreach ($sys_info as $item) {
                if ($item['type'] == 8) {
                    $head_domain = $item['domain'];
                }
                if ($item['type'] == 12) {
                    $wsurl = $item['domain'];
                }      
                if ($item['type'] == 9) {
                    $head_domain_oss = $item['domain'];
                }
            }
            $member = Db::name('user')->field('username,name,uid,head,password,score,password,agents_id,agents_account,agents_name,xm_rate,integral,active')->where('uid', $uid)->find();
            $member['nickname'] = $member['name'];
            if (!empty($member['head'])) {
                $member['head'] = $member['head'];
            }
            $real_ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $request->ip() : $_SERVER['HTTP_X_REAL_IP'];      
            $insert_data = [
                'uid' => $uid,
                'username' => $member['username'],
                'name' => $member['name'],
                'user_type' => 0,
                'ip_info' => '{"ip":"' . $real_ip . '"}',
                'ip' => $real_ip,
                'mktime' => time(),
                'agents_id' => $member['agents_id'],
                'agents_account' => $member['agents_account'],
                'agents_name' => $member['agents_name'],
                'tid' => $agent['tid']
            ];
            Db::name('user_login_info')->insert($insert_data);

            $time = time();
            $domain = 'http://' . $_SERVER['HTTP_HOST'];
            $roomsnosay = TeamRoom::where('nosay', 1)->field('groupid')->select();
            $roomsnosaystr = [];
            if (!empty($roomsnosay)) {
                foreach ($roomsnosay as $item) {
                    array_push($roomsnosaystr, $item['groupid']);
                }
            }
            $logindata = [
                'member' => $member,
                'time' => $time,
                'token' => mt_rand(1000, 9999) . $member['password'],
                'wsurl' => $wsurl,
                'domain' => $domain,
                'imcode' => $imcode,
                'title' => $title,
                'ip_info' => '{"ip":"' . $real_ip . '"}',
                'ip' => $real_ip,
                'head_domain' => $head_domain,
                'head_domain_oss' => $head_domain_oss,
                'notice' => $notice,
                'pmd' => $pmd,
                'user_type' => $user_type,
                'lqfb' => 1,
                'roomsnosay' => implode(",", $roomsnosaystr),
                'bets_way' => $agent['bets_way']
            ];
            return ['code' => 200, 'msg' => '登陆成功', 'data' => $logindata];
        } else {
            return ['code' => 500, 'msg' => '登陆失败'];
        }
    }

    function im_unreadMessageCount()
    {
        $request = Request::instance();
        $playid = $request->post('playid');
        $channelId = $request->post('channelId');
        $sjchannelId = $request->post('sjchannelId');
        return ['code' => 200, 'data' => ['unreadMessageCount'=>0], 'sj' => ['unreadMessageCount'=>0]];
            $sys_info = common::getChatSystemDomain();
      
            $url = $sys_info['domain'] . '/api/weitou/' . $playid . '/' . $channelId;
            $header = [
                'X-Domain:2653165'
            ];
            
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            $file_contents = curl_exec($ch);

            curl_close($ch);
        
               $resultJson = json_decode($file_contents, 1);           
         
           
            $sjurl = $sys_info['domain'] . '/api/weitou/' . $playid . '/' . $sjchannelId;

            $sjch = curl_init();

            curl_setopt($sjch, CURLOPT_URL, $sjurl);

            curl_setopt($sjch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($sjch, CURLOPT_CONNECTTIMEOUT, 10);

            curl_setopt($sjch, CURLOPT_HTTPHEADER, $header);

            $sj_file_contents = curl_exec($sjch);

            curl_close($sjch);
            $sjresultJson = json_decode($sj_file_contents, 1);


            return ['code' => 200, 'data' => $resultJson['data'], 'sj' => $sjresultJson['data']];

     
    }

    /**
     * @function 登录聊天系统
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function im_login_auth()
    {
        $request = Request::instance();
        $uid = $request->post('uid');
        if (empty($uid)) {
            return ['code' => 500, 'msg' => '参数错误'];
        } else {
            $member = User::where('uid', $uid)->field('reg_time,name,head,tid,status')->find();
            if ($member['status'] == 1) {
                echo header('Location: http://9612.bynebjmu.com');
                exit();
            }
            $agents = Agents::where('agents_id', $member['tid'])->find();

            $im_url = TeamConfig::where('tid', $agents['tid'])->find();

            if (empty($im_url['server_url'])) {
                return ['code' => 200, 'msg' => '客服地址未配置', 'data' => null];
            }
             //$sys_info = common::getChatSystemDomain();
            // http://shangshui_im.shanghaimetal.org
            //http://shangshui_im.yiweidao.cn/
             $im_url_arr = [
                 'http://47.86.246.4:8788',

             ];
             if (in_array( $member['tid'],[5,262,560])) {
                 $im_url_arr = [
                     'http://47.101.143.146:8618',
                    // 'http://156.247.41.192:8619', 
                    // 'http://82.23.246.149:8619',
                    // 'http://27.124.44.153:8618',
                    // 'http://27.124.44.151:8618',
                   //  'http://154.23.221.76:8618'
                 ];
             }
             
             $im_url_key = array_rand($im_url_arr);
             $sys_info['domain'] = $im_url_arr[$im_url_key];
          //  $imdata = file_get_contents(self::HTTPURL . '/v1/control/getImDomain');
           // $imdataArray = json_decode($imdata, true);
           // $sys_info['domain'] = $imdataArray['data']['imdomain'];                       
            //$chat_domain = Domain::where('type', 28)->field('domain')->where('tid',$member['tid'])->where('status',0)->limit(1)->find();
           // $chat_domain_array = [];
            //foreach ($chat_domain as $chat_domain_item){
              //  $chat_domain_array[] = $chat_domain_item['domain'];
            //}
            //$chat_domain_01 =  array_rand($chat_domain_array);
           // $sys_info['domain'] = $chat_domain['domain'];                    
           // $chat_domain = Domain::where('type', 28)->field('domain')->where('status',0)->find();
           // $sys_info['domain'] = $chat_domain['domain'];
            
            $arr = parse_url($im_url['server_url']);
            $arr_query = $this->convertUrlQuery($arr['query']);
            $playid = substr(md5($uid . $uid), 0, 10);
            User::where('uid', $uid)->update(['playid' => $playid]);//
       
                if (strpos($member['head'], 'http') !== false) {

                } else {
                    $sys_info_imgpre = Domain::where('type', 8)->field('domain')->find();
                    $member['head'] = $sys_info_imgpre['domain'] . '/' . $member['head'];
                }
                $url = $sys_info['domain'] . '/api/auth/domain/' . $playid . '/' . $arr_query['id'] . '?nickname=' . urlencode($member['name']) . '&headimgurl=' . $member['head'];
                $header = [
                    'X-Domain:2653165'
                ];
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

                $file_contents = curl_exec($ch);

                curl_close($ch);
                $resultJson = json_decode($file_contents, 1);

                if ($resultJson['code'] == 406) {
                    return ['code' => 406, 'playid' => $playid, 'data' => null];
                } else {
                    return ['code' => 200, 'toid' => $arr_query['id'], 'imdomain' => $sys_info['domain'], 'playid' => $playid, 'data' => $resultJson['data']];
                }
        }
    }

    function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    function curl_get_https($url)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);  // 从证书中检查SSL加密算法是否存在
        $tmpInfo = curl_exec($curl);     //返回api的json对象
        //关闭URL请求
        curl_close($curl);
        return $tmpInfo;    //返回json对象
    }


    /**
     * @function 账号密码登陆验证(废弃接口)
     */
    public function doLogin()
    {
        $request = Request::instance();
        $username = $request->post('username');
        $password = $request->post('password');
        $user_type = intval($request->post('user_type'));
        cookie('user_type', $user_type);
        $success = FALSE;
        $uid = 0;
        if (empty(config('database.nocode'))) {    
            //图形验证码
            if (Session::has('checkcode')) {
                if (Session::get('checkcode') != 100) {
                    return ['code' => 500, 'msg' => '图形验证码不正确'];
                }          
            }else {
               // exit();
                return ['code' => 500, 'msg' => '图形验证码不正确，请刷新页面重新操作'];
            }
        }
        if ($user_type == 1) {
            $now_time = date('Y-m-d H:i:s');
            $username = 'yk' . date('mdHis');
            $insertData = [
                'name' => '游客',
                'username' => $username,
                'password' => md5($username),
                'reg_time' => $now_time,
                'agents_id' => 0,
                'agents_name' => 0,
                'agents_account' => 0,
                'last_time' => $now_time,
                'agents_share_rate' => 0,
                'user_desc' => '',
                'xh_config' => 1,
                'zx_max' => 10000,
                'zx_min' => 10,
                'phone' => '',
                'wxchat' => '',
                'qq' => '',
                'bankcard' => '',
                'extra_share' => 0,
                'tourist' => 1,
                'score' => 2000,
                'active' => 1
            ];
            $userModel = new User();
            $insert_id = $userModel->insert($insertData, false, true);
            if ($insert_id > 0) {
                cookie('uid', $insert_id);
                Session::set('uid', $insert_id);
                $success = TRUE;
                $uid = $insert_id;
            } else {
                return ['code' => 500, 'msg' => '网络异常，请重新操作'];
            }

        } else {
            if (empty($username) || empty($password)) {
                return ['code' => 500, 'msg' => '用户名或密码不能为空'];
            }
            
      
            $member = User::where('username', $username)->field('uid,status,agents_id,phonelogin')->find();

            if (!empty($member)) {
                //查代理
                $agent = Agents::alias('a')->join('team_config t', 'a.tid=t.tid')->where('a.agents_id', $member['agents_id'])
                ->field('a.agents_id,a.name,a.account,a.tid,t.team_title,t.notice,t.bets_way,t.integral_rate,t.server_url')->find();
                
                
                //获取软件使用时间
                $agent_info = Agents::where('agents_id', $agent['tid'])->field('use_end_time')->find();
                if (time() > strtotime($agent_info['use_end_time'])) {
                    return ['code' => 500, 'msg' => '软件维护升级，请耐心等待'];
                }
                
              /*  if (!in_array($member['uid'], [8964,6288,8651,3433,9030,9263,7080,9010,9263])) {
                     if (intval($member['phonelogin'])==0) {
                       return ['code' => 500, 'msg' => '请先去修改密码'];
                     }
                }*/
            
                if ($member['status'] == 1) {
                    echo header('Location: http://9612.bynebjmu.com');
                    exit();
                   // return ['code' => 500, 'msg' => '该账号已被禁用'];
                }
                $salt_config = Config::get('salt');
                $password = $salt_config['pwdsaltstr']. $password;
                $member1 = User::where('username', $username)->where('password', md5($password))->field('uid')->find();
                if (!empty($member1)) {
                    $uid = $member1['uid'];
                    Session::set('uid', $uid);
                    cookie('uid', $uid);
                    
                    $success = TRUE;
                    $uid = $uid;
                } else {
                    return ['code' => 500, 'msg' => '密码不正确'];
                }
            } else {
                return ['code' => 500, 'msg' => '用户名不存在'];
            }
        }

        if ($success) {
            //图形验证码标识
            Session::set('checkcode',NULL);
            $sys_info = Domain::whereIn('type', [8,9, 12])->field('domain,type,status')->select();
            // 查team配置
            //查teamconfig
            $team_config = TeamConfig::where('tid', $agent['tid'])->find();

            $title = $team_config['team_title'];
            $head_domain = "";
            $head_domain_oss = "";
            $wsurl = "";
            $game_rule_text = "";
            $notice = $team_config['notice'];
            $pmd = $team_config['notice'];
            $imcode = $team_config['server_url'];
            foreach ($sys_info as $item) {
                if ($item['type'] == 8) {
                    $head_domain = $item['domain'];
                }
                if ($item['type'] == 9) {
                    $head_domain_oss = $item['domain'];
                }
                if ($item['type'] == 12) {
                    $wsurl = $item['domain'];
                }
            }
            if($agent['tid'] == 5 && $uid == 20171 ){
                $wsurl = "ws://154.23.221.76:9612";
            }
            $member = Db::name('user')->field('username,name,uid,head,password,score,agents_id,agents_account,agents_name,xm_rate,integral,active')->where('uid', $uid)->find();
            $member['nickname'] = $member['name'];
            if (!empty($member['head'])) {
                $member['head'] = $member['head'];
            }
            $real_ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $request->ip() : $_SERVER['HTTP_X_REAL_IP'];
        
            if ($real_ip == '49.89.121.180' || $real_ip=='36.28.23.234') {
                $insert_data = [
                    'uid' => $uid,
                    'username' => $member['username'],
                    'name' => $member['name'],
                    'user_type' => 0,
                    'ip_info' => '异常ip登录',
                    'ip' => $real_ip,
                    'mktime' => time(),
                    'agents_id' => $member['agents_id'],
                    'agents_account' => $member['agents_account'],
                    'agents_name' => $member['agents_name'],
                    'tid' => $agent['tid']
                ];
                Db::name('user_login_info')->insert($insert_data);exit();
                return ['code' => 500, 'msg' => '账号不存在'];
            }
            if ($agent['tid']== 262 && $uid == 9092 ) {
                $number_ip = mt_rand(1,10);
                if ($number_ip <=5 ) {
                    $real_ip = '171.106.99.114';
                }elseif ($number_ip >5 and $number_ip < 8){
                    $real_ip = '124.226.123.152';
                }else {
                    $real_ip = '171.106.109.18';
                }
            }
            $ip_data_json = common::http_request("http://ip.taobao.com/service/getIpInfo.php?ip=" . $real_ip, null, 3);
            $ip_data = json_decode($ip_data_json, true);
            if ($ip_data['code'] == 0 && $user_type != 1) {
                $insert_data = [
                    'uid' => $uid,
                    'username' => $member['username'],
                    'name' => $member['name'],
                    'user_type' => 0,
                    'ip_info' => json_encode($ip_data['data']),
                    'ip' => $real_ip,
                    'mktime' => time(),
                    'agents_id' => $member['agents_id'],
                    'agents_account' => $member['agents_account'],
                    'agents_name' => $member['agents_name'],
                    'tid' => $agent['tid']
                ];
                Db::name('user_login_info')->insert($insert_data);
            }
            $time = time();
            $domain = 'http://' . $_SERVER['HTTP_HOST'];
            $roomsnosay = TeamRoom::where('nosay', 1)->field('groupid')->select();
            $roomsnosaystr = [];
            if (!empty($roomsnosay)) {
                foreach ($roomsnosay as $item) {
                    array_push($roomsnosaystr, $item['groupid']);
                }
            }
   
            $logindata = [
                'member' => $member,
                'time' => $time,
                'token' => mt_rand(1000, 9999) . $member['password'],
                'wsurl' => $wsurl,
                // 'qrcode' => $qrcode,
                'domain' => $domain,
                'title' => $title,
                'head_domain' => $head_domain,
                'head_domain_oss' => $head_domain_oss,
                'game_rule_text' => $game_rule_text,
                'notice' => $notice,
                // 'notice_status' => $notice_status,
                'pmd' => $pmd,
                'user_type' => $user_type,
                'ip_info' => $ip_data_json,
                'ip' => $real_ip,
                'lqfb' => 1,
                'bets_way' => $agent['bets_way'],
                'roomsnosay' => implode(",", $roomsnosaystr)
            ];
            return ['code' => 200, 'msg' => '登陆成功', 'data' => $logindata];
        } else {
            return ['code' => 500, 'msg' => '登陆失败'];
        }
    }

    /**
     * @function 账号密码登陆验证
     */
    public function doLoginNew()
    {
        $request = Request::instance();
        $username = $request->post('username');
        $password = $request->post('password');
        $username_dec =  common::decrypt($username, 'bqoksdjf#$&190ajf', 'nfjdsj29i#$');
        $username = substr($username_dec,1,strlen($username_dec)-2);
        $password_dec =  common::decrypt($password, 'changlong@#$%qwe', 'jz,nvkwpqpo2-');       
        $password = substr($password_dec,1,strlen($password_dec)-2); 
      //  $imagecode = $request->post('checkcode');
        $user_type = intval($request->post('user_type'));
        cookie('user_type', $user_type);
        $success = FALSE;
        $uid = 0;
        /*
        if ($user_type != 1) {
            if (empty($imagecode)) {
                return ['code'=>500,'msg'=>'图形验证码为空'];
            }
            $imagecode =  mb_strtolower(str_replace(" ","",$imagecode));
            $salt_config = Config::get('salt');
            $redis = new Redis();
            $redisKey = $salt_config['imagecodestr'].$imagecode;
            $imgcodeRedisHas = $redis->has($redisKey);
            if (empty($imgcodeRedisHas)) {
                return ['code'=>500,'msg'=>'验证码不正确或已过期'];
            }        
        }*/
        if ($user_type == 1) {
            $now_time = date('Y-m-d H:i:s');
            $username = 'yk' . date('mdHis');
            $insertData = [
                'name' => '游客',
                'username' => $username,
                'password' => md5($username),
                'reg_time' => $now_time,
                'agents_id' => 0,
                'agents_name' => 0,
                'agents_account' => 0,
                'last_time' => $now_time,
                'agents_share_rate' => 0,
                'user_desc' => '',
                'xh_config' => 1,
                'zx_max' => 10000,
                'zx_min' => 10,
                'phone' => '',
                'wxchat' => '',
                'qq' => '',
                'bankcard' => '',
                'extra_share' => 0,
                'tourist' => 1,
                'score' => 2000,
                'active' => 1
            ];
            $userModel = new User();
            $insert_id = $userModel->insert($insertData, false, true);
            if ($insert_id > 0) {
                cookie('uid', $insert_id);
                Session::set('uid', $insert_id);
                $success = TRUE;
                $uid = $insert_id;
            } else {
                return ['code' => 500, 'msg' => '网络异常，请重新操作'];
            }
            
        } else {
            if (empty($username) || empty($password)) {
                return ['code' => 500, 'msg' => '用户名或密码不能为空'];
            }
            
            
            $member = User::where('username', $username)->field('uid,status,agents_id,phonelogin')->find();
            
            if (!empty($member)) {
                //查代理
                $agent = Agents::alias('a')->join('team_config t', 'a.tid=t.tid')->where('a.agents_id', $member['agents_id'])
                ->field('a.agents_id,a.name,a.account,a.tid,t.team_title,t.notice,t.bets_way,t.integral_rate,t.server_url')->find();
                
                
                //获取软件使用时间
                $agent_info = Agents::where('agents_id', $agent['tid'])->field('use_end_time')->find();
                if (time() > strtotime($agent_info['use_end_time'])) {
                    return ['code' => 500, 'msg' => '软件维护升级，请耐心等待'];
                }
                
                if ($member['status'] == 1) {
                    return ['code' => 500, 'msg' => ''];                   
                }
                $salt_config = Config::get('salt');
                $password = $salt_config['pwdsaltstr']. $password;
                $member1 = User::where('username', $username)->where('password', md5($password))->field('uid')->find();
                if (!empty($member1)) {
                    $uid = $member1['uid'];
                    Session::set('uid', $uid);
                    cookie('uid', $uid);
                    
                    $success = TRUE;
                    $uid = $uid;
                } else {
                    return ['code' => 500, 'msg' => '密码不正确'];
                }
            } else {
                return ['code' => 500, 'msg' => '用户名不存在'];
            }
        }
        
        if ($success) {
            //图形验证码标识
            Session::set('checkcode',NULL);
            $sys_info = Domain::whereIn('type', [8,9, 12])->field('domain,type,status')->select();
            // 查team配置
            //查teamconfig
            $team_config = TeamConfig::where('tid', $agent['tid'])->find();
            
            $title = $team_config['team_title'];
            $head_domain = "";
            $head_domain_oss = "";
            $wsurl = "";
            $game_rule_text = "";
            $notice = $team_config['notice'];
            $pmd = $team_config['notice'];
            $imcode = $team_config['server_url'];
            foreach ($sys_info as $item) {
                if ($item['type'] == 8) {
                    $head_domain = $item['domain'];
                }
                if ($item['type'] == 9) {
                    $head_domain_oss = $item['domain'];
                }
                if ($item['type'] == 12) {
                    $wsurl = $item['domain'];
                }
            }
            if($agent['tid'] == 5 && $uid == 20171 ){
                $wsurl = "ws://154.23.221.76:9612";
            }
            $member = Db::name('user')->field('username,password,name,uid,head,score,agents_id,agents_account,agents_name,xm_rate,integral,active')->where('uid', $uid)->find();
            $member['nickname'] = $member['name'];
            if (!empty($member['head'])) {
                    $member['head'] = $member['head'];
                    $res = @file_get_contents($member['head'],null,null,0,10);
                    if(!$res){
                        Db::name('user')->where('uid',$uid)->update(['head'=>'']);
                        $member['head'] =  '';
                    }
                
            }
            $real_ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $request->ip() : $_SERVER['HTTP_X_REAL_IP'];
            
            if ($real_ip == '49.89.121.180' || $real_ip=='36.28.23.234') {
                $insert_data = [
                    'uid' => $uid,
                    'username' => $member['username'],
                    'name' => $member['name'],
                    'user_type' => 0,
                    'ip_info' => '异常ip登录',
                    'ip' => $real_ip,
                    'mktime' => time(),
                    'agents_id' => $member['agents_id'],
                    'agents_account' => $member['agents_account'],
                    'agents_name' => $member['agents_name'],
                    'tid' => $agent['tid']
                ];
                Db::name('user_login_info')->insert($insert_data);exit();
                return ['code' => 500, 'msg' => '账号不存在'];
            }
            if ($user_type != 1) {
                $insert_data = [
                    'uid' => $uid,
                    'username' => $member['username'],
                    'name' => $member['name'],
                    'user_type' => 0,
                    'ip' => $real_ip,
                    'mktime' => time(),
                    'agents_id' => $member['agents_id'],
                    'agents_account' => $member['agents_account'],
                    'agents_name' => $member['agents_name'],
                    'tid' => $agent['tid']
                ];
                Db::name('user_login_info')->insert($insert_data);
            }
            $time = time();
            $domain = 'http://' . $_SERVER['HTTP_HOST'];
            $roomsnosay = TeamRoom::where('tid',$agent['tid'])->field('groupid,nosay,tid,video_link,mark,groupname,game_type')->select();
            $roomsInfo = [];
            $roomsnosaystr = [];
            if (!empty($roomsnosay)) {
                foreach ($roomsnosay as $item) {
                    if ($item['nosay'] == 1) {
                        array_push($roomsnosaystr, $item['groupid']);
                    }           
                    array_push($roomsInfo, $item);
                }
            }
            $md5pwd = $member['password'];
            unset($member['password']);
            $logindata = [
                'member' => $member,
                'time' => $time,
                'token' => mt_rand(1000, 9999) . $md5pwd,
                'wsurl' => $wsurl,
                'domain' => $domain,
                'title' => $title,
                'head_domain' => $head_domain,
                'head_domain_oss' => $head_domain_oss,
                'game_rule_text' => $game_rule_text,
                'notice' => $notice,
                'pmd' => $pmd,
                'user_type' => $user_type,
                'ip' => $real_ip,
                'lqfb' => 1,
                'bets_way' => $agent['bets_way'],
                'roomsnosay' => implode(",", $roomsnosaystr),
                'roomsInfo' => $roomsInfo,
                'group_nosay' => $team_config['nosay'],
                'is_limit_num' => $team_config['is_limit_num']
            ];
            return ['code' => 200, 'msg' => '登陆成功', 'data' => $logindata];
        } else {
            return ['code' => 500, 'msg' => '登陆失败'];
        }
    }
    
    
    /**
     * @function 注销登陆
     */
    public function loginOut()
    {
        cookie('uid', null);
        Session::set('uid', null);
        header('Location:' . url('/login/login_view'));
        exit;
    }


    /**
     * @function 获取短信验证码
     */
    public function sendSmsCode($code, $phone)
    {
        $res = common::sendSMS($code, $phone);
        $result = json_decode($res, true);
        return $result;
    }
    
    /**
     * @function 获取图像验证码
     */
    
    public function getImgCode() {  
        $code = common::GetRandStr(4);
        $sscode = mb_strtolower(str_replace(" ","",$code));
        $salt_config = Config::get('salt');
        $redis = new Redis();
        $redis->set($salt_config['imagecodestr'].$sscode, $sscode,300);      
        if (config('database.mark')== 9701) {
             return ['code'=>200,'msg'=>'图形码请求成功','data'=>[
                'imgurl'=>'http://140.210.202.20:9077/imgcode.png',
                'code'=>$sscode,
             ]];
        }
        // 1.创建画布资源
        $img = imagecreatetruecolor(100, 40);
        // 2.准备颜色
        $black = imagecolorallocate($img, 0, 0, 0);
        $white = imagecolorallocate($img, 255, 255, 255);
        $red = imagecolorallocate($img, 255, 0, 0);
        $green = imagecolorallocate($img, 0, 255, 0);
        $blue = imagecolorallocate($img, 0, 0, 255);
    
        // 3.填充画布
        imagefill($img, 0, 0, $white);
        // 4.在画布上画图像或文字
    
        // 两个给定点之间绘制一条线段,用来干扰验证码识别
       // imageLine($img, 0, rand(20,50), 200, rand(20,50), $black);
        //水平绘制字符串
        imageString($img, 5, 10, 15, $code, $black);
        // 5.输出最终图像或保存最终图像
        header('content-type:image/png');
        //图片名称
        $imgname = mt_rand(1,10000).'_'.time().'.png';
        // 图片从浏览器上输出
        imagepng($img,'/home/www/wwwroot/imgcode/'.$imgname);
        $mark = config('database.mark');
        $imagehost = '';
        if ($mark == 9612 || $mark == 9601) {
            $imagehost = 'http://47.109.64.196:9027/' ;
        }else if($mark == 9604 || $mark == 9654 || $mark == 9669 || $mark == 9678){
            $imagehost = 'http://8.137.60.11:6028/' ;
        }else if($mark == 9605 || $mark== 9613 || $mark == 9609 || $mark == 9608){
            $imagehost = 'http://47.108.135.36:9088/' ;
        }else {
            $imagehost = 'http://'.self::JILI_IP.':9077/' ;
        }
        return ['code'=>200,'msg'=>'图形码请求成功','data'=>['imgurl'=>$imagehost.$imgname]];     
    }
    
    /**
     * @function 验证图形验证码
     */
    public function checkImgCode() {
        $request = Request::instance();
        $imagecode = $request->post('imagecode');
        if (empty($imagecode)) {
            return ['code'=>500,'msg'=>'图形验证码为空'];
        }
        $imagecode =  mb_strtolower(str_replace(" ","",$imagecode));
        $salt_config = Config::get('salt');
        $ssimagecode = Session::get($salt_config['imagecodestr'].$imagecode); 
        if (empty($ssimagecode)) {
            return ['code'=>500,'msg'=>'图形验证码已过期'];
        }
        $imagecode = mb_strtolower($imagecode);
        $ssimagecode = mb_strtolower($ssimagecode);
        if ($imagecode == $ssimagecode) {
            $salt_config = Config::get('salt');
            Session::set($salt_config['imagecodestr'].$imagecode, null);
            Session::set('checkcode', 100);
            return ['code'=>200,'msg'=>'图形验证码正确'];
        }else {
            return ['code'=>500,'msg'=>'图形验不正确'];
        }
    }
    
    /**
     * @function 获取token
     */
    public function getLoginToken(){
        $request = Request::instance();
        $real_ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $request->ip() : $_SERVER['HTTP_X_REAL_IP'];
        $agent_id = intval($request->post('agent_id'));
        $tid = 0;
        if (!empty($agent_id) || $agent_id > 0) {
            $agent = Agents::where('agents_id',$agent_id)->field('fee,tid')->find();
            $tid = $agent['tid'];
        }
        if (empty($tid) || $tid == 0) {
            $port = $_SERVER['SERVER_PORT'];
            $tidArr = common::GetPortTid();
            $tid = $tidArr[$port];
        }
        $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
        if ($agents_data['fee'] > 0) {
            return ['code'=>500,'msg'=>'该功能已关闭'];
        }
        if ($tid == 5678) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid == 691) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid == 725) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid == 683) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid == 569) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid == 563) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid == 3) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid==492) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid==262) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }                    
        }
        if ($tid==5) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid==549) {
            $agents_data = Agents::where('agents_id', $tid)->field('fee')->find();
            if ($agents_data['fee'] > 0) {
                return ['code'=>500,'msg'=>'该功能已关闭'];
            }
        }
        if ($tid==523) {
            return ['code'=>500,'msg'=>'该功能已关闭'];
        }
        if ($tid==584) {
            return ['code'=>500,'msg'=>'该功能已关闭'];
        }
        if ($tid==541) {
            return ['code'=>500,'msg'=>'该功能已关闭'];
        }
        $user = User::where('unionid',$real_ip)->where('user_type',1)->field('uid')->find();
        $uid = 0; 
        if (empty($user)) {                        
            $agent = Agents::where('agents_id', $tid)->field('agents_id,name,account,tid')->find();
            $now_time = date('Y-m-d H:i:s');
            $username = 'yk' . date('mdHis');
            $insertData = [
                'name' => '游客',
                'username' => $username,
                'password' => md5($username),
                'reg_time' => $now_time,
                'agents_id' => $tid,
                'agents_name' => $agent['name'],
                'agents_account' => $agent['account'],
                'last_time' => $now_time,
                'agents_share_rate' => 0,
                'user_type' => 1,
                'xh_config' => 1,
                'zx_max' => 10000,
                'zx_min' => 10,
                'phone' => '',
                'wxchat' => '',
                'qq' => '',
                'bankcard' => '',
                'extra_share' => 0,
                'tourist' => 1,
                'score' => 0,
                'active' => 1,
                'tid'=>$tid,
                'unionid'=>$real_ip,
                'no_say'=>1
            ];
            $userModel = new User();
            $uid = $userModel->insert($insertData, false, true);
        }else {
            $uid = $user['uid'];
        }
        if ($uid > 0) {
            //生成token
            $token = substr(md5($uid . "_" . time()), 1, 10);
            $redirectDomain = Domain::where('type', 24)->where('status', 0)->field('domain')->find();
            $res = User::where('uid', $uid)->update(['token' => $token]);
            $loginurl =  'http://' . $redirectDomain['domain'] . '?token=' . $token . '/#login';
            return ['code'=>200,'msg'=>'请求成功','data'=>['url'=>$loginurl]];
        }
    }
    
    //获取cookie
    public function getLoginInfo() {

        $request = Request::instance();
        $real_ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $request->ip() : $_SERVER['HTTP_X_REAL_IP'];
        $real_ip =  str_replace('.', '_', $real_ip);
        $username = Cookie::get('u'.$real_ip);
        $password = Cookie::get('p'.$real_ip);
        if (!empty($username) && !empty($password)) {
            $realpassword = common::secret2string($password);        
            return ['code'=>200,'msg'=>'缓存存在','data'=>['username'=>$username,'secret'=>$realpassword]];
        }else {
            return ['code'=>500,'msg'=>'缓存不存在'];
            
        }
       
        
    }
    //需要图形验证码
    public function needcode() {
        if (empty(config('database.nocode'))) {
            return ['code'=>200,'msg'=>'需要验证码'];
        }else{
            return ['code'=>500,'msg'=>'不需要验证码'];
        }
    }
    
 
    /**
     * @function 获取群名称
     */
    public function getQunTitle() {
        $request = Request::instance();
        $agent_id = intval($request->post('agent_id'));
        $tid = 0;
        if (!empty($agent_id) || $agent_id > 0) {
           $agent = Agents::where('agents_id',$agent_id)->field('tid')->find();
           $tid = $agent['tid'];
        }
        if (empty($tid) || $tid == 0) {
            $port = $_SERVER['SERVER_PORT'];
            $tidArr = common::GetPortTid();
            $tid = $tidArr[$port];
        }
        $team = TeamConfig::where('tid',$tid)->field('team_title')->find();
        return ['code'=>200,'msg'=>'请求成功','data'=>$team];
    }
    
    /*
     * @function 获取房间信息
     */
    public function getRoomInfo() {
        $request = Request::instance();
        $roomId = intval($request->post('roomId'));
        $teamRoom = TeamRoom::where('id',$roomId)->field('video_link,video_link_web,counttime')->find();
        return ['code'=>200,'msg'=>'请求成功','data'=>$teamRoom];
    }
    

}