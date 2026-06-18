<?php
/**
 * Created by PhpStorm.
 * User: tei
 * Date: 2019/8/22
 * Time: 10:59 AM
 */

include_once '../mysql.php';
$db = new DBPDO('127.0.0.1', 'root', '1caifeng', 'skynet9394');
//获取域名
$domain_sql = 'SELECT `domain` FROM `domain` WHERE status=0 AND type=2 limit 1';
$domain_rs = $db->select($domain_sql);
$web_domain = $domain_rs[0]['domain'];//web域名
header('location:' . $web_domain . '/index/index');