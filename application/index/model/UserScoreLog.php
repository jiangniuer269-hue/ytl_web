<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\9\21 0021
 * Time: 17:39
 */
namespace app\index\model;

use think\Db;
use think\Model;

class UserScoreLog extends Model
{
    /**
     * @function 积分变更记录
     *
     * @param $uid
     * @param $num
     * @return bool|mixed
     */
    public static function change($uid, $num, $orderid,$type=0)
    {
        if ($uid == 0 || $num == 0) {
            return false;
        }
        // Db::startTrans();
        try {
            //获取用户余额
            $user = Db::query("SELECT uid,score,ai,tourist FROM `user` WHERE uid={$uid}");
            if (!$user) {
                return 0;
            }
            $current = $user[0]['score'];
            //下分，用户余额不足
            if ($num < 0 && $current < abs($num)) {
                return 0;
            }

            if ($type == 0) {
                if ($num > 0) {
                    $type = 11;
                } else {
                    $type = 12;
                }
            }
            //变化后的值
            $after = $current + $num;
            $log = [
                'uid' => $uid,
                'score' => $current,
                'score_change' => $num,
                'score_after' => $after,
                'type' => $type,
                'time' => time(),
                'ukey' => $type . '_' . $orderid,
                'card_game_id' => 0,
                'orderid' => $orderid,
                'user_ai' => $user[0]['ai'],
                'tourist' => $user[0]['tourist'],
            ];
            $insert_id = UserScoreLog::insert($log, false, true);
            if ($insert_id > 0) {
                User::where('uid', $uid)->update(['score' => $after]);
                Db::commit();
                return $insert_id;
            } else {
                Db::rollback();
                return 0;
            }
        } catch (\Exception $e) {
            Db::rollback();
            return 0;
        }
    }
}