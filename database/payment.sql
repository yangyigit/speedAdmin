/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : payment

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 06/10/2020 20:04:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是管理员',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `fullname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '姓名全称',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理员密码',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1可用0禁用',
  `last_login_time` datetime(0) NULL DEFAULT NULL COMMENT '最后的登录时间',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES (2, 1, 'admin', 'boss', 'e10adc3949ba59abbe56e057f20f883e', 1, '2020-05-11 14:07:33', '2020-04-30 15:28:08', '2020-04-30 15:28:08');
INSERT INTO `admin` VALUES (3, 0, 'yangyi', '杨毅1', 'e10adc3949ba59abbe56e057f20f883e', 1, '2020-04-30 15:28:08', '2020-04-30 15:28:08', '2020-04-30 15:28:08');
INSERT INTO `admin` VALUES (10, 0, 'test', '测试用户1', 'c33367701511b4f6020ec61ded352059', 1, '2020-04-30 15:28:08', '2020-04-30 15:28:08', '2020-04-30 15:28:08');
INSERT INTO `admin` VALUES (11, 1, 'test1', '测试1', 'c33367701511b4f6020ec61ded352059', 1, '2020-04-30 15:28:08', '2020-04-30 15:28:08', '2020-04-30 15:28:08');

-- ----------------------------
-- Table structure for auth_group
-- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group`  (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `describe` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：为1正常，为0禁用',
  `rules` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id， 多个规则\",\"隔开',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `title`(`title`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_group
-- ----------------------------
INSERT INTO `auth_group` VALUES (1, '销售', '销售描述', 1, '');
INSERT INTO `auth_group` VALUES (2, '客服', '客服描述', 1, '');
INSERT INTO `auth_group` VALUES (3, '开发', '开发描述', 1, '2,3,5,12');
INSERT INTO `auth_group` VALUES (4, '普工', '普通员工', 1, '5,12');

-- ----------------------------
-- Table structure for auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `auth_group_access`;
CREATE TABLE `auth_group_access`  (
  `uid` mediumint(8) UNSIGNED NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) UNSIGNED NOT NULL COMMENT '用户组id',
  UNIQUE INDEX `uid_group_id`(`uid`, `group_id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户组明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_group_access
-- ----------------------------
INSERT INTO `auth_group_access` VALUES (2, 4);
INSERT INTO `auth_group_access` VALUES (3, 1);
INSERT INTO `auth_group_access` VALUES (10, 3);
INSERT INTO `auth_group_access` VALUES (11, 2);

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule`  (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则中文名称',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '定义一条规则时，如果type为1， condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：为1正常，为0禁用',
  `condition` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  `p_id` int(11) NOT NULL COMMENT '规则父级id',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '规则表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------
INSERT INTO `auth_rule` VALUES (2, '/RequestLog/showList', '^列表展示', 1, 1, '', 1);
INSERT INTO `auth_rule` VALUES (3, '/auth/Rule/showList', '^列表展示', 1, 1, '', 3);
INSERT INTO `auth_rule` VALUES (4, '/auth/Rule/edit', '规则编辑', 1, 1, '', 3);
INSERT INTO `auth_rule` VALUES (5, '/auth/User/showList', '^用户列表', 1, 1, '', 4);
INSERT INTO `auth_rule` VALUES (6, '/auth/User/add', '添加', 1, 1, '', 4);
INSERT INTO `auth_rule` VALUES (7, '/auth/User/look', '查看', 1, 1, '', 4);
INSERT INTO `auth_rule` VALUES (8, '/auth/User/edit', '编辑', 1, 1, '', 4);
INSERT INTO `auth_rule` VALUES (9, '/auth/User/del', '删除', 1, 1, '', 4);
INSERT INTO `auth_rule` VALUES (10, '/auth/User/allot', '分配', 1, 1, '', 4);
INSERT INTO `auth_rule` VALUES (11, '/auth/Rule/ruleRefresh', '规则刷新', 1, 1, '', 3);
INSERT INTO `auth_rule` VALUES (12, '/auth/Group/showList', '^用户组列表', 1, 1, '', 2);
INSERT INTO `auth_rule` VALUES (13, '/auth/Group/add', '添加', 1, 1, '', 2);
INSERT INTO `auth_rule` VALUES (14, '/auth/Group/delete', '删除', 1, 1, '', 2);
INSERT INTO `auth_rule` VALUES (15, '/auth/Group/user', '查看用户', 1, 1, '', 2);
INSERT INTO `auth_rule` VALUES (16, '/auth/Group/edit', '修改', 1, 1, '', 2);
INSERT INTO `auth_rule` VALUES (17, '/auth/Group/removeUser', '解除用户在用户组', 1, 1, '', 2);
INSERT INTO `auth_rule` VALUES (18, '/auth/Group/allot', '授权', 1, 1, '', 2);

-- ----------------------------
-- Table structure for auth_rule_parent
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule_parent`;
CREATE TABLE `auth_rule_parent`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '规则组中文名称',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  UNIQUE INDEX `title`(`title`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '规则父级表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_rule_parent
-- ----------------------------
INSERT INTO `auth_rule_parent` VALUES (1, '/RequestLogController/', '请求日志管理');
INSERT INTO `auth_rule_parent` VALUES (2, '/auth/GroupController/', '权限管理-用户组');
INSERT INTO `auth_rule_parent` VALUES (3, '/auth/RuleController/', '权限管理-规则管理');
INSERT INTO `auth_rule_parent` VALUES (4, '/auth/UserController/', '权限管理-用户');

-- ----------------------------
-- Table structure for request_log
-- ----------------------------
DROP TABLE IF EXISTS `request_log`;
CREATE TABLE `request_log`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户id',
  `method` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作方法',
  `data` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '请求参数，json格式',
  `add_time` datetime(0) NULL DEFAULT NULL COMMENT '请求时间',
  PRIMARY KEY (`id`)
) ENGINE = ARCHIVE CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '请求日志' ROW_FORMAT = Compressed;

SET FOREIGN_KEY_CHECKS = 1;
