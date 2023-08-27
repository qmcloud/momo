/*
 Navicat Premium Data Transfer

 Source Server         : bd
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : zero

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 08/12/2021 11:46:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for system_apis
-- ----------------------------
DROP TABLE IF EXISTS `system_apis`;
CREATE TABLE `system_apis`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `deleted_at` datetime(0) NULL DEFAULT '2006-01-02 15:04:05',
  `path` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'api路径',
  `description` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'api中文描述',
  `api_group` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'api组',
  `method` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'POST' COMMENT '请求方法',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_apis_deleted_at`(`deleted_at`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 83 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_apis
-- ----------------------------
INSERT INTO `system_apis` VALUES (1, '2021-04-23 20:56:12', '2021-05-04 01:25:11', '2021-05-04 09:25:11', '/admin/updateadminpassword', '修改密码', 'admin', 'PUT');
INSERT INTO `system_apis` VALUES (2, '2021-04-23 20:56:18', '2021-05-04 01:25:17', '2021-05-04 09:25:17', '/admin/logout', '退出登录', 'admin', 'POST');
INSERT INTO `system_apis` VALUES (3, '2021-04-23 22:43:51', '2021-05-04 01:25:05', '2021-05-04 09:25:05', '/admin/info', '-获取信息', 'admin', 'GET');
INSERT INTO `system_apis` VALUES (4, '2021-04-24 04:05:11', '2021-04-24 04:19:43', '2006-01-02 15:04:05', '/admin/delete', '删除', 'admin', 'DELETE');
INSERT INTO `system_apis` VALUES (5, '2021-04-24 04:07:24', '2021-04-24 04:19:22', '2006-01-02 15:04:05', '/admin/list', '列表', 'admin', 'POST');
INSERT INTO `system_apis` VALUES (6, '2021-04-24 04:20:13', '2021-04-24 04:20:13', '2006-01-02 15:04:05', '/admin/deleteBatch', '删除批量', 'admin', 'DELETE');
INSERT INTO `system_apis` VALUES (7, '2021-04-24 04:20:40', '2021-04-24 04:20:40', '2006-01-02 15:04:05', '/admin/find', '查询', 'admin', 'POST');
INSERT INTO `system_apis` VALUES (8, '2021-04-24 04:20:59', '2021-04-24 04:20:59', '2006-01-02 15:04:05', '/admin/adminAdd', '添加', 'admin', 'POST');
INSERT INTO `system_apis` VALUES (9, '2021-04-24 04:21:16', '2021-04-24 04:21:16', '2006-01-02 15:04:05', '/admin/adminUpdate', '修改', 'admin', 'PUT');
INSERT INTO `system_apis` VALUES (10, '2021-04-24 04:22:13', '2021-04-24 04:22:13', '2006-01-02 15:04:05', '/systemMenu/list', '列表', 'systemMenu', 'POST');
INSERT INTO `system_apis` VALUES (11, '2021-04-24 04:22:31', '2021-04-24 04:22:31', '2006-01-02 15:04:05', '/systemMenu/delete', '删除', 'systemMenu', 'DELETE');
INSERT INTO `system_apis` VALUES (12, '2021-04-24 04:22:53', '2021-04-24 04:22:53', '2006-01-02 15:04:05', '/systemMenu/deleteBatch', '删除批量', 'systemMenu', 'DELETE');
INSERT INTO `system_apis` VALUES (13, '2021-04-24 04:23:21', '2021-04-24 04:23:21', '2006-01-02 15:04:05', '/systemMenu/find', '查询', 'systemMenu', 'POST');
INSERT INTO `system_apis` VALUES (14, '2021-04-24 04:23:35', '2021-04-24 04:23:35', '2006-01-02 15:04:05', '/systemMenu/add', '添加', 'systemMenu', 'POST');
INSERT INTO `system_apis` VALUES (15, '2021-04-24 04:23:54', '2021-04-24 04:23:54', '2006-01-02 15:04:05', '/systemMenu/update', '修改', 'systemMenu', 'PUT');
INSERT INTO `system_apis` VALUES (16, '2021-04-24 04:28:06', '2021-04-24 04:28:06', '2006-01-02 15:04:05', '/systemApi/list', '列表', 'systemApi', 'POST');
INSERT INTO `system_apis` VALUES (17, '2021-04-24 04:28:06', '2021-04-24 04:28:06', '2006-01-02 15:04:05', '/systemApi/delete', '删除', 'systemApi', 'DELETE');
INSERT INTO `system_apis` VALUES (18, '2021-04-24 04:28:06', '2021-04-24 04:31:36', '2006-01-02 15:04:05', '/systemApi/deleteBatch', '删除批量', 'systemApi', 'DELETE');
INSERT INTO `system_apis` VALUES (19, '2021-04-24 04:28:06', '2021-04-24 04:28:06', '2006-01-02 15:04:05', '/systemApi/find', '查询', 'systemApi', 'POST');
INSERT INTO `system_apis` VALUES (20, '2021-04-24 04:28:06', '2021-04-24 04:28:06', '2006-01-02 15:04:05', '/systemApi/add', '添加', 'systemApi', 'POST');
INSERT INTO `system_apis` VALUES (21, '2021-04-24 04:28:06', '2021-04-24 04:28:06', '2006-01-02 15:04:05', '/systemApi/update', '修改', 'systemApi', 'PUT');
INSERT INTO `system_apis` VALUES (22, '2021-04-24 04:30:40', '2021-04-24 04:30:40', '2006-01-02 15:04:05', '/systemRole/list', '列表', 'systemRole', 'POST');
INSERT INTO `system_apis` VALUES (23, '2021-04-24 04:30:40', '2021-04-24 04:30:40', '2006-01-02 15:04:05', '/systemRole/delete', '删除', 'systemRole', 'DELETE');
INSERT INTO `system_apis` VALUES (24, '2021-04-24 04:30:40', '2021-04-24 04:31:37', '2006-01-02 15:04:05', '/systemRole/deleteBatch', '删除批量', 'systemRole', 'DELETE');
INSERT INTO `system_apis` VALUES (25, '2021-04-24 04:30:40', '2021-04-24 04:30:40', '2006-01-02 15:04:05', '/systemRole/find', '查询', 'systemRole', 'POST');
INSERT INTO `system_apis` VALUES (26, '2021-04-24 04:30:40', '2021-04-24 04:30:40', '2006-01-02 15:04:05', '/systemRole/add', '添加', 'systemRole', 'POST');
INSERT INTO `system_apis` VALUES (27, '2021-04-24 04:30:40', '2021-04-24 04:30:40', '2006-01-02 15:04:05', '/systemRole/update', '修改', 'systemRole', 'PUT');
INSERT INTO `system_apis` VALUES (28, '2021-04-24 04:31:04', '2021-04-24 04:31:04', '2006-01-02 15:04:05', '/systemDepartment/list', '列表', 'systemDepartment', 'POST');
INSERT INTO `system_apis` VALUES (29, '2021-04-24 04:31:04', '2021-04-24 04:31:04', '2006-01-02 15:04:05', '/systemDepartment/delete', '删除', 'systemDepartment', 'DELETE');
INSERT INTO `system_apis` VALUES (30, '2021-04-24 04:31:04', '2021-04-24 04:31:39', '2006-01-02 15:04:05', '/systemDepartment/deleteBatch', '删除批量', 'systemDepartment', 'DELETE');
INSERT INTO `system_apis` VALUES (31, '2021-04-24 04:31:04', '2021-04-24 04:31:04', '2006-01-02 15:04:05', '/systemDepartment/find', '查询', 'systemDepartment', 'POST');
INSERT INTO `system_apis` VALUES (32, '2021-04-24 04:31:04', '2021-04-24 04:31:04', '2006-01-02 15:04:05', '/systemDepartment/add', '添加', 'systemDepartment', 'POST');
INSERT INTO `system_apis` VALUES (33, '2021-04-24 04:31:04', '2021-04-24 04:31:04', '2006-01-02 15:04:05', '/systemDepartment/update', '修改', 'systemDepartment', 'PUT');
INSERT INTO `system_apis` VALUES (34, '2021-04-27 16:23:30', '2021-04-27 16:23:30', '2006-01-02 15:04:05', '/systemDepartment/parentList', '获取一级部门', 'systemDepartment', 'POST');
INSERT INTO `system_apis` VALUES (35, '2021-04-27 16:24:59', '2021-05-02 14:17:53', '2006-01-02 15:04:05', '/systemRoleApi/byRoleId', '-获取角色iD\'sApi', 'systemRoleApi', 'POST');
INSERT INTO `system_apis` VALUES (36, '2021-04-27 16:29:25', '2021-04-27 16:29:25', '2006-01-02 15:04:05', '/systemMenu/treeList', '获取菜单树[权限分配]', 'systemMenu', 'POST');
INSERT INTO `system_apis` VALUES (37, '2021-04-27 16:30:27', '2021-04-27 16:30:27', '2006-01-02 15:04:05', '/systemRoleApi/add', '角色API添加', 'systemRoleApi', 'POST');
INSERT INTO `system_apis` VALUES (38, '2021-04-27 16:32:45', '2021-05-02 14:17:59', '2006-01-02 15:04:05', '/systemRoleMenu/byRoleId', '-获取Role\'sMenu', 'systemRoleMenu', 'POST');
INSERT INTO `system_apis` VALUES (39, '2021-04-27 16:33:47', '2021-04-27 16:33:47', '2006-01-02 15:04:05', '/systemRoleMenu/add', 'Role\'sMenu添加', 'systemRoleMenu', 'POST');
INSERT INTO `system_apis` VALUES (40, '2021-04-27 16:35:38', '2021-04-27 16:35:38', '2006-01-02 15:04:05', '/systemMenu/parentList', '获取一级菜单', 'systemMenu', 'POST');
INSERT INTO `system_apis` VALUES (41, '2021-04-27 16:36:35', '2021-04-27 16:36:35', '2006-01-02 15:04:05', '/systemRole/parentList', '获取一级角色', 'systemRole', 'POST');
INSERT INTO `system_apis` VALUES (77, '2021-05-22 01:23:55', '2021-05-22 01:23:55', '2006-01-02 15:04:05', '/verifyCode/add', '添加', 'verifyCode', 'POST');
INSERT INTO `system_apis` VALUES (78, '2021-05-22 01:23:56', '2021-05-22 01:23:56', '2006-01-02 15:04:05', '/verifyCode/update', '修改', 'verifyCode', 'PUT');
INSERT INTO `system_apis` VALUES (79, '2021-05-22 01:23:56', '2021-05-22 01:23:56', '2006-01-02 15:04:05', '/verifyCode/find', '查询', 'verifyCode', 'POST');
INSERT INTO `system_apis` VALUES (80, '2021-05-22 01:23:56', '2021-05-22 01:23:56', '2006-01-02 15:04:05', '/verifyCode/delete', '删除', 'verifyCode', 'DELETE');
INSERT INTO `system_apis` VALUES (81, '2021-05-22 01:23:57', '2021-05-22 01:23:57', '2006-01-02 15:04:05', '/verifyCode/deleteBatch', '删除批量', 'verifyCode', 'DELETE');
INSERT INTO `system_apis` VALUES (82, '2021-05-22 01:23:57', '2021-05-22 01:23:57', '2006-01-02 15:04:05', '/verifyCode/list', '列表', 'verifyCode', 'POST');

-- ----------------------------
-- Table structure for system_departments
-- ----------------------------
DROP TABLE IF EXISTS `system_departments`;
CREATE TABLE `system_departments`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `deleted_at` datetime(0) NULL DEFAULT '2006-01-02 15:04:05',
  `ancestors` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '祖级列表',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '部门名称',
  `leader` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '负责人',
  `phone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '联系电话',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '邮箱',
  `status` int(11) NULL DEFAULT 0 COMMENT '部门状态（0正常 1停用）',
  `create_by` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '创建者',
  `update_by` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '更新者',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `parent_id` int(11) NULL DEFAULT 0 COMMENT '父级ID',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_departments_deleted_at`(`deleted_at`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_departments
-- ----------------------------
INSERT INTO `system_departments` VALUES (4, '2021-04-24 00:52:57', '2021-04-24 14:35:33', '2006-01-02 15:04:05', '1', '成都分部', '1', '1', '1', 1, '1', '1', 123, 9);
INSERT INTO `system_departments` VALUES (5, '2021-04-24 01:06:25', '2021-04-24 14:59:01', '2006-01-02 15:04:05', '12', '武汉分部', '135212343873', '12', '11', 12, '361018729@qq.com', '12', 12, 9);
INSERT INTO `system_departments` VALUES (6, '2021-04-24 01:12:12', '2021-04-25 09:06:57', '2006-01-02 15:04:05', '123,123,12,321,3,213,12,3', '深圳分部', '张明', '1352345677', '12345729@qq.com', 0, '李节节', '李明', 123, 0);
INSERT INTO `system_departments` VALUES (7, '2021-04-24 01:47:29', '2021-04-24 14:59:09', '2006-01-02 15:04:05', '12', '广州分部', '145134893873', '12', '12', 12, '361018729@qq.com', '12', 12, 0);
INSERT INTO `system_departments` VALUES (8, '2021-04-24 02:17:40', '2021-04-24 14:58:37', '2006-01-02 15:04:05', '1123,1111', '北京分部', 'liming11', '135248235', '361012312318729@qq.com', 0, 'zhangshan123', 'wangwu', 1, 9);
INSERT INTO `system_departments` VALUES (9, '2021-04-24 02:26:22', '2021-04-24 14:58:23', '2006-01-02 15:04:05', '123', '总部', '23', '13524891234', '123@qq.com', 1, '1', '2', 0, 0);
INSERT INTO `system_departments` VALUES (10, '2021-04-24 06:03:02', '2021-04-24 14:58:53', '2006-01-02 15:04:05', '1,1,1,1,1,1', '上海分部', '李明', '13512345123', '34324@a.com', 0, '111', '122', 0, 9);
INSERT INTO `system_departments` VALUES (11, '2021-05-02 13:38:57', '2021-05-02 13:40:12', '2021-05-02 21:40:12', '', 'aaa', 'bbb', '123123123', '123123123', 0, '', '', 0, 0);
INSERT INTO `system_departments` VALUES (12, '2021-05-02 13:39:10', '2021-05-02 13:40:12', '2021-05-02 21:40:12', '', 'aaaaaa23123231', '123123123', '123123', '123123123', 0, '', '', 0, 0);
INSERT INTO `system_departments` VALUES (13, '2021-05-02 13:39:19', '2021-05-02 13:39:33', '2021-05-02 21:39:33', '', 'a12312312', '123123', '123123', '123123', 0, '', '', 0, 0);

-- ----------------------------
-- Table structure for system_menus
-- ----------------------------
DROP TABLE IF EXISTS `system_menus`;
CREATE TABLE `system_menus`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '添加日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '修改日期',
  `deleted_at` datetime(0) NULL DEFAULT '2006-01-02 15:04:05' COMMENT '删除日期',
  `parent_id` int(11) NULL DEFAULT 0 COMMENT '父菜单ID',
  `path` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '路由path',
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '路由name',
  `hidden` int(11) NULL DEFAULT 0 COMMENT '1是0否在列表隐藏',
  `component` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '对应前端vue文件路径',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序标记',
  `title` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '菜单名称',
  `icon` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图标',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_menus_deleted_at`(`deleted_at`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_menus
-- ----------------------------
INSERT INTO `system_menus` VALUES (11, '2021-04-23 13:31:02', '2021-05-02 15:31:54', '2006-01-02 15:04:05', 15, 'adminList', 'adminList', 0, 'view/superAdmin/systemUser/index', 1, '管理员管理', 'user-solid');
INSERT INTO `system_menus` VALUES (12, '2021-04-24 06:03:33', '2021-05-02 15:34:42', '2006-01-02 15:04:05', 15, 'systemApiList', 'systemApiList', 0, 'view/superAdmin/systemApi/index', 3, 'Api管理', 's-tools');
INSERT INTO `system_menus` VALUES (13, '2021-04-24 11:39:33', '2021-05-02 15:31:43', '2006-01-02 15:04:05', 15, 'systemMenuList', 'systemMenuList', 0, 'view/superAdmin/systemMenu/index', 2, '菜单管理', 'menu');
INSERT INTO `system_menus` VALUES (14, '2021-04-24 11:44:54', '2021-05-02 15:34:07', '2006-01-02 15:04:05', 15, 'systemRoleList', 'systemRoleList', 0, 'view/superAdmin/systemRole/index', 4, '角色管理', 's-tools');
INSERT INTO `system_menus` VALUES (15, '2021-04-24 11:50:20', '2021-05-24 01:09:31', '2006-01-02 15:04:05', 0, 'admin', 'admin', 0, 'view/superAdmin/index', 0, '超级管理员', 'suitcase');
INSERT INTO `system_menus` VALUES (16, '2021-04-24 12:57:27', '2021-05-02 15:33:58', '2006-01-02 15:04:05', 15, 'systemDepartmentList', 'systemDepartmentList', 0, 'view/superAdmin/systemDepartment/index', 5, '部门管理', 's-tools');
INSERT INTO `system_menus` VALUES (17, '2021-04-24 13:24:29', '2021-05-02 11:42:33', '2006-01-02 15:04:05', 0, 'dashboard', 'dashboard', 0, 'view/dashboard/index', -1, 'Dashboard', 's-home');
INSERT INTO `system_menus` VALUES (18, '2021-05-04 20:34:16', '2021-05-24 01:10:48', '2006-01-02 15:04:05', 0, 'k8s', 'k8s', 0, 'view/superAdmin/index', 2, 'K8S管理', 's-grid');
INSERT INTO `system_menus` VALUES (19, '2021-05-04 20:36:52', '2021-05-05 10:28:31', '2006-01-02 15:04:05', 18, 'deployment', 'deployment', 0, 'view/k8s/deployment', 0, 'Deployment', 'star-on');
INSERT INTO `system_menus` VALUES (20, '2021-05-05 10:18:20', '2021-05-05 11:08:35', '2006-01-02 15:04:05', 18, 'namespace', 'namespace', 0, 'view/k8s/namespace', 1, 'Namespace', 'star-on');
INSERT INTO `system_menus` VALUES (21, '2021-05-05 10:18:20', '2021-05-05 11:09:04', '2006-01-02 15:04:05', 18, 'service', 'service', 0, 'view/k8s/service', 2, 'Service', 'star-on');
INSERT INTO `system_menus` VALUES (22, '2021-05-05 10:18:20', '2021-05-05 11:09:27', '2006-01-02 15:04:05', 18, 'ingress', 'ingress', 0, 'view/k8s/ingress', 3, 'Ingress', 'star-on');
INSERT INTO `system_menus` VALUES (23, '2021-05-05 10:18:20', '2021-05-07 01:54:36', '2006-01-02 15:04:05', 18, 'pods', 'pods', 0, 'view/k8s/pods', 3, 'Pods', 'star-on');

-- ----------------------------
-- Table structure for system_role_apis
-- ----------------------------
DROP TABLE IF EXISTS `system_role_apis`;
CREATE TABLE `system_role_apis`  (
  `p_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `v0` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `v1` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `v2` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `v3` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `v4` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `v5` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT ''
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '角色API关系' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of system_role_apis
-- ----------------------------
INSERT INTO `system_role_apis` VALUES ('p', '4', '/admin/adminUpdate', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '4', '/admin/adminAdd', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '4', '/admin/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '1', '/systemRole/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/admin/adminUpdate', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/admin/adminAdd', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/admin/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/admin/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/admin/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/admin/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/admin/info', 'GET', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/admin/logout', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/admin/updateadminpassword', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/systemApi/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/systemApi/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/systemApi/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '6', '/systemApi/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '8', '/systemApi/update', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '8', '/systemApi/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '8', '/systemApi/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '8', '/systemApi/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '8', '/systemApi/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '8', '/systemApi/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/admin/info', 'GET', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemDepartment/parentList', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemDepartment/update', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemDepartment/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemDepartment/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemDepartment/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemDepartment/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemDepartment/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemRole/parentList', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemRole/update', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemRole/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemRole/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemRole/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemRole/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemRole/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemRoleApi/byRoleId', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '5', '/systemRoleMenu/byRoleId', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/admin/adminUpdate', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/admin/adminAdd', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/admin/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/admin/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/admin/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/admin/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemApi/update', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemApi/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemApi/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemApi/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemApi/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemApi/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemDepartment/parentList', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemDepartment/update', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemDepartment/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemDepartment/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemDepartment/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemDepartment/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemDepartment/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemMenu/parentList', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemMenu/treeList', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemMenu/update', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemMenu/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemMenu/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemMenu/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemMenu/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemMenu/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRole/parentList', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRole/update', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRole/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRole/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRole/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRole/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRole/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRoleApi/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRoleApi/byRoleId', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRoleMenu/add', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/systemRoleMenu/byRoleId', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/verifyCode/list', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/verifyCode/deleteBatch', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/verifyCode/delete', 'DELETE', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/verifyCode/find', 'POST', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/verifyCode/update', 'PUT', '', '', '');
INSERT INTO `system_role_apis` VALUES ('p', '7', '/verifyCode/add', 'POST', '', '', '');

-- ----------------------------
-- Table structure for system_role_menus
-- ----------------------------
DROP TABLE IF EXISTS `system_role_menus`;
CREATE TABLE `system_role_menus`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NULL DEFAULT 0 COMMENT '角色ID',
  `menu_id` int(11) NULL DEFAULT 0 COMMENT '菜单ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 222 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '角色菜单关系' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_role_menus
-- ----------------------------
INSERT INTO `system_role_menus` VALUES (54, 4, 17);
INSERT INTO `system_role_menus` VALUES (69, 6, 17);
INSERT INTO `system_role_menus` VALUES (70, 6, 15);
INSERT INTO `system_role_menus` VALUES (71, 6, 11);
INSERT INTO `system_role_menus` VALUES (72, 6, 13);
INSERT INTO `system_role_menus` VALUES (84, 8, 15);
INSERT INTO `system_role_menus` VALUES (85, 8, 13);
INSERT INTO `system_role_menus` VALUES (86, 8, 12);
INSERT INTO `system_role_menus` VALUES (94, 5, 17);
INSERT INTO `system_role_menus` VALUES (95, 5, 15);
INSERT INTO `system_role_menus` VALUES (96, 5, 14);
INSERT INTO `system_role_menus` VALUES (97, 5, 16);
INSERT INTO `system_role_menus` VALUES (204, 7, 17);
INSERT INTO `system_role_menus` VALUES (205, 7, 15);
INSERT INTO `system_role_menus` VALUES (206, 7, 11);
INSERT INTO `system_role_menus` VALUES (207, 7, 13);
INSERT INTO `system_role_menus` VALUES (208, 7, 12);
INSERT INTO `system_role_menus` VALUES (209, 7, 14);
INSERT INTO `system_role_menus` VALUES (210, 7, 16);
INSERT INTO `system_role_menus` VALUES (211, 7, 18);
INSERT INTO `system_role_menus` VALUES (212, 7, 19);
INSERT INTO `system_role_menus` VALUES (213, 7, 20);
INSERT INTO `system_role_menus` VALUES (214, 7, 21);
INSERT INTO `system_role_menus` VALUES (215, 7, 23);
INSERT INTO `system_role_menus` VALUES (216, 7, 22);
INSERT INTO `system_role_menus` VALUES (217, 7, 24);
INSERT INTO `system_role_menus` VALUES (218, 7, 26);
INSERT INTO `system_role_menus` VALUES (219, 7, 25);
INSERT INTO `system_role_menus` VALUES (220, 7, 28);
INSERT INTO `system_role_menus` VALUES (221, 7, 27);

-- ----------------------------
-- Table structure for system_roles
-- ----------------------------
DROP TABLE IF EXISTS `system_roles`;
CREATE TABLE `system_roles`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `deleted_at` datetime(0) NULL DEFAULT '2006-01-02 15:04:05',
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '角色名',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `parent_id` int(11) NULL DEFAULT 0 COMMENT '父级ID',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_apis_deleted_at`(`deleted_at`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_roles
-- ----------------------------
INSERT INTO `system_roles` VALUES (4, '2021-04-23 23:56:39', '2021-04-24 23:02:31', '2006-01-02 15:04:05', '销售部管理员', 6, 6);
INSERT INTO `system_roles` VALUES (5, '2021-04-23 23:57:33', '2021-04-25 09:07:58', '2006-01-02 15:04:05', '技术部管理员', 6, 0);
INSERT INTO `system_roles` VALUES (6, '2021-04-23 23:58:44', '2021-04-24 23:02:24', '2006-01-02 15:04:05', '总部管理员', 0, 0);
INSERT INTO `system_roles` VALUES (7, '2021-04-24 06:02:20', '2021-04-24 13:11:51', '2006-01-02 15:04:05', '超级管理员', 0, 0);
INSERT INTO `system_roles` VALUES (8, '2021-05-02 14:05:23', '2021-05-02 14:06:13', '2006-01-02 15:04:05', 'aa', 0, 6);
INSERT INTO `system_roles` VALUES (9, '2021-05-02 14:05:30', '2021-05-02 14:06:14', '2006-01-02 15:04:05', 'aa1', 0, 0);
INSERT INTO `system_roles` VALUES (10, '2021-05-02 14:05:34', '2021-05-02 14:06:15', '2006-01-02 15:04:05', 'aa234324324', 0, 0);

-- ----------------------------
-- Table structure for system_users
-- ----------------------------
DROP TABLE IF EXISTS `system_users`;
CREATE TABLE `system_users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `dept_id` int(11) NULL DEFAULT 0 COMMENT '部门ID',
  `role_id` int(11) NULL DEFAULT 0 COMMENT '角色ID',
  `user_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户账号',
  `nick_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户昵称',
  `user_type` int(11) NULL DEFAULT 0 COMMENT '用户类型（0系统用户）',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '用户邮箱',
  `phonenumber` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '手机号码',
  `sex` int(11) NULL DEFAULT 0 COMMENT '用户性别（0男 1女 2未知）',
  `avatar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '头像地址',
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '密码',
  `status` int(11) NULL DEFAULT 0 COMMENT '帐号状态（0正常 1停用）',
  `del_flag` int(11) NULL DEFAULT 0 COMMENT '删除标志（0代表存在 2代表删除）',
  `login_ip` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '最后登录IP',
  `login_date` datetime(0) NULL DEFAULT '2006-01-02 15:04:05' COMMENT '最后登录时间',
  `create_by` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '创建者',
  `remark` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Empty string' COMMENT '备注',
  `update_by` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '更新者',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  `deleted_at` datetime(0) NULL DEFAULT '2006-01-02 15:04:05' COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '系统用户信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_users
-- ----------------------------
INSERT INTO `system_users` VALUES (1, 9, 7, 'admin', '张三', 1, '1234729@qq.com', '12345677', 1, 'bf0812cf724eef1e27fb4c3946d8b05f_20210519015947.jpeg', 'e10adc3949ba59abbe56e057f20f883e', 0, 0, '127.0.0.1', '2021-12-08 09:54:06', '', 'remarks1', '', '2021-04-24 06:34:04', '2021-12-08 09:54:06', '2006-01-02 15:04:05');

-- ----------------------------
-- Table structure for verify_codes
-- ----------------------------
DROP TABLE IF EXISTS `verify_codes`;
CREATE TABLE `verify_codes`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `deleted_at` datetime(0) NULL DEFAULT '2006-01-02 15:04:05',
  `account` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '号码(手机或邮箱)',
  `code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '验证码',
  `type` int(11) NULL DEFAULT 0 COMMENT '类型0手机1邮箱',
  `status` int(11) NULL DEFAULT 0 COMMENT '状态0未验证1已验证2验证错误',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_apis_deleted_at`(`deleted_at`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '验证码表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of verify_codes
-- ----------------------------
INSERT INTO `verify_codes` VALUES (1, '2021-05-22 11:42:32', '2021-05-22 11:42:16', '2006-01-02 15:04:05', '361018729@qq.com', '3777', 1, 1);
INSERT INTO `verify_codes` VALUES (2, '2021-05-22 12:26:00', '2021-05-22 12:28:05', '2006-01-02 15:04:05', '361018729@qq.com', '8522', 1, 1);
INSERT INTO `verify_codes` VALUES (3, '2021-05-22 12:26:18', '2021-05-22 12:26:20', '2006-01-02 15:04:05', '361018729@qq.com', '2227', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
