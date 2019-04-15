<?php
/**
 * 通联支付 通联通 配置参数
 * reference location: https://tlt.allinpay.com/apidoc
 */
return [
    /** - 必填配置` - **/

    //商户号
    'merchant_id'      => env('ALLINPAY_MERCHANT_ID'),
    //用户名
    'user_name'        => env('ALLINPAY_USERNAME'),
    //用户密码
    'user_pass'        => env('ALLINPAY_USER_PASS'),
    //商户私钥
    'private_key_file' => env('ALLINPAY_PRIVATE_KEY_FILE'),
    //通联公钥
    'public_key_file'  => env('ALLINPAY_PUBLIC_KEY_FILE'),
    //接口地址
    'api_url'          => env('ALLINPAY_API_URL')
    /** - 可选配置 - **/
//    //版本
//    'version' => '05',
//    //数据类型
//    'data_type' => '2',
//    //处理级别
//    'level' => 5
];
