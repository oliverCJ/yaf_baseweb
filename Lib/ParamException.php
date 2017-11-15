<?php
namespace Lib;

/**
 * 参数效验异常
 * @author oliverCJ <cgjp123@163.com>
 */
class ParamException extends \Exception
{
    /**
     * 实例化函数
     *
     * @param array|string $message 异常信息
     * @param integer      $code    错误编码
     */
    public function __construct($message = null, $code = 0)
    {
        if (empty($message)) {
            $messageString = '';
        } else {
            if (is_array($message)) {
                $messageString = json_encode($message);
            } else {
                $messageString = $message;
            }
        }
        parent::__construct($messageString, $code);
    }

    /**
     * 以函数方式调用时触发
     *
     * @return array
     */
    public function __invoke()
    {
        $messageArray = [];
        $message = $this->getMessage();
        if (!empty($message)) {
            $messageArray = json_decode($message, true);
            if (!$messageArray) {
                $messageArray = $message;
            }
        }
        return $messageArray;
    }
}
