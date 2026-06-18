<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/7/23
 * Time: 1:41 PM
 */

namespace app\index\model;

use think\Model;

class User extends Model
{

    /**
     * @function 获取会员信息
     */
    public static  function info($uid)
    {
        $data = User::where('uid',$uid)->where('status',0)
            ->field('username,name,head,no_say,status,uid,score')->find();
        return $data;
    }
}
