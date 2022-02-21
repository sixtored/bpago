/*
 Navicat Premium Data Transfer

 Source Server         : sixtored
 Source Server Type    : MySQL
 Source Server Version : 50733
 Source Host           : localhost:3306
 Source Schema         : servicios

 Target Server Type    : MySQL
 Target Server Version : 50733
 File Encoding         : 65001

 Date: 19/01/2022 11:25:43
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for mp_users
-- ----------------------------
DROP TABLE IF EXISTS `MP_USERS`;
CREATE TABLE `MP_USERS`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mp_access_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mp_public_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mp_refresh_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mp_user_id` int(11) NULL DEFAULT 0,
  `mp_expired_in` date NULL DEFAULT 0,
  `mp_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mp_created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `mp_update_at` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `mp_activo` tinyint(4) NULL DEFAULT 1,
  `mp_token_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mp_scope` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mp_livemode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `user_id`(`mp_user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
