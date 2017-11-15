<?php
/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用
 * 这些方法, 都接受一个参数:\Yaf\Dispatcher $dispatcher, 调用的次序, 和申明的次序相同
 * @author oliverCJ <cgjp123@163.com>
 * @see    http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 */
class Bootstrap extends \Yaf\Bootstrap_Abstract
{
    /**
     * 初始化框架，配置等
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initFramework(\Yaf\Dispatcher $dispatcher)
    {
        $config = [];
        // 读取配置
        $configPattern = glob(APPLICATION_PATH . '/conf/*.ini');
        foreach ($configPattern as $configFile) {
            $tmpConfig = (new Yaf\Config\Ini($configFile))->toArray();
            if (basename($configFile) == 'application.ini') {
                $tmpConfig = $tmpConfig['product'];
            }
            if (!empty($tmpConfig)) {
                $config = array_merge($config, $tmpConfig);
            }
        }
        \Yaf\Registry::set('config', new Yaf\Config\Simple($config));
    }

    /**
     * 初始化按命名空间自动加载
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initAutoload(\Yaf\Dispatcher $dispatcher)
    {
        if (empty(\Yaf\Registry::get('config')->application['ext_dir'])) {
            return true;
        }
        $loadPath = \Yaf\Registry::get('config')->application['ext_dir'];
        spl_autoload_register(function ($className) use ($loadPath) {
            if (class_exists($className)) {
                return true;
            }
            $tmpPath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
            $classNameArray = explode(DIRECTORY_SEPARATOR, $tmpPath);
            if (isset($classNameArray[1])) {
                $fileName = ucfirst(array_pop($classNameArray));
                $nameSpacePath = lcfirst(array_shift($classNameArray));
                $classPath = '';
                if (!empty($classNameArray)) {
                    $classNameArray = array_map(function ($val) {
                        return ucfirst($val);
                    }, $classNameArray);
                    $classPath = implode(DIRECTORY_SEPARATOR, $classNameArray) . DIRECTORY_SEPARATOR;
                }
                if (!empty($loadPath[$nameSpacePath])) {
                    $filePath = $loadPath[$nameSpacePath] . DIRECTORY_SEPARATOR . $classPath . $fileName . '.php';
                    if (file_exists($filePath)) {
                        \Yaf\Loader::import($filePath);
                        return true;
                    }
                }
            }
            return true;
        });
        unset($loadPath);
    }

    /**
     * 初始化,插件
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initPlugin(\Yaf\Dispatcher $dispatcher)
    {
        // 加载hook
        $dispatcher->registerPlugin((new \Plugins\TruckPlugin()));
    }

    /**
     * 其他安全相关配置
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initSelfSafeParam(\Yaf\Dispatcher $dispatcher)
    {
        // 注销掉全局变量，必须走通用方法获取
        unset($_POST, $_GET, $_ENV, $_SERVER);
    }
}
