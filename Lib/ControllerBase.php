<?php
namespace Lib;

/**
 * 控制器基类
 *
 * @name   BaseController
 * @author oliverCJ <cgjp123@163.com>
 * @see    http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
abstract class ControllerBase extends \Yaf\Controller_Abstract
{
    /**
     * 存储过滤后的请求参数
     * @var array
     */
    private $_requestParams = [];

    /**
     * 抽象类，子类必须实现
     * @return mixed
     */
    abstract public function beforeAction();

    /**
     * 此方法是Yaf_Controller_Abstract中的复写
     * init方法会在每个Action前执行，用作controller的初始化操作
     */
    public function init()
    {
        // 初始化视图模板引擎
        $this->initView();
        // 子类初始化
        $this->beforeAction();
        // 初始化参数
        $this->initParams();
    }

    /**
     * 初始化并过滤请求入参
     */
    private function initParams()
    {
        $getParam = \Helper\Validate::filterUnSafeCharacter($this->getRequest()->getQuery());
        $postParam = \Helper\Validate::filterUnSafeCharacter($this->getRequest()->getPost());
        $routeParam = \Helper\Validate::filterUnSafeCharacter($this->getRequest()->getParams());
        $this->_requestParams = array_merge($getParam, $postParam, $routeParam);
    }

    /**
     * 获取入参通用方法
     *
     * @param null  $name     入参名称
     * @param null  $default  默认值
     * @param array $validate 验证条件
     *
     * @return array|mixed|null
     */
    public function getParams($name = null, $default = null, $validate = ['must' => 0, 'type' => 'string'])
    {
        if (!empty($name)) {
            if (isset($this->_requestParams[$name])) {
                $default = $this->_requestParams[$name];
            }
            // 参数验证
            if (!empty($validate)) {
                $re = \Helper\Validate::checkParamIsEnough($validate, $name, $default);
                // 入参检测失败
                if (!$re) {
                    $this->checkErrorInfo();
                }
                // 验证成功后，按照要求类型进行转换，保证入参类型正确
                switch ($validate['type']) {
                    case 'integer':
                        $default = isset($default) ? intval($default) : null;
                        break;
                    case 'string':
                        $default = isset($default) ? strval($default) : null;
                        break;
                    case 'float':
                        $default = isset($default) ? floatval($default) : null;
                        break;
                    case 'boolean':
                        $default = isset($default) ? boolval($default) : null;
                        break;
                    default:
                        break;
                }
            }
            return $default;
        }
        return $this->_requestParams;
    }

    /**
     * 获取并解析错误信息
     *
     * @return [type] [description]
     */
    private function checkErrorInfo()
    {
        if (!empty(\Yaf\Registry::get('errorInfo'))) {
            throw new \Lib\ParamException(\Yaf\Registry::get('errorInfo'));
        }
    }

    /**
     * 输出JSON
     * @param array $result
     * @param int   $code
     */
    public function responseJson($result = [], $code = 0)
    {
        //response对象用作接口返回过程中的数据设置
        $response = new \Yaf\Response\Http();
        $response->setHeader('Content-Type', 'application/json;charset=utf-8');
        $response->setBody(json_encode(['code'=>$code,'data'=>$result]));
        $response->response();
    }

    public function alertMsg($msg = '')
    {
        $this->forward('error', 'errorAlert', ['errorTip' => $msg]);
    }

    public function auth()
    {
        return true;
    }
}
