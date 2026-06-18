<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/7/24
 * Time: 3:19 PM
 */

namespace app\index\controller;


use app\index\model\GroupTable;
use app\index\model\TeamRoom;
use think\Cache;
use think\Db;
use think\Request;
use think\Session;
use app\index\model\BetsMerge;
use app\index\model\BetsLog;

class Bets
{

    /**
     * @function 获取投注表
     */
    public function getStopBet()
    {
        $request = Request::instance();
        $groupid = intval($request->post('groupid'));
        $card_game_id = intval($request->post('round'));
        $x_js = $request->post('x_js');
        if ($groupid == 0) {
            return ['code' => 500, 'msg' => '参数错误'];
        }
        $bets_time = date('m-d H:i');
        $group_info = GroupTable::getGroupInfo($groupid);
        $data = [];
        $bets = Db::name('bets_log')->alias('b')->join('user m', 'm.uid=b.uid')
            ->where('b.groupid', $groupid)->where('b.card_game_id', $card_game_id)->where('b.state', '<>', 3)
            ->field('b.card_game_id,b.odds,b.type,b.uid,b.time,m.name')->select();
        $count = [
            'name' => '合计：',
            'z' => 0,
            'x' => 0,
            'h' => 0,
            'zd' => 0,
            'xd' => 0,
            'ch' => 0,
            'xy' => 0,
        ];
        //合并下注
        foreach ($bets as $item) {
            $bets_time = date('m-d H:i', $item['time']);
            $key = 'uid' . $item['uid'];
            $type = $item['type'];
            $odds = $item['odds'];
            if (empty($data[$key])) {
                $data[$key] = [
                    'name' => $item['name'],
                    'z' => 0,
                    'x' => 0,
                    'h' => 0,
                    'zd' => 0,
                    'xd' => 0,
                    'ch' => 0,
                    'xy' => 0,
                ];
                if ($type == 1) {
                    $data[$key]['z'] += $odds;
                    $count['z'] += $odds;
                } elseif ($type == 2) {
                    $data[$key]['x'] += $odds;
                    $count['x'] += $odds;
                } elseif ($type == 3) {
                    $data[$key]['h'] += $odds;
                    $count['h'] += $odds;
                } elseif ($type == 4) {
                    $data[$key]['zd'] += $odds;
                    $count['zd'] += $odds;
                } elseif ($type == 5) {
                    $data[$key]['xd'] += $odds;
                    $count['xd'] += $odds;
                } elseif ($type == 6) {
                    $data[$key]['ch'] += $odds;
                    $count['ch'] += $odds;
                } elseif ($type == 7) {
                    $data[$key]['xy'] += $odds;
                    $count['xy'] += $odds;
                }
            } else {
                if ($type == 1) {
                    $data[$key]['z'] += $odds;
                    $count['z'] += $odds;
                } elseif ($type == 2) {
                    $data[$key]['x'] += $odds;
                    $count['x'] += $odds;
                } elseif ($type == 3) {
                    $data[$key]['h'] += $odds;
                    $count['h'] += $odds;
                } elseif ($type == 4) {
                    $data[$key]['zd'] += $odds;
                    $count['zd'] += $odds;
                } elseif ($type == 5) {
                    $data[$key]['xd'] += $odds;
                    $count['xd'] += $odds;
                } elseif ($type == 6) {
                    $data[$key]['ch'] += $odds;
                    $count['ch'] += $odds;
                } elseif ($type == 7) {
                    $data[$key]['xy'] += $odds;
                    $count['xy'] += $odds;
                }
            }
        }
        $data[] = $count;
        //组装html
        $str = '';
        foreach ($data as $item) {
            $str .= '<tr><td>';
            $str .= $item['name'];
            $str .= '</td><td class="bluecolor">';
            $str .= $item['x'];
            $str .= '</td><td class="redcolor">';
            $str .= $item['z'];
            $str .= '</td><td class="greencolor">';
            $str .= $item['h'];
            $str .= '</td><td class="bluecolor">';
            $str .= $item['xd'];
            $str .= '</td><td class="redcolor">';
            $str .= $item['zd'];
            $str .= '</td><td class="bluecolor">';
            $str .= $item['xy'];
            $str .= '</td>';
        }
        $result = [
            'StopBetTime' => $bets_time,
            'TNum' => $group_info['mark'],
            'X_JS' => $x_js,
            'strHTML' => $str
        ];
        return $result;

    }

    /**
     * @function 获取余分表
     */
    public function GetCalBet()
    {
        $request = Request::instance();
        $card_game_id = intval($request->post('round'));
        $groupid = intval($request->post('groupid'));
        if ($groupid == 0) {
            return ['code' => 500, 'msg' => '参数错误'];
        }
        $group_info = TeamRoom::getGroupInfo($groupid);
        $card_game = Db::name("card_game")->field('room_id,boots_number,ju,text,uptime')
            ->where('groupid', $groupid)->where('id', $card_game_id)->where('state', 1)->find();
        $card_game_text = json_decode($card_game['text'], true);
        //闲牌
        $Player = $this->get_pai($card_game_text['p1']) . '+' . $this->get_pai($card_game_text['p2']);
        if ($card_game_text['p5'] > 0) {
            $Player .= '+' . $this->get_pai($card_game_text['p5']);
        }
        //庄牌
        $Banker = $this->get_pai($card_game_text['p3']) . '+' . $this->get_pai($card_game_text['p4']);
        if ($card_game_text['p6'] > 0) {
            $Banker .= '+' . $this->get_pai($card_game_text['p6']);
        }

        //牌局结果
        $game_result = $card_game_text['win_msg'];
        $bets = Db::name('bets_log')->alias('b')->join('user u', 'b.uid=u.uid')->where('b.card_game_id', $card_game_id)->where('b.state', 1)
            ->field('b.card_game_id,b.win,b.uid,b.score_before,u.name')->select();
        $bets_user = [];
        foreach ($bets as $value1) {
            $uid = $value1['uid'];
            if (empty($bets_user[$uid])) {
                $bets_user[$uid]['win'] = $value1['win'];
                $bets_user[$uid]['uid'] = $uid;
                $bets_user[$uid]['name'] = $value1['name'];
                $bets_user[$uid]['score_before'] = $value1['score_before'];
            } else {
                $bets_user[$uid]['win'] = $bets_user[$uid]['win'] + $value1['win'];
                if ($value1['score_before'] > $bets_user[$uid]['score_before']) {
                    $bets_user[$uid]['score_before'] = $value1['score_before'];
                }
            }
        }
        //获取所有用户
        // $users = User::where('uid', '>', 2)->field('name,uid,score')->order('score', 'desc')->select();
        $strHtml = "";
        foreach ($bets_user as $item) {
            $win = 0;
            $score_before = 0;
            if (!empty($bets_user[$item['uid']])) {
                $win = $bets_user[$item['uid']]['win'];
                $score_before = $bets_user[$item['uid']]['score_before'];
            }
            if ($win > 0) {
                $color = 'bluecolor';
            } elseif ($win < 0) {
                $color = 'redcolor';
            } else {
                $color = 'greencolor';
            }

            $strHtml .= '<tr><td>';
            $strHtml .= $item['name'];
            $strHtml .= '</td><td class="' . $color . '">';
            $strHtml .= intval($win);
            $strHtml .= '</td><td>';
            $strHtml .= intval($win + $score_before);
            $strHtml .= '</td></tr>';
        }

        $result = [
            "Player" => $Player,
            "Banker" => $Banker,
            "Result" => $game_result,
            "X_JS" => $card_game['boots_number'] . '-' . $card_game['ju'],
            "TNum" => $group_info['mark'],
            "CalBetTime" => date('m-d H:i', $card_game['uptime']),
            "strHTML" => $strHtml
        ];
        return $result;

    }

    /**
     * @function 游戏结果
     */
    public function GetResultInfo()
    {
        $request = Request::instance();
        $card_game_id = intval($request->post('round'));
        $groupid = intval($request->post('groupid'));
        if ($card_game_id == 0 || $groupid == 0) {
            return ['code' => 500, 'msg' => '参数错误'];
        }
        //   $redis_key = 'GetResultInfo_' . $card_game_id . '_' . $groupid;
//        if (!empty(Cache::get($redis_key))) {
//            $data = json_decode(Cache::get($redis_key), true);
//            return $data;
//        } else {
        $card_game = Db::name("card_game")->field('room_id,boots_number,ju,text,uptime,zhuang,zhuang_dui,xian_dui,lucky_six')
            ->where('groupid', $groupid)->where('id', $card_game_id)->where('state', 2)->find();
        $card_game_text = json_decode($card_game['text'], true);
        $p1 = $card_game_text['p1'];
        $p2 = $card_game_text['p2'];
        $p3 = $card_game_text['p3'];
        $p4 = $card_game_text['p4'];
        $p5 = $card_game_text['p5'];
        $p6 = $card_game_text['p6'];
        $xp1 = $p1 % 13 > 9 ? 0 : ($p1 ? $p1 % 13 : 0);
        $xp2 = $p2 % 13 > 9 ? 0 : ($p2 ? $p2 % 13 : 0);
        $xp3 = $p3 % 13 > 9 ? 0 : ($p3 ? $p3 % 13 : 0);
        $xp4 = $p4 % 13 > 9 ? 0 : ($p4 ? $p4 % 13 : 0);
        $xp5 = $p5 % 13 > 9 ? 0 : ($p5 ? $p5 % 13 : 0);
        $xp6 = $p6 % 13 > 9 ? 0 : ($p6 ? $p6 % 13 : 0);
        $xpoint = ($xp1 + $xp2 + $xp5) % 10;
        $zpoint = ($xp3 + $xp4 + $xp6) % 10;
        $game_result_color = '';
        $game_result_zxh = '';
        $zhuang_win = $card_game['zhuang'];
        $zhuang_dui = $card_game['zhuang_dui'];
        $lucky_six = $card_game['lucky_six'];
        $xian_dui = $card_game['xian_dui'];
        if ($zhuang_win == 1) {
            $game_result_zxh = '庄赢';
            $game_result_color = '#b51410';
        } elseif ($zhuang_win == 2) {
            $game_result_zxh = '闲赢';
            $game_result_color = '#4a97da';
        } elseif ($zhuang_win == 3) {
            $game_result_zxh = '和';
            $game_result_color = '#57cd75';
        }
        if ($zhuang_dui > 0 && $xian_dui > 0) {
            $game_result_d = '双对';
        } else {
            if ($zhuang_dui > 0) {
                $game_result_d = '庄对';
            } elseif ($xian_dui > 0)
                $game_result_d = '闲对';
            else {
                $game_result_d = '无对';
            }
        }

        if ($lucky_six == 6) {
            $game_result_d = '幸运六12倍';
        } elseif ($lucky_six == 7) {
            $game_result_d = '幸运六20倍';
        }

        $x_img = '';
        $p1 > 0 ? $x_img .= '<img src="/front/images/poker/poker' . $p1 . '.png" /> ' : '';
        $p2 > 0 ? $x_img .= '<img src="/front/images/poker/poker' . $p2 . '.png" /> ' : '';
        $p5 > 0 ? $x_img .= '<img src="/front/images/poker/poker' . $p5 . '.png" /> ' : '';
        $z_img = '';
        $p3 > 0 ? $z_img .= '<img src="/front/images/poker/poker' . $p3 . '.png" /> ' : '';
        $p4 > 0 ? $z_img .= '<img src="/front/images/poker/poker' . $p4 . '.png" /> ' : '';
        $p6 > 0 ? $z_img .= '<img src="/front/images/poker/poker' . $p6 . '.png" /> ' : '';

        $data = [
            "X_JS" => $card_game['boots_number'] . '-' . $card_game['ju'],
            "X_Point" => $xpoint,
            "Z_Point" => $zpoint,
            "X_Img" => $x_img,
            "Z_Img" => $z_img,
            "ResultWin" => $game_result_zxh,
            "ResultWinColor" => $game_result_color,
            "ResultPair" => $game_result_d
        ];
       // Cache::set($redis_key, json_encode($data), 36000);
        return $data;
        // }
    }

    /**
     * @function 下注记录
     */
    public function GetBetRecord2()
    {
        $request = Request::instance();
        $uid_soso = intval(abs($request->post('uid')));
        $groupid = intval(abs($request->post('groupid')));
        $query_sql = new BetsMerge();
        $query_sql->field('mark,tid,id,uid,nickname,zhuang,xian,he,zhuang_dui,xian_dui,lucky_six,score_before,score_after,win,game_zhuang,game_zhuang_dui,game_xian_dui,game_lucky_six,game_lq,game_fb,game_super_he,card_game_id,mktime,room_id,boots_number,ju,state,groupinfo,groupid,lq,fb,super_he');
        $datas = $query_sql->where('uid', $uid_soso)->where('groupid', $groupid)->order('id', 'desc')->limit(30)->select();
        foreach ($datas as &$item) {
            if (empty(Session::get('uid')) || empty(Session::get('tid'))){
                Session::set('uid', $item['uid']);
                Session::set('tid', $item['tid']);
            }
            $item['mktime'] = date('m-d H:i', $item['mktime']);
            $odds_text = '';
            if ($item['zhuang'] > 0) {
                $odds_text .= '庄' . $item['zhuang'] . ' ';
            }
            if ($item['xian'] > 0) {
                $odds_text .= '闲' . $item['xian'] . ' ';
            }
            if ($item['he'] > 0) {
                $odds_text .= '和' . $item['he'] . ' ';
            }
            if ($item['zhuang_dui'] > 0) {
                $odds_text .= '庄对' . $item['zhuang_dui'] . ' ';
            }
            if ($item['xian_dui'] > 0) {
                $odds_text .= '闲对' . $item['xian_dui'] . ' ';
            }
            if ($item['lucky_six'] > 0) {
                $odds_text .= '幸运六' . $item['lucky_six'] . ' ';
            }
            if ($item['lq'] > 0) {
                $odds_text .= '龙七' . $item['lq'] . ' ';
            }
            if ($item['fb'] > 0) {
                $odds_text .= '凤八' . $item['fb'] . ' ';
            }
            if ($item['super_he'] > 0) {
                $odds_text .= '超和' . $item['super_he'] . ' ';
            }

            $item['odds_text'] = $odds_text;
            $game_result = '';
            if ($item['game_zhuang'] == 1) {
                $game_result .= '庄赢 ';
            } elseif ($item['game_zhuang'] == 2) {
                $game_result .= '闲赢 ';
            } elseif ($item['game_zhuang'] == 3) {
                $game_result .= '和 ';
            }
            if ($item['game_zhuang_dui'] > 0 && $item['game_xian_dui'] > 0) {
                $game_result .= '双对 ';
            } else {
                if ($item['game_zhuang_dui'] > 0) {
                    $game_result .= '庄对 ';
                } elseif ($item['game_xian_dui'] > 0) {
                    $game_result .= '闲对 ';
                } else {
                    $game_result .= '无对 ';
                }
            }
            if ($item['game_lucky_six'] == 6) {
                $game_result .= '幸运六12倍 ';
            } elseif ($item['game_lucky_six'] == 7) {
                $game_result .= '幸运六20倍 ';
            }
            if ($item['game_lq'] > 0) {
                $game_result .= '龙七 ';
            }
            if ($item['game_fb'] > 0) {
                $game_result .= '凤八 ';
            }
            if ($item['game_super_he'] > 0) {
                $game_result .= '超和 ';
            }
            $item['game_result'] = $game_result;
            $item['score_before'] = intval($item['score_before']);
            $item['score_after'] = intval($item['score_before'] + $item['win']);
            $item['win'] = intval($item['win']);
            if ($item['win'] > 0) {
                $item['color'] = 'blue';
            } elseif ($item['win'] < 0) {
                $item['color'] = 'red';
            } else {
                $item['color'] = 'black';
            }
        }
        return ['data' => $datas];
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


    /**
     * @function 牌局记录
     */
    public function CardGameRes()
    {
        $request = Request::instance();
        $groupid = intval($request->post('groupid'));
        if ($groupid == 0) {
            return ['code' => 500, 'msg' => '参数错误'];
        }
        $card_games = Db::name("card_game")->field('room_id,boots_number,ju,text,uptime,zhuang,zhuang_dui,xian_dui,lucky_six')
            ->where('groupid', $groupid)->where('state', 2)->order('id', 'desc')->limit(30)->select();
        $data = [];
        foreach ($card_games as $card_game) {
            $card_game_text = json_decode($card_game['text'], true);
            $point = '庄 ' . $card_game_text['zhuang_dian'] . '点  闲  ' . $card_game_text['xian_dian'] . '点';
            $game_result_zxh = ''; //游戏结果
            $zhuang_win = $card_game['zhuang'];
            $zhuang_dui = $card_game['zhuang_dui'];
            $xian_dui = $card_game['xian_dui'];
            $lucky_six = $card_game['lucky_six'];
            if ($zhuang_win == 1) {
                $game_result_zxh .= '庄赢 ';
            } elseif ($zhuang_win == 2) {
                $game_result_zxh .= '闲赢 ';
            } elseif ($zhuang_win == 3) {
                $game_result_zxh .= '和 ';
            }
            if ($zhuang_dui > 0 && $xian_dui > 0) {
                $game_result_zxh .= '双对 ';
            } else {
                if ($zhuang_dui > 0) {
                    $game_result_zxh .= '庄对 ';
                } elseif ($xian_dui > 0)
                    $game_result_zxh .= '闲对 ';
                else {
                    $game_result_zxh .= '无对 ';
                }
            }
            if ($lucky_six == 6) {
                $game_result_zxh .= '幸运六12倍';
            } elseif ($lucky_six == 7) {
                $game_result_zxh .= '幸运六20倍';
            }


            //闲牌
            $Player = $this->get_pai($card_game_text['p1']) . '+' . $this->get_pai($card_game_text['p2']);
            if ($card_game_text['p5'] > 0) {
                $Player .= '+' . $this->get_pai($card_game_text['p5']);
            }
            //庄牌
            $Banker = $this->get_pai($card_game_text['p3']) . '+' . $this->get_pai($card_game_text['p4']);
            if ($card_game_text['p6'] > 0) {
                $Banker .= '+' . $this->get_pai($card_game_text['p6']);
            }

            $game_pai = '庄  ' . $Banker . ' 闲  ' . $Player;
            $data[] = [
                "xueju" => $card_game['room_id'] . '桌' . $card_game['boots_number'] . '-' . $card_game['ju'] . '局',
                "game_result" => $game_result_zxh,
                "point" => $point,
                "game_pai" => $game_pai,
                'uptime' => date('m-d H:i', $card_game['uptime'])
            ];
        }
        return $data;
    }


    /*-------------------------龙虎------------------------------*/
    /**
     * @function 下注记录
     */
    public function lhGetBetRecord2()
    {
        $request = Request::instance();
        $uid_soso = intval(abs($request->post('uid')));
        $groupid = intval(abs($request->post('groupid')));
        $query_sql = new BetsMerge();
        $query_sql->field('id,uid,nickname,zhuang,xian,he,score_before,score_after,win,game_zhuang,card_game_id,mktime,room_id,boots_number,ju,state,groupinfo,groupid');
        $datas = $query_sql->where('uid', $uid_soso)->where('game_type', 1)->order('id', 'desc')->limit(30)->select();
        foreach ($datas as &$item) {
            $item['mktime'] = date('m-d H:i', $item['mktime']);
            $odds_text = '';
            if ($item['zhuang'] > 0) {
                $odds_text .= '龙' . $item['zhuang'] . ' ';
            }
            if ($item['xian'] > 0) {
                $odds_text .= '虎' . $item['xian'] . ' ';
            }
            if ($item['he'] > 0) {
                $odds_text .= '和' . $item['he'] . ' ';
            }
            $item['odds_text'] = $odds_text;
            $game_result = '';
            if ($item['game_zhuang'] == 1) {
                $game_result .= '龙赢 ';
            } elseif ($item['game_zhuang'] == 2) {
                $game_result .= '虎赢 ';
            } elseif ($item['game_zhuang'] == 3) {
                $game_result .= '和局 ';
            }

            $item['game_result'] = $game_result;
            $item['score_before'] = intval($item['score_before']);
            $item['score_after'] = intval($item['score_before'] + $item['win']);
            $item['win'] = intval($item['win']);
            if ($item['win'] > 0) {
                $item['color'] = 'blue';
            } elseif ($item['win'] < 0) {
                $item['color'] = 'red';
            } else {
                $item['color'] = 'black';
            }
        }
        return ['data' => $datas];
    }


    /**
     * @function 牌局记录
     */
    public function lhCardGameRes()
    {
        $request = Request::instance();
        $groupid = intval($request->post('groupid'));
        if ($groupid == 0) {
            return ['code' => 500, 'msg' => '参数错误'];
        }
        $card_games = Db::name("lh_card_game")->field('room_id,boots_number,ju,text,uptime,zhuang')
            ->where('groupid', $groupid)->where('state', 2)->order('id', 'desc')->limit(30)->select();
        $data = [];
        foreach ($card_games as $card_game) {
            $card_game_text = json_decode($card_game['text'], true);
            $point = '龙 ' . $card_game_text['zhuang_dian'] . '点  虎 ' . $card_game_text['xian_dian'] . '点';
            $game_result_zxh = ''; //游戏结果
            $zhuang_win = $card_game['zhuang'];
            if ($zhuang_win == 1) {
                $game_result_zxh = '龙赢 ';
            } elseif ($zhuang_win == 2) {
                $game_result_zxh = '虎赢 ';
            } elseif ($zhuang_win == 3) {
                $game_result_zxh = '和 ';
            }

            //龙牌
            $Player = $this->get_pai($card_game_text['p2']);
            //虎牌
            $Banker = $this->get_pai($card_game_text['p1']);

            $game_pai = '龙 ' . $Banker . ' 虎 ' . $Player;
            $data[] = [
                "xueju" => $card_game['room_id'] . '桌' . $card_game['boots_number'] . '-' . $card_game['ju'] . '局',
                "game_result" => $game_result_zxh,
                "point" => $point,
                "game_pai" => $game_pai,
                'uptime' => date('m-d H:i', $card_game['uptime'])
            ];
        }
        return $data;
    }


    /**
     * @function 游戏结果
     */
    public function lhGetResultInfo()
    {
        $request = Request::instance();
        $card_game_id = intval($request->post('round'));
        $groupid = intval($request->post('groupid'));
        if ($card_game_id == 0 || $groupid == 0) {
            return ['code' => 500, 'msg' => '参数错误'];
        }
        //   $redis_key = 'GetResultInfo_' . $card_game_id . '_' . $groupid;
//        if (!empty(Cache::get($redis_key))) {
//            $data = json_decode(Cache::get($redis_key), true);
//            return $data;
//        } else {
        $card_game = Db::name("lh_card_game")->field('room_id,boots_number,ju,text,uptime,zhuang')
            ->where('groupid', $groupid)->where('id', $card_game_id)->where('state', 2)->find();
        $card_game_text = json_decode($card_game['text'], true);
        $p1 = $card_game_text['p1'];
        $p2 = $card_game_text['p2'];
        //龙
        $zpoint = $card_game_text['zhuang_dian'];
        //虎
        $xpoint = $card_game_text['xian_dian'];

        $game_result_color = '';
        $game_result_zxh = '';
        $zhuang_win = $card_game['zhuang'];
        if ($zhuang_win == 1) {
            $game_result_zxh = '龙赢';
            $game_result_color = '#b51410';
        } elseif ($zhuang_win == 2) {
            $game_result_zxh = '虎赢';
            $game_result_color = '#4a97da';
        } elseif ($zhuang_win == 3) {
            $game_result_zxh = '和';
            $game_result_color = '#57cd75';
        }

        $x_img = '';
        $z_img = '';
        $p1 > 0 ? $z_img .= '<img src="/front/images/poker/poker' . $p1 . '.png" /> ' : '';
        $p2 > 0 ? $x_img .= '<img src="/front/images/poker/poker' . $p2 . '.png" /> ' : '';

        $data = [
            "X_JS" => $card_game['boots_number'] . '-' . $card_game['ju'],
            "Z_Point" => $zpoint,
            "X_Point" => $xpoint,
            "X_Img" => $x_img,
            "Z_Img" => $z_img,
            "ResultWin" => $game_result_zxh,
            "ResultWinColor" => $game_result_color,
        ];
        //Cache::set($redis_key, json_encode($data), 36000);
        return $data;
        // }
    }

    /*-------------------------炸金花------------------------------*/
    /**
     * @function 下注记录
     */
    public function zjhGetBetRecord2()
    {
        $request = Request::instance();
        $uid_soso = intval(abs($request->post('uid')));
        $groupid = intval(abs($request->post('groupid')));
        $query_sql = new BetsMerge();
        $query_sql->field('id,uid,nickname,zhuang,xian,he,zhuang_dui,xian_dui,lucky_six,super_he,score_before,score_after,win,game_zhuang,game_zhuang_dui,game_xian_dui,game_dui_ba,game_super_he,game_lucky_six,card_game_id,mktime,room_id,boots_number,ju,state,groupinfo,groupid');
        $datas = $query_sql->where('uid', $uid_soso)->where('game_type', 2)->order('id', 'desc')->limit(30)->select();
        foreach ($datas as &$item) {
            $item['mktime'] = date('m-d H:i', $item['mktime']);
            $odds_text = '';
            if ($item['zhuang'] > 0) {
                $odds_text .= '龙' . $item['zhuang'] . ' ';
            }
            if ($item['xian'] > 0) {
                $odds_text .= '凤' . $item['xian'] . ' ';
            }
            if ($item['he'] > 0) {
                $odds_text .= '幸运一击' . $item['he'] . ' ';
            }
            if ($item['zhuang_dui'] > 0) {
                $odds_text .= '顺子' . $item['zhuang_dui'] . ' ';
            }
            if ($item['xian_dui'] > 0) {
                $odds_text .= '同花' . $item['xian_dui'] . ' ';
            }
            if ($item['lucky_six'] > 0) {
                $odds_text .= '同花顺' . $item['lucky_six'] . ' ';
            }
            if ($item['super_he'] > 0) {
                $odds_text .= '豹子' . $item['super_he'] . ' ';
            }

            $item['odds_text'] = $odds_text;
            $game_result = '';
            if ($item['game_zhuang'] == 1) {
                $game_result .= '龙赢 ';
            } elseif ($item['game_zhuang'] == 2) {
                $game_result .= '凤赢 ';
            } elseif ($item['game_zhuang'] == 3) {
                $game_result .= '和局 ';
            }
            if ($item['game_zhuang_dui'] > 0) {
                $game_result .= '顺子 ';
            }
            if ($item['game_xian_dui'] > 0) {
                $game_result .= '同花 ';
            }
            if ($item['game_dui_ba'] > 0) {
                $game_result .= '幸运一击 ';
            }
            if ($item['game_super_he'] > 0) {
                $game_result .= '豹子 ';
            }
            if ($item['game_lucky_six'] > 0) {
                $game_result .= '同花顺 ';
            }

            $item['game_result'] = $game_result;
            $item['score_before'] = intval($item['score_before']);
            $item['score_after'] = intval($item['score_before'] + $item['win']);
            $item['win'] = intval($item['win']);
            if ($item['win'] > 0) {
                $item['color'] = 'blue';
            } elseif ($item['win'] < 0) {
                $item['color'] = 'red';
            } else {
                $item['color'] = 'black';
            }
        }
        return ['data' => $datas];
    }


    /**
     * @function 牌局记录
     */
    public function zjhCardGameRes()
    {
        $request = Request::instance();
        $groupid = intval($request->post('groupid'));
        if ($groupid == 0) {
            return ['code' => 500, 'msg' => '参数错误'];
        }
        $card_games = Db::name("zjh_card_game")->field('room_id,boots_number,ju,text,uptime,zhuang,zhuang_dui,xian_dui,super_he,dui_ba,lucky_six')
            ->where('groupid', $groupid)->where('state', 2)->order('id', 'desc')->limit(30)->select();
        $data = [];
        foreach ($card_games as $card_game) {
            $card_game_text = json_decode($card_game['text'], true);
            $point = '龙 ' . $card_game_text['l_msg'] . ' 凤 ' . $card_game_text['f_msg'];
            $game_result_zxh = ''; //游戏结果
            $zhuang_win = $card_game['zhuang'];
            if ($zhuang_win == 1) {
                $game_result_zxh .= '龙赢 ';
            } elseif ($zhuang_win == 2) {
                $game_result_zxh .= '凤赢 ';
            } elseif ($zhuang_win == 3) {
                $game_result_zxh .= '和局 ';
            }
            if ($card_game['zhuang_dui'] > 0) {
                $game_result_zxh .= '顺子 ';
            }
            if ($card_game['xian_dui'] > 0) {
                $game_result_zxh .= '同花 ';
            }
            if ($card_game['dui_ba'] > 0) {
                $game_result_zxh .= '幸运一击 ';
            }
            if ($card_game['super_he'] > 0) {
                $game_result_zxh .= '豹子 ';
            }
            if ($card_game['lucky_six'] > 0) {
                $game_result_zxh .= '同花顺 ';
            }
            //闲牌
            $Player = $this->get_pai($card_game_text['p1']) . '+' . $this->get_pai($card_game_text['p2']) . '+' . $this->get_pai($card_game_text['p5']);
            //凤牌
            $Banker = $this->get_pai($card_game_text['p3']) . '+' . $this->get_pai($card_game_text['p4']) . '+' . $this->get_pai($card_game_text['p6']);

            $game_pai = '龙 ' . $Banker . ' 凤 ' . $Player;
            $data[] = [
                "xueju" => $card_game['room_id'] . '桌' . $card_game['boots_number'] . '-' . $card_game['ju'] . '局',
                "game_result" => $game_result_zxh,
                "point" => $point,
                "game_pai" => $game_pai,
                'uptime' => date('m-d H:i', $card_game['uptime'])
            ];
        }
        return $data;
    }

    /*-----------------------牛牛-----------------------*/

    /**
     * @function 下注记录
     */
    public function nnGetBetRecord2()
    {
        $request = Request::instance();
        $uid_soso = intval(abs($request->post('uid')));
        $groupid = intval(abs($request->post('groupid')));
        $query_sql = new BetsMerge();
        $query_sql->field('id,uid,nickname,zhuang,xian,he,zhuang_dui,xian_dui,lucky_six,super_he,score_before,score_after,win,game_zhuang,game_zhuang_dui,game_xian_dui,game_dui_ba,game_super_he,game_lucky_six,card_game_id,mktime,room_id,boots_number,ju,state,groupinfo,groupid,dui_ba,niu1,niu2,niu3,niu4,niu5,niu7,niu9,game_niu1,game_niu2,game_niu3,game_niu4,game_niu5,game_niu7,game_niu9,double_hong,double_hei,game_double_hong,game_double_hei');
        $datas = $query_sql->where('uid', $uid_soso)->where('game_type', 3)->order('id', 'desc')->limit(30)->select();
        foreach ($datas as &$item) {
            $item['mktime'] = date('m-d H:i', $item['mktime']);
            $odds_text = '';
            if ($item['zhuang'] > 0) {
                $odds_text .= '红牛' . $item['zhuang'] . ' ';
            }
            if ($item['xian'] > 0) {
                $odds_text .= '黑牛' . $item['xian'] . ' ';
            }
            if ($item['he'] > 0) {
                $odds_text .= '和' . $item['he'] . ' ';
            }
            if ($item['niu1'] > 0) {
                $odds_text .= '牛一' . $item['niu1'] . ' ';
            }
            if ($item['niu2'] > 0) {
                $odds_text .= '牛二' . $item['niu2'] . ' ';
            }
            if ($item['niu3'] > 0) {
                $odds_text .= '牛三' . $item['niu3'] . ' ';
            }
            if ($item['niu4'] > 0) {
                $odds_text .= '牛四' . $item['niu4'] . ' ';
            }
            if ($item['niu5'] > 0) {
                $odds_text .= '牛五' . $item['niu5'] . ' ';
            }
            if ($item['lucky_six'] > 0) {
                $odds_text .= '牛六' . $item['lucky_six'] . ' ';
            }
            if ($item['niu7'] > 0) {
                $odds_text .= '牛七' . $item['niu7'] . ' ';
            }
            if ($item['dui_ba'] > 0) {
                $odds_text .= '牛八' . $item['dui_ba'] . ' ';
            }
            if ($item['niu9'] > 0) {
                $odds_text .= '牛九' . $item['niu9'] . ' ';
            }
            if ($item['zhuang_dui'] > 0) {
                $odds_text .= '牛牛' . $item['zhuang_dui'] . ' ';
            }
            if ($item['xian_dui'] > 0) {
                $odds_text .= '双牛牛' . $item['xian_dui'] . ' ';
            }
            if ($item['super_he'] > 0) {
                $odds_text .= '银牛/金牛/炸弹/五小牛' . $item['super_he'] . ' ';
            }
            if ($item['double_hong'] > 0) {
                $odds_text .= '翻倍红牛' . $item['double_hong'] . ' ';
            }
            if ($item['double_hei'] > 0) {
                $odds_text .= '翻倍黑牛' . $item['double_hei'] . ' ';
            }

            $item['odds_text'] = $odds_text;
            $game_result = '';
            if ($item['game_zhuang'] == 1) {
                $game_result .= '红牛赢 ';
            } elseif ($item['game_zhuang'] == 2) {
                $game_result .= '黑牛赢 ';
            } elseif ($item['game_zhuang'] == 3) {
                $game_result .= '和局 ';
            }
            if ($item['game_niu1'] > 0) {
                $game_result .= '牛一 ';
            }
            if ($item['game_niu2'] > 0) {
                $game_result .= '牛二 ';
            }
            if ($item['game_niu3'] > 0) {
                $game_result .= '牛三 ';
            }
            if ($item['game_niu4'] > 0) {
                $game_result .= '牛四 ';
            }
            if ($item['game_niu5'] > 0) {
                $game_result .= '牛五 ';
            }
            if ($item['game_lucky_six'] > 0) {
                $game_result .= '牛六 ';
            }
            if ($item['game_niu7'] > 0) {
                $game_result .= '牛七 ';
            }
            if ($item['game_dui_ba'] > 0) {
                $game_result .= '牛八 ';
            }
            if ($item['game_niu9'] > 0) {
                $game_result .= '牛九 ';
            }
            if ($item['game_zhuang_dui'] > 0) {
                $game_result .= '牛牛 ';
            }
            if ($item['game_xian_dui'] > 0) {
                $game_result .= '双牛牛 ';
            }
            if ($item['game_super_he'] > 0) {
                $game_result .= '银牛/金牛/炸弹/五小牛 ';
            }

            $item['game_result'] = $game_result;
            $item['score_before'] = intval($item['score_before']);
            $item['score_after'] = intval($item['score_before'] + $item['win']);
            $item['win'] = intval($item['win']);
            if ($item['win'] > 0) {
                $item['color'] = 'blue';
            } elseif ($item['win'] < 0) {
                $item['color'] = 'red';
            } else {
                $item['color'] = 'black';
            }
        }
        return ['data' => $datas];
    }

    /**
     * @function 牛牛牌局记录
     */
    public function nnCardGameRes()
    {
        $request = Request::instance();
        $groupid = intval($request->post('groupid'));
        if ($groupid == 0) {
            return ['code' => 500, 'msg' => '参数错误'];
        }
        $card_games = Db::name("nn_card_game")->field('room_id,boots_number,ju,text,uptime,zhuang,zhuang_dui,xian_dui,super_he,dui_ba,lucky_six,niu1,niu2,niu3,niu4,niu5,niu7,niu9')
            ->where('groupid', $groupid)->where('state', 2)->order('id', 'desc')->limit(30)->select();
        $data = [];
        foreach ($card_games as $card_game) {
            $card_game_text = json_decode($card_game['text'], true);
            $point = '红牛 ' . $card_game_text['l_msg'] . ' 黑牛 ' . $card_game_text['f_msg'];
            $game_result_zxh = ''; //游戏结果
            $zhuang_win = $card_game['zhuang'];
            if ($zhuang_win == 1) {
                $game_result_zxh .= '红牛赢 ';
            } elseif ($zhuang_win == 2) {
                $game_result_zxh .= '黑牛赢 ';
            } elseif ($zhuang_win == 3) {
                $game_result_zxh .= '和局 ';
            }

            if ($card_game['niu1'] > 0) {
                $game_result_zxh .= '牛一 ';
            }
            if ($card_game['niu2'] > 0) {
                $game_result_zxh .= '牛二 ';
            }
            if ($card_game['niu3'] > 0) {
                $game_result_zxh .= '牛三 ';
            }
            if ($card_game['niu4'] > 0) {
                $game_result_zxh .= '牛四 ';
            }
            if ($card_game['niu5'] > 0) {
                $game_result_zxh .= '牛五 ';
            }
            if ($card_game['lucky_six'] > 0) {
                $game_result_zxh .= '牛六 ';
            }
            if ($card_game['niu7'] > 0) {
                $game_result_zxh .= '牛七 ';
            }
            if ($card_game['dui_ba'] > 0) {
                $game_result_zxh .= '牛八 ';
            }
            if ($card_game['niu9'] > 0) {
                $game_result_zxh .= '牛九 ';
            }
            if ($card_game['zhuang_dui'] > 0) {
                $game_result_zxh .= '牛牛 ';
            }
            if ($card_game['xian_dui'] > 0) {
                $game_result_zxh .= '双牛牛 ';
            }
            if ($card_game['super_he'] > 0) {
                $game_result_zxh .= '银牛/金牛/炸弹/五小牛 ';
            }
            //红牛牌
            $Player = $this->get_pai($card_game_text['p1']) . '+' . $this->get_pai($card_game_text['p2']) . '+' . $this->get_pai($card_game_text['p3']) . '+' . $this->get_pai($card_game_text['p4']) . '+' . $this->get_pai($card_game_text['p5']);
            //黑牛牌
            $Banker = $this->get_pai($card_game_text['p6']) . '+' . $this->get_pai($card_game_text['p7']) . '+' . $this->get_pai($card_game_text['p8']) . '+' . $this->get_pai($card_game_text['p9']) . '+' . $this->get_pai($card_game_text['p10']);

            $game_pai = '红牛 ' . $Banker . ' 黑牛 ' . $Player;
            $data[] = [
                "xueju" => $card_game['room_id'] . '桌' . $card_game['boots_number'] . '-' . $card_game['ju'] . '局',
                "game_result" => $game_result_zxh,
                "point" => $point,
                "game_pai" => $game_pai,
                'uptime' => date('m-d H:i', $card_game['uptime'])
            ];
        }
        return $data;
    }
    
    
    /**
     * @function 获取当局闲注总额
     */
    public function getXianAll(){
        $request = Request::instance();
        $xian = intval($request->post('xian'));
        $card_game_id = intval($request->post('card_game_id'));
        $groupid = intval($request->post('groupid'));
        $xianBets =  BetsLog::where('card_game_id',$card_game_id)->where('type',2)->where('user_ai',0)->where('tourist',0)->where('state',0)->field('odds')->select();
        $room = TeamRoom::where('id',$groupid)->field('single')->find();
        $xianAll = 0;
        foreach ($xianBets as $item){
            $xianAll += $item['odds'];
        }
        if ($xian + $xianAll <=$room['single']) {
            return ['code'=>200,'msg'=>'台面单边限红'.$room['single']];
        }else{
            return ['code'=>500,'msg'=>'下注金额大于台面单边限红'.$room['single'].',本次下注无效'];
        }
    }

}