<?php
namespace Lib;

/**
 * Model层抽象基类
 * @author oliverCJ <cgjp123@163.com>
 */
abstract class ModelBase
{
    /**
     * 子类实例存储
     * @var array
     */
    private static $instance = [];

    /**
     * 主库链接实例
     * @var object
     */
    protected $dbMasterInstance;

    /**
     * 从库链接实例
     * @var object
     */
    protected $dbSlaveInstance;

    /**
     * 是否启动了事务
     * @var boolean
     */
    private $_useTransaction = false;

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

    /**
     * 获取数据库操作实例
     * @param string $targetdb 目标数据库master主库，slave从库
     *
     * @return mixed
     */
    public function getDbInstance($targetdb = 'master')
    {
        return \Db\DbBase::getInstance($targetdb);
    }

    /**
     * 启动事务
     * @return [type] [description]
     */
    public function startTransaction()
    {
        if ($this->dbMasterInstance instanceof \Db\Driver\Pdo) {
            if (!$this->_useTransaction) {
                $this->dbMasterInstance->beginTransaction();
                $this->_useTransaction = true;
                return true;
            }
        }
        return false;
    }

    /**
     * 回滚事务
     * @return [type] [description]
     */
    public function rollBackTransaction()
    {
        if ($this->dbMasterInstance instanceof \Db\Driver\Pdo) {
            if ($this->_useTransaction) {
                $this->dbMasterInstance->rollBack();
                $this->_useTransaction = false;
                return true;
            }
        }

        return false;
    }

    /**
     * 提交事务
     * @return [type] [description]
     */
    public function commitTransaction()
    {
        if ($this->dbMasterInstance instanceof \Db\Driver\Pdo) {
            if ($this->_useTransaction) {
                $this->dbMasterInstance->commit();
                $this->_useTransaction = false;
                return true;
            }
        }
        return false;
    }
}
