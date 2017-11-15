<?php
namespace Plugins;
/**
 * Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @author oliverCJ <cgjp123@163.com>
 * @see    http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 */
class TruckPlugin extends \Yaf\Plugin_Abstract
{
    /**
     * 启动路由之前触发
     * @param Yaf\Request_Abstract
     * @param Yaf\Response_Abstract
     */
    public function routerStartup(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }

    /**
     * 路由结束之后触发
     * 此时路由一定正确完成, 否则这个事件不会触发
     * @param Yaf\Request_Abstract
     * @param Yaf\Response_Abstract
     */
    public function routerShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }

    /**
     * 分发循环开始之前被触发
     * @param Yaf\Request_Abstract
     * @param Yaf\Response_Abstract
     */
    public function dispatchLoopStartup(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }

    /**
     * 分发之前触发
     * 如果在一个请求处理过程中, 发生了forward, 则这个事件会被触发多次
     * @param Yaf\Request_Abstract
     * @param Yaf\Response_Abstract
     */
    public function preDispatch(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }

    /**
     * 分发结束之后触发
     * 此时动作已经执行结束, 视图也已经渲染完成. 和preDispatch类似, 此事件也可能触发多次
     * @param Yaf\Request_Abstract
     * @param Yaf\Response_Abstract
     */
    public function postDispatch(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }

    /**
     * 分发循环结束之后触发
     * 此时表示所有的业务逻辑都已经运行完成, 但是响应还没有发送
     * @param Yaf\Request_Abstract
     * @param Yaf\Response_Abstract
     */
    public function dispatchLoopShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }
}