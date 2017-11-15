<?php
namespace Helper;

/**
 * SQL生成类
 * 用例：
 * 实例化类
 * $sqlBuilder = new QueryBuilder($tableName);
 *
 * 生成select查询语句：
 * $sqlBuilder->select('`field_1`', '`field_2`', '`field_3 AS a`')->from($tableName, 'u')->where()->and('u.field_1', ['aasas', 'sdsdsd'], ['array' => QueryBuilder::OP_IN, 'string' => QueryBuilder::OP_EQUAL]);
 * $sqlBuilder->build();
 * 生成语句：SELECT `field_1`, `field_2`, `field_3 AS a` FROM tablename AS u WHERE u.field_1 in ('aasas', 'sdsdsd');
 *
 * 生成select查询语句，包含LEFTJOIN：
 * $sqlBuilder->select('`field_1`', '`field_2`', '`field_3 AS a`', '`tablename2.field_4`')->from($tableName)->leftJoin($tableName2, '', "{$tableName2}.field_4={$tableName}.field_1")->where()->and('field_2', 'test%', ['string' => QueryBuilder::OP_LIKE]);
 * $sqlBuilder->build();
 * 生成语句：SELECT `field_1`, `field_2`, `field_3 AS a`, `tablename2.field_4` FROM tablename LEFT JOIN tablename2 ON tablename2.field_4=tableName.field_1 WHERE field_2 LIKE 'test%';
 *
 * 生成select查询语句，包含groupby,having,orderby,limit：
 * $sqlBuilder->select('`field_1`', '`field_2`', '`field_3 AS a`', 'SUM(field_4) AS a')->from($tableName, 'u')->where()->and('u.field_1', ['aasas', 'sdsdsd'], ['array' => QueryBuilder::OP_IN, 'string' => QueryBuilder::OP_EQUAL])->groupby('u.field_1')->having('a>100', 'a<200')->orderby('u.field_1')->limit(0, 100);
 * $sqlBuilder->build();
 * 生成语句：SELECT `field_1`, `field_2`, `field_3 AS a`, SUM(field_4) AS a FROM tablename AS u WHERE u.field_1 in ('aasas', 'sdsdsd') GROUP BY u.field_1 HAVING (a>100 AND a<200) ORDER BY u.field_1 LIMIT 0,100;
 *
 * 生成INSER语句
 * $sqlBuilder->insert($tablename)->insertFieldValue(['field_1', 'aaa'], ['field_2', 123]);
 * $sqlBuilder->build();
 * 生成语句：INSERT INTO tablename (`field_1`, `field_2`) VALUES ('aaa', 123);
 *
 * 生成UPDATE语句
 * $sqlBuilder->update($tablename)->updateFieldValue(['field_1', 'aaa'], ['field_2', 123])->where()->and('field_3', 'bbb', ['string' => QueryBuilder::EQUAL]);
 * $sqlBuilder->build();
 * 生成语句：UPDATE tablename SET `field_1`=aaa, `field_2`=123 WHERE `field_3`='bbb';
 *
 * 生成DELETE语句
 * $sqlBuilder->delete($tablename)->where()->and('field_1', 'aaa', ['string' => QueryBuilder::EQUAL]);
 * $sqlBuilder->build();
 * 生成语句：DELETE FROM tablename WHERE `field_1`='aaa';
 * @author chengjun <chengjun@huoyunren.com>
 */
class QueryBuilder
{
    /**
     * 操作符
     */
    const OP_EQUAL = '=';
    const OP_NOTEQUAL = '!=';
    const OP_LIKE  = 'LIKE';
    const OP_IN    = 'IN';
    const OP_LS    = '<';
    const OP_RS    = '>';
    const OP_LSE    = '<=';
    const OP_RSE    = '>=';
    const OP_ISNULL = 'IS NULL';
    const OP_ISNOTNULL = 'IS NOT NULL';
    const OP_INT_EMPTY = 'INT_EMPTY';
    const OP_STRING_EMPTY = 'STRING_EMPTY';
    const OP_INT_NOT_EMPTY = 'INT_NOT_EMPTY';
    const OP_STRING_NOT_EMPTY = 'STRING_NOT_EMPTY';

    /**
     * 条件连接符
     */
    const TAG_AND = 'AND';
    const TAG_OR = 'OR';
    const TAG_ANDOR = 'ANDOR';

    /**
     * 基础query
     * @var string
     */
    protected $queryBase = '';

    /**
     * From语句
     *
     * @var string
     */
    protected $queryFrom = '';

    /**
     * Insert或update设置语句
     * @var string
     */
    protected $querySet = '';

    /**
     * Where语句
     * @var string
     */
    protected $queryWhere = '';

    /**
     * 条件语句
     * @var string
     */
    protected $queryCondition = '';

    /**
     * 其他语句
     * @var string
     */
    protected $queryAfter = '';

    /**
     * LIMIT语句
     * @var string
     */
    protected $queryLimit = '';

    /**
     * 最终生成的语句
     * @var string
     */
    protected $query = '';

    /**
     * 最终生成的count语句
     *
     * @var string
     */
    protected $queryCount = '';

    protected $dbName;

    protected $insertSql = false;

    /**
     * 初始化
     */
    public function __construct($db)
    {
        $this->dbName = $db;
    }

    /**
     * 设置select语句，默认*
     * @param string $fields 查询字段,多个字段用逗号隔开
     *
     * @return QueryBuilder
     */
    public function select(string ...$fields): self
    {
        if (empty($fields)) {
            $fieldsString = '*';
        } else {
            $fieldsString = implode(', ', $fields);
        }
        $this->queryBase .= 'SELECT ' . $fieldsString;
        return $this;
    }

    /**
     * 设置insert语句
     * @param string $db 表名
     *
     * @return QueryBuilder
     */
    public function insert($db = ''): self
    {
        if (empty($db)) {
            $db = $this->dbName;
        }
        $this->queryBase .= 'INSERT INTO ' . $db;
        $this->insertSql = true;
        return $this;
    }

    /**
     * 设置update语句
     * @param string $db 表名
     *
     * @return QueryBuilder
     */
    public function update($db = ''): self
    {
        if (empty($db)) {
            $db = $this->dbName;
        }
        $this->queryBase .= 'UPDATE ' . $db;
        return $this;
    }

    /**
     * 设置delete语句
     * @param string $db 表名
     *
     * @return QueryBuilder
     */
    public function delete($db = ''): self
    {
        if (empty($db)) {
            $db = $this->dbName;
        }
        $this->queryBase .= 'DELETE FROM ' . $db;
        return $this;
    }

    /**
     * 设置from语句
     * @param string $db    表名
     * @param string $alias 别名
     *
     * @return QueryBuilder
     */
    public function from($db = '', $alias = ''): self
    {
        if (empty($db)) {
            $db = $this->dbName;
        }
        if (!empty($alias)) {
            $db = "{$db} AS {$alias}";
        }
        $this->queryFrom .= ' FROM ' . $db;
        return $this;
    }

    /**
     * 设置leftJoin语句
     * @param string $db         表名
     * @param string $alias      别名
     * @param string $conditions Leftjon条件，支持多个条件
     *
     * @return QueryBuilder
     */
    public function leftJoin($db, $alias = '', string ...$conditions): self
    {
        if (empty($conditions)) {
            return $this;
        }
        if (!empty($alias)) {
            $db = $db . ' AS ' . $alias;
        }
        $conditionString = implode(' AND ', $conditions);
        $this->queryFrom .= " LEFT JOIN {$db} ON ({$conditionString})";
        return $this;
    }

    /**
     * 设置innerJoin语句
     * @param string $db         表名
     * @param string $alias      别名
     * @param string $conditions Leftjon条件，支持多个条件
     *
     * @return QueryBuilder
     */
    public function innerJoin($db, $alias = '', ...$conditions): self
    {
        if (empty($conditions)) {
            return $this;
        }
        if (!empty($alias)) {
            $db = $db . ' AS ' . $alias;
        }
        $conditionString = implode(' AND ', $conditions);
        $this->queryFrom .= " INNER JOIN {$db} ON ({$conditionString})";
        return $this;
    }

    /**
     * 设置insert语句写入字段和值
     * @param array $keyValues 字段、值数组，支持多个数组
     *
     * @return QueryBuilder
     */
    public function insertFieldValue(array ...$keyValues): self
    {
        if (empty($keyValues)) {
            return $this;
        }
        $keyList = [];
        $valList = [];
        foreach ($keyValues as $keyValue) {
            if (!empty($keyValue) && count($keyValue) == 2) {
                $keyList[] = $keyValue[0];
                $valList[] = var_export($keyValue[1], true);
            }
        }
        $keyString = implode('`, `', $keyList);
        $valString = implode(',', $valList);
        $this->querySet .= " (`{$keyString}`) VALUES ({$valString})";
        unset($keyList, $valList, $keyString, $valString);
        return $this;
    }

    /**
     * 设置update语句更新字段和值
     * @param array $keyValues 字段、值数组，支持多个数组
     *
     * @return QueryBuilder
     */
    public function updateFieldValue(array ...$keyValues): self
    {
        if (empty($keyValues)) {
            return $this;
        }
        $kvList = [];
        foreach ($keyValues as $keyValue) {
            if (!empty($keyValue) && count($keyValue) == 2) {
                $kvList[] = " `{$keyValue[0]}`=" . var_export($keyValue[1], true);
            }
        }
        $this->querySet .= ' SET' . implode(', ', $kvList);
        unset($kvList);
        return $this;
    }

    /**
     * WHERE语句
     *
     * @return QueryBuilder
     */
    public function where(): self
    {
        $this->queryWhere .= ' WHERE ';
        return $this;
    }

    /**
     * 设置AND条件,例如 a AND b
     * @param string $field 字段
     * @param string $value 值，值为NULL时，当前条件忽略
     * @param array  $op    条件操作符，可设置不同的值类型使用不同的操作符，例：['array'=>OP_IN，'integer'=>OP_EQUAL],当$value为数组时，用In操作符，当$value为整型时，用等号操作符
     *
     * @return QueryBuilder
     */
    public function and($field, $value = null, $op = []): self
    {
        if (empty($op) || empty($field) || is_null($value)) {
            return $this;
        }
        $valueType = gettype($value);
        if (!array_key_exists($valueType, $op)) {
            return $this;
        }
        $opReal = strtoupper($op[$valueType]);
        $queryCondition = $this->setConditions($opReal, $field, $value);

        if ($this->queryCondition != '') {
            $this->queryCondition .= ' ' . self::TAG_AND;
        }
        $this->queryCondition .= " {$queryCondition}";
        return $this;
    }

    /**
     * 设置OR条件,例如 a OR b
     * @param string $field 字段
     * @param string $value 值，值为NULL时，当前条件忽略
     * @param array  $op    条件操作符，可设置不同的值类型使用不同的操作符，例：['array'=>OP_IN，'integer'=>OP_EQUAL],当$value为数组时，用In操作符，当$value为整型时，用等号操作符
     *
     * @return QueryBuilder
     */
    public function or($field, $value = null, $op = []): self
    {
        if (empty($op) || empty($field) || is_null($value)) {
            return $this;
        }
        $valueType = gettype($value);
        if (!array_key_exists($valueType, $op)) {
            return $this;
        }
        $opReal = strtoupper($op[$valueType]);
        $queryCondition = $this->setConditions($opReal, $field, $value);
        if ($this->queryCondition != '') {
            $this->queryCondition .= ' ' . self::TAG_OR;
        }
        $this->queryCondition .= " {$queryCondition}";
        return $this;
    }

    /**
     * 设置ANDOR条件，表示AND条件内的子OR语句，例如 a AND (b OR c) AND d
     * @param array  $conditions 条件数组，至少2个条件参与，每个条件数组示例：['field' => a, 'value' => 'b', 'op' => ['array'=>OP_IN，'integer'=>OP_EQUAL]]
     *
     * @return QueryBuilder
     */
    public function andOr(array ...$conditions): self
    {
        if (empty($conditions)) {
            return $this;
        }
        $conditionTmp = [];
        foreach ($conditions as $con) {
            if (empty($con['field']) || empty($con['op']) || is_null($con['value'])) {
                continue;
            }
            $valueType = gettype($con['value']);
            if (!array_key_exists($valueType, $con['op'])) {
                continue;
            }
            $opReal = strtoupper($con['op'][$valueType]);
            $conditionTmp[] = [
                'field' => $con['field'],
                'value' => $con['value'],
                'op'    => $opReal
            ];
        }
        if (count($conditionTmp) <= 1) {
            return $this;
        }
        $tmp = [];
        foreach ($conditionTmp as $val) {
            $tmp[] = $this->setConditions($val['op'], $val['field'], $val['value']);
        }
        if ($this->queryCondition != '') {
            $this->queryCondition .= ' ' . self::TAG_AND;
        }
        $this->queryCondition .= ' (' . implode(' ' . self::TAG_OR . ' ', $tmp) . ')';
        unset($conditionTmp, $tmp);
        return $this;
    }

    /**
     * 设置GROUP BY语句
     * @param string $field 字段，支持传入多个字段
     *
     * @return QueryBuilder
     */
    public function groupBy(string ...$field): self
    {
        if (!empty($field)) {
            $fieldString = implode(',', $field);
            $this->queryAfter .= " GROUP BY {$fieldString}";
        }
        return $this;
    }

    /**
     * 设置having语句
     * @param string $con 条件，支持传入多个条件
     *
     * @return QueryBuilder
     */
    public function having(string ...$con): self
    {
        if (!empty($con)) {
            $conString = implode(' AND ', $con);
            $this->queryAfter .= " HAVING ({$conString})";
        }
        return $this;
    }

    /**
     * 设置order by语句
     * @param string $field 字段，支持传入多个字段
     *
     * @return QueryBuilder
     */
    public function orderBy(string ...$field): self
    {
        if (!empty($field)) {
            $fieldString = implode(',', $field);
            $this->queryAfter .= " ORDER BY {$fieldString}";
        }
        return $this;
    }

    /**
     * 设置limit语句
     * @param integer $star   开始位置
     * @param integer $length 返回条数
     *
     * @return QueryBuilder
     */
    public function limit($star = 0, $length = 10): self
    {
        $this->queryLimit = " LIMIT {$star}, {$length}";
        return $this;
    }

    /**
     * 组合sql,可传入withCount同时生成count语句，用于分页
     * @param bool   withCount 是否生成count语句
     * @param string countAlias count别名
     *
     * @return string|array
     */
    public function build($withCount = false, $countAlias = 'total')
    {
        if (empty($this->queryBase) || ((empty($this->queryCondition) && !$this->insertSql))) {
            return '';
        }
        $this->query = $this->queryBase . $this->queryFrom . $this->querySet . $this->queryWhere . $this->queryCondition . $this->queryAfter . $this->queryLimit . ';';
        if ($withCount && empty($this->querySet)) {
            $this->buildCount($countAlias);
            $this->clean();
            return ['sql' => $this->query, 'count' => $this->queryCount];
        }
        $this->clean();
        return $this->query;
    }

    /**
     * 组合COUNT语句
     * @param string $countAlias 别名
     *
     * @return string
     */
    public function buildCount($countAlias = 'total')
    {
        if (empty($this->queryBase) || empty($this->queryCondition)) {
            return '';
        }
        $this->queryCount = "SELECT count(*) as {$countAlias}" . $this->queryFrom . $this->queryWhere . $this->queryCondition . $this->queryAfter . ';';
        $this->clean();
        return  $this->queryCount;
    }

    /**
     * 返回统计语句
     *
     * @return string
     */
    public function getQueryCount(): string
    {
        return $this->queryCount;
    }

    /**
     * 返回已组合的sql
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * 设置条件语句
     * @param string $op    操作符
     * @param string $field 字段
     * @param string $value 值
     *
     * @return string|bool
     */
    private function setConditions($op, $field, $value)
    {
        if (empty($op) || empty($field) || is_null($value)) {
            return false;
        }
        $queryCondition = '';
        switch ($op) {
            case self::OP_EQUAL:
            case self::OP_NOTEQUAL:
            case self::OP_LIKE:
            case self::OP_LS:
            case self::OP_LSE:
            case self::OP_RS:
            case self::OP_RSE:
                $queryCondition = "{$field} {$op} " . var_export($value, true);
                break;
            case self::OP_IN:
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if (!get_magic_quotes_gpc()) {
                            $value[$k] = var_export($v, true);
                        }
                    }
                    $value = implode(",", $value);
                } else {
                    $value = var_export($value, true);
                }
                $queryCondition = "{$field} $op ({$value})";
                break;
            case self::OP_ISNULL:
            case self::OP_ISNOTNULL:
                $queryCondition = "{$field} {$op} ";
                break;
            case self::OP_INT_EMPTY:
                $queryCondition = "({$field} IS NULL OR {$field} = 0)";
                break;
            case self::OP_STRING_EMPTY:
                $queryCondition = "({$field} IS NULL OR {$field} = '')";
                break;
            case self::OP_INT_NOT_EMPTY:
                $queryCondition = "{$field} > 0";
                break;
            case self::OP_STRING_NOT_EMPTY:
                $queryCondition = "LENGTH({$field}) > 0";
                break;
            default:
                return false;
                break;
        }
        return $queryCondition;
    }

    /**
     * 清理
     *
     * @return null
     */
    private function clean()
    {
        $this->queryBase = '';
        $this->querySet = '';
        $this->queryWhere = '';
        $this->queryCondition = '';
        $this->queryAfter = '';
    }
}
