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

 Date: 18/01/2022 13:17:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cobradores
-- ----------------------------
DROP TABLE IF EXISTS `COBRADORES`;
CREATE TABLE `COBRADORES`  (
  `IDCOBRADOR` int(11) NULL DEFAULT 0,
  `NOMBRE` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `DOMICILIO` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `TELEFONO` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `PORCENTAJE` decimal(9, 2) NULL DEFAULT 0.00,
  `NOTA` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `TPROCESO` tinyint(1) UNSIGNED ZEROFILL NULL DEFAULT 1,
  `MPROCESO` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `activo` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `idcobrador`(`IDCOBRADOR`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
