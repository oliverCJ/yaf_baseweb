<?php
namespace Db;

use Db\Driver\Pdo;

/**
 * 数据操作基类
 * @author oliverCJ <cgjp123@163.com>
 */
class DbBase {

    public static $connections = [];

    /**
     * 获取数据库操作链接
     * @param string $instance
     *
     * @return object
     */
    public static function getInstance($instance = 'master')
    {
        $config = \Yaf\Registry::get('config')->database;
        if(!isset(self::$connections[$instance])) {
            if (empty($config->{$instance})) {
                throw new \Exception("not found connect config");
            }
            $config = $config->{$instance};
            $config = [
                'host' => $config->get('host'),
                'port' => $config->get('port'),
                'charset' => $config->get('charset'),
                'prefix' => $config->get('prefix'),
                'username' => $config->get('username'),
                'password' => $config->get('password'),
                'database' => $config->get('database'),
            ];
            static::$connections[$instance] = new Pdo($config);
        }
        return static::$connections[$instance];
    }
}