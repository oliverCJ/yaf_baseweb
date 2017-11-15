<?php
namespace Lib;

/**
 * Service抽象基类
 * @author oliverCJ <cgjp123@163.com>
 */
abstract class ServiceBase
{
    /**
     * 子类实例存储
     * @var array
     */
    private static $instance = [];

    /**
     * 抽象类，类开始方法
     */
    abstract protected function init();

    /**
     * 获取类实例
     * @return mixed
     */
    protected static function getInstance()
    {
        $calledClass = get_called_class();
        if (!isset(self::$instance[$calledClass])) {
            self::$instance[$calledClass] = new static;
        }
        return self::$instance[$calledClass];
    }

    /**
     * 私有化构造函数
     */
    private function __construct()
    {
        // 子类实现，用于初始化操作
        $this->init();
    }
}
