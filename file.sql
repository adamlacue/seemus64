-- MySQL dump 10.13  Distrib 8.3.0, for macos14.2 (x86_64)
--
-- Host: localhost    Database: Seemus
-- ------------------------------------------------------
-- Server version	8.2.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `Seemus`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `Seemus` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `Seemus`;

--
-- Table structure for table `tbContent`
--

 TABLE IF EXISTS `tbContent`;
/*!4010DROP1 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbContent` (
  `fdAutoId` int NOT NULL AUTO_INCREMENT,
  `fdID` varchar(45) DEFAULT NULL,
  `fdTitle` blob,
  `fdDesc` blob,
  `fdHTML` varchar(45) DEFAULT NULL,
  `fdDTCreated` varchar(45) DEFAULT NULL,
  `ftDTupdated` varchar(45) DEFAULT NULL,
  `fdCreator` varchar(45) DEFAULT NULL,
  `fdArchive` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`fdAutoId`),
  UNIQUE KEY `fdAutoId_UNIQUE` (`fdAutoId`),
  UNIQUE KEY `fdID_UNIQUE` (`fdID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbContent`
--

LOCK TABLES `tbContent` WRITE;
/*!40000 ALTER TABLE `tbContent` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbContent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbJoinUserContent`
--

DROP TABLE IF EXISTS `tbJoinUserContent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbJoinUserContent` (
  `fdautoID` int NOT NULL,
  `fdID` varchar(45) DEFAULT NULL,
  `fdID_USER` varchar(45) DEFAULT NULL,
  `FDID_CONTENT` varchar(45) DEFAULT NULL,
  `FDREAD` varchar(45) DEFAULT NULL,
  `FDWRITE` varchar(45) DEFAULT NULL,
  `FDARCLINE` varchar(45) DEFAULT NULL,
  `FDDELETE` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`fdautoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbJoinUserContent`
--

LOCK TABLES `tbJoinUserContent` WRITE;
/*!40000 ALTER TABLE `tbJoinUserContent` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbJoinUserContent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbTable`
--

DROP TABLE IF EXISTS `tbTable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbTable` (
  `idfd_table` int NOT NULL,
  `fd_tablecol` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idfd_table`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbTable`
--

LOCK TABLES `tbTable` WRITE;
/*!40000 ALTER TABLE `tbTable` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbTable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbUsers`
--

DROP TABLE IF EXISTS `tbUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbUsers` (
  `firstname` varchar(60) NOT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`firstname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbUsers`
--

LOCK TABLES `tbUsers` WRITE;
/*!40000 ALTER TABLE `tbUsers` DISABLE KEYS */;
INSERT INTO `tbUsers` VALUES ('John','Doe','john@example.com','johndoe1','$2y$10$bZtFsTRrho8zIDj3f.CDKuuzCTv/hiLb3y4Jr/Q4hoYG484p3JMKK'),('John2','Doe','john@example.com',NULL,NULL),('John67','Doe','john@example.com',NULL,NULL);
/*!40000 ALTER TABLE `tbUsers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-19 12:01:31
