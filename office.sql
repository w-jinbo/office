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

 Date: 01/08/2019 17:27:40
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
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `is_valid` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否有效，1：是，0：否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '公共表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of notice
-- ----------------------------
INSERT INTO `notice` VALUES (1, '国台办回应“暂停赴台个人游试点”：民进党当局严重破坏基础', '针对文化和旅游部官网日前公告，自8月1日起暂停47个城市大陆居民赴台个人游试点', '<p><span>针对文化和旅游部官网日前公告，自8月1日起暂停47个城市大陆居民赴台个人游试点，国台办发言人马晓光今日应询表示，大陆居民赴台个人游试点工作于2011年启动，是在两岸关系和平发展大背景下扩大两岸人员往来和交流的积极举措。多年来，大陆居民赴台旅游对台湾旅游及相关产业发展产生了积极促进作用。民进党当局不断推进“台独”活动，不断煽动对大陆敌意，挑动两岸对立，严重破坏了大陆居民赴台个人游试点的基础和条件。我相信，两岸同胞都希望两岸关系早日回到和平发展正确轨道上来，大陆居民赴台旅游能够尽快回复正常、健康的发展局面。(央视记者 赵超逸)</span></p>', 1, '2019-08-01 17:06:55', '2019-08-01 17:06:55');
INSERT INTO `notice` VALUES (2, '商务部透露中美经贸磋商具体内容：就两个主题进行交流', '【环球时报-环球网报道 记者 倪浩】商务部新闻发言人高峰在8月1日举行的商务部新闻发布会上透露了7月30至31日第十二轮中美经贸高级别磋商的具体内容。', '<div class=\"img-container\"><img class=\"normal\" width=\"401px\" data-loadfunc=\"0\" src=\"http://pics1.baidu.com/feed/43a7d933c895d14350d1350ca01bb5075baf076f.jpeg?token=47ff0b778359f35affa5719f338af8d2&amp;s=9612EA2140513BC074B0DA870300E087\" data-loaded=\"0\"></div><p style=\"text-align: justify;\"><span class=\"bjh-p\">商务部新闻发言人高峰</span></p><p style=\"text-align: justify;\"><span class=\"bjh-p\">【环球时报-环球网报道 记者 倪浩】商务部新闻发言人高峰在8月1日举行的商务部新闻发布会上透露了7月30至31日第十二轮中美经贸高级别磋商的具体内容。</span></p><p style=\"text-align: justify;\"><span class=\"bjh-p\">高峰说，关于这次磋商，双方就两个主题进行了交流：一是过去怎么看？主要是讨论磋商中断的原因，澄清对一些经贸问题的看法;二是未来怎么办？主要是明确下一步的磋商的原则和方法，以及相关的实践。此外双方还讨论了中国根据国内需要增加自美采购农产品，以及美方将为采购创造良好条件。</span></p><p style=\"text-align: justify;\"><span class=\"bjh-p\">对于媒体提问磋商时间偏短的原因，高峰说，“双方的交流是坦诚的高效建设性，至于磋商的时间，据我所知，磋商是按原计划结束。”</span></p>', 1, '2019-08-01 17:07:38', '2019-08-01 17:07:38');

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
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '办公室信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of office
-- ----------------------------
INSERT INTO `office` VALUES (1, '办公室1', '一楼', '一楼办公室', 1, '2019-08-01 17:02:40', '2019-08-01 17:02:40');
INSERT INTO `office` VALUES (2, '办公室2', '二楼', '二楼办公室', 1, '2019-08-01 17:02:51', '2019-08-01 17:02:59');

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
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '办公室申请记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of office_apply
-- ----------------------------
INSERT INTO `office_apply` VALUES (1, 36, 1, '办公室1', '开会', '2019-08-01', '15:00:00', '16:00:00', 4, 1, 'admin', '设备维修', '2019-08-01 17:04:31', '2019-08-01 17:03:34', '2019-08-01 17:04:31');
INSERT INTO `office_apply` VALUES (2, 1, 2, '办公室2', '开会', '2019-08-01', '15:00:00', '16:00:00', 1, NULL, NULL, NULL, NULL, '2019-08-01 17:05:31', '2019-08-01 17:05:31');

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
INSERT INTO `role` VALUES (1, '超级管理员', 1, 'user_manage,user_list,user_list_view,user_list_add,user_list_edit,user_list_del,user_list_reset_pwd,role_list,role_list_view,role_list_add,role_list_edit,role_list_del,vacation_manage,vacation_list,vacation_list_view,vacation_list_add,vacation_list_edit,vacation_list_del,vacation_apply,vacation_apply_view,vacation_apply_add,vacation_apply_cancel,vacation_apply_audit,office_manage,office_list,office_list_view,office_list_add,office_list_edit,office_list_del,office_apply,office_apply_view,office_apply_add,office_apply_audit,stationery_manage,stationery_list,stationery_list_view,stationery_list_add,stationery_list_edit,stationery_list_del,stationery_apply,stationery_apply_view,stationery_apply_add,stationery_apply_audit,stationery_apply_grant,notice_manage,notice_list,notice_list_add,notice_list_edit,notice_list_del', '超级管理员', 0, '2019-08-01 16:50:35', '2019-08-01 16:50:45');
INSERT INTO `role` VALUES (2, '员工', 1, 'vacation_manage,vacation_apply,vacation_apply_view,vacation_apply_add,vacation_apply_cancel,office_manage,office_apply,office_apply_view,office_apply_add,stationery_manage,stationery_apply,stationery_apply_view,stationery_apply_add', '普通员工', 0, '2019-08-01 16:51:16', '2019-08-01 16:51:16');
INSERT INTO `role` VALUES (3, '前台', 1, 'office_manage,office_list,office_list_view,office_list_add,office_list_edit,office_list_del,office_apply,office_apply_view,office_apply_add,office_apply_audit,stationery_manage,stationery_list,stationery_list_view,stationery_list_add,stationery_list_edit,stationery_list_del,stationery_apply,stationery_apply_view,stationery_apply_add,stationery_apply_audit,stationery_apply_grant', '前台，负责办公室预约和文具管理', 0, '2019-08-01 16:52:10', '2019-08-01 16:52:10');
INSERT INTO `role` VALUES (4, '主管', 1, 'user_manage,user_list,user_list_view,user_list_add,user_list_edit,user_list_del,user_list_reset_pwd,vacation_manage,vacation_list,vacation_list_view,vacation_list_add,vacation_list_edit,vacation_list_del,vacation_apply,vacation_apply_view,vacation_apply_add,vacation_apply_cancel,vacation_apply_audit', '负责假期审核工作', 0, '2019-08-01 16:52:55', '2019-08-01 16:52:55');

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
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物品信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stationery
-- ----------------------------
INSERT INTO `stationery` VALUES (1, '签字笔', '支', '得力签字笔', 1, '2019-08-01 17:08:22', '2019-08-01 17:08:22');
INSERT INTO `stationery` VALUES (2, '笔记本', '本', '晨光笔记本', 1, '2019-08-01 17:08:36', '2019-08-01 17:08:43');

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物品申请记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stationery_apply
-- ----------------------------
INSERT INTO `stationery_apply` VALUES (1, 36, '奖品', 2, 38, '前台', '同意', '2019-08-01 17:10:07', NULL, NULL, '2019-08-01 17:09:30', '2019-08-01 17:10:07');

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
INSERT INTO `stationery_apply_item` VALUES (1, 1, 1, '签字笔', '支', 5, 0, '2019-08-01 17:09:30', '2019-08-01 17:09:30');
INSERT INTO `stationery_apply_item` VALUES (2, 1, 2, '笔记本', '本', 5, 0, '2019-08-01 17:09:30', '2019-08-01 17:09:30');

-- ----------------------------
-- Table structure for system_tip
-- ----------------------------
DROP TABLE IF EXISTS `system_tip`;
CREATE TABLE `system_tip`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT '通知类型',
  `log_id` int(10) UNSIGNED NOT NULL COMMENT '记录id',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户id',
  `is_read` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否已读，0：否，1：是',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统通知记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_tip
-- ----------------------------
INSERT INTO `system_tip` VALUES (1, 1, 1, 0, 1, '2019-08-01 16:58:44', '2019-08-01 16:59:58');
INSERT INTO `system_tip` VALUES (2, 3, 1, 36, 1, '2019-08-01 17:01:21', '2019-08-01 17:01:35');
INSERT INTO `system_tip` VALUES (3, 4, 1, 36, 1, '2019-08-01 17:04:31', '2019-08-01 17:04:40');
INSERT INTO `system_tip` VALUES (4, 2, 1, 0, 1, '2019-08-01 17:09:30', '2019-08-01 17:09:53');
INSERT INTO `system_tip` VALUES (5, 5, 1, 36, 0, '2019-08-01 17:10:07', NULL);

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
) ENGINE = InnoDB AUTO_INCREMENT = 39 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户账号表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'admin', '83b6a74c511c060e786d6adf4de1a58b', '3674', '', 'admin', '13113113111', 0, 1, 1, '研发部', NULL, '2019-07-15 10:46:22', '2019-07-15 10:46:15', '2019-08-01 16:19:25');
INSERT INTO `user` VALUES (36, 'yuangong@pvc123.com', 'a67f24b17c14f7e5af6feca0d35a1950', '5247', '2', '普通员工', '13113113112', 0, 0, 1, '研发部', NULL, NULL, '2019-08-01 16:53:39', '2019-08-01 16:53:39');
INSERT INTO `user` VALUES (37, 'zhuguan@pvc123.com', 'dfdf4fc387584d12ec0c6e44156db57a', '6362', '2,4', '主管', '13113113112', 0, 0, 1, '研发部', NULL, NULL, '2019-08-01 16:54:20', '2019-08-01 16:54:20');
INSERT INTO `user` VALUES (38, 'qiantai@pvc123.com', 'ac95be6c77301271a572dafe2d3c509f', '7418', '2,3', '前台', '13113113113', 0, 0, 1, '前台', NULL, NULL, '2019-08-01 16:55:04', '2019-08-01 16:55:40');

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
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '假期类型表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of vacation
-- ----------------------------
INSERT INTO `vacation` VALUES (1, '心情假', '心情假，半天，一年两次', 1, '2019-08-01 16:57:37', '2019-08-01 16:57:37');
INSERT INTO `vacation` VALUES (2, '健康假', '健康假', 1, '2019-08-01 16:57:54', '2019-08-01 16:57:54');
INSERT INTO `vacation` VALUES (3, '事假', '事假', 1, '2019-08-01 16:58:05', '2019-08-01 16:58:05');
INSERT INTO `vacation` VALUES (4, '病假', '病假', 1, '2019-08-01 16:58:14', '2019-08-01 16:58:18');

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '假期申请记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of vacation_apply
-- ----------------------------
INSERT INTO `vacation_apply` VALUES (1, 36, 1, '心情假', '心情不好', '2019-08-02', 2, '2019-08-02', 2, 2, 37, '主管', '同意', '2019-08-01 17:01:21', '2019-08-01 16:58:44', '2019-08-01 17:01:21');

SET FOREIGN_KEY_CHECKS = 1;
