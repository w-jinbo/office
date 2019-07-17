/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : office

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 17/07/2019 08:25:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '物品名称',
  `unit` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '物品单位',
  `summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物品描述',
  `is_vaild` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否有效，1：是，0：否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物品信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for article_apply
-- ----------------------------
DROP TABLE IF EXISTS `article_apply`;
CREATE TABLE `article_apply`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '申请人用户id',
  `apply_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '申请理由',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请状态，0：已申请，1：待领取，2：已领取，-1:拒绝申请',
  `audit_user_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '审批人账号id',
  `audit_user_username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批人账号',
  `audit_opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批意见',
  `audit_time` datetime(0) NULL DEFAULT NULL COMMENT '审批时间',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物品申请记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for article_apply_item
-- ----------------------------
DROP TABLE IF EXISTS `article_apply_item`;
CREATE TABLE `article_apply_item`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `article_apply_id` int(11) UNSIGNED NOT NULL COMMENT '物品申请记录id',
  `article_id` int(11) UNSIGNED NOT NULL COMMENT '物品id',
  `article_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '物品名称',
  `article_unit` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '物品单位',
  `apply_num` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请数量',
  `grant_num` tinyint(4) UNSIGNED NULL DEFAULT 0 COMMENT '发放数量',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物品申请子项记录表' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '办公室信息表' ROW_FORMAT = Dynamic;

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
  `apply_begin_time` datetime(0) NOT NULL COMMENT '申请开始时间',
  `apply_end_time` datetime(0) NOT NULL COMMENT '申请结束时间',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请状态，0：已申请，1：正在使用，2：申请过期，-1:拒绝申请，-2：关闭申请',
  `audit_user_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '审批人账号id',
  `audit_user_username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批人账号',
  `audit_opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批意见',
  `audit_time` datetime(0) NULL DEFAULT NULL COMMENT '审批时间',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '办公室申请记录表' ROW_FORMAT = Dynamic;

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
INSERT INTO `role` VALUES (4, '员工', 0, 'vacation_manage,vacation_apply,vacation_apply_view,vacation_apply_add,vacation_apply_edit', '普通员工1', 0, '2019-07-16 15:56:57', '2019-07-16 16:34:26');

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
) ENGINE = InnoDB AUTO_INCREMENT = 32 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户账号表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'admin', 'c022b1471357c2458559a33e29f19003', '4849', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:53:15');
INSERT INTO `user` VALUES (2, 'admin2', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (3, 'admin3', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (4, 'admin4', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (5, 'admin5', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (6, 'admin6', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (7, 'admin7', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (8, 'admin8', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (9, 'admin9', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (10, 'admin10', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (11, 'admin11', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (12, 'admin12', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (13, 'admin13', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (14, 'admin14', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (15, 'admin15', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (16, 'admin16', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (17, 'admin17', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (18, 'admin18', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (19, 'admin19', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (20, 'admin20', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (21, 'admin21', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (22, 'admin22', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (23, 'admin23', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (24, 'admin24', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (25, 'admin25', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (26, 'admin26', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (27, 'admin27', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 1, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (28, 'admin28', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 0, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (29, 'admin29', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 0, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (30, 'admin30', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 0, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');
INSERT INTO `user` VALUES (31, 'admin', 'b605e86d02eef8bfd0646f6a704c17c9', '1234', NULL, 'admin', '13113113111', 0, 0, 0, '１研发部２６', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-07-16 09:16:04');

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '假期类型表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for vacation_apply
-- ----------------------------
DROP TABLE IF EXISTS `vacation_apply`;
CREATE TABLE `vacation_apply`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '申请用户id',
  `vocation_id` int(11) UNSIGNED NOT NULL COMMENT '假期id',
  `vocation_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '假期名称',
  `apply_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '申请理由',
  `apply_begin_time` datetime(0) NOT NULL COMMENT '申请开始时间',
  `apply_end_time` datetime(0) NOT NULL COMMENT '申请结束时间',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请状态，0：申请中，1：同意申请，-1：拒绝申请，-2：取消申请',
  `audit_user_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '审批人账号id',
  `audit_user_username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批人账号',
  `audit_opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审批意见',
  `audit_time` datetime(0) NULL DEFAULT NULL COMMENT '审批时间',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`, `user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '假期申请记录表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
