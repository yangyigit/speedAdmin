<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * common.php
 *
 * 公共函数，避免功能性函数重复书写
 * 书写规范，必须使用function_exists()方法判断
 * User：YM
 * Date：2019/12/15
 * Time：上午10:27
 */

if (! function_exists('getServerLocalIp')) {
    /**
     * getServerLocalIp
     * 获取服务端内网ip地址
     * User：YM
     * Date：2019/12/19
     * Time：下午5:48
     * @return string
     */
    function getServerLocalIp()
    {
        $ip = '127.0.0.1';
        $ips = array_values(swoole_get_local_ip());
        foreach ($ips as $v) {
            if ($v && $v != $ip) {
                $ip = $v;
                break;
            }
        }

        return $ip;
    }
}





