/*
SQLyog Community v12.12 (64 bit)
MySQL - 10.1.13-MariaDB : Database - kalreido
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`kalreido` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `kalreido`;

/*Table structure for table `ci_sessions` */

DROP TABLE IF EXISTS `ci_sessions`;

CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `inicis_pay_result` */

DROP TABLE IF EXISTS `inicis_pay_result`;

CREATE TABLE `inicis_pay_result` (
  `idx` int(10) NOT NULL AUTO_INCREMENT,
  `tid` varchar(500) DEFAULT NULL COMMENT '거래아이디',
  `payMethod` varchar(255) DEFAULT NULL COMMENT '결제수단',
  `resultCode` varchar(100) DEFAULT NULL,
  `resultMsg` varchar(255) DEFAULT NULL,
  `TotPrice` int(10) DEFAULT NULL,
  `MOID` varchar(255) DEFAULT NULL,
  `applDate` date DEFAULT NULL COMMENT '승인일',
  `applTime` time DEFAULT NULL COMMENT '승인시간',
  `payment` varchar(50) DEFAULT '결제대기' COMMENT '결제대기, 결제성공, 환불대기, 환불, 결제실패',
  `createDatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `kakao_pay_result` */

DROP TABLE IF EXISTS `kakao_pay_result`;

CREATE TABLE `kakao_pay_result` (
  `idx` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `ResultCode` varchar(10) DEFAULT NULL COMMENT '결과코드',
  `ResultMsg` varchar(255) DEFAULT NULL COMMENT '결과메세지',
  `AuthDate` datetime DEFAULT NULL COMMENT '승인일시 YYDDHH24mmss',
  `AuthCode` varchar(255) DEFAULT NULL COMMENT '승인번호',
  `BuyerName` varchar(255) DEFAULT NULL COMMENT '구매자명',
  `GoodsName` varchar(255) DEFAULT NULL COMMENT '상품명',
  `PayMethod` varchar(255) DEFAULT NULL COMMENT '결제수단',
  `MID` varchar(255) DEFAULT NULL COMMENT '가맹점ID',
  `TID` varchar(255) DEFAULT NULL COMMENT '거래ID',
  `Moid` varchar(255) DEFAULT NULL COMMENT '주문번호',
  `Amt` float DEFAULT NULL COMMENT '금액',
  `CardCode` varchar(50) DEFAULT NULL COMMENT '카드사코드',
  `CardName` varchar(255) DEFAULT NULL COMMENT '결제카드사명',
  `CardQuota` varchar(10) DEFAULT NULL COMMENT '할부개월수',
  `CardInterest` varchar(10) DEFAULT NULL COMMENT '무이자여부',
  `CardCl` varchar(10) DEFAULT NULL COMMENT '체크카드여부',
  `CardBin` varchar(255) DEFAULT NULL COMMENT '카드 BIN번호',
  `CardPoint` varchar(2) DEFAULT NULL COMMENT '카드사포인트사용여부',
  `NON_REP_TOKEN` text COMMENT '부인방지토큰값',
  `paySuccess` tinyint(1) DEFAULT '0' COMMENT '결과 코드',
  `createDatetime` datetime DEFAULT NULL COMMENT '생성일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
