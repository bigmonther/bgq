<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2016-4-13 14:32:54 by caowenpeng , caowenpeng1990@126.com
 * 数据配置文件
 */
return [
    'key' => [
        'hanvon' => 'b7be897a-a101-4163-88c9-d914cd9ecb59'
        //,'juhe' => '9379ea56576d3d2c07c992afa3383f3b'
    ],
    'sms' => [
        'userid' => 14394,
        'account' => 'chinamatop',
        'password' => 'jyh1606_jh',
        'token' => 'qSWyynIzTJE9xMKJ' // 防止短信接口被攻击所设
    ],
    'encrypt'=>[
        'key'=>'e878caddbb44ee591f30389477f21e30a3cd4377', //实际要求要32位
        'salt'=>'d2339263f44886091b8a62ef43196f15',
    ],
    'weixin' => [
        'appID' => 'wx0cf353f9fc03aad0',
        'appsecret' => '460366e0beddcf18a21873db11a4eb1c',
        'token'=>'cwptest',
        'mchid'=>'1348060901',
        'App_mchid'=>'1350978901',
        'key'=>'33DB349F8DB955DC78FC5F84F8E5D3F8',
        'sslcert_path'=> dirname(__FILE__).'/wxcert/apiclient_cert.pem',
        'sslkey_path'=> dirname(__FILE__).'/wxcert/apiclient_key.pem',
        'notify_url'=>'/wx/wx-notify',
        'AppID'=>'wxb450720adce7295f',     //APP端的 开放平台appid
        'AppSecret'=>'1c721c93e80578c2b707358993dcd371',
        'master_ip'=>'112.74.88.156' ,   //中控服务器ip
        'master_domain'=>'m.chinamatop.com'    //中控服务器域名
    ],
    'alipay' => [
        'partner' => '2088221650569093',  //合作者身份
        'seller_id' => 'maclub@chinama.club',
        'notify_url'=>'/wx/ali-notify',
        'sslkey_path'=>dirname(__FILE__).'/alipay/cacert.pem',
        'private_key'=>  dirname(__FILE__).'/alipay/key/rsa_private_key.pem',
        'alipay_public_key'=>  dirname(__FILE__).'/alipay/key/alipay_public_key.pem',
    ],
    'umeng_android' => [
        'AppKey' => '57ba6b60e0f55ab9d9000c6b',
        'AppMasterSecret' => '3hp1turhor6aedcp2wbbgwzbystkhybl',
        'UmengMessageSecret' => 'b0ca7c93038671e440142e5263768d8e',
    ],
    'umeng_ios' => [
        'AppKey' => '57568930e0f55ab0d1002af0',
        'AppMasterSecret' => 'yfmgulh7mqcyfbbmax5wpmughka4mwv2',
//        'UmengMessageSecret' => '795ffe74e61cd9b28cba5efd98f97171',
    ],
    'redis_server'=>[
      'host'=> Cake\Core\Configure::read('debug')&&$_SERVER['SERVER_ADDR']=='127.0.0.1'?'192.168.1.7':'127.0.0.1',
      'port'=>'6379'  
    ],
    'pvlog'=>[
      'store_nums'=>100, //pvlog的redis 缓冲区数目 
    ],
];
