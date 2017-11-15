<?php
namespace Models\Demo;

use \Helper\QueryBuilder;

/**
 * 设备model层
 * @author oliverCJ <cgjp123@163.com>
 */
class DemoModel extends \Lib\ModelBase
{
    /**
     * 数据链接实例
     *
     * @var [type]
     */
    protected $dbMasterInstance;

    /**
     * 获取操作实例
     * @return mixed
     */
    public static function instance()
    {
        return parent::getInstance();
    }

    /**
     * 初始化
     */
    public function init()
    {
        $this->dbMasterInstance = $this->getDbInstance();
    }

    public function test()
    {
        $sql = "select * from account";
        return $this->dbMasterInstance->getRows($sql);
    }
}