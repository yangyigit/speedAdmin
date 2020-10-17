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


if (! function_exists('createSearchWhere')) {

    /**
     * 构建后台搜索条件
     * @param $requestData
     * @return array
     */
    function createSearchWhere($requestData){
        $where = [];
        foreach ($requestData as $k => $v) {
            $k = trim($k);
            $v = trim($v);
            if(strpos($k, '#') && !empty($v)){
                list($table, $field, $type) = explode('#', $k);
                switch ($type){
                    case '=':
                        //相等
                        $search_type = '=';
                        $search_value = $v;
                        break;
                    case 'like':
                        $search_type = 'like';
                        $search_value = '%'.$v.'%';
                        break;
                    case 'time':
                        $search_type = 'between';
                        $search_value = explode(' - ', $v);
                        $search_value = [$search_value[0], $search_value[1]];
                        break;
                    default:
                        $search_type = '=';
                        $search_value = $v;
                }

                $where[] = [$table.'.'.$field, $search_type, $search_value];
            }
        }
        return $where;
    }
}

if (! function_exists('btnShow')) {

    /**
     * 筛选有权限显示的按钮
     * @param array $btns 按钮数据
     * @param $session session类
     * @return array
     */
    function btnShow(array $btns, $session){
        $UserModel = new \App\Model\auth\User();
        $userId = $UserModel -> getUserId($session);

        $auth = new \yangyi\hyperf\Auth();

        $data = \Hyperf\DbConnection\Db::table('auth_rule')
            -> select('name')
            -> where([
                ['status', '=', 1]
            ])
            -> get();

        $new_arr = [];
        foreach ($data as $k => $v) {
            if (!$auth->check([], $v['name'], $userId) && empty($session -> get('admin_isAdmin'))) {
                unset($data[$k]);
            }else{
                $new_arr[] = $v['name'];
            }
        }
        foreach ($btns as $kb=>$vb) {
            foreach ($btns[$kb] as $kvb=>$vvb) {
                if(!in_array($vvb['url'],$new_arr)){
                    unset($btns[$kb][$kvb]);
                }
            }
        }
        return $btns;
    }
}

if (! function_exists('getOffset')) {

    /**
     * 获取offset
     * @param $requestData
     * @return int
     */
    function getOffset($requestData){
        if(empty($requestData['page']) || empty($requestData['limit'])){
            return 0;
        }
        return ((int)$requestData['page'] - 1) * (int)($requestData['limit']);
    }


}