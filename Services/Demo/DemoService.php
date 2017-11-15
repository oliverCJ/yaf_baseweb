<?php
namespace Services\Demo;

/**
 * 注册账号服务
 * @author oliverCJ <cgjp123@163.com>
 */
class DemoService extends \Lib\ServiceBase
{
    public static function instance()
    {
        return parent::getInstance();
    }

    public function init()
    {
        // 初始化
    }

    public function test()
    {
        return \Models\Demo\DemoModel::instance()->test();
    }
}
