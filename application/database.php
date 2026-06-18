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
$domain_config = [
    '8.137.60.11:6678'=>'shangshui9678',//jilibaba9678
    '8.137.60.11:5169'=>'shangshui9669',//正式服9669
    '8.137.60.11:9154'=>'shangshui9654',//正式服9654
    '8.137.60.11:9159'=>'shangshui9654',//正式服9654
    '8.137.60.11:6168'=>'shangshui9604',//正式服9604
    '8.137.158.109:9302'=>'shangshui9602',//长龙9602
    '8.156.93.222:9312' => 'shangshui9612',// 正式服shangshui9612
    '47.108.135.36:9305' => 'shangshui9605',// 正式服shangshui9605
    '47.108.135.36:9308' => 'shangshui9608',// 正式服shangshui9608
    '47.108.135.36:9309' => 'shangshui9609',// 正式服shangshui9609
    '47.108.135.36:9313' => 'shangshui9613',// 正式服shangshui9613
    '8.134.248.14:9305' => 'shangshui9605',// 正式服shangshui9605
    '8.134.248.14:9309' => 'shangshui9609',// 正式服shangshui9609
    '154.23.221.76:9527' => 'shangshui9612',// 正式服shangshui9612
    '8.156.93.222:9301' => 'shangshui9601',// 正式服shangshui9601
    '106.15.177.93:9302'=>'shangshui9602',//长龙9602
    '43.99.61.147:9306'=>'shangshui9606',// 正式服9606
    '43.99.61.147:9310' => 'shangshui9610',// 正式服9610
    '43.99.61.147:9311' => 'shangshui9611',// 正式服9611
    '47.100.28.255:9305' => 'shangshui9605',// 正式服shangshui9605
    '47.100.28.255:9308' => 'shangshui9608',// 正式服shangshui9608
    '47.100.28.255:9309' => 'shangshui9609',// 正式服shangshui9609
    '47.100.28.255:9313' => 'shangshui9613',// 正式服shangshui9613
    '47.109.25.25:7191' => 'yitiaolong7801',//
    '47.109.25.25:7898' => 'yitiaolong7808',// 
];

$db_config = [
    'yitiaolong7808' =>
    [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'yitiaolong7808',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'tTAbmnECGJ6EW256',
        // 端口
        'hostport' => '3803',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => false,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        // Builder类
        'builder' => '',
        // Query类
        'query' => '\\think\\db\\Query',
        // 是否需要断线重连
        'break_reconnect' => false,
        // 断线标识字符串
        'break_match_str' => [],
        'mark'=>7808
    ],
    
    'yitiaolong7801' =>
    [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'yitiaolong7801',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'tTAbmnECGJ6EW256',
        // 端口
        'hostport' => '3803',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => false,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        // Builder类
        'builder' => '',
        // Query类
        'query' => '\\think\\db\\Query',
        // 是否需要断线重连
        'break_reconnect' => false,
        // 断线标识字符串
        'break_match_str' => [],
        'mark'=>7801
    ],
    'shangshui9602' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9602',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'S6HDTh8tSCPFLtaN',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 2,
        'tid' => 492,
        'qrcode'=>1,//账号密码登陆标识,0 账号密码，1微信
        'nocode'=>1
    ],
    'shangshui9604' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9604',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'kCYDi3Q1cdKWDkQn',
        // 端口
        'hostport' => '2759',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 9604,
        'tid' => 262
    ],
    'shangshui9654' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9654',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'kCYDi3Q1cdKWDkQn',
        // 端口
        'hostport' => '2759',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'qrcode'=>1, //1可以扫码登陆 0只能账号密码登陆
        'mark' => 9654,
        'tid' => 569
    ],
    'shangshui9669' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9669',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'kCYDi3Q1cdKWDkQn',
        // 端口
        'hostport' => '2759',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'qrcode'=>1, //1可以扫码登陆 0只能账号密码登陆
        'mark' => 9669,
        'tid' => 669
    ],
    'shangshui9605' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9605',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'S6HDTh8tSCPFLtaN',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 9605,
        'tid' => 5,
        'qrcode'=>1
    ],
    'shangshui9606' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9606',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'sWFYimikST5awkRY',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 2,
        'tid' => 522,
        'qrcode'=>1
    ],
    'shangshui9608' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9608',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'S6HDTh8tSCPFLtaN',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 9608,
        'tid' => 525,
        'qrcode'=>1
    ],
    'shangshui9610' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9610',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'sWFYimikST5awkRY',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 2,
        'tid' => 1,
        'nocode'=>1
        
    ],
    'shangshui9611' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9611',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'sWFYimikST5awkRY',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 2,
        'tid' => 544,
        'qrcode'=>0,//账号密码登陆标识
        'nocode'=>1
    ], 
    'shangshui9612' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9612',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'S6HDTh8tSCPFLtaN',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 9612,
        'tid' => 584,
        'qrcode'=>1
    ],
    'shangshui9613' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9613',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'S6HDTh8tSCPFLtaN',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 9613,
        'tid' => 3,
        'qrcode'=>1,
        'nocode'=>0 
    ],
    'shangshui9633' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9633',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'S6HDTh8tSCPFLtaN',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 9633,
        'tid' => 8,
        'qrcode'=>1
    ],
    'shangshui9678' =>
    [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9678',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'kCYDi3Q1cdKWDkQn',
        // 端口
        'hostport' => '2759',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => false,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        // Builder类
        'builder' => '',
        // Query类
        'query' => '\\think\\db\\Query',
        // 是否需要断线重连
        'break_reconnect' => false,
        // 断线标识字符串
        'break_match_str' => [],
        //积分比例
        'inte_rate' => 9,
        'mark'=>9678,
        'qrcode'=>0,//账号密码登陆标识
    ],
    'shangshui9601' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'shangshui9601',
        // 用户名
        'username' => 'root',
        // 密码
        'password' => 'S6HDTh8tSCPFLtaN',
        // 端口
        'hostport' => '3603',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8mb4
        'charset' => 'utf8mb4',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 自动读取主库数据
        'read_master' => false,
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
        //标识
        'mark' => 9601,
        'tid' => 560,
        'qrcode'=>0
    ],

];

$domain = $_SERVER['HTTP_HOST'];
if($domain=='8.137.60.11' || $domain=='106.15.177.93' || $domain=='47.100.28.255' || $domain=='8.156.93.222' || $domain=='43.99.61.147' || $domain='47.109.25.25'){
    $domain = $domain.':'.$_SERVER['SERVER_PORT'];
}
return $db_config[$domain_config[$domain]];