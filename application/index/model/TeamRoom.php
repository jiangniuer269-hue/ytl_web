<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/8/28
 * Time: 7:46 PM
 */
namespace app\index\model;

use think\Model;

class TeamRoom extends Model
{
    /**
     * @function  获取单个群组信息
     */
    public static function getGroupInfo($groupid = 0, $field = 'createtime,groupname,headimage,mark,groupid,xstate,id,game_type')
    {
        $data = TeamRoom::where('groupid', $groupid)->where('xstate', 1)->field($field)->find();
        return $data;
    }

    /**
     * @function 获取所有群组信息
     *
     * @param string $field
     * @return array|false|\PDOStatement|string|Model
     */
    public static function getAllGroupInfo($field = 'createtime,groupname,headimage,mark,groupid,xstate,id,game_type')
    {
        $data = TeamRoom::where('state', 0)->field($field)->order('id', 'asc')->order('game_type', 'asc')->select();
        return $data;
    }
}