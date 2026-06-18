<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/7/23
 * Time: 1:55 PM
 */
namespace app\index\controller;


use app\index\model\Domain;
use app\index\model\Group;
use app\index\model\GroupTable;
use app\index\model\TeamRoom;
use app\index\model\User;
use think\Db;
use think\Request;
use app\index\common;

class GroupInfo
{
    public function __construct()
    {
        common::checkLogin();
    }

    /**
     * 限制字符
     */
    public function limitWord()
    {
        $data = Group::where('type', 1)->where('status', 0)->field('contend')->find();
        return ['LimitWord' => $data['contend']];
    }

    /**
     * @function 群组最新消息
     */
    public function QueryRecentMessage()
    {
        $groupTable = TeamRoom::getAllGroupInfo('tid,createtime,groupname,headimage,mark,groupid,xstate,id,game_type,ps_name,has_luckysix,lq,fb,xian_dui,zhuang_dui,he,super_he,xian,zhuang,lucky_six_12,lucky_six_20');
        $group = [];
        $request = Request::instance();
        $uid = $request->post('uid');
        $user =  User::where('uid', $uid)->field('tid')->find();
        foreach ($groupTable as $item) {
            if($user->tid == $item['tid']){
                $groupid = $item['groupid'];
                $game_type = $item['game_type'];
            // $db_name = NULL;
            // if ($game_type == 0) {
            //     $db_name = 'chat_packet';
            // } elseif ($game_type == 1) {
            //     $db_name = 'lh_chat_packet';
            // }elseif ($game_type == 2) {
            //     $db_name = 'zjh_chat_packet';
            // } elseif ($game_type == -1) {
            //     $db_name = 'chat_packet';
            // }else if ($game_type == 3) {
            //     $db_name = 'nn_chat_packet';
            // }
            // if (!empty($db_name)) {
                // $chat_log = Db::name($db_name)->where('groupid', $groupid)->order('id', 'desc')
                //     ->field('createtime,fromuid,fromuser,message,msgtype,id,only_uid')->limit(1)->find();

                // $fromuser = json_decode($chat_log['fromuser'], true);
                $data = [
                    'fromuser' => [
                        'nickname' => "",
                        'headimage' => "",
                        'talkstate' => "",
                        'score' => ""
                    ],
                    'group' => [
                        'createtime' => $item['createtime'],
                        'groupname' => $item['groupname'],
                        'headimgurl' => $item['headimage'],
                        'ps_name' => $item['ps_name'],
                        'mark' => $item['mark'],
                        'groupid' => $groupid,
                        'has_luckysix' => $item['has_luckysix'],
                        'lq' => floatval($item['lq']),
                        'fb' => floatval($item['fb']),
                        'xian_dui' => floatval($item['xian_dui']),
                        'zhuang_dui' => floatval($item['zhuang_dui']),
                        'he' => floatval($item['he']),
                        'super_he' => floatval($item['super_he']),
                        'xian' => floatval($item['xian']),
                        'zhuang' => floatval($item['zhuang']),
                        'lucky_six_12' => floatval($item['lucky_six_12']),
                        'lucky_six_20' => floatval($item['lucky_six_20']),
                        'xstate' => $item['xstate']
                    ],
                    'groupid' => $groupid,
                    'has_luckysix' => $item['has_luckysix'],
                    'fromuid' => "",
                    // 'cur_time' => date('m-d H:i', $chat_log['createtime']),
                    // 'createtime' => $chat_log['createtime'],
                    // 'msgtype' => $chat_log['msgtype'],
                    'message' => " ",
                    // 'id' => $chat_log['id'],
                    // 'only_uid' => $chat_log['only_uid']
                ];
                $group[] = $data;
                // }
            }
        }
        return $group;
    }

    /**
     * @function 群组信息
     */
    public function groupInfo()
    {
        $request = Request::instance();
        $id = $request->get('id');
        $data = TeamRoom::getGroupInfo($id);
        if (!empty($data)) {
            return $data;
        } else {
            return ['code' => 500, '群组不存在'];
        }
    }

    /**
     * @function 群信息
     */
    public function group4Info()
    {
        $request = Request::instance();
        $id = $request->get('groupid');
        $groupInfo = TeamRoom::getGroupInfo($id, 'createtime,groupname,headimage,mark,groupid,xstate,id,video_link');
        $data = [
            'AddTime' => date('m-d H:i:s', $groupInfo['createtime']),
            'BTID' => 0,
            'BeginCountDown' => 0,
            'EndCountDown' => 0,
            'GameState' => 0,
            'GroupID' => $id,
            'Remark' => "",
            'Robot' => 2,
            'Round' => 0,
            'TBName' => $groupInfo['mark'],
            'WSLink' => $groupInfo['video_link'],
            'WSTD' => "td301",
            'fromID' => 9
        ];
        return $data;
    }

    /**
     * @function 群聊记录
     */
    public function groupMessageQuery()
    {
        $request = Request::instance();
        $uid = common::checkLogin();
        $groupid = intval($request->get('groupid'));
        $startCount = intval($request->get('startCount'));
        $data = Db::name('chat_packet')->where('groupid', $groupid)->field('createtime,fromuid,fromuser,message,msgtype,id,only_uid,group_info,groupid')
            ->limit($startCount, 30)->order('id', 'desc')->select();
        $message = [];
        foreach ($data as $item) {
            if ($item['only_uid'] > 0 and $item['only_uid'] != $uid) {
                continue;
            }
            $fromuser = json_decode($item['fromuser'], true);
            $message[] = [
                'fromuser' => [
                    'nickname' => $fromuser['name'],
                    'headimage' => $fromuser['head'],
                    'score' => $fromuser['score']
                ],
                'group' => $item['group_info'],
                'groupid' => $item['groupid'],
                'fromuid' => $item['fromuid'],
                'createtime' => date('m-d H:i', $item['createtime']),
                'msgtype' => $item['msgtype'],
                'message' => $item['message'],
                'id' => $item['id']
            ];

        }
        return $message;
    }

    /**
     * @function 换台
     */
    public function queryGroupUserByID()
    {
        $data = TeamRoom::getAllGroupInfo('createtime,groupname,game_type,headimage,mark,groupid,xstate,groupid,id');
        $resp = [];
        $dating = NULL;
        $p17 = NULL;
        foreach($data as $item){
            if($item->game_type == -1){
                $dating = $item;
            }else if($item->mark == 'P17'){
                $p17 = $item;
            }else{
                array_push($resp,$item);
            }
        }
        !empty($dating) && array_push($resp,$dating);
        !empty($p17) && array_push($resp,$p17);

        return $resp;
    }


}