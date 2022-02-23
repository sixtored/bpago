/*
 Navicat Premium Data Transfer

 Source Server         : BdTest
 Source Server Type    : MySQL
 Source Server Version : 50645
 Source Host           : 138.36.237.67:3306
 Source Schema         : BDTEST

 Target Server Type    : MySQL
 Target Server Version : 50645
 File Encoding         : 65001

 Date: 23/02/2022 11:10:25
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for WEBHOOKS
-- ----------------------------
DROP TABLE IF EXISTS `WEBHOOKS`;
CREATE TABLE `WEBHOOKS`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mp` int(11) NULL DEFAULT NULL,
  `live_mode` tinyint(1) NULL DEFAULT 0,
  `aplication_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `version` int(8) NULL DEFAULT NULL,
  `api_version` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NULL DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NULL DEFAULT NULL,
  `action` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NULL DEFAULT NULL,
  `data_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_spanish2_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
