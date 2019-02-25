<?php
/**
 * Created by PhpStorm.
 * User: chunmeng_jiang
 * Date: 19-2-25
 * Time: 下午5:54
 */

namespace XiangDangDang\AllInPayTlt\Exceptions;


use Throwable;

class BusinessException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("通联业务异常:" . $message, $code, $previous);
    }
}