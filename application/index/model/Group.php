<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/7/23
 * Time: 2:10 PM
 */

namespace app\index\model;

use think\Model;

class Group extends Model
{
    /**
     * @function 获取内容
     */
    public static function getContend($type, $status = 0, $groupid = 0)
    {
        $data = Group::where('type', $type)->where('status', $status)->where('group_id', $groupid)
            ->field('type,contend,status')->select();
        return $data;
    }

    /**
     * @function
     */
    public static function getContendByType($type, $groupid = 0, $status = 0)
    {
        $data = Group::where('type', $type)->where('status', $status)->where('group_id', $groupid)
            ->field('type,contend,status')->find();
        return $data;
    }
}