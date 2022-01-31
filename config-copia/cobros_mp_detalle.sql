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

 Date: 17/01/2022 15:23:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cobros_mp_detalle
-- ----------------------------
DROP TABLE IF EXISTS `COBROS_MP_DETALLE`;
CREATE TABLE `COBROS_MP_DETALLE`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `id_cobrosmp` int(11) NULL DEFAULT 0,
  `idabonado` int(11) NULL DEFAULT 0,
  `periodo` int(4) NULL DEFAULT 0,
  `importe` decimal(11, 2) NULL DEFAULT 0.00,
  `detalle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `fchpago` date NULL DEFAULT NULL,
  `idcta` int(11) NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `sincro` tinyint(1) NULL DEFAULT 0,
  `idctapago` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
