<?php
/**
 * Created by PhpStorm.
 * User: chunmeng_jiang
 * Date: 19-2-25
 * Time: 下午10:41
 */

namespace XiangDangDang\AllInPayTlt;

/**
 * 通联支付 - 通联通
 *
 * Class TltPay
 * @package XiangDangDang\AllInPay
 */
class TltPay extends BasePay
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 通联通 pay 对象
     * @return TltPay
     * @throws Exceptions\ConfigException
     */
    public static function pay()
    {
        return new self();
    }
}
