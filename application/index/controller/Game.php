<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2020/1/4
 * Time: 9:12 PM
 */

namespace app\index\controller;

use app\index\common;
use app\index\model\TeamRoom;
use think\Request;
use app\index\model\CardGame;
use app\index\model\LhCardGame;
use app\index\model\ZjhCardGame;
use app\index\model\NnCardGame;

class Game
{

    /**
     * @function 游戏规则
     */
    public function gameRule()
    {
        $_REQUEST = Request::instance();
        $groupid = $_REQUEST->get('groupid');

        $system = TeamRoom::where('groupid', $groupid)->field('game_rule')->find();
        return ['code' => 200, 'msg' => '请求成功', 'data' => ['game_rule' => $system['game_rule']]];
    }

    /**
     * @function 牌局记录
     *
     * @return \think\response\View
     */
    public function gameList()
    {
        $request = Request::instance();
        $game_type = intval($request->post('game_type'));
       
        
        $sql = CardGame::alias('c');
        if ($game_type == 0) {
            $sql = CardGame::alias('c');
        } elseif ($game_type == 1) {
            $sql = LhCardGame::alias('c');
        } elseif ($game_type == 2) {
            $sql = ZjhCardGame::alias('c');
        } elseif ($game_type == 3) {
            $sql = NnCardGame::alias('c');
        }
        $sql->field('id,room_id,boots_number,ju,mktime,text')->where('state', 2)->order('id', 'desc');
        
        $gameData = $sql->limit(200)->select();
       
        $data = [];
        $banker = '';
        $player = '';
        foreach ($gameData as $item) {
            $card_game_text = json_decode($item['text'], true);
            if (!empty($card_game_text['p1']) && !empty($card_game_text['p2'])) {
                //闲牌
                if ($game_type == 0) {
                    $player = $this->get_pai($card_game_text['p1']) . '+' . $this->get_pai($card_game_text['p2']);
                    if ($card_game_text['p5'] > 0) {
                        $player .= '+' . $this->get_pai($card_game_text['p5']);
                    }
                    //庄牌
                    $banker = $this->get_pai($card_game_text['p3']) . '+' . $this->get_pai($card_game_text['p4']);
                    if ($card_game_text['p6'] > 0) {
                        $banker .= '+' . $this->get_pai($card_game_text['p6']);
                    }
                } elseif ($game_type == 1) {
                    $player = $this->get_pai($card_game_text['p2']);
                    $banker = $this->get_pai($card_game_text['p1']);
                } elseif ($game_type == 2) {
                    $player = $this->get_pai($card_game_text['p1']) . '+' . $this->get_pai($card_game_text['p2']) . '+' . $this->get_pai($card_game_text['p5']);
                    $banker = $this->get_pai($card_game_text['p3']) . '+' . $this->get_pai($card_game_text['p4']) . '+' . $this->get_pai($card_game_text['p6']);
                } elseif ($game_type == 3) {
                    $player = $this->get_pai($card_game_text['p1']) . '+' . $this->get_pai($card_game_text['p2']) . '+' . $this->get_pai($card_game_text['p3']) . '+' . $this->get_pai($card_game_text['p4']) . '+' . $this->get_pai($card_game_text['p5']);
                    $banker = $this->get_pai($card_game_text['p6']) . '+' . $this->get_pai($card_game_text['p7']) . '+' . $this->get_pai($card_game_text['p8']) . '+' . $this->get_pai($card_game_text['p9']) . '+' . $this->get_pai($card_game_text['p10']);
                }
            }

            if ($game_type == 2 or $game_type == 3) {
                $card_game_text['zhuang_dian'] = $card_game_text['l_msg'];
                $card_game_text['xian_dian'] = $card_game_text['f_msg'];
            }

            $data[] = [
                'card_game_id' => $item['id'],
                'room_id' => common::exchangeRoom($item['room_id']),
                'boots_number' => $item['boots_number'],
                'ju' => $item['ju'],
                'game_result' => $card_game_text['win_msg'],
                'mktime' => date('Y-m-d H:i:s', $item['mktime']),
                'zhuang_dian' => $card_game_text['zhuang_dian'],
                'xian_dian' => $card_game_text['xian_dian'],
                'player' => $banker,
                'banker' => $player
            ];
        }
        return ['code' => 200, 'msg' => '操作成功', 'data' =>$data];
    }


     /**
     * @function 获取牌型
     */
    function get_pai($pai)
    {
        $dian = $pai % 13;
        if ($dian > 10) {
            if ($dian == 11) {
                return 'J';
            } elseif ($dian == 12) {
                return 'Q';
            }
        } else {
            if ($dian == 0) {
                return 'K';
            } else {
                return $dian;
            }
        }
    }


}