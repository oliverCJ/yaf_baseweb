<?php
/**
 * 默认控制器
 * @author oliverCJ <cgjp123@163.com>
 */
class ErrorController extends \Lib\ControllerBase
{
    /**
     * 存储controller动作执行过程中产生的各种参数
     * @var array
     */
    public $controllerParam = [];

    /**
     * 此方法是Yaf_Controller_Abstract中的复写
     * init方法会在每个Action前执行，用作controller的初始化操作
     */
    public function beforeAction()
    {
    }

    /**
     * 捕获异常
     *
     * @param  [type] $exception [description]
     *
     * @return [type]            [description]
     */
    public function errorAction($exception)
    {
        $this->getView()->assign('exception', $exception);
        return true;
    }

    /**
     * 错误提示
     *
     * @return [type] [description]
     */
    public function errorAlertAction()
    {
        $msg = $this->getParams('errorTip');
        $this->getView()->assign('errorMsg', $msg);
        return true;
    }
}
