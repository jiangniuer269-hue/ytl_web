<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\9\15 0015
 * Time: 23:11
 */

namespace app\index\controller;

use app\index\common;
use think\Db;
use think\Request;
use app\index\model\BetsMerge;
use app\index\model\Agents;

class BetManage
{

    public function __construct()
    {
        common::checkLogin();
    }

    /**
     * @function 下注列表
     * @return \think\response\View
     */
    public function betList()
    {
        $request = Request::instance();
        $game_type = intval($request->post('game_type'));
       
        $uid = $request->post('uid');
        $data=[];
        $query_sql = BetsMerge::where('uid','=',$uid);
        if ($game_type >= 0) {
            $query_sql->where('game_type', $game_type);
        }
        $betsData = $query_sql->order('id', 'desc')->limit(200)->select();
        foreach ($betsData as $item) {
            $odds_text = '';
            $game_result_text = '';
            if ($item['zhuang'] > 0) {
                if ($item['game_type'] == 0) {
                    $odds_text .= '庄' . $item['zhuang'] . ' ';
                } elseif ($item['game_type'] == 1) {
                    $odds_text .= '龙' . $item['zhuang'] . ' ';
                } elseif ($item['game_type'] == 2) {
                    $odds_text .= '龙' . $item['zhuang'] . ' ';
                } elseif ($item['game_type'] == 3) {
                    $odds_text .= '红牛' . $item['zhuang'] . ' ';
                }
            }
            if ($item['xian'] > 0) {
                if ($item['game_type'] == 0) {
                    $odds_text .= '闲' . $item['xian'] . ' ';
                } elseif ($item['game_type'] == 1) {
                    $odds_text .= '虎' . $item['xian'] . ' ';
                } elseif ($item['game_type'] == 2) {
                    $odds_text .= '凤' . $item['xian'] . ' ';
                } elseif ($item['game_type'] == 3) {
                    $odds_text .= '黑牛' . $item['xian'] . ' ';
                }
            }
            if ($item['he'] > 0) {
                if ($item['game_type'] == 0) {
                    $odds_text .= '和' . $item['he'] . ' ';
                } elseif ($item['game_type'] == 1) {
                    $odds_text .= '和' . $item['he'] . ' ';
                } elseif ($item['game_type'] == 2) {
                    $odds_text .= '幸运一击' . $item['he'] . ' ';
                } elseif ($item['game_type'] == 3) {
                    $odds_text .= '和' . $item['he'] . ' ';
                }
            }
            if ($item['niu1'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '牛一' . $item['niu1'] . ' ';
                }
            }
            if ($item['niu2'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '牛二' . $item['niu2'] . ' ';
                }
            }
            if ($item['niu3'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '牛三' . $item['niu3'] . ' ';
                }
            }
            if ($item['niu4'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '牛四' . $item['niu4'] . ' ';
                }
            }
            if ($item['niu5'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '牛五' . $item['niu5'] . ' ';
                }
            }
            if ($item['lucky_six'] > 0) {
                if ($item['game_type'] == 0) {
                    $odds_text .= '幸运六' . $item['lucky_six'] . ' ';
                } elseif ($item['game_type'] == 2) {
                    $odds_text .= '同花顺' . $item['lucky_six'] . ' ';
                } elseif ($item['game_type'] == 3) {
                    $odds_text .= '牛六' . $item['lucky_six'] . ' ';
                }
            }
            if ($item['niu7'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '牛七' . $item['niu7'] . ' ';
                }
            }
            if ($item['dui_ba'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '牛八' . $item['dui_ba'] . ' ';
                }
            }
            if ($item['niu9'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '牛九' . $item['niu9'] . ' ';
                }
            }
            if ($item['zhuang_dui'] > 0) {
                if ($item['game_type'] == 0) {
                    $odds_text .= '庄对' . $item['zhuang_dui'] . ' ';
                } elseif ($item['game_type'] == 2) {
                    $odds_text .= '顺子' . $item['zhuang_dui'] . ' ';
                } elseif ($item['game_type'] == 3) {
                    $odds_text .= '牛牛' . $item['zhuang_dui'] . ' ';
                }
            }
            if ($item['xian_dui'] > 0) {
                if ($item['game_type'] == 0) {
                    $odds_text .= '闲对' . $item['xian_dui'] . ' ';
                } elseif ($item['game_type'] == 2) {
                    $odds_text .= '同花' . $item['xian_dui'] . ' ';
                } elseif ($item['game_type'] == 3) {
                    $odds_text .= '双牛牛' . $item['xian_dui'] . ' ';
                }
            }
            if ($item['super_he'] > 0) {
                if ($item['game_type'] == 2) {
                    $odds_text .= '豹子' . $item['super_he'];
                } elseif ($item['game_type'] == 3) {
                    $odds_text .= '银牛/金牛/炸弹/五小牛' . $item['super_he'];
                }
            }
            if ($item['double_hong'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '翻倍红牛' . $item['double_hong'];
                }
            }
            if ($item['double_hei'] > 0) {
                if ($item['game_type'] == 3) {
                    $odds_text .= '翻倍黑牛' . $item['double_hei'];
                }
            }
            if ($item['game_zhuang'] == 1) {
                if ($item['game_type'] == 0) {
                    $game_result_text .= ' 庄 ';
                } elseif ($item['game_type'] == 1) {
                    $game_result_text .= ' 龙 ';
                } elseif ($item['game_type'] == 2) {
                    $game_result_text .= ' 龙 ';
                } elseif ($item['game_type'] == 3) {
                    $double = '(平倍)';
                    if ($item['game_double_hong'] > 1) {
                        $double = '(' . $item['game_double_hong'] . '倍)';
                    }
                    $game_result_text .= ' 红牛' . $double . ' ';

                }
            } elseif ($item['game_zhuang'] == 2) {
                if ($item['game_type'] == 0) {
                    $game_result_text .= ' 闲 ';
                } elseif ($item['game_type'] == 1) {
                    $game_result_text .= ' 虎 ';
                } elseif ($item['game_type'] == 2) {
                    $game_result_text .= ' 凤 ';
                } elseif ($item['game_type'] == 3) {
                    $double = '(平倍)';
                    if ($item['game_double_hei'] > 1) {
                        $double = '(' . $item['game_double_hei'] . '倍)';
                    }
                    $game_result_text .= ' 黑牛' . $double . ' ';
                }
            } elseif ($item['game_zhuang'] == 3) {
                $game_result_text .= ' 和局 ';
            }

            if ($item['game_type'] == 0) {
                if ($item['game_zhuang_dui'] > 0 && $item['game_xian_dui'] > 0) {
                    $game_result_text .= ' 双对';
                } elseif ($item['game_zhuang_dui'] == 0 && $item['game_xian_dui'] == 0) {
                    $game_result_text .= ' 无对';
                } elseif ($item['game_zhuang_dui'] > 0) {
                    $game_result_text .= ' 庄对';
                } elseif ($item['game_xian_dui'] > 0) {
                    $game_result_text .= ' 闲对';
                }
                if ($item['game_lucky_six'] == 6) {
                    $game_result_text .= ' 幸运六12倍';
                } elseif ($item['game_lucky_six'] == 7) {
                    $game_result_text .= ' 幸运六20倍';
                }
            }
            if ($item['game_niu1'] > 0) {
                if ($item['game_type'] == 3) {
                    $game_result_text .= '牛一 ';
                }
            }
            if ($item['game_niu2'] > 0) {
                if ($item['game_type'] == 3) {
                    $game_result_text .= '牛二 ';
                }
            }
            if ($item['game_niu3'] > 0) {
                if ($item['game_type'] == 3) {
                    $game_result_text .= '牛三 ';
                }
            }
            if ($item['game_niu4'] > 0) {
                if ($item['game_type'] == 3) {
                    $game_result_text .= '牛四 ';
                }
            }
            if ($item['game_niu5'] > 0) {
                if ($item['game_type'] == 3) {
                    $game_result_text .= '牛五 ';
                }
            }
            if ($item['game_lucky_six'] > 0) {
                if ($item['game_type'] == 2) {
                    $game_result_text .= '同花顺 ';
                } elseif ($item['game_type'] == 3) {
                    $game_result_text .= '牛六 ';
                }
            }
            if ($item['game_niu7'] > 0) {
                if ($item['game_type'] == 3) {
                    $game_result_text .= '牛七 ';
                }
            }
            if ($item['game_dui_ba'] > 0) {
                if ($item['game_type'] == 2) {
                    $game_result_text .= '幸运一击 ';
                } elseif ($item['game_type'] == 3) {
                    $game_result_text .= '牛八 ';
                }
            }
            if ($item['game_niu9'] > 0) {
                if ($item['game_type'] == 3) {
                    $game_result_text .= '牛九 ';
                }
            }
            if ($item['game_zhuang_dui'] > 0) {
                if ($item['game_type'] == 2) {
                    $game_result_text .= '顺子 ';
                } elseif ($item['game_type'] == 3) {
                    $game_result_text .= '牛牛 ';
                }
            }
            if ($item['game_xian_dui'] > 0) {
                if ($item['game_type'] == 2) {
                    $game_result_text .= '同花 ';
                } elseif ($item['game_type'] == 3) {
                    $game_result_text .= '双牛牛 ';
                }
            }
            if ($item['game_super_he'] > 0) {
                if ($item['game_type'] == 2) {
                    $game_result_text .= '豹子 ';
                } elseif ($item['game_type'] == 3) {
                    $game_result_text .= '银牛/金牛/炸弹/五小牛 ';
                }
            }
            
            $data[] = [
                'id' => $item['id'],
                'uid' => $item['uid'],
                'nickname' => $item['nickname'],
                'username' => $item['username'],
                'score_before' => $item['score_before'],
                'score_after' => sprintf("%.2f", $item['score_before'] + $item['user_zx_losewin'] + $item['user_sb_losewin'] + $item['user_lucky_losewin']),
                'win' => sprintf("%.2f", $item['user_zx_losewin'] + $item['user_sb_losewin'] + $item['user_lucky_losewin']),
                'room_id' => common::exchangeRoom($item['room_id']),
                'boots_number' => $item['boots_number'],
                'ju' => $item['ju'],
                'odds_text' => $odds_text,
                'game_result_text' => $game_result_text,
                'agents_account' => $item['agents_account'],
                'agents_name' => $item['agents_name'],
                'mktime' => date('Y-m-d H:i:s', $item['mktime']),
                'xm_type' => $item['xm_type'],
                'xm_rate' => $item['xm_rate'],
                'score_before' => $item['score_before'],
                'score_after' => $item['score_after'],
                'ip' => $item['ip'],
                'share_rate' => $item['share_rate'],
                'xm' => sprintf("%.2f", ($item['user_zx_xm'] + $item['user_sb_xm'] + $item['user_lucky_xm']) / 1000),
                'usertype' => 2,
                'extra_share' => $item['extra_share'],
                'extra_share_score' => $item['extra_share_score'],
            ];
        };

        return [
            'code' => 200,
            'msg' => '操作成功',
            'data' => $data
        ];

    }

}

