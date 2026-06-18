<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/7/23
 * Time: 1:29 PM
 */
namespace app\index\controller;

use app\index\common;
use app\index\model\Domain;
use app\index\model\Group;
use app\index\model\TeamRoom;
use think\Cache;
use think\Cookie;
use think\Db;
use think\Request;

class Index
{

    public function __construct()
    {
        common::checkLogin();
    }


    /**
     * 首页
     */
    public function index()
    {
        $sys_info = Domain::where('type', 26)->field('domain')->find();
        $kfcode = $sys_info['domain'];
        return view('dist/index', [
            'kfcode' => $kfcode
        ]);
        $request = Request::instance();
        $uid = common::checkLogin();
        $sys_info = Domain::whereIn('type', [11, 8, 5, 9, 10])->field('domain,type')->select();
        $title = "腾龙国际";
        $head_domain = "";
        $wsurl = "";
        $game_rule_text = "";
        $notice = "";
        foreach ($sys_info as $item) {
            if ($item['type'] == 11) {
                $title = $item['domain'];
            }
            if ($item['type'] == 8) {
                $head_domain = $item['domain'];
            }
            if ($item['type'] == 5) {
                $wsurl = $item['domain'];
            }
            if ($item['type'] == 9) {
                $game_rule_text = $item['domain'];
            }
            if ($item['type'] == 10) {
                $notice = $item['domain'];
            }
        }
        $user_type = Cookie::get('user_type');

        $member = Db::name('user')->field('username,name,uid,head,password,score,password,inte_rate,agents_id,agents_account,agents_name')->where('uid', $uid)->find();
        $member['nickname'] = $member['name'];
        if (!empty($member['head'])) {
            $member['head'] = $head_domain . $member['head'];
        }

        $real_ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $request->ip() : $_SERVER['HTTP_X_REAL_IP'];

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
                'agents_name' => $member['agents_name']
            ];
            Db::name('user_login_info')->insert($insert_data);
        }
        $time = time();
        $domain = 'http://' . $_SERVER['HTTP_HOST'];
        return view('index', [
            'member' => $member,
            'time' => $time,
            'token' => mt_rand(1000, 9999) . $member['password'],
            'wsurl' => $wsurl,
            'domain' => $domain,
            'title' => $title,
            'head_domain' => $head_domain,
            'game_rule_text' => $game_rule_text,
            'notice' => $notice,
            'user_type' => $user_type
        ]);
    }

    /**
     * @function 下注字符
     */
    public function odds_str()
    {
        $request = Request::instance();
        $user_str = $request->get('odds_str');
        $odds_str = strtolower($user_str);
        if (empty($odds_str)) {
            return ['code' => 500, 'msg' => '消息不能为空'];
        } else {
            $error_order = 3;
            $str = preg_replace('# #', '', $odds_str);
            $reg = '#([a-z×️]+)([0-9]+)#';
            preg_match_all($reg, $str, $m);
            if (!empty($m[0])) {
                $error_order = 2;
                //存在下注指令
                $strr = '';
                $format_odds_str = '';
                foreach ($m[0] as $item) {
                    $strr .= $item;
                    $format_odds_str .= $item . ',';
                }
                if ($strr == $str) {
                    $odds_order = ['z', 'x', 'h', 'l', 'zd', 'xd', 'sb', 'd'];
                    $right_odds_order = TRUE;
                    foreach ($m[1] as $value) {
                        if ($right_odds_order == TRUE) {
                            if (!in_array($value, $odds_order)) {
                                $right_odds_order = FALSE;
                            }
                        }
                    }
                    foreach ($m[2] as $v) {
                        if ($v <= 0) {
                            $right_odds_order = FALSE;
                        }
                    }
                    if ($right_odds_order) {
                        $error_order = 1;
                        $format_odds_str = substr($format_odds_str, 0, strlen($format_odds_str) - 1);
                    }
                }
                return ['code' => 200, 'msg' => $user_str, 'odds_str' => $format_odds_str, 'error_order' => $error_order];
            } else {
                $reg = '#([a-z×️]+)#';
                preg_match_all($reg, $str, $m);
                if (!empty($m[0])) {
                    $strr = '';
                    foreach ($m[0] as $item) {
                        $strr .= $item;
                    }
                    if ($strr == $str) {
                        $error_order = 2;
                    }
                }
                //不存在下注指令
                return ['code' => 200, 'msg' => $user_str, 'odds_str' => '', 'error_order' => $error_order];
            }
        }
    }


}