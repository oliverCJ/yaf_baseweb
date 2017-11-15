<?php
namespace Helper;

/**
 * 数据验证类
 * @author oliverCJ <cgjp123@163.com>
 */
class Validate
{
    /**
     * 检查入参是否是纯数字且在限定范围内
     *
     * @param int   $data 需要检查的入参
     * @param array $scope 设定限定范围
     *
     * @return bool
     */
    public static function checkIntScope($data, array $scope = [])
    {
        // 验证是否纯数字
        if (!preg_match("#^-?[\d]+$#", (string)$data)) {
            Tools::globalSaveErrorMessage('param is a illegal integer val');
            return false;
        }
        // 根据设定作用域判定
        if (!empty($scope)) {
            if (!empty($scope['enum']) && (!is_array($scope['enum']) || !in_array($data, $scope['enum']))) {
                Tools::globalSaveErrorMessage("param is not in " . implode(',', $scope['enum']));
                return false;
            }
            if (isset($scope['max']) && (!is_int($scope['max']) || $data > $scope['max'])) {
                Tools::globalSaveErrorMessage("param is greater than {$scope['max']}");
                return false;
            }
            if (isset($scope['min']) && (!is_int($scope['min']) || $data < $scope['min'])) {
                Tools::globalSaveErrorMessage("param is less than {$scope['min']}");
                return false;
            }
        }
        return true;
    }

    /**
     * 检查浮点数
     * @param float $data
     * @param array $scope
     *
     * @return bool
     */
    public static function checkFloatScope($data, array $scope = [])
    {
        // 验证是否浮点数
        if (!preg_match("#^-?[\d]+\.[\d]+$#", (string)$data)) {
            Tools::globalSaveErrorMessage('param is a illegal float val');
            return false;
        }
        // 根据设定作用域判定
        if (!empty($scope)) {
            if (!empty($scope['enum']) && (!is_array($scope['enum']) || !in_array($data, $scope['enum']))) {
                Tools::globalSaveErrorMessage("param is not in " . implode(',', $scope['enum']));
                return false;
            }
            if (isset($scope['max']) && (!is_int($scope['max']) || $data > $scope['max'])) {
                Tools::globalSaveErrorMessage("param is greater than {$scope['max']}");
                return false;
            }
            if (isset($scope['min']) && (!is_int($scope['min']) || $data < $scope['min'])) {
                Tools::globalSaveErrorMessage("param is less than {$scope['min']}");
                return false;
            }
        }
        return true;
    }

    /**
     * 检查布尔值
     * @param boolean $data
     *
     * @return bool
     */
    public static function checkBool($data)
    {
        $boolMap = ['true', 'false', 0, 1];
        if (!in_array(strtolower((string)$data), $boolMap)) {
            Tools::globalSaveErrorMessage('param is a illegal boolean val');
            return false;
        }
        return true;
    }

    /**
     * 检查入参是否是字符串且在限定范围内
     * @param string $data  需要检查的入参
     * @param array  $scope 设定限定范围
     *
     * @return bool
     */
    public static function checkStringScope($data, array $scope = [])
    {
        // 验证是否为字符串
        if (!is_string($data)) {
            Tools::globalSaveErrorMessage("param is a illegal string val");
            return false;
        }
        if (!empty($scope)) {
            if (!empty($scope['enum']) && (!is_array($scope['enum']) || !in_array($data, $scope['enum']))) {
                Tools::globalSaveErrorMessage("param is not in " . implode(',', $scope['enum']));
                return false;
            }
            if (isset($scope['max']) && (!is_int($scope['max']) || strlen($data) > $scope['max'])) {
                Tools::globalSaveErrorMessage("The length of the param is greater than {$scope['max']}");
                return false;
            }
            if (isset($scope['min']) && (!is_int($scope['min']) || strlen($data) < $scope['min'])) {
                Tools::globalSaveErrorMessage("The length of the param is less than {$scope['min']}");
                return false;
            }
        }
        return true;
    }

    /**
     * 检查入参是否是数组且在限定范围内
     * @param array $data 需要检查的入参
     * @param array $scope 设定限定范围
     *
     * @return bool
     */
    public static function checkArrayScope($data, array $scope = [])
    {
        if (!is_array($data)) {
            Tools::globalSaveErrorMessage("param is a illegal array val");
            return false;
        }
        if (!empty($scope)) {
            if (isset($scope['max']) && (!is_int($scope['max']) || count($data) > $scope['max'])) {
                Tools::globalSaveErrorMessage("The key number of the param is greater than {$scope['max']}");
                return false;
            }
            if (isset($scope['min']) && (!is_int($scope['min']) || count($data) < $scope['min'])) {
                Tools::globalSaveErrorMessage("The key number of the param is less than {$scope['min']}");
                return false;
            }
        }
        return true;
    }

    /**
     * 检查入参是否是对象且在限定范围内
     * @param object $data  需要检查的入参
     * @param array  $scope 设定限定范围
     *
     * @return bool
     */
    public static function checkObjectScope($data, array $scope = [])
    {
        if (!is_object($data)) {
            Tools::globalSaveErrorMessage("param is a illegal object val");
            return false;
        }
        if (!empty($scope)) {
            if (isset($scope['max']) && (!is_int($scope['max']) || count($data) > $scope['max'])) {
                Tools::globalSaveErrorMessage("The member number of the param is greater than {$scope['max']}");
                return false;
            }
            if (isset($scope['min']) && (!is_int($scope['min']) || count($data) < $scope['min'])) {
                Tools::globalSaveErrorMessage("The member number of the param is less than {$scope['min']}");
                return false;
            }
        }
        return true;
    }

    /**
     * 检查是否合法的json格式
     *
     * @param array $data 需要检查的入参
     *
     * @return bool
     */
    public static function checkJson($data)
    {
        if (!json_decode($data)) {
            Tools::globalSaveErrorMessage('param is a illegal json val');
            return false;
        }
        return true;
    }

    /**
     * 验证单个入参数是否满足需求
     *
     * @param string $validate 验证条件
     * @param string $field    字段名称
     * @param multi  $data     字段内容
     *
     * @return bool
     */
    public static function checkParamIsEnough(array $validate, string $field, $data = null)
    {
        if (empty($validate) || empty($field)) {
            Tools::globalSaveErrorMessage("miss params for check");
            return false;
        }

        if (!isset($validate['must']) || !isset($validate['type'])) {
            Tools::globalSaveErrorMessage("the validate array must include 'must' and 'type' field");
            return false;
        }

        if ($validate['must'] == 1 && is_null($data)) {
            Tools::globalSaveErrorMessage("the {$field} is necessary");
            return false;
        }
        // 非必传情况下，值为NULL则忽略检查
        if (is_null($data)) {
            return true;
        }

        $scope = [];
        if (isset($validate['max'])) {
            $scope['max'] = $validate['max'];
        }
        if (isset($validate['min'])) {
            $scope['min'] = $validate['min'];
        }
        if (isset($validate['enum'])) {
            $scope['enum'] = $validate['enum'];
        }
        switch ($validate['type']) {
            case 'integer':
                if (!self::checkIntScope($data, $scope)) {
                    $msg = \Yaf_Registry::get('errorInfo')['msg'];
                    Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                    return false;
                }
                break;
            case 'float':
                if (!self::checkFloatScope($data, $scope)) {
                    $msg = \Yaf_Registry::get('errorInfo')['msg'];
                    Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                    return false;
                }
                break;
            case 'boolean':
                if (!self::checkBool($data)) {
                    $msg = \Yaf_Registry::get('errorInfo')['msg'];
                    Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                    return false;
                }
                break;
            case 'string':
                if (!self::checkStringScope($data, $scope)) {
                    $msg = \Yaf_Registry::get('errorInfo')['msg'];
                    Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                    return false;
                }
                break;
            case 'json':
                if (!self::checkJson($data)) {
                    $msg = \Yaf_Registry::get('errorInfo')['msg'];
                    Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                    return false;
                }
                break;
            /*
             * 去掉检测数组和对象
             * 应当尽量避免把数组或对象当做请求参数,如果必须传入复杂类型，建议使用json类型代替
            case 'array':
                if (!self::checkArrayScope($data, $scope)) {
                    $msg = \Yaf_Registry::get('errorInfo')['msg'];
                    Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                    return false;
                }
                break;
            case 'object':
                if (!self::checkObjectScope($data, $scope)) {
                    $msg = \Yaf_Registry::get('errorInfo')['msg'];
                    Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                    return false;
                }
                break;
            */
            case '':
            default:
                Tools::globalSaveErrorMessage("the expect setting of the type is wrong");
                return false;
                break;
        }

        return true;
    }

    /**
     * 批量验证参数是否满足预期
     * @param array $fields 需要进行预期判断的字段
     * @param array $data   需要检查的入参
     *
     * @return bool
     */
    public static function checkParamsIsEnough(array $fields = [], array $data = [])
    {
        if (empty($fields) || empty($data)) {
            Tools::globalSaveErrorMessage("miss params for check");
            return false;
        }
        foreach ($data as $key => $val) {
            if (!isset($fields[$key])) {
                // 存在多余字段
                Tools::globalSaveErrorMessage("the {$key} is unnecessary");
                return false;
            }
        }
        foreach ($fields as $field => $set) {
            // 检查是否有必传字段没有传入
            if (!isset($data[$field]) && $set['must'] == 1) {
                Tools::globalSaveErrorMessage("the {$field} is necessary");
                return false;
            }
            if (isset($data[$field])) {
                // 检查类型
                if (isset($set['type'])) {
                    // 设置限定值
                    $scope = [];
                    if (isset($set['max'])) {
                        $scope['max'] = $set['max'];
                    }
                    if (isset($set['min'])) {
                        $scope['min'] = $set['min'];
                    }
                    if (isset($set['enum'])) {
                        $scope['enum'] = $set['enum'];
                    }
                    // 检查类型
                    switch ($set['type']) {
                        case 'integer':
                            if (!self::checkIntScope($data[$field], $scope)) {
                                $msg = \Yaf_Registry::get('errorInfo')['msg'];
                                Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                                return false;
                            }
                            break;
                        case 'float':
                            if (!self::checkFloatScope($data[$field], $scope)) {
                                $msg = \Yaf_Registry::get('errorInfo')['msg'];
                                Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                                return false;
                            }
                            break;
                        case 'boolean':
                            if (!self::checkBool($data[$field])) {
                                $msg = \Yaf_Registry::get('errorInfo')['msg'];
                                Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                                return false;
                            }
                            break;
                        case 'string':
                            if (!self::checkStringScope($data[$field], $scope)) {
                                $msg = \Yaf_Registry::get('errorInfo')['msg'];
                                Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                                return false;
                            }
                            break;
                        case 'json':
                            if (!self::checkJson($data)) {
                                $msg = \Yaf_Registry::get('errorInfo')['msg'];
                                Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                                return false;
                            }
                            break;
                        /*
                         * 去掉检测数组和对象
                         * 应当尽量避免把数组或对象当做请求参数,如果必须传入复杂类型，建议使用json类型代替
                        case 'array':
                            if (!self::checkArrayScope($data[$field], $scope)) {
                                $msg = \Yaf_Registry::get('errorInfo')['msg'];
                                Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                                return false;
                            }
                            break;
                        case 'object':
                            if (!self::checkObjectScope($data[$field], $scope)) {
                                $msg = \Yaf_Registry::get('errorInfo')['msg'];
                                Tools::globalSaveErrorMessage($msg .  ", check param：{$field}");
                                return false;
                            }
                            break;
                        */
                        case '':
                        default:
                            Tools::globalSaveErrorMessage("the expect setting of the type is wrong");
                            return false;
                            break;
                    }
                }
            }
        }
        return true;
    }

    /**
     * XSS字符过滤方法
     * @param array $data
     *
     * @return array|bool
     */
    public static function filterUnSafeCharacter($data = [])
    {
        if (empty($data)) {
            return [];
        }
        foreach ($data as $key => &$val) {
            if (is_array($val)) {
                $val = self::filterUnSafeCharacter($val);
            } else {
                // Xss过滤
                $val = htmlspecialchars(strip_tags($val), ENT_NOQUOTES);
                // Sql注入过滤
                $val = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x19]|[#!]|union|select|update|insert/i', '', $val);
            }
        }
        return $data;
    }
}
