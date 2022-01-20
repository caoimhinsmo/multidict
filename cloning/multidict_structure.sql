-- MariaDB dump 10.19  Distrib 10.5.13-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: multidict_structure
-- ------------------------------------------------------
-- Server version	10.5.13-MariaDB-0ubuntu0.21.10.1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `banext`
--

DROP TABLE IF EXISTS `banext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banext` (
  `ext` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banext`
--

LOCK TABLES `banext` WRITE;
/*!40000 ALTER TABLE `banext` DISABLE KEYS */;
/*!40000 ALTER TABLE `banext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clilstore`
--

DROP TABLE IF EXISTS `clilstore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clilstore` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `sl` varchar(63) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(1) NOT NULL,
  `title` varchar(511) COLLATE utf8_unicode_ci NOT NULL,
  `text` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `medembed` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `medfloat` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `medtype` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=none;1=sound only;2=video',
  `medlen` int(11) NOT NULL DEFAULT 0 COMMENT 'Total length in seconds',
  `summary` varchar(1023) COLLATE utf8_unicode_ci NOT NULL,
  `langnotes` varchar(1023) COLLATE utf8_unicode_ci NOT NULL,
  `css` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `words` int(11) DEFAULT NULL,
  `created` int(11) NOT NULL DEFAULT 0 COMMENT 'Unix time',
  `changed` int(11) NOT NULL DEFAULT 0 COMMENT 'Unix time',
  `licence` varchar(31) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'BY-SA',
  `test` tinyint(4) NOT NULL DEFAULT 0,
  `admininfo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `buttons` int(11) NOT NULL DEFAULT 0,
  `files` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `likes` double NOT NULL DEFAULT 0,
  `offer` varchar(31) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Offered to',
  `offerTime` int(11) NOT NULL DEFAULT 0 COMMENT 'Unix time',
  `newclickTime` int(11) NOT NULL DEFAULT 0,
  `editHtml` int(1) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  KEY `sl` (`sl`),
  KEY `level` (`level`),
  KEY `title` (`title`(255)),
  KEY `medlen` (`medlen`),
  KEY `words` (`words`),
  KEY `created` (`created`),
  KEY `changed` (`changed`),
  KEY `offer` (`offer`),
  CONSTRAINT `clilstore_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `users` (`user`) ON UPDATE CASCADE,
  CONSTRAINT `clilstore_ibfk_2` FOREIGN KEY (`offer`) REFERENCES `users` (`user`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10144 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Store for uploaded CLIL material';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clilstore`
--

LOCK TABLES `clilstore` WRITE;
/*!40000 ALTER TABLE `clilstore` DISABLE KEYS */;
/*!40000 ALTER TABLE `clilstore` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csButtons`
--

DROP TABLE IF EXISTS `csButtons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `csButtons` (
  `id` int(11) NOT NULL,
  `ord` tinyint(4) NOT NULL,
  `but` varchar(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `wl` tinyint(4) NOT NULL DEFAULT 0,
  `new` tinyint(4) NOT NULL DEFAULT 0,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`,`ord`),
  CONSTRAINT `csButtons_ibfk_1` FOREIGN KEY (`id`) REFERENCES `clilstore` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Store for uploaded CLIL material';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csButtons`
--

LOCK TABLES `csButtons` WRITE;
/*!40000 ALTER TABLE `csButtons` DISABLE KEYS */;
/*!40000 ALTER TABLE `csButtons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csFields`
--

DROP TABLE IF EXISTS `csFields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `csFields` (
  `fid` tinyint(4) NOT NULL,
  `fd` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `minmax` tinyint(4) NOT NULL DEFAULT 0,
  `m0` tinyint(4) NOT NULL,
  `m1` tinyint(4) NOT NULL,
  `m2` tinyint(4) NOT NULL,
  `m3` tinyint(4) NOT NULL,
  `sortpri` int(4) NOT NULL,
  `sortord` tinyint(4) NOT NULL COMMENT '1=ASC; -1=DESC',
  `val1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `val2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`fid`),
  UNIQUE KEY `field` (`fd`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Field names and properties for Clilstore filter';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csFields`
--

LOCK TABLES `csFields` WRITE;
/*!40000 ALTER TABLE `csFields` DISABLE KEYS */;
/*!40000 ALTER TABLE `csFields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csFiles`
--

DROP TABLE IF EXISTS `csFiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `csFiles` (
  `fileid` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bloigh` mediumblob NOT NULL,
  `mime` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`fileid`),
  UNIQUE KEY `id_filename` (`id`,`filename`),
  CONSTRAINT `csFiles_ibfk_1` FOREIGN KEY (`id`) REFERENCES `clilstore` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11646 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Store for uploaded CLIL material';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csFiles`
--

LOCK TABLES `csFiles` WRITE;
/*!40000 ALTER TABLE `csFiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `csFiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csFilter`
--

DROP TABLE IF EXISTS `csFilter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `csFilter` (
  `csid` int(11) NOT NULL,
  `fd` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `m0` tinyint(4) NOT NULL,
  `m1` tinyint(4) NOT NULL,
  `m2` tinyint(4) NOT NULL,
  `m3` tinyint(4) NOT NULL,
  `sortpri` int(4) NOT NULL DEFAULT 0,
  `sortord` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1=ASC; -1=DESC',
  `val1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `val2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`csid`,`fd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='For cs session, remembers fields present/absent, sort priority and order';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csFilter`
--

LOCK TABLES `csFilter` WRITE;
/*!40000 ALTER TABLE `csFilter` DISABLE KEYS */;
/*!40000 ALTER TABLE `csFilter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csSession`
--

DROP TABLE IF EXISTS `csSession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `csSession` (
  `csid` int(11) NOT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT 0,
  `incTest` tinyint(4) NOT NULL DEFAULT 0,
  `user` varchar(31) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `crTime` int(11) NOT NULL,
  `nCalls` int(11) NOT NULL DEFAULT 0,
  `IPaddr` varchar(63) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`csid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Clilstore browser session, linked to by cookie';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csSession`
--

LOCK TABLES `csSession` WRITE;
/*!40000 ALTER TABLE `csSession` DISABLE KEYS */;
/*!40000 ALTER TABLE `csSession` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csVoc`
--

DROP TABLE IF EXISTS `csVoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `csVoc` (
  `vocid` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `sl` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `word` varchar(50) CHARACTER SET utf8 NOT NULL,
  `calls` int(11) NOT NULL DEFAULT 0,
  `head` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `meaning` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`vocid`),
  UNIQUE KEY `user_2` (`user`,`sl`,`word`) USING BTREE,
  KEY `user` (`user`),
  KEY `sl` (`sl`),
  CONSTRAINT `csVoc_ibfk_1` FOREIGN KEY (`sl`) REFERENCES `lang` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `csVoc_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`user`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13227 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csVoc`
--

LOCK TABLES `csVoc` WRITE;
/*!40000 ALTER TABLE `csVoc` DISABLE KEYS */;
/*!40000 ALTER TABLE `csVoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csVocUnit`
--

DROP TABLE IF EXISTS `csVocUnit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `csVocUnit` (
  `vocid` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `calls` int(11) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`vocid`,`unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csVocUnit`
--

LOCK TABLES `csVocUnit` WRITE;
/*!40000 ALTER TABLE `csVocUnit` DISABLE KEYS */;
/*!40000 ALTER TABLE `csVocUnit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csWclick`
--

DROP TABLE IF EXISTS `csWclick`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `csWclick` (
  `unit` int(11) NOT NULL,
  `word` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `newclicks` int(11) NOT NULL DEFAULT 0,
  `utime` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`unit`,`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Counts total clicks on each word in each unit';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csWclick`
--

LOCK TABLES `csWclick` WRITE;
/*!40000 ALTER TABLE `csWclick` DISABLE KEYS */;
/*!40000 ALTER TABLE `csWclick` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cspf`
--

DROP TABLE IF EXISTS `cspf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cspf` (
  `pf` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `prio` int(11) NOT NULL COMMENT 'initialized to utime',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`pf`),
  UNIQUE KEY `user` (`user`,`prio`),
  CONSTRAINT `cspf_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cspf`
--

LOCK TABLES `cspf` WRITE;
/*!40000 ALTER TABLE `cspf` DISABLE KEYS */;
/*!40000 ALTER TABLE `cspf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cspfPermit`
--

DROP TABLE IF EXISTS `cspfPermit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cspfPermit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pf` int(11) NOT NULL,
  `teacher` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `pf` (`pf`,`teacher`) USING BTREE,
  KEY `teacher` (`teacher`) USING BTREE,
  CONSTRAINT `cspfPermit_ibfk_2` FOREIGN KEY (`teacher`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cspfPermit_ibfk_3` FOREIGN KEY (`pf`) REFERENCES `cspf` (`pf`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cspfPermit`
--

LOCK TABLES `cspfPermit` WRITE;
/*!40000 ALTER TABLE `cspfPermit` DISABLE KEYS */;
/*!40000 ALTER TABLE `cspfPermit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cspfUnit`
--

DROP TABLE IF EXISTS `cspfUnit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cspfUnit` (
  `pfu` int(11) NOT NULL AUTO_INCREMENT,
  `pf` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `ord` int(11) NOT NULL COMMENT 'Sort order',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`pfu`),
  UNIQUE KEY `pf` (`pf`,`unit`) USING BTREE,
  KEY `unit` (`unit`),
  CONSTRAINT `cspfUnit_ibfk_3` FOREIGN KEY (`unit`) REFERENCES `clilstore` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cspfUnit_ibfk_4` FOREIGN KEY (`pf`) REFERENCES `cspf` (`pf`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=522 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cspfUnit`
--

LOCK TABLES `cspfUnit` WRITE;
/*!40000 ALTER TABLE `cspfUnit` DISABLE KEYS */;
/*!40000 ALTER TABLE `cspfUnit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cspfUnitLearned`
--

DROP TABLE IF EXISTS `cspfUnitLearned`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cspfUnitLearned` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pfu` int(11) NOT NULL,
  `learned` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `ord` int(11) NOT NULL COMMENT 'Sort order',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pfu` (`pfu`),
  CONSTRAINT `cspfUnitLearned_ibfk_1` FOREIGN KEY (`pfu`) REFERENCES `cspfUnit` (`pfu`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cspfUnitLearned`
--

LOCK TABLES `cspfUnitLearned` WRITE;
/*!40000 ALTER TABLE `cspfUnitLearned` DISABLE KEYS */;
/*!40000 ALTER TABLE `cspfUnitLearned` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cspfUnitWork`
--

DROP TABLE IF EXISTS `cspfUnitWork`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cspfUnitWork` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pfu` int(11) NOT NULL,
  `work` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ord` int(11) NOT NULL COMMENT 'Sort order',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pfu` (`pfu`),
  CONSTRAINT `cspfUnitWork_ibfk_1` FOREIGN KEY (`pfu`) REFERENCES `cspfUnit` (`pfu`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cspfUnitWork`
--

LOCK TABLES `cspfUnitWork` WRITE;
/*!40000 ALTER TABLE `cspfUnitWork` DISABLE KEYS */;
/*!40000 ALTER TABLE `cspfUnitWork` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom`
--

DROP TABLE IF EXISTS `custom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom` (
  `idc` int(11) NOT NULL AUTO_INCREMENT,
  `sl` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `word` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `disambig` varchar(31) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pri` int(11) NOT NULL DEFAULT 0,
  `gram` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idc`),
  UNIQUE KEY `sl` (`sl`,`word`,`disambig`),
  KEY `sl_word` (`sl`,`word`)
) ENGINE=InnoDB AUTO_INCREMENT=1012 DEFAULT CHARSET=utf8mb4 COMMENT='Custom wordlists ‘dictionary’';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom`
--

LOCK TABLES `custom` WRITE;
/*!40000 ALTER TABLE `custom` DISABLE KEYS */;
/*!40000 ALTER TABLE `custom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customtr`
--

DROP TABLE IF EXISTS `customtr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customtr` (
  `idctr` int(11) NOT NULL AUTO_INCREMENT,
  `idc` int(11) NOT NULL,
  `tl` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `meaning` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idctr`),
  KEY `idc` (`idc`),
  CONSTRAINT `customtr_ibfk_1` FOREIGN KEY (`idc`) REFERENCES `custom` (`idc`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1480 DEFAULT CHARSET=utf8mb4 COMMENT='Custom wordlists ‘dictionary’';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customtr`
--

LOCK TABLES `customtr` WRITE;
/*!40000 ALTER TABLE `customtr` DISABLE KEYS */;
/*!40000 ALTER TABLE `customtr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customwf`
--

DROP TABLE IF EXISTS `customwf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customwf` (
  `idcwf` int(11) NOT NULL AUTO_INCREMENT,
  `wf` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `idc` int(11) NOT NULL,
  `pri` int(11) NOT NULL DEFAULT 50,
  `priWhy` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idcwf`),
  KEY `lang` (`wf`),
  KEY `idc` (`idc`),
  CONSTRAINT `customwf_ibfk_1` FOREIGN KEY (`idc`) REFERENCES `custom` (`idc`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=473 DEFAULT CHARSET=utf8mb4 COMMENT='wordform index to words in custom';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customwf`
--

LOCK TABLES `customwf` WRITE;
/*!40000 ALTER TABLE `customwf` DISABLE KEYS */;
/*!40000 ALTER TABLE `customwf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dict`
--

DROP TABLE IF EXISTS `dict`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dict` (
  `dict` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(63) COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(31) COLLATE utf8_unicode_ci NOT NULL COMMENT 'm=mini; p=page-image',
  `type` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `icon` blob NOT NULL COMMENT 'Blob',
  `mimetype` varchar(63) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'image/png',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(255) CHARACTER SET utf8 NOT NULL,
  `noHttps` int(11) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`dict`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='A registry of online dictionaries and their Multidict ids';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dict`
--

LOCK TABLES `dict` WRITE;
/*!40000 ALTER TABLE `dict` DISABLE KEYS */;
/*!40000 ALTER TABLE `dict` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dictHl`
--

DROP TABLE IF EXISTS `dictHl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dictHl` (
  `dict` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0 COMMENT '0=default',
  `hl` varchar(31) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user int. lang.',
  `hlCode` varchar(31) COLLATE utf8_unicode_ci NOT NULL COMMENT 'if specific to dict.',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`dict`,`hl`),
  UNIQUE KEY `dict_order` (`dict`,`order`),
  KEY `lang` (`hl`),
  CONSTRAINT `dictHl_ibfk_1` FOREIGN KEY (`dict`) REFERENCES `dict` (`dict`) ON UPDATE CASCADE,
  CONSTRAINT `dictHl_ibfk_2` FOREIGN KEY (`hl`) REFERENCES `lang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Lists user interface langs for dicts which provide several';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dictHl`
--

LOCK TABLES `dictHl` WRITE;
/*!40000 ALTER TABLE `dictHl` DISABLE KEYS */;
/*!40000 ALTER TABLE `dictHl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dictLang`
--

DROP TABLE IF EXISTS `dictLang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dictLang` (
  `dict` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `lang` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `langCode` varchar(31) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'if specific to dict.',
  `encoding` varchar(31) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'if ≠ dict default',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`dict`,`lang`),
  KEY `lang` (`lang`),
  CONSTRAINT `dictLang_ibfk_1` FOREIGN KEY (`dict`) REFERENCES `dict` (`dict`) ON UPDATE CASCADE,
  CONSTRAINT `dictLang_ibfk_2` FOREIGN KEY (`lang`) REFERENCES `lang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Lists languages catered for by dictionaries which can do any';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dictLang`
--

LOCK TABLES `dictLang` WRITE;
/*!40000 ALTER TABLE `dictLang` DISABLE KEYS */;
/*!40000 ALTER TABLE `dictLang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dictPage`
--

DROP TABLE IF EXISTS `dictPage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dictPage` (
  `dictindex` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `word` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'first/last word (⇒ dictPageURL)',
  `original` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'preserve word',
  `page` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`dictindex`,`word`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dictPage`
--

LOCK TABLES `dictPage` WRITE;
/*!40000 ALTER TABLE `dictPage` DISABLE KEYS */;
/*!40000 ALTER TABLE `dictPage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dictPageURL`
--

DROP TABLE IF EXISTS `dictPageURL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dictPageURL` (
  `dictindex` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `inst` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'instance',
  `firstword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vol` tinyint(4) NOT NULL DEFAULT 1,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'including any GET parameters',
  `pparams` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'POST parameters',
  `firstlast` varchar(31) COLLATE utf8_unicode_ci NOT NULL COMMENT 'first|last (which word of page indexed)',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`dictindex`,`inst`,`firstword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dictPageURL`
--

LOCK TABLES `dictPageURL` WRITE;
/*!40000 ALTER TABLE `dictPageURL` DISABLE KEYS */;
/*!40000 ALTER TABLE `dictPageURL` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dictParam`
--

DROP TABLE IF EXISTS `dictParam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dictParam` (
  `dict` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `sl` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `tl` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `quality` int(11) NOT NULL DEFAULT 0,
  `url` varchar(2046) COLLATE utf8_unicode_ci NOT NULL COMMENT 'including any GET parameters',
  `urlc` varchar(2046) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pparams` varchar(4091) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'POST parameters',
  `encoding` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `wfrule` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Rule for deriving wordforms',
  `charextra` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'stripAccent=sguab às accents; entity=rudan mar &#233;',
  `handling` varchar(4091) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'redirect|zapOnload|form',
  `lem` tinyint(4) DEFAULT NULL COMMENT '1=lemmatises;0=doesn’t',
  `etym` float DEFAULT NULL COMMENT 'quality (0-1) airson etymology',
  `hide` tinyint(4) NOT NULL DEFAULT 0,
  `message` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` varchar(1023) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`dict`,`sl`,`tl`),
  KEY `sl` (`sl`,`tl`,`dict`),
  KEY `tl` (`tl`),
  CONSTRAINT `dictParam_ibfk_1` FOREIGN KEY (`dict`) REFERENCES `dict` (`dict`) ON UPDATE CASCADE,
  CONSTRAINT `dictParam_ibfk_2` FOREIGN KEY (`sl`) REFERENCES `lang` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `dictParam_ibfk_3` FOREIGN KEY (`tl`) REFERENCES `lang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Parameters required to look up words in online dictionaries';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dictParam`
--

LOCK TABLES `dictParam` WRITE;
/*!40000 ALTER TABLE `dictParam` DISABLE KEYS */;
/*!40000 ALTER TABLE `dictParam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `dictParamV`
--

DROP TABLE IF EXISTS `dictParamV`;
/*!50001 DROP VIEW IF EXISTS `dictParamV`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `dictParamV` (
  `dict` tinyint NOT NULL,
  `sl` tinyint NOT NULL,
  `tl` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `quality` tinyint NOT NULL,
  `url` tinyint NOT NULL,
  `pparams` tinyint NOT NULL,
  `encoding` tinyint NOT NULL,
  `charextra` tinyint NOT NULL,
  `handling` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lang`
--

DROP TABLE IF EXISTS `lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lang` (
  `id` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `ll` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iso_639_3` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `endonym` varchar(31) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_en` varchar(31) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wiki` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `quality` int(4) NOT NULL COMMENT 'Rank as Multidict tl',
  `pools` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Information on languages (and variants)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lang`
--

LOCK TABLES `lang` WRITE;
/*!40000 ALTER TABLE `lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `langAltSl`
--

DROP TABLE IF EXISTS `langAltSl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `langAltSl` (
  `id` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `alt` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `ord` int(11) NOT NULL DEFAULT 1 COMMENT 'increasing distance',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`,`alt`,`ord`),
  KEY `alt` (`alt`),
  CONSTRAINT `langAltSl_ibfk_1` FOREIGN KEY (`id`) REFERENCES `lang` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `langAltSl_ibfk_2` FOREIGN KEY (`alt`) REFERENCES `lang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Shows related languages';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `langAltSl`
--

LOCK TABLES `langAltSl` WRITE;
/*!40000 ALTER TABLE `langAltSl` DISABLE KEYS */;
/*!40000 ALTER TABLE `langAltSl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `langAltTl`
--

DROP TABLE IF EXISTS `langAltTl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `langAltTl` (
  `id` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `alt` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `ord` int(11) NOT NULL DEFAULT 1 COMMENT 'increasing distance',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`,`alt`,`ord`),
  KEY `alt` (`alt`),
  CONSTRAINT `langAltTl_ibfk_1` FOREIGN KEY (`id`) REFERENCES `lang` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `langAltTl_ibfk_2` FOREIGN KEY (`alt`) REFERENCES `lang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Shows related languages';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `langAltTl`
--

LOCK TABLES `langAltTl` WRITE;
/*!40000 ALTER TABLE `langAltTl` DISABLE KEYS */;
/*!40000 ALTER TABLE `langAltTl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `langIcons`
--

DROP TABLE IF EXISTS `langIcons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `langIcons` (
  `id` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `icon` blob NOT NULL,
  `mimetype` varchar(63) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'image/png',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Information on languages (and variants)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `langIcons`
--

LOCK TABLES `langIcons` WRITE;
/*!40000 ALTER TABLE `langIcons` DISABLE KEYS */;
/*!40000 ALTER TABLE `langIcons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `langV`
--

DROP TABLE IF EXISTS `langV`;
/*!50001 DROP VIEW IF EXISTS `langV`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `langV` (
  `id` tinyint NOT NULL,
  `endonym` tinyint NOT NULL,
  `name_en` tinyint NOT NULL,
  `wiki` tinyint NOT NULL,
  `parentage` tinyint NOT NULL,
  `parentage_ord` tinyint NOT NULL,
  `quality` tinyint NOT NULL,
  `pools` tinyint NOT NULL,
  `icon` tinyint NOT NULL,
  `mimetype` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lemmas`
--

DROP TABLE IF EXISTS `lemmas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lemmas` (
  `lang` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `batch` varchar(31) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ord` int(11) NOT NULL DEFAULT 0,
  `wordform` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lemma` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `gram` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`lang`,`batch`,`wordform`,`lemma`,`gram`),
  KEY `ord` (`ord`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lemmas`
--

LOCK TABLES `lemmas` WRITE;
/*!40000 ALTER TABLE `lemmas` DISABLE KEYS */;
/*!40000 ALTER TABLE `lemmas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `utime` int(11) NOT NULL COMMENT 'utime',
  `ip` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(63) COLLATE utf8_unicode_ci NOT NULL,
  `user2` varchar(31) COLLATE utf8_unicode_ci DEFAULT NULL,
  `info` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`logid`),
  KEY `user` (`user`),
  KEY `toiseach` (`utime`)
) ENGINE=InnoDB AUTO_INCREMENT=27495 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mimetypes`
--

DROP TABLE IF EXISTS `mimetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mimetypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ext` varchar(31) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mime` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `seq` tinyint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ext` (`ext`),
  KEY `type` (`mime`)
) ENGINE=InnoDB AUTO_INCREMENT=1381 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mimetypes`
--

LOCK TABLES `mimetypes` WRITE;
/*!40000 ALTER TABLE `mimetypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `mimetypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mt`
--

DROP TABLE IF EXISTS `mt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `priName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `endonym` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inbhe` varchar(31) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nonLL=non LingList; nonCT=non CompTree',
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ord` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Order among sibs',
  `parent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parentage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parentage_ord` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `altNames` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `geog` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `confid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `end` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lid` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `lid` (`lid`),
  UNIQUE KEY `parentage` (`parentage`),
  UNIQUE KEY `parent` (`parent`,`ord`),
  UNIQUE KEY `parentage_ord` (`parentage_ord`),
  KEY `codes` (`codes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='LinguistList Composite Tree';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mt`
--

LOCK TABLES `mt` WRITE;
/*!40000 ALTER TABLE `mt` DISABLE KEYS */;
/*!40000 ALTER TABLE `mt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `token` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `expires` int(11) NOT NULL COMMENT 'utime',
  `user` varchar(31) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `purpose` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `data` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`token`),
  KEY `user` (`user`),
  CONSTRAINT `user` FOREIGN KEY (`user`) REFERENCES `users` (`user`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userGroup`
--

DROP TABLE IF EXISTS `userGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userGroup` (
  `user` varchar(31) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `grp` varchar(31) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user`,`grp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userGroup`
--

LOCK TABLES `userGroup` WRITE;
/*!40000 ALTER TABLE `userGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `userGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userGrp`
--

DROP TABLE IF EXISTS `userGrp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userGrp` (
  `user` varchar(31) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `grp` varchar(31) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user`,`grp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userGrp`
--

LOCK TABLES `userGrp` WRITE;
/*!40000 ALTER TABLE `userGrp` DISABLE KEYS */;
/*!40000 ALTER TABLE `userGrp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_unit`
--

DROP TABLE IF EXISTS `user_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_unit` (
  `user` varchar(31) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `unit` int(11) NOT NULL,
  `likes` double NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user`,`unit`),
  KEY `unit` (`unit`),
  CONSTRAINT `user_unit_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_unit_ibfk_2` FOREIGN KEY (`unit`) REFERENCES `clilstore` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_unit`
--

LOCK TABLES `user_unit` WRITE;
/*!40000 ALTER TABLE `user_unit` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emailVerUtime` int(11) NOT NULL DEFAULT 0 COMMENT 'verification utime',
  `joined` int(11) NOT NULL COMMENT 'utime',
  `adlev` int(11) NOT NULL DEFAULT 0 COMMENT 'Admin level',
  `unitLang` varchar(31) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `highlightRow` tinyint(4) NOT NULL DEFAULT 0 COMMENT '-1=never;0=default;1=always',
  `record` int(11) NOT NULL DEFAULT 1,
  `csid` int(11) DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user`),
  UNIQUE KEY `email` (`email`),
  KEY `unitLang` (`unitLang`),
  KEY `csid` (`csid`),
  CONSTRAINT `csid` FOREIGN KEY (`csid`) REFERENCES `csSession` (`csid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wlSessDict`
--

DROP TABLE IF EXISTS `wlSessDict`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wlSessDict` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `cuil` tinyint(4) NOT NULL COMMENT 'Cyclic index',
  `sl` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `tl` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `dict` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `utime` int(11) NOT NULL COMMENT 'unix time',
  PRIMARY KEY (`sid`,`cuil`),
  KEY `id_smo` (`sl`)
) ENGINE=InnoDB AUTO_INCREMENT=16786 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Log de na logins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wlSessDict`
--

LOCK TABLES `wlSessDict` WRITE;
/*!40000 ALTER TABLE `wlSessDict` DISABLE KEYS */;
/*!40000 ALTER TABLE `wlSessDict` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wlSession`
--

DROP TABLE IF EXISTS `wlSession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wlSession` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `sl` varchar(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tl` varchar(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dict` varchar(31) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `word` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `wfs` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `inc` int(11) NOT NULL DEFAULT 0,
  `rmLi` tinyint(4) NOT NULL DEFAULT 0,
  `mode` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT 'How dictionary appears: ss=splitscreen; st=same tab; nt=new tab; pu=popup',
  `navsize` smallint(3) NOT NULL DEFAULT -1,
  `mdadv` int(11) NOT NULL DEFAULT -1 COMMENT 'MD advanced interface',
  `utime` int(11) NOT NULL DEFAULT 0 COMMENT 'unix time',
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`sid`),
  KEY `id_smo` (`sl`)
) ENGINE=InnoDB AUTO_INCREMENT=4863632 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Log de na logins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wlSession`
--

LOCK TABLES `wlSession` WRITE;
/*!40000 ALTER TABLE `wlSession` DISABLE KEYS */;
/*!40000 ALTER TABLE `wlSession` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wlSlTlDictCalls`
--

DROP TABLE IF EXISTS `wlSlTlDictCalls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wlSlTlDictCalls` (
  `sl` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `tl` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `dict` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `calls` int(11) NOT NULL,
  `best` tinyint(4) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`sl`,`tl`,`dict`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Fiosrachadh mu dheidhinn fhaclairean';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wlSlTlDictCalls`
--

LOCK TABLES `wlSlTlDictCalls` WRITE;
/*!40000 ALTER TABLE `wlSlTlDictCalls` DISABLE KEYS */;
/*!40000 ALTER TABLE `wlSlTlDictCalls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wlUser`
--

DROP TABLE IF EXISTS `wlUser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wlUser` (
  `uid` int(11) NOT NULL,
  `calls` int(11) NOT NULL DEFAULT 0,
  `sid` int(11) NOT NULL DEFAULT 0,
  `IP` varchar(31) COLLATE utf8_unicode_ci NOT NULL COMMENT 'last IP address',
  `crIP` varchar(31) COLLATE utf8_unicode_ci NOT NULL COMMENT 'IP addr. at creation',
  `utime` int(11) NOT NULL COMMENT 'last Unix time',
  `crTime` int(11) NOT NULL,
  `crHost` varchar(63) COLLATE utf8_unicode_ci NOT NULL,
  `crReferer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Information on choices and previous use per user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wlUser`
--

LOCK TABLES `wlUser` WRITE;
/*!40000 ALTER TABLE `wlUser` DISABLE KEYS */;
/*!40000 ALTER TABLE `wlUser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wlUserSlTl`
--

DROP TABLE IF EXISTS `wlUserSlTl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wlUserSlTl` (
  `uid` int(11) NOT NULL,
  `sl` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `tl` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `calls` int(11) NOT NULL COMMENT 'Total calls for that sl,tl',
  `dict` varchar(31) COLLATE utf8_unicode_ci NOT NULL COMMENT 'last dict used for that sl,tl',
  `utime` int(11) NOT NULL COMMENT 'Unix time',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`uid`,`sl`,`tl`),
  KEY `uidSl` (`uid`,`sl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Information on choices and previous use per user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wlUserSlTl`
--

LOCK TABLES `wlUserSlTl` WRITE;
/*!40000 ALTER TABLE `wlUserSlTl` DISABLE KEYS */;
/*!40000 ALTER TABLE `wlUserSlTl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `dictParamV`
--

/*!50001 DROP TABLE IF EXISTS `dictParamV`*/;
/*!50001 DROP VIEW IF EXISTS `dictParamV`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`caoimhin`@`localhost` SQL SECURITY INVOKER */
/*!50001 VIEW `dictParamV` AS select `multidict`.`dictParam`.`dict` AS `dict`,`multidict`.`dictParam`.`sl` AS `sl`,`multidict`.`dictParam`.`tl` AS `tl`,`multidict`.`dict`.`name` AS `name`,`multidict`.`dictParam`.`quality` AS `quality`,`multidict`.`dictParam`.`url` AS `url`,`multidict`.`dictParam`.`pparams` AS `pparams`,`multidict`.`dictParam`.`encoding` AS `encoding`,`multidict`.`dictParam`.`charextra` AS `charextra`,`multidict`.`dictParam`.`handling` AS `handling` from (`multidict`.`dictParam` join `multidict`.`dict`) where `multidict`.`dictParam`.`dict` = `multidict`.`dict`.`dict` and `multidict`.`dictParam`.`hide` <> 1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `langV`
--

/*!50001 DROP TABLE IF EXISTS `langV`*/;
/*!50001 DROP VIEW IF EXISTS `langV`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`caoimhin`@`localhost` SQL SECURITY INVOKER */
/*!50001 VIEW `langV` AS select `multidict`.`lang`.`id` AS `id`,`multidict`.`lang`.`endonym` AS `endonym`,`multidict`.`lang`.`name_en` AS `name_en`,`multidict`.`lang`.`wiki` AS `wiki`,`multidict`.`mt`.`parentage` AS `parentage`,`multidict`.`mt`.`parentage_ord` AS `parentage_ord`,`multidict`.`lang`.`quality` AS `quality`,`multidict`.`lang`.`pools` AS `pools`,`multidict`.`langIcons`.`icon` AS `icon`,`multidict`.`langIcons`.`mimetype` AS `mimetype` from ((`multidict`.`lang` left join `multidict`.`langIcons` on(`multidict`.`lang`.`id` = `multidict`.`langIcons`.`id`)) left join `multidict`.`mt` on(`multidict`.`lang`.`ll` = `multidict`.`mt`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-01-19 22:36:48
