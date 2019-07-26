/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50727
 Source Host           : 127.0.0.1:3306
 Source Schema         : office

 Target Server Type    : MySQL
 Target Server Version : 50727
 File Encoding         : 65001

 Date: 26/07/2019 17:45:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for notice
-- ----------------------------
DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
  `summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '描述',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '公共表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for office
-- ----------------------------
DROP TABLE IF EXISTS `office`;
CREATE TABLE `office`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '办公室名称',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '办公室位置',
  `summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '描述',
  `is_valid` tinyint(1) NULL DEFAULT NULL COMMENT '是否有效，1：是，0：否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '办公室信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of office
-- ----------------------------
INSERT INTO `office` VALUES (8, '办公室1', '一楼', '一楼办公室', 1, '2019-07-24 14:50:05', '2019-07-24 14:50:05');
INSERT INTO `office` VALUES (9, '办公室2', '一楼', '一楼办公室', 1, '2019-07-24 14:50:20', '2019-07-24 14:50:20');
INSERT INTO `office` VALUES (10, '办公室3', '二楼', '二楼办公室', 0, '2019-07-24 14:50:38', '2019-07-24 14:50:38');
INSERT INTO `office` VALUES (11, '办公室4', '三楼', '三楼办公室', 1, '2019-07-24 14:50:55', '2019-07-24 14:50:55');

-- ----------------------------
-- Table structure for office_apply
-- ----------------------------
DROP TABLE IF EXISTS `office_apply`;
CREATE TABLE `office_apply`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '申请人用户id',
  `office_id` int(11) UNSIGNED NOT NULL COMMENT '办公室id',
  `office_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '办公室名称',
  `apply_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '申请理由',
  `apply_date` date NOT NULL COMMENT '申请日期',
  `apply_begin_time` time(0) NOT NULL COMMENT '申请开始时间',
  `apply_end_time` time(0) NOT NULL COMMENT '申请结束时间',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请状态，1：已申请，2：正在使用，3：申请过期，4:拒绝申请，5：关闭申请',
  `audit_user_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '审批人账号id',
  `audit_user_realname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批人账号',
  `audit_opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批意见',
  `audit_time` datetime(0) NULL DEFAULT NULL COMMENT '审批时间',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '办公室申请记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of office_apply
-- ----------------------------
INSERT INTO `office_apply` VALUES (1, 32, 11, '办公室1', 'test', '2019-07-25', '09:52:22', '14:52:24', 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `office_apply` VALUES (2, 32, 8, '办公室1', '123', '2019-07-25', '10:52:21', '11:52:22', 5, 1, 'admin1', '32131231', '2019-07-25 16:19:20', '2019-07-25 11:12:42', '2019-07-25 16:19:20');
INSERT INTO `office_apply` VALUES (3, 32, 9, '办公室2', '123', '2019-07-25', '09:51:21', '09:58:22', 4, 1, 'admin1', '12312321', '2019-07-25 16:19:11', '2019-07-25 11:14:53', '2019-07-25 16:19:11');
INSERT INTO `office_apply` VALUES (4, 32, 8, '办公室1', '12321312', '2019-07-26', '00:00:00', '10:00:00', 1, NULL, NULL, NULL, NULL, '2019-07-25 14:46:58', '2019-07-25 14:46:58');

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色名称',
  `is_valid` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否有效，1：有效，0：无效',
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '权限集合',
  `summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '角色描述',
  `is_delete` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否删除，1：是，0：否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES (2, '21321321', 1, 'user_manage,user_list,user_list_view,user_list_add,user_list_edit,user_list_del,user_list_reset_pwd,role_list,role_list_view,role_list_add,role_list_edit,role_list_del', '321312', 0, '2019-07-16 15:52:31', '2019-07-16 15:52:31');
INSERT INTO `role` VALUES (3, 'ceshi', 1, 'user_manage,user_list,user_list_view,user_list_add,user_list_edit,user_list_del,user_list_reset_pwd,role_list,role_list_view,role_list_add,role_list_edit,role_list_del,vacation_manage,vacation_list,vacation_list_view,vacation_list_add,vacation_list_edit,vacation_list_del,vacation_apply,vacation_apply_view,vacation_apply_add,vacation_apply_edit,vacation_apply_audit', 'cehsi', 0, '2019-07-16 15:54:47', '2019-07-16 15:54:47');
INSERT INTO `role` VALUES (4, '员工', 1, 'vacation_manage,vacation_apply,vacation_apply_view,vacation_apply_add,office_manage,office_apply,office_apply_view,office_apply_add,stationery_manage,stationery_apply,stationery_apply_view,stationery_apply_add', '普通员工1', 0, '2019-07-16 15:56:57', '2019-07-26 16:57:28');

-- ----------------------------
-- Table structure for stationery
-- ----------------------------
DROP TABLE IF EXISTS `stationery`;
CREATE TABLE `stationery`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '物品名称',
  `unit` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '物品单位',
  `summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物品描述',
  `is_valid` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否有效，1：是，0：否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物品信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stationery
-- ----------------------------
INSERT INTO `stationery` VALUES (1, '笔记本', '本', '笔记本', 1, '2019-07-25 17:16:35', '2019-07-25 17:16:35');
INSERT INTO `stationery` VALUES (2, '签字笔', '支', '签字笔', 1, '2019-07-26 08:33:48', '2019-07-26 08:33:55');
INSERT INTO `stationery` VALUES (3, '1', '1', '1', 1, '2019-07-26 14:31:10', '2019-07-26 14:31:10');
INSERT INTO `stationery` VALUES (4, '2', '2', '2', 1, '2019-07-26 14:31:15', '2019-07-26 14:31:15');
INSERT INTO `stationery` VALUES (5, '3', '3', '3', 1, '2019-07-26 14:31:49', '2019-07-26 14:31:49');
INSERT INTO `stationery` VALUES (6, '4', '4', '4', 1, '2019-07-26 14:31:55', '2019-07-26 14:31:55');

-- ----------------------------
-- Table structure for stationery_apply
-- ----------------------------
DROP TABLE IF EXISTS `stationery_apply`;
CREATE TABLE `stationery_apply`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '申请人用户id',
  `apply_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '申请理由',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请状态，1：已申请，2：待领取，3：已领取，4:拒绝申请',
  `audit_user_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '审批人账号id',
  `audit_user_realname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批人账号',
  `audit_opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批意见',
  `audit_time` datetime(0) NULL DEFAULT NULL COMMENT '审批时间',
  `grant_time` datetime(0) NULL DEFAULT NULL COMMENT '发放时间',
  `grant_remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '发放备注',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物品申请记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stationery_apply
-- ----------------------------
INSERT INTO `stationery_apply` VALUES (11, 32, '12313', 3, 1, 'admin1', '32131231', '2019-07-26 16:34:55', NULL, '2019-07-26 17:28:42', '2019-07-26 15:33:41', '2019-07-26 17:28:42');

-- ----------------------------
-- Table structure for stationery_apply_item
-- ----------------------------
DROP TABLE IF EXISTS `stationery_apply_item`;
CREATE TABLE `stationery_apply_item`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `stationery_apply_id` int(11) UNSIGNED NOT NULL COMMENT '物品申请记录id',
  `stationery_id` int(11) UNSIGNED NOT NULL COMMENT '物品id',
  `stationery_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '物品名称',
  `stationery_unit` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '物品单位',
  `apply_num` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请数量',
  `grant_num` tinyint(4) UNSIGNED NULL DEFAULT 0 COMMENT '发放数量',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物品申请子项记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stationery_apply_item
-- ----------------------------
INSERT INTO `stationery_apply_item` VALUES (1, 11, 1, '笔记本', '本', 1, 1, '2019-07-26 15:33:41', '2019-07-26 17:28:42');
INSERT INTO `stationery_apply_item` VALUES (2, 11, 3, '1', '1', 1, 5, '2019-07-26 15:33:41', '2019-07-26 17:28:42');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '登录账号，电子邮箱',
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `salt` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码盐',
  `role_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户角色id集合',
  `realname` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '姓名',
  `tel` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '联系电话',
  `sex` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '性别，1：男，0：女',
  `is_super` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否超级管理员，1：是，0：否',
  `is_valid` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否有效，1：是，0：否',
  `department` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '部门',
  `last_login_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '最后一次登录IP地址',
  `last_login_time` datetime(0) NULL DEFAULT NULL COMMENT '最后一次登录时间',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`, `username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户账号表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'admin', 'c022b1471357c2458559a33e29f19003', '4849', '2,3,4', 'admin1', '13113113111', 0, 1, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-22 11:17:10');
INSERT INTO `user` VALUES (32, 'yg@pvc123.com', '04c539b0a7ad475bc4d394fdb717d445', '4035', '4', '员工', '13113113111', 0, 0, 1, '研发部', NULL, NULL, '2019-07-24 17:59:11', '2019-07-24 17:59:11');

-- ----------------------------
-- Table structure for vacation
-- ----------------------------
DROP TABLE IF EXISTS `vacation`;
CREATE TABLE `vacation`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '假期名称',
  `summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '假期描述',
  `is_valid` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否有效，1：是，0：否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '假期类型表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of vacation
-- ----------------------------
INSERT INTO `vacation` VALUES (3, '心情假', '心情假', 1, '2019-07-22 15:01:17', '2019-07-22 15:01:17');
INSERT INTO `vacation` VALUES (4, '健康假', '健康假', 1, '2019-07-22 15:01:33', '2019-07-22 15:01:33');
INSERT INTO `vacation` VALUES (5, 'test', '123', 1, '2019-07-24 14:40:15', '2019-07-24 14:40:15');
INSERT INTO `vacation` VALUES (6, '心情假', '123', 0, '2019-07-24 14:44:38', '2019-07-24 14:44:38');

-- ----------------------------
-- Table structure for vacation_apply
-- ----------------------------
DROP TABLE IF EXISTS `vacation_apply`;
CREATE TABLE `vacation_apply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '申请用户id',
  `vacation_id` int(11) UNSIGNED NOT NULL COMMENT '假期id',
  `vacation_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '假期名称',
  `apply_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '申请理由',
  `apply_begin_date` date NOT NULL COMMENT '申请开始日期',
  `apply_begin_period` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '申请开始时间段，1：上午，2：下午',
  `apply_end_date` date NOT NULL COMMENT '申请结束日期',
  `apply_end_period` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '申请结束时间段，1：上午，2：下午',
  `status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '申请状态，1：申请中，2：同意申请，3：拒绝申请，4：取消申请',
  `audit_user_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '审批人账号id',
  `audit_user_realname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批人账号',
  `audit_opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批意见',
  `audit_time` datetime(0) NULL DEFAULT NULL COMMENT '审批时间',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`, `user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '假期申请记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of vacation_apply
-- ----------------------------
INSERT INTO `vacation_apply` VALUES (5, 1, 3, '心情假', '心情不好', '2019-07-25', 2, '2019-07-25', 2, 2, 1, 'admin1', '12312321321321', '2019-07-24 11:38:40', '2019-07-24 11:23:15', '2019-07-24 11:38:40');
INSERT INTO `vacation_apply` VALUES (6, 1, 4, '健康假', '123', '2019-07-24', 1, '2019-07-25', 1, 4, NULL, NULL, NULL, NULL, '2019-07-24 11:42:04', '2019-07-24 11:50:27');

SET FOREIGN_KEY_CHECKS = 1;
