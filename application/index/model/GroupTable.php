<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/7/23
 * Time: 3:32 PM
 */

namespace app\index\model;

use think\Model;

class GroupTable extends Model
{
    /**
     * @function 获取所有群组
     */
    public static function getGroupAll()
    {
        $data = GroupTable::where('xstate', '<>', 2)->field('id,groupname,headimgurl,xstate,mark,createtime')->select();
        return $data;
    }

    /**
     * @function 获取群组信息
     */
    public static function getGroupInfo($id)
    {
        $data = GroupTable::where('xstate', '<>', 2)->where('id', $id)
            ->field('id,groupname,headimgurl,xstate,mark,createtime')->find();
        return $data;
    }
}