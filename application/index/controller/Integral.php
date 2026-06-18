<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/9/3
 * Time: 5:32 PM
 */

namespace app\index\controller;


use app\index\model\User;
use app\index\model\IntegralDate;
use think\Cache;
use think\Db;
use think\Request;
use app\index\common;
use app\index\model\IntegralLog;
use app\index\model\UserScoreLog;
use app\index\model\UserMoneyLog;

class Integral
{
    /**
     * @function 手动上下积分
     */
    public function integral_hand()
    {
        $uid = common::checkLogin();
        $request = Request::instance();
        $integral_exchange = intval(abs($request->get('integral_exchange')));
        $user = User::where('uid', $uid)->field('inte_rate,ai,tourist,score')->find();
        $inte_rate = $user['inte_rate'];
        $integral = IntegralDate::getUserIntegral(['uid' => $uid]);
        if ($integral_exchange != 0) {
            try {
                $state = 1;
                $integral_exchange = -$integral_exchange;
                if ($integral_exchange < 0) {
                    if ($integral_exchange + $integral < 0) {
                        return ['code' => 500, 'msg' => '可提积分不足'];
                    }
                } elseif ($integral_exchange > 0) {
                    $state = 0;
                }
                $date_time = intval(date('Ymd', time()));
                $date_time_ukey = $uid . '_' . intval(date('YmdHi', time())) . '_' . $integral_exchange;
                $insertData = new IntegralLog();
                $insert_data = [
                    'uid' => $uid,
                    'date_time' => $date_time,
                    'integral' => $integral_exchange,
                    'score' => 0,
                    'ukey' => $date_time_ukey,
                    'mktime' => time(),
                    'inte_rate' => $inte_rate,
                    'card_game_id' => 0,
                    'state' => $state
                ];
                $insert_id = $insertData->insert($insert_data, false, true);
                if ($insert_id > 0) {
                    $insert_id1 = IntegralDate::change($uid, $integral_exchange);
                    if ($insert_id1 > 0) {
                        $fen = intval(abs($integral_exchange * $inte_rate));
                        $cur_time = date("Y-m-d H:i:s");
                        $db_data['uid'] = $uid;
                        $db_data['time'] = $cur_time;
                        $db_data['type'] = 11;
                        $db_data['money_change'] = $fen;
                        $db_data['money'] = $user['score'] + $fen;
                        $db_data['money_type'] = 100; //0是微信 1是支付宝;2是银行卡，20 后台操作,100码粮结算
                        $db_data['state'] = 1;
                        $db_data['user_ai'] = $user['ai'];
                        $db_data['tourist'] = $user['tourist'];
                        $db_data['mktime'] = $cur_time;
                        $money_log_res = UserMoneyLog::create($db_data);
                        UserScoreLog::change($uid, $fen, $money_log_res->id, 100);
                        $datas = IntegralDate::field('integral,integral_total,integral_exchange,date_time')->where('uid', $uid)->order('id', 'desc')->limit(30)->select();
                        $strHtml = '';
                        foreach ($datas as &$item_inte) {
                            $date_time = $item_inte['date_time'];
                            $item['date_time'] = substr($date_time, 0, 4) . '-' . substr($date_time, 4, 2) . '-' . substr($date_time, 6, 2);
                        }
                        return ['code' => 200, 'msg' => '操作成功',
                            'data' => [
                                'html' => $strHtml,
                                'integral_exchange' => intval(abs($integral_exchange)),
                                'exchange_fen' => $fen,
                                'score' => $user['score'] + $fen
                            ]
                        ];
                    } else {
                        return ['code' => 500, 'msg' => '操作失败，请过一分钟后再试'];
                    }
                } else {
                    return ['code' => 500, 'msg' => '操作失败，请过一分钟后再试'];
                }
            } catch (\Exception $e) {
                return ['code' => 500, 'msg' => '操作频繁，请过一分钟后再试'];
            }
        } else {
            return ['code' => 500, 'msg' => '参数错误'];
        }
    }


    /**
     * @function 获取积分结果
     */
    public function getIntegralDate()
    {
        $request = Request::instance();
        $uid = intval($request->get('uid'));
        $datas = IntegralDate::field('integral,integral_total,integral_exchange,date_time')->where('uid', $uid)->order('id', 'desc')->limit(30)->select();
        $strHtml = '';
        foreach ($datas as $item) {
            $strHtml .= '<tr><th>';
            $strHtml .= $item['date_time'];
            $strHtml .= '</th><th>';
            $strHtml .= $item['integral_total'];
            $strHtml .= '</th><th>';
            $strHtml .= $item['integral'];
            $strHtml .= '</th><th>';
            $strHtml .= $item['integral_exchange'];
            $strHtml .= '</th></tr>';
        }
        return ['data' => $strHtml];
    }

    /**
     * @function 日积分列表
     */
    public function integralList()
    {
        $request = Request::instance();
        $uid = intval($request->post('uid'));
        $datas = IntegralDate::field('integral,integral_total,integral_exchange,date_time')->where('uid', $uid)->order('id', 'desc')->limit(30)->select();
        $integralRes = IntegralDate::field('integral_total')->where('uid', $uid)->order('id', 'desc')->limit(1)->find();
        $integral = 0;
        if (!empty($integralRes)) {
            $integral = intval($integralRes['integral_total']);
        }
        foreach ($datas as &$item) {
            $date_time = $item['date_time'];
            $item['date_time'] = substr($date_time, 0, 4) . '-' . substr($date_time, 4, 2) . '-' . substr($date_time, 6, 2);
            $item['integral_total'] = intval($item['integral_total']);
            $item['integral'] = intval($item['integral']);
            $item['integral_exchange'] = intval($item['integral_exchange']);
        }
        return [
            'code' => 200,
            'msg' => '请求成功',
            'data' => [
                'list' => $datas,
                'integral' => $integral
            ]];
    }
}