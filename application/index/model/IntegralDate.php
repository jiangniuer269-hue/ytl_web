<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/6/8
 * Time: 11:37 AM
 */

namespace app\index\model;

use think\Model;

class IntegralDate extends Model
{
    /**
     * @function 查询日积分
     *
     * @param array $where
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function selectIntegralData($where = [], $field = '*')
    {
        $data = IntegralDate::where($where)->field($field)->select();
        return $data;
    }


    /**
     * @function 获取剩余积分
     */
    public static function getUserIntegral($where = [])
    {
        $data = IntegralDate::where($where)->field('integral_total')->order('id', 'desc')->limit(1)->find();
        if (!empty($data)) {
            return intval($data['integral_total']);
        } else {
            return 0;
        }

    }

    /**
     * @function 修改用户积分
     */
    public static function change($uid, $num)
    {
        $data = IntegralDate::where('uid', $uid)->field('integral_total,id,integral_exchange')->order('id', 'desc')->limit(1)->find();
        if (!empty($data)) {
            $integral = $data['integral_total'] + $num;
            $integral_exchange = $data['integral_exchange'];
            if ($num < 0) {
                $integral_exchange = $integral_exchange + $num;
            }
            $res = IntegralDate::where('id', $data['id'])->update(['integral_total' => $integral, 'integral_exchange' => $integral_exchange]);
            return $res;
        } else {
            //uid,date_time,integral,ukey,type,integral_total
            $insert_id = IntegralDate::insert([
                'uid' => $uid,
                'date_time' => date('Ymd'),
                'integral' => 0,
                'ukey' => $uid . '_' . date('Ymd'),
                'type' => 2,
                'integral_total' => $num,
                'integral_exchange' => 0
            ],false,true);
            return $insert_id;
        }
    }

}