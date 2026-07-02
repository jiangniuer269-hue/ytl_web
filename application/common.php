<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
namespace app\index;

use think\Cookie;
use think\Session;
use app\index\model\User;
use app\index\model\Domain;
use OSS\OssClient;
use OSS\Core\OssException;

class common
{
    /**
     * @function 验证登录
     *
     * @return bool|mixed
     */
    public static function checkLogin()
    {
        if (!empty(Cookie::get('uid'))) {
            $uid = Cookie::get('uid');
            $member = User::where('uid', $uid)->field('uid,status')->find();
            if (!empty($member) && $member['status'] == 0) {
                //更新session时间
                Cookie::set('uid', $uid, 2592000);
                return $uid;
            } else {
                return ['code' => 400, 'msg' => '未登录'];
                // header('Location:' . url('/login/login_view'));
                //exit;
            }
        } else {
            return ['code' => 400, 'msg' => '未登录'];
            // header('Location:' . url('/login/login_view'));
            //exit;
        }
    }

    /**
     * @function http请求
     *
     * @param $url
     * @param null $data
     * @param int $secound
     * @return mixed
     */
    public static function http_request($url, $data = null, $secound = 0, $header = ['X-Domain:2653165'])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if ($secound > 0) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $secound); //设置超时时间
        }

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /**
     * @function 获取上月开始、结束日期
     */
    public static function getLastMonth()
    {
        $m_statetime = date('Y-m-01 00:00:00', strtotime('-1 month'));
        $m_endtime = date("Y-m-d 23:59:59", strtotime(-date('d') . 'day'));
        return [$m_statetime, $m_endtime];
    }

    /**
     * @return array 获取当月开始，结束日期
     */
    public static function getCurMonth()
    {
        $m_statetime = date('Y-m-01 00:00:00', strtotime(date("Y-m-d")));
        $m_endtime = date('Y-m-d 23:59:59', strtotime("$m_statetime +1 month -1 day"));
        return [$m_statetime, $m_endtime];
    }

    /**
     * @function 昨日开始，结束日期
     *
     * @return array
     */
    public static function getYesterday()
    {
        $m_statetime = date('Y-m-d 00:00:00', time() - 86400);
        $m_endtime = date("Y-m-d 23:59:59", time() - 86400);
        return [$m_statetime, $m_endtime];
    }

    /**
     * @function 今日开始，结束时间
     *
     * @return array
     */
    public static function getToday()
    {
        $m_statetime = date('Y-m-01 00:00:00', time());
        $m_endtime = date("Y-m-d 23:59:59", time());
        return [$m_statetime, $m_endtime];
    }

    /**
     * @function 判断文件夹是否存在不存在则创建
     *
     * @param $dir
     * @param int $mode
     * @return bool
     */
    public static function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        if (!mkdirs(dirname($dir), $mode)) return FALSE;
        return @mkdir($dir, $mode);
    }

    /**
     * 远程登陆服务器
     */
    public static function remote_login($cmd = '', $config = [])
    {
        if (empty($config)) {
            return false;
        }

        // 连接服务器
        $connection = ssh2_connect($config['host'], $config['port']);

        // 身份验证
        ssh2_auth_password($connection, $config['username'], $config['password']);

        // 执行命令
        $ret = ssh2_exec($connection, $cmd);

        // 获取结果
        stream_set_blocking($ret, true);

        // 返回结果
        return stream_get_contents($ret);
    }


    /**
     * @function 加密函数
     *
     * @param $txt
     * @param string $key
     * @return string
     */
    public static function string2secret($txt, $key = 'www.jb.net')
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $nh = rand(0, 64);
        $ch = $chars[$nh];
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = base64_encode($txt);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $txt[$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }
        return urlencode($ch . $tmp);
    }

    /**
     * @function 解密函数
     *
     * @param $txt
     * @param string $key
     * @return string
     */
    public static function secret2string($txt, $key = 'www.jb.net')
    {
        $txt = urldecode($txt);
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $ch = $txt[0];
        $nh = strpos($chars, $ch);
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = substr($txt, 1);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) $j += 64;
            $tmp .= $chars[$j];
        }
        return base64_decode($tmp);
    }

    /**
     * @function 实例化redis
     */
    public static function initRedis()
    {
        $redis = new \Redis();
        //连接
        $redis->connect('tl-wt-data.122672.com', 6380);
        $redis->auth("12345678"); //密码验证
        return $redis;
    }


     /**
     * @function 类型转换
     * @type 类型,110取消下注，122领取红包，111牌局结算，122重新结算,11上分，12下分,13下分删除,20手动上分，21手动下分
     */
    public static function exchangeType($type)
    {
        $typeArr = [
            1 => '下注',
            2 => '下注',
            3 => '下注',
            4 => '下注',
            5 => '下注',
            7 => '下注',
            15 => '下注',
            16 => '下注',
            17 => '下注',
            11 => '上分',
            12 => '下分',
            13 => '下分删除',
            20 => '手动上分',
            21 => '手动下分',
            100 => '码粮结算',
            110 => '取消下注',
            111 => '牌局结算',
            112 => '牌局取消',
            121 => '重新结算',
            122 => '领取红包',
            1100 => '代理上分',
            1200 => '代理下分'
        ];
        if (empty($typeArr[$type])) {
            return '下注';
        }
        return $typeArr[$type];
    }
    /**
     * @function  更改桌号
     */
    public static function exchangeRoom($room_id)
    {
        switch ($room_id) {
            case 71:
                return 'V1';
                break;
            case 72:
                return 'V2';
                break;
            case 73:
                return 'V3';
                break;
            case 74:
                return 'V4';
                break;
            case 75:
                return 'V5';
                break;
            case 76:
                return 'V6';
                break;
            case 77:
                return 'V7';
                break;
            case 78:
                return 'P17';
                break;
            case 79:
                return 'V8';
                break;
            case 'v1':
                return 71;
                break;
            case 'V1':
                return 71;
                break;
            case 'v2':
                return 72;
                break;
            case 'V2':
                return 72;
                break;
            case 'v3':
                return 73;
                break;
            case 'V3':
                return 73;
                break;
            case 'v4':
                return 74;
                break;
            case 'V4':
                return 74;
                break;
            case 'v5':
                return 75;
                break;
            case 'V5':
                return 75;
                break;
            case 'v6':
                return 76;
                break;
            case 'V6':
                return 76;
                break;
            case 'v7':
                return 77;
                break;
            case 'V7':
                return 77;
                break;
            case 'P17':
                return 78;
                break;
            case 'p17':
                return 78;
                break;
            case 'V8':
                return 79;
                break;
            case 'v8':
                return 79;
                break;
        }
    }


     /**
     * @function 发送短信验证码
     */
    public static function sendSMS($code, $phone)
    {
        $accountSid = '781cbb65340e2ff621d6499fcc9b2402';
        $token = 'd23abca4fc0506c5bdac30febd0b88d7';
        $timestamp = self::msectime();
        $url = 'https://openapi.miaodiyun.com/distributor/sendSMS';
        $postData = [
            'accountSid' => $accountSid,//开发者账号
            // 'smsContent' => '【银钻娱乐】您的验证码为{1}，请于{2}分钟内正确输入，如非本人操作，请忽略此短信。',//短信内容
            'templateid' => '248794',//模板ID
            'to' => (string)$phone,//手机号
            'param' => "$code,2",
            'timestamp' => $timestamp,
            'sig' => md5($accountSid . $token . $timestamp),
        ];
        $res = self::http_request($url, $postData, 5);
        return $res;
    }


    /**
     * @function 返回当前毫秒时间戳
     */
    public static function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    /**
     * @function oss 上传
     *
     * @param $accessKeyId
     * @param $accessKeySecret
     * @param $endpoint
     * @param $bucket
     * @param $object
     * @param $content
     * @return mixed
     */
    public static function moveOss($object, $content)
    {       
        
        $accessKeyId = 'LTAI5t69YqvT74ChcUJ4ogeh';
        $accessKeySecret = 'VNp2ZNO7NB5KVV1nvNFkmtIcSlU0uJ';
        $endpoint = 'oss-cn-beijing.aliyuncs.com';
        $bucket = 'shangshui-image-168';
        $options = array(
            // 可以参看https://help.aliyun.com/document_detail/31859.html?spm=a2c4g.11186623.2.10.481e2b72ggLS4F#concept-lkf-swy-5db
            OssClient::OSS_CONTENT_TYPE => 'image/jpg',  // 简单的举例使用 要根据实际的图片类型 可以看下MimeTypes::getMimetype()里的
        );
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $res = $ossClient->putObject($bucket, $object, $content, $options);
        } catch (OssException $e) {
            print $e->getMessage();
        }
        return $res['info']['url'];
    }
    

    public static function getChatSystemDomain(){
        $sys_info = Domain::where('type', 28)->field('domain')->find();
        $url = $sys_info['domain'] . '/api/domain/business';
        $file_contents = self::http_request($url, null, 3);
        $resultJson = json_decode($file_contents, 1);
        return  ['domain' => $resultJson['url']];
       
    }
    
    /**
     * @function 检测微信浏览器
     *
     */
    
    public static function is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
    
   
    /**
     * @function 生成随机字符
     */
    public static function GetRandStr($length){
        //字符组合
        $str = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $len = strlen($str)-1;
        $randstr = '';
        for($i=0;$i<$length;$i++) {
            $num=mt_rand(0,$len);
            $randstr .=" ".$str[$num];
        }
        
        return $randstr;
    }
    
    /**
     * @function 端口对应的tid
     */
    public static function GetPortTid(){
        return  ['7191'=>191,'7898'=>563,'7292'=>802];
    }
    

        /**
         * @param $data
         * @param $key
         * @param $iv
         * @return false|string
         */
        public static function decrypt($data, $key, $iv)
        {
            $data = base64_decode($data);
            $key = md5($key);
            $iv = substr(md5($iv), 0, 8);        //取前8位
            
            $decrypted = openssl_decrypt($data, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, $iv);
            return $decrypted;
        }
        
        /**
         * @param $str
         * @param $key
         * @param $iv
         * @return string
         */
        public static function encrypt($str, $key, $iv)
        {
            $key = md5($key);
            $iv = substr(md5($iv), 0, 8);        //取前8位
            
            $data = base64_encode(openssl_encrypt($str, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, $iv));
            return $data;
        }
    
}