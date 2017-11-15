<?php
/**
 * 默认控制器
 * @author oliverCJ <cgjp123@163.com>
 */
class IndexController extends \Lib\ControllerBase
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
        $this->auth();
    }

    /**
     * 默认方法
     */
    public function indexAction()
    {
        try {
            $this->controllerParam['getTest'] = $this->getParams('getTest', null, ['must' => 0, 'type' => 'string', 'max'  => 100, 'min'  => 0]);
            $this->controllerParam['postTest'] = $this->getParams('postTest', null, ['must' => 0, 'type' => 'string']);
            $this->controllerParam['routeTest'] = $this->getParams('routeTest', 1, ['must' => 0, 'type' => 'integer']);
        } catch (\Lib\ParamException $e) {
            // 参数非法提示处理
            $this->alertMsg($e());
            // 返回false,禁止Yaf渲染当前动作视图
            return false;
        }

        // 调用逻辑代码
        $result = \Services\Demo\DemoService::instance()->test();
        if ($result === false) {
            $this->alertMsg(\Yaf\Registry::get('errorInfo')['msg'] ?? '');
            return false;
        }
        // 给模板引擎赋值
        $this->getView()->assign('result', $result);
        //返回true, Yaf将自动渲染视图
        return true;
    }

    /**
     * 输出JSON,用于AJAX
     */
    public function ajaxAction()
    {
        $result = \Service\Demo\DemoHandle::instance()->test();
        $this->responseJson($result);
        // 返回FALSE，不渲染视图
        return false;
    }
}
