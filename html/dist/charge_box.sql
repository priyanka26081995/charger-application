-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 28, 2024 at 03:09 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `servicekeedadb`
--

-- --------------------------------------------------------

--
-- Table structure for table `charge_box`
--

DROP TABLE IF EXISTS `charge_box`;
CREATE TABLE IF NOT EXISTS `charge_box` (
  `charge_box_pk` int NOT NULL AUTO_INCREMENT,
  `charge_box_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `endpoint_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ocpp_protocol` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `registration_status` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'Accepted',
  `charge_point_vendor` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `charge_point_model` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `charge_point_serial_number` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `charge_box_serial_number` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fw_version` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fw_update_status` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fw_update_timestamp` timestamp(6) NULL DEFAULT NULL,
  `iccid` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imsi` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `meter_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `meter_serial_number` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `diagnostics_status` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `diagnostics_timestamp` timestamp(6) NULL DEFAULT NULL,
  `last_heartbeat_timestamp` timestamp(6) NULL DEFAULT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `note` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `location_latitude` decimal(11,8) DEFAULT NULL,
  `location_longitude` decimal(11,8) DEFAULT NULL,
  `address_pk` int DEFAULT NULL,
  `admin_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `insert_connector_status_after_transaction_msg` tinyint(1) DEFAULT '1',
  `IS_ACTIVE` tinyint(1) DEFAULT '1',
  `COMPANY_ID` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`charge_box_pk`),
  UNIQUE KEY `chargeBoxId_UNIQUE` (`charge_box_id`),
  KEY `chargebox_op_ep_idx` (`ocpp_protocol`,`endpoint_address`),
  KEY `FK_charge_box_address_apk` (`address_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `charge_box`
--

INSERT INTO `charge_box` (`charge_box_pk`, `charge_box_id`, `endpoint_address`, `ocpp_protocol`, `registration_status`, `charge_point_vendor`, `charge_point_model`, `charge_point_serial_number`, `charge_box_serial_number`, `fw_version`, `fw_update_status`, `fw_update_timestamp`, `iccid`, `imsi`, `meter_type`, `meter_serial_number`, `diagnostics_status`, `diagnostics_timestamp`, `last_heartbeat_timestamp`, `description`, `note`, `location_latitude`, `location_longitude`, `address_pk`, `admin_address`, `insert_connector_status_after_transaction_msg`, `IS_ACTIVE`, `COMPANY_ID`) VALUES
(10, '123', NULL, NULL, 'Accepted', 'SERVICEKEEDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '19.04820700', '73.07979600', NULL, NULL, 1, 1, 1),
(11, 'EFILLCCS3000124', NULL, 'ocpp1.6J', 'Accepted', 'SERVICEKEEDA', 'BISON30', 'MXP001', NULL, 'V3.0.4(3.15.39-0D)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-04-09 13:08:13.762000', NULL, NULL, '19.04820700', '73.07979600', NULL, NULL, 0, 1, 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `charge_box`
--
ALTER TABLE `charge_box`
  ADD CONSTRAINT `FK_charge_box_address_apk` FOREIGN KEY (`address_pk`) REFERENCES `address` (`address_pk`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
