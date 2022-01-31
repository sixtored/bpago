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

 Date: 17/01/2022 14:45:02
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ctabotonpago
-- ----------------------------
DROP TABLE IF EXISTS `CTABOTONPAGO`;
CREATE TABLE `CTABOTONPAGO`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `idcta` int(11) NULL DEFAULT 0,
  `idabonado` int(11) NULL DEFAULT 0,
  `periodo` int(6) NULL DEFAULT NULL,
  `impo1` decimal(11, 2) NULL DEFAULT 0.00,
  `impo3` decimal(11, 2) NULL DEFAULT 0.00,
  `impo4` decimal(11, 2) NULL DEFAULT 0.00,
  `qimpo` decimal(11, 2) NULL DEFAULT 0.00,
  `idcob` int(8) NULL DEFAULT 0,
  `idcaja` int(11) NULL DEFAULT 0,
  `tpago` int(4) NULL DEFAULT 0,
  `pagado` tinyint(1) NULL DEFAULT 0,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  `vto1` date NULL DEFAULT NULL,
  `vto2` date NULL DEFAULT NULL,
  `vto3` date NULL DEFAULT NULL,
  `vto4` date NULL DEFAULT NULL,
  `impo2` decimal(11, 2) NULL DEFAULT 0.00,
  `cantvtos` int(4) NULL DEFAULT 1,
  `fchpago` datetime(0) NULL DEFAULT NULL,
  `pago_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `docu` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `IDCTA`(`idcta`) USING BTREE,
  INDEX `DOCU`(`docu`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
