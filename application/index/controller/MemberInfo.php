<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/7/23
 * Time: 2:29 PM
 */

namespace app\index\controller;


use app\index\common;
use app\index\model\Domain;
use app\index\model\User;
use app\index\model\Agents;

use app\index\model\UserScoreLog;

use think\Config;
use think\Session;
use think\Request;
use think\Db;

class MemberInfo
{

    /**
     * @function 会员信息
     */
    public function memberInfo()
    {
        $uid = common::checkLogin();
        if (is_array($uid)) {
            return ['code' => 400, 'msg' => '登录失效'];
        }
        
        $member = User::info($uid);

        //获取图片域名
        $domain_data = Domain::where('type', 8)->field('domain')->find();
        $head_domain = $domain_data['domain'];
        $head = '';
        if (!empty($member['head'])) {
            $head = $head_domain . $member['head'];
        }
        $myinfo = [
            'id' => $member['uid'],
            'nickname' => $member['name'],
            'headimage' => $head,
            'username' => $member['username'],
            'talkstate' => $member['no_say'],
            'score' => intval($member['score']),
        ];
        return $myinfo;
    }

    /**
     * @function 修改图片
     */
    public function UploadChatImage()
    {
        $request = Request::instance();
        $uid = intval(abs($request->post('uid')));

        $user =  User::where('tourist',1)->where('user_type',1)->where('uid',$uid)->find();
        if (!empty($user)) {
            return ['code' => 500, 'msg' => '游客禁止修改'];
        }
        $fileName = $request->post('fileName');
        $file = $request->file($fileName);
        $img = $file->getInfo();
        $object = "images/" . date('YmdHis') . '_' . $img['name'];
        $content = file_get_contents($img['tmp_name']);
       // $headimg = common::moveOss($object, $content);
        $time = date('YmdHis');
        $mark = config("database.mark");
//        //获取图片域名
        $domain_data = Domain::where('type', 9)->field('domain')->find();
        $domain = $domain_data['domain'];
//        // 成功上传后 获取上传信息
        $headimg1 = '/upload/headimage/' . $uid . '_headimg' . $mark . '_' . $time . '.png';
        $image = \think\Image::open($request->file($fileName));
        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
        $image->thumb(150, 150)->save('/home/www/wwwroot/shangshui_image/upload/headimage/' . $uid . '_headimg' . $mark . '_' . $time . '.png');
        $headimg = $domain . $headimg1;
        User::where('uid', $uid)->update(['head' => $headimg]);
        return ['code' => 200, 'data' => $headimg];
    }

    /**
     * @function 修改昵称
     */
    public function UpdateNickName()
    {
        $request = Request::instance();
        $uid = common::checkLogin();
        if (is_array($uid)) {
            return ['code' => 400, 'msg' => '登录失效'];
        }
        
        $nickname = $request->post('nickname');
       $user =  User::where('tourist',1)->where('user_type',1)->where('uid',$uid)->find();
       if (!empty($user)) {
           return ['code' => 500, 'msg' => '游客禁止修改'];
       }
        
        if (!empty($nickname)) {
            if (strlen($nickname)>15){
                return ['code' => 500, 'msg' => '该昵称过长，修改失败'];
            }
            $user = User::where('name', $nickname)->find();
            if (!empty($user)) {
                return ['code' => 500, 'msg' => '修改失败，该昵称已被使用'];
            } else {            
                User::where('uid', $uid)->update(['name' => $nickname]);
                return ['code' => 200, 'msg' => '修改成功'];
            }

        } else {
            return ['code' => 500, 'msg' => '修改失败,参数错误'];
        }
    }


    /**
     * @function 查询红包历史记录
     */
    public function queryMyHbHis()
    {
        $request = Request::instance();
        $uid = common::checkLogin();
        if (is_array($uid)) {
            return ['code' => 400, 'msg' => '登录失效'];
        }
        
        $datas = Db::name('hongbao_user')->alias('hu')->join('hongbao hb', 'hu.hb_id = hb.hb_id')->field('hu.score,hu.uptime,hb.title,hu.lucky')->where('hu.uid', $uid)->order('hu.uptime desc')->limit(30)->select();

        $count = Db::name('hongbao_user')->alias('hu')->join('hongbao hb', 'hu.hb_id = hb.hb_id')->field('sum(hu.score) as count')->where('hu.uid', $uid)->group('hu.uid')->select();
        if (empty($count)) {
            return ['code' => 200, 'data' => $datas, 'total' => 0];
        } else {
            return ['code' => 200, 'data' => $datas, 'total' => $count[0]['count']];
        }

    }


    /**
     * @function 找上级聊天
     */
    public function getChat()
    {
        $uid = common::checkLogin();
        if (is_array($uid)) {
            return ['code' => 400, 'msg' => '登录失效'];
        }
        
        $im_url = Domain::where('type', 27)->field('domain')->find();

        $user = User::where('uid', $uid)->find();
        $agents_boss = Agents::where('agents_id', $user->agents_id)->find();

        if (empty($agents_boss->playid)) {//上线未注册聊天系统
            return ['code' => 500, 'msg' => '上线未注册聊天系统'];
        } else {
            $touserid = $agents_boss->playid;
        }

        if (is_array($uid)) {
            return ['code' => 400, 'msg' => '登录失效'];
        }
        if (empty($user['playid'])) {
            $playid = substr(md5($uid . $user['reg_time']), 0, 10);
            User::where('uid', $uid)->update(['playid' => $playid]);//
        } else {
            $playid = $user['playid'];
        }
        //获取聊天信息
        try {
            if (strpos($user['head'], 'http') !== false) {

            } else {
                $sys_info_imgpre = Domain::where('type', 8)->field('domain')->find();
                $user['head'] = $sys_info_imgpre['domain'] . '/' . $user['head'];
            }

            $sys_info = common::getChatSystemDomain();
            $url = $sys_info['domain'] . '/api/auth/domain/' . $playid . '/' . $touserid . '?nickname=' . urlencode($user['name']) . '&headimgurl=' . $user['head'];
            $header = [
                'X-Domain:2653165'
            ];
            $file_contents = common::http_request($url, null, 3, $header);
            $resultJson = json_decode($file_contents, 1);
            if ($resultJson['code'] == 406) {
                return ['code' => 406, 'playid' => $playid, 'data' => null];
            } else {
                return ['code' => 200, 'toid' => $touserid, 'imdomain' => $sys_info['domain'], 'playid' => $playid, 'data' => $resultJson['data']];
            }

        } catch (Exception $e) {
            return ['code' => 500, 'msg' => '请求错误', 'data' => null];
        }
    }


    /**
     * @function 绑定手机
     */

    public function sendSms()
    {
        $request = Request::instance();
        $uid = common::checkLogin();
        if (is_array($uid)) {
            return ['code' => 400, 'msg' => '登录失效'];
        }
        
        $phone = $request->post('phone');

        if (is_array($uid)) {
            return ['code' => 400, 'msg' => '登录失效'];
        }

        $user = User::where('phone', $phone)->find();

        if (!empty($user)) {
            return ['code' => 500, 'msg' => '该手机号已被绑定', 'phone' => $phone];
        }
        //发送验证码
        $code = mt_rand(999, 9999);
        $res = $this->sendSmsCode($code, $phone);

        if ($res['failCount'] == 0) {
            Session::set('user_bindphone_code' . $uid, $code);
            Session::set('user_bindphone_code_exp' . $uid, time());
            Session::set('user_bindphone' . $uid, $phone);
            return ['code' => 200, 'msg' => '已向' . $phone . '发送验证码', 'sms' => TRUE];
        } else {
            return ['code' => 200, 'msg' => '验证码发送失败,请联系技术人员', 'sms' => FALSE];
        }
    }

    /**
     * @function 绑定手机验证code
     */
    public function sendCode()
    {
        $request = Request::instance();
        $uid = common::checkLogin();

        if (is_array($uid)) {
            return ['code' => 400, 'msg' => '登录失效'];
        }

        //需要验证验证码
        $code = Session::get('user_bindphone_code' . $uid);
        $code_exp = Session::get('user_bindphone_code_exp' . $uid);
        $phone = Session::get('user_bindphone' . $uid);

        if (!empty($code) && !empty($code_exp) && (time() - $code_exp < 120)) {
            if ((string)$code === (string)$request->post('code')) {
                $user = User::where('phone', $phone)->find();

                if (empty($user)) {
                    User::where('uid', $uid)->update(['phone' => $phone]);
                    return ['code' => 200, 'msg' => '绑定成功', 'phone' => $phone];
                } else {
                    return ['code' => 500, 'msg' => '该手机号已被绑定'];
                }
            } else {
                return ['code' => 500, 'msg' => '验证码不正确'];
            }
        } else {
            return ['code' => 500, 'msg' => '验证码已过期'];
        }

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
     * @function 修改密码
     */
    public function UpdatePwd()
    {
        $request = Request::instance();
        $uid = intval(abs($request->post('userid')));
        $oldpassword = $request->post('oldpwd');
        $newpassword = $request->post('newpwd1');
        $newpassword2 = $request->post('newpwd2');
        $user = User::where('password', md5($oldpassword))->where('uid', $uid)->find();
        if (!empty($user)) {
            if (!empty($newpassword2) && !empty($newpassword) && $newpassword == $newpassword2) {
                User::where('uid', $uid)->update(['password' => md5($newpassword)]);
                return ['code' => 200, 'msg' => '操作成功'];
            } else {
                return ['code' => 500, 'msg' => '两次密码输入不一致'];
            }
        } else {
            return ['code' => 500, 'msg' => '旧密码错误'];
        }
    }

    /**
     * @function 获取个人余分明细
     */
    public function getyfdetail()
    {
        $request = Request::instance();
        $uid = common::checkLogin();
        if (is_array($uid)) {
            return ['code' => 400, 'msg' => '登录失效'];
        }
        
        // $begin_time = $request->post('begin_time');
        // $end_time = $request->post('end_time');
        //固定七日时间戳
        $begin_time = strtotime(date("Y-m-d", strtotime("-2 day")));
        $end_time = strtotime(date('Ymd')) + 86400;

        $type = $request->post('type');

        $sql = UserScoreLog::alias('m')->join('user u', 'u.uid=m.uid')
            ->where('m.uid', $uid)
            ->field('u.uid,u.name,u.username,m.id,m.score,m.score_change,m.score_after,m.time,m.note,m.type,m.card_game_id')
            ->order('m.id', 'desc');
        if (!empty($type)) {
            if ($type == 1) {//上分
                $sql->where('m.type', '=', 11);
            } else if ($type == 2) {//下分
                $sql->where('m.type', '=', 12);
            } else if ($type == 3) {//下注
                $sql->where('m.type', 'in', [1, 2, 3, 4, 5, 7, 15, 16, 17]);
            } else if ($type == 4) {//结算
                $sql->where('m.type', '=', 111);
            }
        }
        if (!empty($begin_time)) {
            $sql->where('m.time', '>=', $begin_time);
        }
        if (!empty($end_time)) {
            $sql->where('m.time', '<=', $end_time);
        }
        if (empty($begin_time) && empty($end_time)) {
            $logs = $sql->limit(50)->select();
        } else {
            $logs = $sql->select();
        }
        $card_game_id_arr = [];
        $data = [];
        foreach ($logs as $item) {
            if ($item['card_game_id'] > 0 && empty($card_game_id_arr['id' . $item['card_game_id']])) {
                $card_game_id_arr['id' . $item['card_game_id']] = $item['card_game_id'];
            }
            $data[] = [
                'time' => date('Y-m-d H:i:s', $item['time']),
                'note' => $item['note'],
                'name' => $item['name'],
                'username' => $item['username'],
                'uid' => $item['uid'],
                'score' => $item['score'],
                'score_change' => $item['score_change'],
                'score_after' => $item['score_after'],
                'type' => common::exchangeType($item['type']),
                'card_game_id' => $item['card_game_id']
            ];
        }

        return [
            'code' => 200,
            'msg' => '操作成功',
            'data' => $data,
        ];


    }

    /**
     * @function 客服代码
     */
    public function service()
    {
        $request = Request::instance();
        $service_code = $request->post('service_code');
        if (!empty($service_code)) {
            $agents_id = common::secret2string($service_code);
            $agents = Db::name('agents')->field('service_code')->where('agents_id', $agents_id)->find();
            if (!empty($agents) && !empty($agents['service_code'])) {
                return [
                    'code' => 200,
                    'msg' => '操作成功',
                    'data' => [
                        'service_code' => $agents['service_code']
                    ]
                ];
            } else {
                return ['code' => 500, 'msg' => '暂未设置客服'];
            }
        } else {
            return ['code' => 500, 'msg' => '代理不存在'];
        }
    }
    
    //修改会员密码
    public function updateUserPwd(){
        $request = Request::instance();
        $password = $request->post('password');
        $username = $request->post('username');
        $newpassword = $request->post('newpassword');
        $user = User::where('username', $username)->field('uid,phonelogin')->find();
        if (!empty($user)) {
            $salt_config = Config::get('salt');
                $password = $salt_config['pwdsaltstr']. $password;
           
            $res = User::where('username', $username)->where('password', md5($password))->field('uid')->find();
            
             if (!empty($res)) {
                 // code...
                 $uid = $user['uid'];                
                 $newpassword = $salt_config['pwdsaltstr']. $newpassword;
                 User::where('uid',$uid)->update(['password'=>md5($newpassword),'phonelogin'=>1]);
                return ['code' => 200, 'msg' => '密码修改成功'];
             }else{
                return ['code' => 500, 'msg' => '原始密码不正确'];
             }
        }else{
           return ['code' => 500, 'msg' => '用户名不存在'];
        }
    }
    


}