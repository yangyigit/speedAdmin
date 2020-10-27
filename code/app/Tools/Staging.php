<?php
/**
 * 权限认证类
 * 功能特性：
 * 1，是对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证。
 *      $auth=new Auth();  $auth->check('规则名称','用户id')
 * 2，可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
 *      $auth=new Auth();  $auth->check('规则1,规则2','用户id','and')
 *      第三个参数为and时表示，用户需要同时具有规则1和规则2的权限。 当第三个参数为or时，表示用户值需要具备其中一个条件即可。默认为or
 * 3，一个用户可以属于多个用户组(tp_auth_group_access表 定义了用户所属用户组)。我们需要设置每个用户组拥有哪些规则(tp_auth_group 定义了用户组权限)
 *
 * 4，支持规则表达式。
 *      在tp_auth_rule 表中定义一条规则时，如果type为1， condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。
 */

//数据库
/*
-- ----------------------------
-- tp_admin，用户表，
-- id:主键，is_admin：是否是管理员
-- ----------------------------
 DROP TABLE IF EXISTS `tp_admin`;
CREATE TABLE `tp_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员用户名',
  `fullname` varchar(50) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `password_reset_token` varchar(255) NOT NULL DEFAULT '',
  `access_token` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员密码',
  `login_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `login_ip` varchar(20) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆时间',
  `last_login_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '上次登陆ip',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上次登陆时间',
  `user_agent` varchar(500) NOT NULL DEFAULT '' COMMENT 'user_agent',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1可用0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
-- ----------------------------
-- tp_auth_rule，规则表，
-- id:主键，name：规则唯一标识, title：规则中文名称 status 状态：为1正常，为0禁用，condition：规则表达式，为空表示存在就验证，不为空表示按照条件验证
-- ----------------------------
 DROP TABLE IF EXISTS `tp_auth_rule`;
CREATE TABLE `tp_auth_rule` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `name` char(80) NOT NULL DEFAULT '',
    `title` char(20) NOT NULL DEFAULT '',
    `type` tinyint(1) NOT NULL DEFAULT '1',
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `condition` char(100) NOT NULL DEFAULT '',  # 规则附件条件,满足附加条件的规则,才认为是有效的规则
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
-- ----------------------------
-- tp_auth_group 用户组表，
-- id：主键， title:用户组中文名称， rules：用户组拥有的规则id， 多个规则","隔开，status 状态：为1正常，为0禁用
-- ----------------------------
 DROP TABLE IF EXISTS `tp_auth_group`;
CREATE TABLE `tp_auth_group` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `title` char(100) NOT NULL DEFAULT '',
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `rules` char(80) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
-- ----------------------------
-- tp_auth_group_access 用户组明细表
-- uid:用户id，group_id：用户组id
-- ----------------------------
DROP TABLE IF EXISTS `tp_auth_group_access`;
CREATE TABLE `tp_auth_group_access` (
    `uid` mediumint(8) unsigned NOT NULL,
    `group_id` mediumint(8) unsigned NOT NULL,
    UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
    KEY `uid` (`uid`),
    KEY `group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
 */

namespace App\Tools;


class Staging{

    /**
     * 创建控制器代码
     */
    public function createController($class, $table, $explain_class, $author, $res_db)
    {
        $fileName = $class.'Controller.'.'php';
        $dir = BASE_PATH.'/app/Controller/Admin';
        $content_dir = BASE_PATH.'/storage/tpl/controller.tpl';

        $content = file_get_contents($content_dir);

        $content = str_replace('#class#', $class, $content);
        $content = str_replace('#explain_class#', $explain_class, $content);
        $content = str_replace('#author#', $author, $content);
        $content = str_replace('#table#', $table, $content);
        $content = str_replace('#primary_key#', $res_db[0]['name'], $content);
        $content = str_replace('#request#', $class.'Request', $content);
        $content = str_replace('#url_name#', strtolower($class), $content);


        //创建文件夹
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        //写入文件
        $res = file_put_contents($dir.'/'.$fileName, $content);

        if($res){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 创建列表生成网页源码
     */
    public function createListView($class, $search, $res_db, $explain_class, $table)
    {
        $new_field = [];
        $fileName = 'showList.html';
        $dir_name = trim(strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $class)), '_');
        $dir = BASE_PATH.'/storage/view/'.$dir_name;
        $content_dir = BASE_PATH.'/storage/tpl/list.tpl';

        $content = file_get_contents($content_dir);
        foreach ($res_db as $k=>$v) {
            $new_field[$v['name']] = $v['remark'];
        }
        $search_content = '';
        //解析搜索条件
        if ('' !== $new_field) {

            foreach ($search as $v) {
                $search_content .= <<<eof
                        <div class="layui-inline">
                            <label class="layui-form-label">{$new_field[$v]}</label>
                            <div class="layui-input-inline">
                                <input name="search_{$table}#{$v}" autocomplete="off" class="layui-input" type="text">
                                <input type="hidden" name="type_{$table}#{$v}" value="like">
                            </div>
                        </div>


eof;
            }

        }

        $fields = '';
        foreach ($res_db as $v) {
            $fields .= <<<eof
                {field: '{$v['name']}', width: 200, title: '{$v['remark']}'},

eof;
        }

        $content = str_replace('#explain_web#', $explain_class, $content);
        $content = str_replace('#fields#', $fields, $content);
        $content = str_replace('#search#', $search_content, $content);
        $content = str_replace('#primary_key#', $res_db[0]['name'], $content);


        //创建文件夹
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        } else {
            fwrite(STDOUT, iconv('UTF-8', 'GB2312', '网页文件夹已经存在，是否覆盖文件,y/n: '));
            if ('y' != trim(fgets(STDIN))) {
                echo iconv('UTF-8', 'GB2312', '执行结束');
                die;
            }
        }

        //写入文件
        $res = file_put_contents($dir.'/'.$fileName, $content);

        if($res){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 创建新增生成网页源码
     */
    public function createAddView($class, $res_db, $explain_web)
    {
        $fileName = 'add.html';
        $dir_name = trim(strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $class)), '_');
        $dir = BASE_PATH.'/storage/view/'.$dir_name;
        $content_dir = BASE_PATH.'/storage/tpl/add.tpl';

        $content = file_get_contents($content_dir);

        $fields = '';
        foreach ($res_db as $v) {
            $fields .= <<<eof
                    <div class="layui-form-item">
                        <label class="layui-form-label"><i class="layui-badge-dot layui-bg-orange"></i> {$v['remark']}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="{$v['name']}" lay-verify="required" autocomplete="off" placeholder=""
                                   value="" class="layui-input">
                        </div>
                    </div>


eof;
        }

        $content = str_replace('#explain_web#', $explain_web, $content);
        $content = str_replace('#fields#', $fields, $content);

        //创建文件夹
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        //写入文件
        $res = file_put_contents($dir.'/'.$fileName, $content);

        if($res){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 创建编辑网页源码
     */
    public function createEditView($class, $res_db, $explain_web)
    {
        $fileName = 'edit.html';
        $dir_name = trim(strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $class)), '_');
        $dir = BASE_PATH.'/storage/view/'.$dir_name;
        $content_dir = BASE_PATH.'/storage/tpl/edit.tpl';

        $content = file_get_contents($content_dir);

        $fields = '';
        foreach ($res_db as $k => $v) {
            if ($k == 0){
                continue;
            }

            $fields .=<<<eof
            <div class="layui-form-item">
                    <label class="layui-form-label"><i class="layui-badge-dot layui-bg-orange"></i> {$v['remark']}</label>
                    <div class="layui-input-block">
                        <input type="text" name="{$v['name']}" autocomplete="off" placeholder=""
                              value="{\$info.{$v['name']}}" class="layui-input" lay-verify="required">
                    </div>
                </div>
eof;
        }

        $content = str_replace('#explain_web#', $explain_web, $content);
        $content = str_replace('#fields#', $fields, $content);
        $content = str_replace('#primary_key#', $res_db[0]['name'], $content);

        //创建文件夹
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        //写入文件
        $res = file_put_contents($dir.'/'.$fileName, $content);

        if($res){
            return true;
        }else{
            return false;
        }
    }

}