<?php
/**
 * Created by PhpStorm.
 * User: chunmeng_jiang
 * Date: 19-2-25
 * Time: 下午2:51
 */

namespace XiangDangDang\AllInPayTlt\Exceptions;

use Throwable;

/**
 * 配置错误异常
 *
 * Class ConfigExceptions
 * @package XiangDangDang\BasePay\Exceptions
 */
class ConfigException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("通联配置错误:" . $message, $code, $previous);
    }
}
