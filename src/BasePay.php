<?php
/**
 * Created by PhpStorm.
 * User: chunmeng_jiang
 * Date: 19-2-25
 * Time: 上午9:21
 */

namespace XiangDangDang\AllInPayTlt;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use XiangDangDang\AllInPayTlt\Exceptions\BusinessException;
use XiangDangDang\AllInPayTlt\Exceptions\ConfigException;

/**
 * 通联通
 *  https://tlt.allinpay.com/apidoc/
 * Class BasePay
 * @package XiangDangDang\BasePay
 */
abstract class BasePay
{
    protected $config;

    protected $version = '05';

    protected $dataType = 2;

    protected $level = 5;

    protected $merchantId;

    protected $userName;

    protected $userPass;

    protected $privateKeyFile;

    protected $publicKeyFile;

    protected $api_url;

    protected $req_sn;

    /**
     * BasePay constructor.
     * @param array|null
     *
     * @throws ConfigException
     */
    protected function __construct()
    {
        $this->checkAndSetConfig();
    }

    /**
     * 检查载入配置
     * @param array|null
     * @throws ConfigException
     */
    protected function checkAndSetConfig()
    {
        if (empty(config('allinpaytlt.merchant_id'))) {
            throw new ConfigException("商户号必须配置");
        }
        $this->merchantId = config('allinpaytlt.merchant_id');

        if (empty(config('allinpaytlt.api_url'))) {
            throw new ConfigException("api接口地址必须配置");
        }
        $this->api_url = config('allinpaytlt.api_url');

        if (empty(config('allinpaytlt.user_name'))) {
            throw new ConfigException('用户名必须配置');
        }
        $this->userName = config('allinpaytlt.user_name');

        if (empty(config('allinpaytlt.user_pass'))) {
            throw new ConfigException('用户密码必须配置');
        }
        $this->userPass = config('allinpaytlt.user_pass');

        if (empty(config('allinpaytlt.private_key_file'))) {
            throw new ConfigException('商户私钥必须配置');
        }
        $this->privateKeyFile = config('allinpaytlt.private_key_file');

        if (empty(config('allinpaytlt.public_key_file'))) {
            throw new ConfigException('通联公钥必须配置');
        }
        $this->publicKeyFile = config('allinpaytlt.public_key_file');

    }

    /**
     * 发起接口请求
     * @param string $trx_code
     * @param array $parameters
     * @return \SimpleXMLElement
     * @throws BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest(string $trx_code, array $parameters)
    {
        $client       = new Client();
        $this->req_sn = uniqid('tl_');
        $response     = $client->request('POST', $this->api_url . "?MERCHANT_ID=" . $this->merchantId . "&REQ_SN=" . $this->req_sn, [
            'body'   => $this->generateXmlContext($trx_code, $parameters),
            'verify' => App::environment('local') ? false : true
        ]);

        $message = (string)$response->getBody();
        if (!$this->verifySignature($message)) {
            throw new BusinessException("验签错误[res:" . $message . "][req_sn:" . $this->req_sn . "]");
        } else {
            return simplexml_load_string($message);
        }
    }

    /**
     * 生成请求报文
     *
     * @param string $trx_code
     * @param array $parameters
     * @return mixed
     */
    protected function generateXmlContext(string $trx_code, array $parameters)
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="GBK"?><AIPG></AIPG>');

        // 添加 INFO dom
        $info = $xml->addChild("INFO");
        $info->addChild('TRX_CODE', $trx_code);
        $info->addChild('VERSION', $this->version);
        $info->addChild('DATA_TYPE', $this->dataType);
        $info->addChild('LEVEL', $this->level);
        $info->addChild('MERCHANT_ID', $this->merchantId);
        $info->addChild('USER_NAME', $this->userName);
        $info->addChild('USER_PASS', $this->userPass);
        $info->addChild('REQ_SN', $this->req_sn);

        // 添加 TRANS dom
        foreach ($parameters as $nodeName => $children) {
            $pnode = $xml->addChild($nodeName);
            foreach ($children as $key => $value) {
                $pnode->addChild($key, $value);
            }
        }

        //</INFO> 前加入空行
        $xml = str_replace('</INFO>', "\n\n</INFO>", $xml->asXML());

        //数据验签
        return $this->signature($xml);
    }

    /**
     * 对报文进行签名
     *
     * @param string $message
     * @return mixed
     */
    protected function signature(string $message)
    {
        $xml     = $message;
        $message = str_replace("TRANS_DETAIL2", "TRANS_DETAIL", $message);

        $privateKey = file_get_contents($this->privateKeyFile);

        $pKeyId = openssl_pkey_get_private($privateKey, $this->userPass);
        openssl_sign($message, $signature, $pKeyId);
        openssl_free_key($pKeyId);

        $xmlDom = new \SimpleXMLElement($xml);
        $info   = $xmlDom->INFO;
        $info->addChild('SIGNED_MSG', bin2hex($signature));
        return $xmlDom->saveXML();
    }

    /**
     * 验证签名
     *
     * @param string $message
     * @return bool
     */
    protected function verifySignature(string $message)
    {
        $signature = '';

        if (preg_match('/<SIGNED_MSG>(.*)<\/SIGNED_MSG>/i', $message, $matches)) {
            $signature = $matches[1];
        }

        $xmlResponseSrc = preg_replace('/<SIGNED_MSG>.*<\/SIGNED_MSG>/i', '', $message);
        $pubKeyId       = openssl_get_publickey(file_get_contents($this->publicKeyFile));
        $flag           = (bool)openssl_verify($xmlResponseSrc, hex2bin($signature), $pubKeyId);
        openssl_free_key($pubKeyId);
        return $flag;
    }
}