<?php
namespace Db\Driver;
/**
 * PDO数据操作层
 */
class Pdo {

    protected $dbConnect;

    public function __construct($dbConfig)
    {
        if (empty($dbConfig)) {
            throw new \Exception("not found connect config", 1);
        }
        try {
        // 建立主库链接
            $dsn = "mysql:host={$dbConfig['host']}; port={$dbConfig['port']}; dbname={$dbConfig['database']}";
            $this->dbConnect = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], array(\PDO::ATTR_PERSISTENT => true));
            $this->dbConnect->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->dbConnect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->dbConnect->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            $this->dbConnect->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $this->dbConnect->exec("SET NAMES {$dbConfig['charset']}");
        } catch (\Exception $e) {
            throw new \Exception("mysql connect failed", 1);
        }
    }

    public function getRows($sql){
        return $this->dbConnect->query($sql)->fetchAll();
    }

    public function getOne($sql)
    {
        return $this->dbConnect->query($sql)->fetch();
    }

    public function exec($sql)
    {
        return $this->dbConnect->exec($sql);
    }
}