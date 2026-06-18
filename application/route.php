<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

$prefix = 'v1/';

//登录
Route::get($prefix . 'login/login_view', 'Login/login_view');
Route::post($prefix . 'Login/DoLogin', 'Login/doLogin');
Route::get($prefix . 'Login/LoginOut', 'Login/loginOut');//退出登录
Route::get($prefix . 'admin/sendSmsCode', 'Login/sendSmsCode');//验证码
Route::post($prefix . 'Login/doLoginNew', 'Login/doLoginNew');//新版登陆
Route::post($prefix . 'Login/getQunTitle', 'Login/getQunTitle');//获取群名称
Route::post($prefix . 'Login/getRoomInfo', 'Login/getRoomInfo');//获取房间信息

//获取最新domain信息
Route::post($prefix . 'domain/info', 'Login/domainInfo');

//微信扫码登陆
Route::get($prefix . 'login/wx_login_quick', 'Login/wx_login_view_quick');

Route::get($prefix . 'login/wx_login', 'Login/wx_login_view');
// Route::get($prefix .'login/redirect', 'Login/wx_login_redirect');
Route::post($prefix . 'Login/wx_login_auth', 'Login/wx_login_auth');
Route::post($prefix . 'Login/wx_login_auth_token', 'Login/wx_login_auth_token');
Route::post($prefix . 'Login/phone_login_auth', 'Login/phone_login_auth');

Route::post($prefix . 'Login/phone_reg_auth', 'Login/phone_reg_auth');
Route::post($prefix . 'Login/phone_reg_code', 'Login/phone_reg_code');
Route::post($prefix . 'Login/phone_reg_pwd', 'Login/phone_reg_pwd');

Route::post($prefix . 'Login/phone_forgetpwd_code', 'Login/phone_forgetpwd_code');
Route::post($prefix . 'Login/phone_forget_auth', 'Login/phone_forget_auth');
Route::post($prefix . 'Login/phone_forget_pwd', 'Login/phone_forget_pwd');

Route::get($prefix . 'wxauth/getCode', 'Wxauth/getCode');
Route::get($prefix . 'wxauth/callback', 'Wxauth/callback');
Route::get($prefix . 'wxauth/getUnionid2', 'Wxauth/getUnionid2');


Route::post($prefix . 'Login/im_login_auth', 'Login/im_login_auth');
Route::post($prefix . 'Login/im_unreadMessageCount', 'Login/im_unreadMessageCount');
Route::post($prefix . 'Login/getImgCode', 'Login/getImgCode');
Route::post($prefix . 'Login/checkImgCode', 'Login/checkImgCode');
Route::post($prefix . 'Login/getLoginToken', 'Login/getLoginToken');
Route::post($prefix . 'Login/getLoginInfo', 'Login/getLoginInfo');
Route::get($prefix . 'Login/needcode', 'Login/needcode');


//首页
Route::get($prefix . 'index/index', 'Index/index');
Route::get($prefix . 'index/odds_str', 'Index/odds_str');

//群组信息
Route::get($prefix . 'Group/GetLimitWord', 'GroupInfo/limitWord');
Route::post($prefix . 'Group/QueryRecentMessage', 'GroupInfo/queryRecentMessage');
Route::get($prefix . 'Group/GroupInfo', 'GroupInfo/groupInfo');
Route::get($prefix . 'Group/Group4Info', 'GroupInfo/group4Info');
Route::get($prefix . 'Group/GroupMessageQuery', 'GroupInfo/groupMessageQuery');
Route::post($prefix . 'Group/QueryGroupUserByID', 'GroupInfo/queryGroupUserByID');

//下注
Route::post($prefix . 'Group/getXianAll', 'Bets/getXianAll');//闲注bug
Route::post($prefix . 'Group/GetStopBet', 'Bets/getStopBet');
Route::post($prefix . 'Group/GetCalBet', 'Bets/GetCalBet');
Route::post($prefix . 'Group/GetResultInfo', 'Bets/GetResultInfo');//牌局结果
Route::post($prefix . 'Group/GetBetRecord2', 'Bets/GetBetRecord2');//下注记录
Route::post($prefix . 'Group/CardGameRes', 'Bets/CardGameRes'); //牌局记录
Route::post($prefix . 'Group/lhGetBetRecord2', 'Bets/lhGetBetRecord2');//龙虎下注记录
Route::post($prefix . 'Group/lhCardGameRes', 'Bets/lhCardGameRes');//龙虎牌局结果
Route::post($prefix . 'Group/lhGetResultInfo', 'Bets/lhGetResultInfo'); //龙虎牌局记录
Route::post($prefix . 'Group/zjhGetBetRecord2', 'Bets/zjhGetBetRecord2');//炸金花下注记录
Route::post($prefix . 'Group/zjhCardGameRes', 'Bets/zjhCardGameRes'); //炸金花牌局记录
Route::post($prefix . 'Group/nnGetBetRecord2', 'Bets/nnGetBetRecord2');//牛牛下注记录
Route::post($prefix . 'Group/nnCardGameRes', 'Bets/nnCardGameRes'); // 牛牛牌局记录

Route::post($prefix . 'bet/list', 'BetManage/betList'); //下注列表web

//会员信息
Route::get($prefix . 'Friend/MyInfo', 'MemberInfo/memberInfo');
Route::post($prefix . 'Upload/UploadChatImage', 'MemberInfo/UploadChatImage');
Route::post($prefix . 'User/UpdatePwd', 'MemberInfo/UpdatePwd');
Route::post($prefix . 'User/UpdateNickName', 'MemberInfo/UpdateNickName');
Route::post($prefix . 'User/service', 'MemberInfo/service');
Route::post($prefix . 'User/getyfdetail', 'MemberInfo/getyfdetail');
Route::post($prefix . 'User/sendSms', 'MemberInfo/sendSms');
Route::post($prefix . 'User/sendCode', 'MemberInfo/sendCode');
Route::post($prefix . 'User/getChat', 'MemberInfo/getChat');
Route::post($prefix . 'user/updateUserPwd', 'MemberInfo/updateUserPwd');

//游戏
Route::post($prefix . 'game/rule', 'Game/gameRule');
Route::post($prefix . 'game/list', 'Game/gameList'); //牌局列表web


//个人积分
Route::post($prefix . 'integral/list', 'Integral/integralList');

//查询红包记录
Route::post($prefix . 'User/queryMyHbHis', 'MemberInfo/queryMyHbHis');


