<?php
namespace Helper;

use \Yaf\Registry;

/**
 * 工具类
 * @package Helper
 * @author  oliverCJ <cgjp123@163.com>
 */
class Tools
{
    /**
     * 使用curl进行远程调用
     * @param string $url
     * @param string $method
     * @param null   $params
     * @param array  $timer
     * @param null   $header
     *
     * @return mixed
     */
    public static function getCurlData($url, $method = 'GET', $params = null, $timer = [], $header = [])
    {
        $ch = curl_init();
        if ($method == 'POST') {
            // 封装POST参数
            if (is_array($params) && !empty($params)) {
                $paramsString = http_build_query($params);
            } else {
                $paramsString = $params;
            }
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsString);
        }
        // 设置参数
        curl_setopt_array(
            $ch,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => $timer['connection_timeout'] ?? 3,
                CURLOPT_TIMEOUT => $timer['execute_timeout'] ?? 3,
            ]
        );
        // 设置头信息
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $response = curl_exec($ch);
        if (($errorCode = curl_errno($ch)) != 0) {
            $errorInfo = curl_error($ch);
        }
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }

    /**
     * 保存全局错误信息，可在任何地方调用或取用
     * 主要用于项目内部错误信息
     * @param string|array  $errorInfo 错误信息
     * @param integer       $errorCode 错误代码
     *
     * @return bool
     */
    public static function globalSaveErrorMessage($errorInfo)
    {
        if (empty($errorInfo)) {
            return false;
        }
        if (Registry::has('errorInfo')) {
            Registry::del('errorInfo');
        }
        Registry::set('errorInfo', $errorInfo);
        return true;
    }

    /**
     * 通过身份证号获取信息
     * 出生日期，年龄，性别
     *
     * @param sting $idCard 身份证号
     *
     * @return array
     */
    public static function getInfoFromIdCard($idCard)
    {
        $result = [];
        if (strlen($idCard) == 15) {
            $date = substr($idCard, 6, 6);
            $result['gender'] = $idCard{14} % 2;
        }
        if (strlen($idCard) == 18) {
            $date = substr($idCard, 6, 8);
            $result['gender'] = $idCard{16} % 2;
        }
        $dateTimeStamp = strtotime($date);
        if ($dateTimeStamp) {
            // 计算出生月数
            $diffMonth = abs(date('Y') - date('Y', $dateTimeStamp)) * 12 + abs(date('m') - date('m', $dateTimeStamp));
            $result['born'] = date('Y-m-d', $dateTimeStamp);
            $result['age'] = intval(floor($diffMonth / 12));
        }
        return $result;
    }
}
