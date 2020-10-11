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


if (! function_exists('groupStatusType')) {
    /**
     * 用户组状态
     * @param $groupStatus
     * @return string
     */
    function groupStatusType($groupStatus)
    {
        switch ($groupStatus) {
            case 0:
                $groupStatus = '禁用';
                break;
            case 1:
                $groupStatus = '启用';
                break;
            default:
                $groupStatus = '未知';
        }
        return $groupStatus;
    }
}

if (! function_exists('isAdminStatusType')) {

    /**
     * 用户管理是否是管理员
     * @param $isStatus
     * @return string
     */
    function isAdminStatusType($isStatus)
    {
        switch ($isStatus) {
            case 0:
                $isStatus = '否';
                break;
            case 1:
                $isStatus = '是';
                break;
            default:
                $isStatus = '未知';
        }
        return $isStatus;
    }
}

if (! function_exists('adminStatusType')) {
    /**
     * 用户管理状态
     * @param $status
     * @return string
     */
    function adminStatusType($status)
    {
        switch ($status) {
            case 0:
                $status = '停用';
                break;
            case 1:
                $status = '激活';
                break;
            default:
                $status = '未知';
        }
        return $status;
    }
}
