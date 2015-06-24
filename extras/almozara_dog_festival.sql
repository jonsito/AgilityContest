-- MySQL dump 10.15  Distrib 10.0.19-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: agility
-- ------------------------------------------------------
-- Server version	10.0.19-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias_perro`
--

DROP TABLE IF EXISTS `categorias_perro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_perro` (
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_perro`
--

LOCK TABLES `categorias_perro` WRITE;
/*!40000 ALTER TABLE `categorias_perro` DISABLE KEYS */;
INSERT INTO `categorias_perro` VALUES 
('-','Sin especificar'),
('L','Large - Standard - 60'),
('M','Medium - Midi - 50'),
('S','Small - Mini - 40'),
('T','Tiny - Toy - 30');
/*!40000 ALTER TABLE `categorias_perro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clubes`
--

DROP TABLE IF EXISTS `clubes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clubes` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL DEFAULT '',
  `NombreLargo` varchar(255) NOT NULL DEFAULT '""',
  `Direccion1` varchar(255) DEFAULT NULL,
  `Direccion2` varchar(255) DEFAULT NULL,
  `Provincia` varchar(32) NOT NULL DEFAULT '-- Sin asignar --',
  `Pais` varchar(32) NOT NULL DEFAULT 'España',
  `Contacto1` varchar(255) DEFAULT NULL,
  `Contacto2` varchar(255) DEFAULT NULL,
  `Contacto3` varchar(255) DEFAULT NULL,
  `GPS` varchar(255) DEFAULT NULL,
  `Web` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Facebook` varchar(255) DEFAULT NULL,
  `Google` varchar(255) DEFAULT NULL,
  `Twitter` varchar(255) DEFAULT NULL,
  `Logo` varchar(255) DEFAULT 'rsce.png',
  `Federations` int(4) NOT NULL DEFAULT '1',
  `Observaciones` varchar(255) DEFAULT NULL,
  `Baja` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Clubes_Nombre` (`Nombre`),
  KEY `Clubes_Provincia` (`Provincia`),
  CONSTRAINT `Clubes_ibfk_1` FOREIGN KEY (`Provincia`) REFERENCES `provincias` (`Provincia`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=267 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubes`
--

LOCK TABLES `clubes` WRITE;
/*!40000 ALTER TABLE `clubes` DISABLE KEYS */;
INSERT INTO `clubes` VALUES 
(1,'-- Sin asignar --','\"\"','','','-- Sin asignar --','ES','','','','','','','','','','rsce.png',7,'NO BORRAR ESTA ENTRADA. SE USARA PARA AQUELLOS GUIAS QUE NO TENGAN CLUB ASIGNADO',0),
(2,'L\'Almozara','','Camino de Pinseque, 147-A','50190 Garrapinillos (Zaragoza)','-- Sin Asignar --','ES',' + 34 637 54 15 86','','','','','','','','','almozara.png',1,'',0),
(3,'Afghanistan','','','','-- Sin asignar --','AF','','','','','','','','','','../../server/i18n/AF.png',1,'',0),
(4,'Albania','','','','-- Sin asignar --','AL','','','','','','','','','','../../server/i18n/AL.png',1,'',0),
(5,'Algeria','','','','-- Sin asignar --','DZ','','','','','','','','','','../../server/i18n/DZ.png',1,'',0),
(6,'American Samoa','','','','-- Sin asignar --','AS','','','','','','','','','','../../server/i18n/AS.png',1,'',0),
(7,'Andorra','','','','-- Sin asignar --','AD','','','','','','','','','','../../server/i18n/AD.png',1,'',0),
(8,'Angola','','','','-- Sin asignar --','AO','','','','','','','','','','../../server/i18n/AO.png',1,'',0),
(9,'Anguilla','','','','-- Sin asignar --','AI','','','','','','','','','','../../server/i18n/AI.png',1,'',0),
(10,'Antarctica','','','','-- Sin asignar --','AQ','','','','','','','','','','../../server/i18n/AQ.png',1,'',0),
(11,'Antigua and Barbuda','','','','-- Sin asignar --','AG','','','','','','','','','','../../server/i18n/AG.png',1,'',0),
(12,'Argentina','','','','-- Sin asignar --','AR','','','','','','','','','','../../server/i18n/AR.png',1,'',0),
(13,'Armenia','','','','-- Sin asignar --','AM','','','','','','','','','','../../server/i18n/AM.png',1,'',0),
(14,'Aruba','','','','-- Sin asignar --','AW','','','','','','','','','','../../server/i18n/AW.png',1,'',0),
(15,'Australia','','','','-- Sin asignar --','AU','','','','','','','','','','../../server/i18n/AU.png',1,'',0),
(16,'Austria','','','','-- Sin asignar --','AT','','','','','','','','','','../../server/i18n/AT.png',1,'',0),
(17,'Azerbaijan','','','','-- Sin asignar --','AZ','','','','','','','','','','../../server/i18n/AZ.png',1,'',0),
(18,'Bahamas','','','','-- Sin asignar --','BS','','','','','','','','','','../../server/i18n/BS.png',1,'',0),
(19,'Bahrain','','','','-- Sin asignar --','BH','','','','','','','','','','../../server/i18n/BH.png',1,'',0),
(20,'Bangladesh','','','','-- Sin asignar --','BD','','','','','','','','','','../../server/i18n/BD.png',1,'',0),
(21,'Barbados','','','','-- Sin asignar --','BB','','','','','','','','','','../../server/i18n/BB.png',1,'',0),
(22,'Belarus','','','','-- Sin asignar --','BY','','','','','','','','','','../../server/i18n/BY.png',1,'',0),
(23,'Belgium','','','','-- Sin asignar --','BE','','','','','','','','','','../../server/i18n/BE.png',1,'',0),
(24,'Belize','','','','-- Sin asignar --','BZ','','','','','','','','','','../../server/i18n/BZ.png',1,'',0),
(25,'Benin','','','','-- Sin asignar --','BJ','','','','','','','','','','../../server/i18n/BJ.png',1,'',0),
(26,'Bermuda','','','','-- Sin asignar --','BM','','','','','','','','','','../../server/i18n/BM.png',1,'',0),
(27,'Bhutan','','','','-- Sin asignar --','BT','','','','','','','','','','../../server/i18n/BT.png',1,'',0),
(28,'Bolivia','','','','-- Sin asignar --','BO','','','','','','','','','','../../server/i18n/BO.png',1,'',0),
(29,'Bosnia and Herzegovina','','','','-- Sin asignar --','BA','','','','','','','','','','../../server/i18n/BA.png',1,'',0),
(30,'Botswana','','','','-- Sin asignar --','BW','','','','','','','','','','../../server/i18n/BW.png',1,'',0),
(31,'Bouvet Island','','','','-- Sin asignar --','BV','','','','','','','','','','../../server/i18n/BV.png',1,'',0),
(32,'Brazil','','','','-- Sin asignar --','BR','','','','','','','','','','../../server/i18n/BR.png',1,'',0),
(33,'British Antarctic Territory','','','','-- Sin asignar --','BQ','','','','','','','','','','../../server/i18n/BQ.png',1,'',0),
(34,'British Indian Ocean Territory','','','','-- Sin asignar --','IO','','','','','','','','','','../../server/i18n/IO.png',1,'',0),
(35,'British Virgin Islands','','','','-- Sin asignar --','VG','','','','','','','','','','../../server/i18n/VG.png',1,'',0),
(36,'Brunei','','','','-- Sin asignar --','BN','','','','','','','','','','../../server/i18n/BN.png',1,'',0),
(37,'Bulgaria','','','','-- Sin asignar --','BG','','','','','','','','','','../../server/i18n/BG.png',1,'',0),
(38,'Burkina Faso','','','','-- Sin asignar --','BF','','','','','','','','','','../../server/i18n/BF.png',1,'',0),
(39,'Burundi','','','','-- Sin asignar --','BI','','','','','','','','','','../../server/i18n/BI.png',1,'',0),
(40,'Cambodia','','','','-- Sin asignar --','KH','','','','','','','','','','../../server/i18n/KH.png',1,'',0),
(41,'Cameroon','','','','-- Sin asignar --','CM','','','','','','','','','','../../server/i18n/CM.png',1,'',0),
(42,'Canada','','','','-- Sin asignar --','CA','','','','','','','','','','../../server/i18n/CA.png',1,'',0),
(43,'Canton and Enderbury Islands','','','','-- Sin asignar --','CT','','','','','','','','','','../../server/i18n/CT.png',1,'',0),
(44,'Cape Verde','','','','-- Sin asignar --','CV','','','','','','','','','','../../server/i18n/CV.png',1,'',0),
(45,'Cayman Islands','','','','-- Sin asignar --','KY','','','','','','','','','','../../server/i18n/KY.png',1,'',0),
(46,'Central African Republic','','','','-- Sin asignar --','CF','','','','','','','','','','../../server/i18n/CF.png',1,'',0),
(47,'Chad','','','','-- Sin asignar --','TD','','','','','','','','','','../../server/i18n/TD.png',1,'',0),
(48,'Chile','','','','-- Sin asignar --','CL','','','','','','','','','','../../server/i18n/CL.png',1,'',0),
(49,'China','','','','-- Sin asignar --','CN','','','','','','','','','','../../server/i18n/CN.png',1,'',0),
(50,'Christmas Island','','','','-- Sin asignar --','CX','','','','','','','','','','../../server/i18n/CX.png',1,'',0),
(51,'Cocos [Keeling] Islands','','','','-- Sin asignar --','CC','','','','','','','','','','../../server/i18n/CC.png',1,'',0),
(52,'Colombia','','','','-- Sin asignar --','CO','','','','','','','','','','../../server/i18n/CO.png',1,'',0),
(53,'Comoros','','','','-- Sin asignar --','KM','','','','','','','','','','../../server/i18n/KM.png',1,'',0),
(54,'Congo - Brazzaville','','','','-- Sin asignar --','CG','','','','','','','','','','../../server/i18n/CG.png',1,'',0),
(55,'Congo - Kinshasa','','','','-- Sin asignar --','CD','','','','','','','','','','../../server/i18n/CD.png',1,'',0),
(56,'Cook Islands','','','','-- Sin asignar --','CK','','','','','','','','','','../../server/i18n/CK.png',1,'',0),
(57,'Costa Rica','','','','-- Sin asignar --','CR','','','','','','','','','','../../server/i18n/CR.png',1,'',0),
(58,'Croatia','','','','-- Sin asignar --','HR','','','','','','','','','','../../server/i18n/HR.png',1,'',0),
(59,'Cuba','','','','-- Sin asignar --','CU','','','','','','','','','','../../server/i18n/CU.png',1,'',0),
(60,'Cyprus','','','','-- Sin asignar --','CY','','','','','','','','','','../../server/i18n/CY.png',1,'',0),
(61,'Czech Republic','','','','-- Sin asignar --','CZ','','','','','','','','','','../../server/i18n/CZ.png',1,'',0),
(62,'Côte d’Ivoire','','','','-- Sin asignar --','CI','','','','','','','','','','../../server/i18n/CI.png',1,'',0),
(63,'Denmark','','','','-- Sin asignar --','DK','','','','','','','','','','../../server/i18n/DK.png',1,'',0),
(64,'Djibouti','','','','-- Sin asignar --','DJ','','','','','','','','','','../../server/i18n/DJ.png',1,'',0),
(65,'Dominica','','','','-- Sin asignar --','DM','','','','','','','','','','../../server/i18n/DM.png',1,'',0),
(66,'Dominican Republic','','','','-- Sin asignar --','DO','','','','','','','','','','../../server/i18n/DO.png',1,'',0),
(67,'Dronning Maud Land','','','','-- Sin asignar --','NQ','','','','','','','','','','../../server/i18n/NQ.png',1,'',0),
(68,'East Germany','','','','-- Sin asignar --','DD','','','','','','','','','','../../server/i18n/DD.png',1,'',0),
(69,'Ecuador','','','','-- Sin asignar --','EC','','','','','','','','','','../../server/i18n/EC.png',1,'',0),
(70,'Egypt','','','','-- Sin asignar --','EG','','','','','','','','','','../../server/i18n/EG.png',1,'',0),
(71,'El Salvador','','','','-- Sin asignar --','SV','','','','','','','','','','../../server/i18n/SV.png',1,'',0),
(72,'Equatorial Guinea','','','','-- Sin asignar --','GQ','','','','','','','','','','../../server/i18n/GQ.png',1,'',0),
(73,'Eritrea','','','','-- Sin asignar --','ER','','','','','','','','','','../../server/i18n/ER.png',1,'',0),
(74,'Estonia','','','','-- Sin asignar --','EE','','','','','','','','','','../../server/i18n/EE.png',1,'',0),
(75,'Ethiopia','','','','-- Sin asignar --','ET','','','','','','','','','','../../server/i18n/ET.png',1,'',0),
(76,'Falkland Islands','','','','-- Sin asignar --','FK','','','','','','','','','','../../server/i18n/FK.png',1,'',0),
(77,'Faroe Islands','','','','-- Sin asignar --','FO','','','','','','','','','','../../server/i18n/FO.png',1,'',0),
(78,'Fiji','','','','-- Sin asignar --','FJ','','','','','','','','','','../../server/i18n/FJ.png',1,'',0),
(79,'Finland','','','','-- Sin asignar --','FI','','','','','','','','','','../../server/i18n/FI.png',1,'',0),
(80,'France','','','','-- Sin asignar --','FR','','','','','','','','','','../../server/i18n/FR.png',1,'',0),
(81,'French Guiana','','','','-- Sin asignar --','GF','','','','','','','','','','../../server/i18n/GF.png',1,'',0),
(82,'French Polynesia','','','','-- Sin asignar --','PF','','','','','','','','','','../../server/i18n/PF.png',1,'',0),
(83,'French Southern Territories','','','','-- Sin asignar --','TF','','','','','','','','','','../../server/i18n/TF.png',1,'',0),
(84,'French Southern and Antarctic Territories','','','','-- Sin asignar --','FQ','','','','','','','','','','../../server/i18n/FQ.png',1,'',0),
(85,'Gabon','','','','-- Sin asignar --','GA','','','','','','','','','','../../server/i18n/GA.png',1,'',0),
(86,'Gambia','','','','-- Sin asignar --','GM','','','','','','','','','','../../server/i18n/GM.png',1,'',0),
(87,'Georgia','','','','-- Sin asignar --','GE','','','','','','','','','','../../server/i18n/GE.png',1,'',0),
(88,'Germany','','','','-- Sin asignar --','DE','','','','','','','','','','../../server/i18n/DE.png',1,'',0),
(89,'Ghana','','','','-- Sin asignar --','GH','','','','','','','','','','../../server/i18n/GH.png',1,'',0),
(90,'Gibraltar','','','','-- Sin asignar --','GI','','','','','','','','','','../../server/i18n/GI.png',1,'',0),
(91,'Greece','','','','-- Sin asignar --','GR','','','','','','','','','','../../server/i18n/GR.png',1,'',0),
(92,'Greenland','','','','-- Sin asignar --','GL','','','','','','','','','','../../server/i18n/GL.png',1,'',0),
(93,'Grenada','','','','-- Sin asignar --','GD','','','','','','','','','','../../server/i18n/GD.png',1,'',0),
(94,'Guadeloupe','','','','-- Sin asignar --','GP','','','','','','','','','','../../server/i18n/GP.png',1,'',0),
(95,'Guam','','','','-- Sin asignar --','GU','','','','','','','','','','../../server/i18n/GU.png',1,'',0),
(96,'Guatemala','','','','-- Sin asignar --','GT','','','','','','','','','','../../server/i18n/GT.png',1,'',0),
(97,'Guernsey','','','','-- Sin asignar --','GG','','','','','','','','','','../../server/i18n/GG.png',1,'',0),
(98,'Guinea','','','','-- Sin asignar --','GN','','','','','','','','','','../../server/i18n/GN.png',1,'',0),
(99,'Guinea-Bissau','','','','-- Sin asignar --','GW','','','','','','','','','','../../server/i18n/GW.png',1,'',0),
(100,'Guyana','','','','-- Sin asignar --','GY','','','','','','','','','','../../server/i18n/GY.png',1,'',0),
(101,'Haiti','','','','-- Sin asignar --','HT','','','','','','','','','','../../server/i18n/HT.png',1,'',0),
(102,'Heard Island and McDonald Islands','','','','-- Sin asignar --','HM','','','','','','','','','','../../server/i18n/HM.png',1,'',0),
(103,'Honduras','','','','-- Sin asignar --','HN','','','','','','','','','','../../server/i18n/HN.png',1,'',0),
(104,'Hong Kong SAR China','','','','-- Sin asignar --','HK','','','','','','','','','','../../server/i18n/HK.png',1,'',0),
(105,'Hungary','','','','-- Sin asignar --','HU','','','','','','','','','','../../server/i18n/HU.png',1,'',0),
(106,'Iceland','','','','-- Sin asignar --','IS','','','','','','','','','','../../server/i18n/IS.png',1,'',0),
(107,'India','','','','-- Sin asignar --','IN','','','','','','','','','','../../server/i18n/IN.png',1,'',0),
(108,'Indonesia','','','','-- Sin asignar --','ID','','','','','','','','','','../../server/i18n/ID.png',1,'',0),
(109,'Iran','','','','-- Sin asignar --','IR','','','','','','','','','','../../server/i18n/IR.png',1,'',0),
(110,'Iraq','','','','-- Sin asignar --','IQ','','','','','','','','','','../../server/i18n/IQ.png',1,'',0),
(111,'Ireland','','','','-- Sin asignar --','IE','','','','','','','','','','../../server/i18n/IE.png',1,'',0),
(112,'Isle of Man','','','','-- Sin asignar --','IM','','','','','','','','','','../../server/i18n/IM.png',1,'',0),
(113,'Israel','','','','-- Sin asignar --','IL','','','','','','','','','','../../server/i18n/IL.png',1,'',0),
(114,'Italy','','','','-- Sin asignar --','IT','','','','','','','','','','../../server/i18n/IT.png',1,'',0),
(115,'Jamaica','','','','-- Sin asignar --','JM','','','','','','','','','','../../server/i18n/JM.png',1,'',0),
(116,'Japan','','','','-- Sin asignar --','JP','','','','','','','','','','../../server/i18n/JP.png',1,'',0),
(117,'Jersey','','','','-- Sin asignar --','JE','','','','','','','','','','../../server/i18n/JE.png',1,'',0),
(118,'Johnston Island','','','','-- Sin asignar --','JT','','','','','','','','','','../../server/i18n/JT.png',1,'',0),
(119,'Jordan','','','','-- Sin asignar --','JO','','','','','','','','','','../../server/i18n/JO.png',1,'',0),
(120,'Kazakhstan','','','','-- Sin asignar --','KZ','','','','','','','','','','../../server/i18n/KZ.png',1,'',0),
(121,'Kenya','','','','-- Sin asignar --','KE','','','','','','','','','','../../server/i18n/KE.png',1,'',0),
(122,'Kiribati','','','','-- Sin asignar --','KI','','','','','','','','','','../../server/i18n/KI.png',1,'',0),
(123,'Kuwait','','','','-- Sin asignar --','KW','','','','','','','','','','../../server/i18n/KW.png',1,'',0),
(124,'Kyrgyzstan','','','','-- Sin asignar --','KG','','','','','','','','','','../../server/i18n/KG.png',1,'',0),
(125,'Laos','','','','-- Sin asignar --','LA','','','','','','','','','','../../server/i18n/LA.png',1,'',0),
(126,'Latvia','','','','-- Sin asignar --','LV','','','','','','','','','','../../server/i18n/LV.png',1,'',0),
(127,'Lebanon','','','','-- Sin asignar --','LB','','','','','','','','','','../../server/i18n/LB.png',1,'',0),
(128,'Lesotho','','','','-- Sin asignar --','LS','','','','','','','','','','../../server/i18n/LS.png',1,'',0),
(129,'Liberia','','','','-- Sin asignar --','LR','','','','','','','','','','../../server/i18n/LR.png',1,'',0),
(130,'Libya','','','','-- Sin asignar --','LY','','','','','','','','','','../../server/i18n/LY.png',1,'',0),
(131,'Liechtenstein','','','','-- Sin asignar --','LI','','','','','','','','','','../../server/i18n/LI.png',1,'',0),
(132,'Lithuania','','','','-- Sin asignar --','LT','','','','','','','','','','../../server/i18n/LT.png',1,'',0),
(133,'Luxembourg','','','','-- Sin asignar --','LU','','','','','','','','','','../../server/i18n/LU.png',1,'',0),
(134,'Macau SAR China','','','','-- Sin asignar --','MO','','','','','','','','','','../../server/i18n/MO.png',1,'',0),
(135,'Macedonia','','','','-- Sin asignar --','MK','','','','','','','','','','../../server/i18n/MK.png',1,'',0),
(136,'Madagascar','','','','-- Sin asignar --','MG','','','','','','','','','','../../server/i18n/MG.png',1,'',0),
(137,'Malawi','','','','-- Sin asignar --','MW','','','','','','','','','','../../server/i18n/MW.png',1,'',0),
(138,'Malaysia','','','','-- Sin asignar --','MY','','','','','','','','','','../../server/i18n/MY.png',1,'',0),
(139,'Maldives','','','','-- Sin asignar --','MV','','','','','','','','','','../../server/i18n/MV.png',1,'',0),
(140,'Mali','','','','-- Sin asignar --','ML','','','','','','','','','','../../server/i18n/ML.png',1,'',0),
(141,'Malta','','','','-- Sin asignar --','MT','','','','','','','','','','../../server/i18n/MT.png',1,'',0),
(142,'Marshall Islands','','','','-- Sin asignar --','MH','','','','','','','','','','../../server/i18n/MH.png',1,'',0),
(143,'Martinique','','','','-- Sin asignar --','MQ','','','','','','','','','','../../server/i18n/MQ.png',1,'',0),
(144,'Mauritania','','','','-- Sin asignar --','MR','','','','','','','','','','../../server/i18n/MR.png',1,'',0),
(145,'Mauritius','','','','-- Sin asignar --','MU','','','','','','','','','','../../server/i18n/MU.png',1,'',0),
(146,'Mayotte','','','','-- Sin asignar --','YT','','','','','','','','','','../../server/i18n/YT.png',1,'',0),
(147,'Metropolitan France','','','','-- Sin asignar --','FX','','','','','','','','','','../../server/i18n/FX.png',1,'',0),
(148,'Mexico','','','','-- Sin asignar --','MX','','','','','','','','','','../../server/i18n/MX.png',1,'',0),
(149,'Micronesia','','','','-- Sin asignar --','FM','','','','','','','','','','../../server/i18n/FM.png',1,'',0),
(150,'Midway Islands','','','','-- Sin asignar --','MI','','','','','','','','','','../../server/i18n/MI.png',1,'',0),
(151,'Moldova','','','','-- Sin asignar --','MD','','','','','','','','','','../../server/i18n/MD.png',1,'',0),
(152,'Monaco','','','','-- Sin asignar --','MC','','','','','','','','','','../../server/i18n/MC.png',1,'',0),
(153,'Mongolia','','','','-- Sin asignar --','MN','','','','','','','','','','../../server/i18n/MN.png',1,'',0),
(154,'Montenegro','','','','-- Sin asignar --','ME','','','','','','','','','','../../server/i18n/ME.png',1,'',0),
(155,'Montserrat','','','','-- Sin asignar --','MS','','','','','','','','','','../../server/i18n/MS.png',1,'',0),
(156,'Morocco','','','','-- Sin asignar --','MA','','','','','','','','','','../../server/i18n/MA.png',1,'',0),
(157,'Mozambique','','','','-- Sin asignar --','MZ','','','','','','','','','','../../server/i18n/MZ.png',1,'',0),
(158,'Myanmar [Burma]','','','','-- Sin asignar --','MM','','','','','','','','','','../../server/i18n/MM.png',1,'',0),
(159,'Namibia','','','','-- Sin asignar --','NA','','','','','','','','','','../../server/i18n/NA.png',1,'',0),
(160,'Nauru','','','','-- Sin asignar --','NR','','','','','','','','','','../../server/i18n/NR.png',1,'',0),
(161,'Nepal','','','','-- Sin asignar --','NP','','','','','','','','','','../../server/i18n/NP.png',1,'',0),
(162,'Netherlands','','','','-- Sin asignar --','NL','','','','','','','','','','../../server/i18n/NL.png',1,'',0),
(163,'Netherlands Antilles','','','','-- Sin asignar --','AN','','','','','','','','','','../../server/i18n/AN.png',1,'',0),
(164,'Neutral Zone','','','','-- Sin asignar --','NT','','','','','','','','','','../../server/i18n/NT.png',1,'',0),
(165,'New Caledonia','','','','-- Sin asignar --','NC','','','','','','','','','','../../server/i18n/NC.png',1,'',0),
(166,'New Zealand','','','','-- Sin asignar --','NZ','','','','','','','','','','../../server/i18n/NZ.png',1,'',0),
(167,'Nicaragua','','','','-- Sin asignar --','NI','','','','','','','','','','../../server/i18n/NI.png',1,'',0),
(168,'Niger','','','','-- Sin asignar --','NE','','','','','','','','','','../../server/i18n/NE.png',1,'',0),
(169,'Nigeria','','','','-- Sin asignar --','NG','','','','','','','','','','../../server/i18n/NG.png',1,'',0),
(170,'Niue','','','','-- Sin asignar --','NU','','','','','','','','','','../../server/i18n/NU.png',1,'',0),
(171,'Norfolk Island','','','','-- Sin asignar --','NF','','','','','','','','','','../../server/i18n/NF.png',1,'',0),
(172,'North Korea','','','','-- Sin asignar --','KP','','','','','','','','','','../../server/i18n/KP.png',1,'',0),
(173,'North Vietnam','','','','-- Sin asignar --','VD','','','','','','','','','','../../server/i18n/VD.png',1,'',0),
(174,'Northern Mariana Islands','','','','-- Sin asignar --','MP','','','','','','','','','','../../server/i18n/MP.png',1,'',0),
(175,'Norway','','','','-- Sin asignar --','NO','','','','','','','','','','../../server/i18n/NO.png',1,'',0),
(176,'Oman','','','','-- Sin asignar --','OM','','','','','','','','','','../../server/i18n/OM.png',1,'',0),
(177,'Pacific Islands Trust Territory','','','','-- Sin asignar --','PC','','','','','','','','','','../../server/i18n/PC.png',1,'',0),
(178,'Pakistan','','','','-- Sin asignar --','PK','','','','','','','','','','../../server/i18n/PK.png',1,'',0),
(179,'Palau','','','','-- Sin asignar --','PW','','','','','','','','','','../../server/i18n/PW.png',1,'',0),
(180,'Palestinian Territories','','','','-- Sin asignar --','PS','','','','','','','','','','../../server/i18n/PS.png',1,'',0),
(181,'Panama','','','','-- Sin asignar --','PA','','','','','','','','','','../../server/i18n/PA.png',1,'',0),
(182,'Panama Canal Zone','','','','-- Sin asignar --','PZ','','','','','','','','','','../../server/i18n/PZ.png',1,'',0),
(183,'Papua New Guinea','','','','-- Sin asignar --','PG','','','','','','','','','','../../server/i18n/PG.png',1,'',0),
(184,'Paraguay','','','','-- Sin asignar --','PY','','','','','','','','','','../../server/i18n/PY.png',1,'',0),
(185,'People\'s Democratic Republic of Yemen','','','','-- Sin asignar --','YD','','','','','','','','','','../../server/i18n/YD.png',1,'',0),
(186,'Peru','','','','-- Sin asignar --','PE','','','','','','','','','','../../server/i18n/PE.png',1,'',0),
(187,'Philippines','','','','-- Sin asignar --','PH','','','','','','','','','','../../server/i18n/PH.png',1,'',0),
(188,'Pitcairn Islands','','','','-- Sin asignar --','PN','','','','','','','','','','../../server/i18n/PN.png',1,'',0),
(189,'Poland','','','','-- Sin asignar --','PL','','','','','','','','','','../../server/i18n/PL.png',1,'',0),
(190,'Portugal','','','','-- Sin asignar --','PT','','','','','','','','','','../../server/i18n/PT.png',1,'',0),
(191,'Puerto Rico','','','','-- Sin asignar --','PR','','','','','','','','','','../../server/i18n/PR.png',1,'',0),
(192,'Qatar','','','','-- Sin asignar --','QA','','','','','','','','','','../../server/i18n/QA.png',1,'',0),
(193,'Romania','','','','-- Sin asignar --','RO','','','','','','','','','','../../server/i18n/RO.png',1,'',0),
(194,'Russia','','','','-- Sin asignar --','RU','','','','','','','','','','../../server/i18n/RU.png',1,'',0),
(195,'Rwanda','','','','-- Sin asignar --','RW','','','','','','','','','','../../server/i18n/RW.png',1,'',0),
(196,'Réunion','','','','-- Sin asignar --','RE','','','','','','','','','','../../server/i18n/RE.png',1,'',0),
(197,'Saint Barthélemy','','','','-- Sin asignar --','BL','','','','','','','','','','../../server/i18n/BL.png',1,'',0),
(198,'Saint Helena','','','','-- Sin asignar --','SH','','','','','','','','','','../../server/i18n/SH.png',1,'',0),
(199,'Saint Kitts and Nevis','','','','-- Sin asignar --','KN','','','','','','','','','','../../server/i18n/KN.png',1,'',0),
(200,'Saint Lucia','','','','-- Sin asignar --','LC','','','','','','','','','','../../server/i18n/LC.png',1,'',0),
(201,'Saint Martin','','','','-- Sin asignar --','MF','','','','','','','','','','../../server/i18n/MF.png',1,'',0),
(202,'Saint Pierre and Miquelon','','','','-- Sin asignar --','PM','','','','','','','','','','../../server/i18n/PM.png',1,'',0),
(203,'Saint Vincent and the Grenadines','','','','-- Sin asignar --','VC','','','','','','','','','','../../server/i18n/VC.png',1,'',0),
(204,'Samoa','','','','-- Sin asignar --','WS','','','','','','','','','','../../server/i18n/WS.png',1,'',0),
(205,'San Marino','','','','-- Sin asignar --','SM','','','','','','','','','','../../server/i18n/SM.png',1,'',0),
(206,'Saudi Arabia','','','','-- Sin asignar --','SA','','','','','','','','','','../../server/i18n/SA.png',1,'',0),
(207,'Senegal','','','','-- Sin asignar --','SN','','','','','','','','','','../../server/i18n/SN.png',1,'',0),
(208,'Serbia','','','','-- Sin asignar --','RS','','','','','','','','','','../../server/i18n/RS.png',1,'',0),
(209,'Serbia and Montenegro','','','','-- Sin asignar --','CS','','','','','','','','','','../../server/i18n/CS.png',1,'',0),
(210,'Seychelles','','','','-- Sin asignar --','SC','','','','','','','','','','../../server/i18n/SC.png',1,'',0),
(211,'Sierra Leone','','','','-- Sin asignar --','SL','','','','','','','','','','../../server/i18n/SL.png',1,'',0),
(212,'Singapore','','','','-- Sin asignar --','SG','','','','','','','','','','../../server/i18n/SG.png',1,'',0),
(213,'Slovakia','','','','-- Sin asignar --','SK','','','','','','','','','','../../server/i18n/SK.png',1,'',0),
(214,'Slovenia','','','','-- Sin asignar --','SI','','','','','','','','','','../../server/i18n/SI.png',1,'',0),
(215,'Solomon Islands','','','','-- Sin asignar --','SB','','','','','','','','','','../../server/i18n/SB.png',1,'',0),
(216,'Somalia','','','','-- Sin asignar --','SO','','','','','','','','','','../../server/i18n/SO.png',1,'',0),
(217,'South Africa','','','','-- Sin asignar --','ZA','','','','','','','','','','../../server/i18n/ZA.png',1,'',0),
(218,'South Georgia and the South Sandwich Islands','','','','-- Sin asignar --','GS','','','','','','','','','','../../server/i18n/GS.png',1,'',0),
(219,'South Korea','','','','-- Sin asignar --','KR','','','','','','','','','','../../server/i18n/KR.png',1,'',0),
(220,'Spain','','','','-- Sin asignar --','ES','','','','','','','','','','../../server/i18n/ES.png',1,'',0),
(221,'Sri Lanka','','','','-- Sin asignar --','LK','','','','','','','','','','../../server/i18n/LK.png',1,'',0),
(222,'Sudan','','','','-- Sin asignar --','SD','','','','','','','','','','../../server/i18n/SD.png',1,'',0),
(223,'Suriname','','','','-- Sin asignar --','SR','','','','','','','','','','../../server/i18n/SR.png',1,'',0),
(224,'Svalbard and Jan Mayen','','','','-- Sin asignar --','SJ','','','','','','','','','','../../server/i18n/SJ.png',1,'',0),
(225,'Swaziland','','','','-- Sin asignar --','SZ','','','','','','','','','','../../server/i18n/SZ.png',1,'',0),
(226,'Sweden','','','','-- Sin asignar --','SE','','','','','','','','','','../../server/i18n/SE.png',1,'',0),
(227,'Switzerland','','','','-- Sin asignar --','CH','','','','','','','','','','../../server/i18n/CH.png',1,'',0),
(228,'Syria','','','','-- Sin asignar --','SY','','','','','','','','','','../../server/i18n/SY.png',1,'',0),
(229,'São Tomé and Príncipe','','','','-- Sin asignar --','ST','','','','','','','','','','../../server/i18n/ST.png',1,'',0),
(230,'Taiwan','','','','-- Sin asignar --','TW','','','','','','','','','','../../server/i18n/TW.png',1,'',0),
(231,'Tajikistan','','','','-- Sin asignar --','TJ','','','','','','','','','','../../server/i18n/TJ.png',1,'',0),
(232,'Tanzania','','','','-- Sin asignar --','TZ','','','','','','','','','','../../server/i18n/TZ.png',1,'',0),
(233,'Thailand','','','','-- Sin asignar --','TH','','','','','','','','','','../../server/i18n/TH.png',1,'',0),
(234,'Timor-Leste','','','','-- Sin asignar --','TL','','','','','','','','','','../../server/i18n/TL.png',1,'',0),
(235,'Togo','','','','-- Sin asignar --','TG','','','','','','','','','','../../server/i18n/TG.png',1,'',0),
(236,'Tokelau','','','','-- Sin asignar --','TK','','','','','','','','','','../../server/i18n/TK.png',1,'',0),
(237,'Tonga','','','','-- Sin asignar --','TO','','','','','','','','','','../../server/i18n/TO.png',1,'',0),
(238,'Trinidad and Tobago','','','','-- Sin asignar --','TT','','','','','','','','','','../../server/i18n/TT.png',1,'',0),
(239,'Tunisia','','','','-- Sin asignar --','TN','','','','','','','','','','../../server/i18n/TN.png',1,'',0),
(240,'Turkey','','','','-- Sin asignar --','TR','','','','','','','','','','../../server/i18n/TR.png',1,'',0),
(241,'Turkmenistan','','','','-- Sin asignar --','TM','','','','','','','','','','../../server/i18n/TM.png',1,'',0),
(242,'Turks and Caicos Islands','','','','-- Sin asignar --','TC','','','','','','','','','','../../server/i18n/TC.png',1,'',0),
(243,'Tuvalu','','','','-- Sin asignar --','TV','','','','','','','','','','../../server/i18n/TV.png',1,'',0),
(244,'U.S. Minor Outlying Islands','','','','-- Sin asignar --','UM','','','','','','','','','','../../server/i18n/UM.png',1,'',0),
(245,'U.S. Miscellaneous Pacific Islands','','','','-- Sin asignar --','PU','','','','','','','','','','../../server/i18n/PU.png',1,'',0),
(246,'U.S. Virgin Islands','','','','-- Sin asignar --','VI','','','','','','','','','','../../server/i18n/VI.png',1,'',0),
(247,'Uganda','','','','-- Sin asignar --','UG','','','','','','','','','','../../server/i18n/UG.png',1,'',0),
(248,'Ukraine','','','','-- Sin asignar --','UA','','','','','','','','','','../../server/i18n/UA.png',1,'',0),
(249,'Union of Soviet Socialist Republics','','','','-- Sin asignar --','SU','','','','','','','','','','../../server/i18n/SU.png',1,'',0),
(250,'United Arab Emirates','','','','-- Sin asignar --','AE','','','','','','','','','','../../server/i18n/AE.png',1,'',0),
(251,'United Kingdom','','','','-- Sin asignar --','GB','','','','','','','','','','../../server/i18n/GB.png',1,'',0),
(252,'United States','','','','-- Sin asignar --','US','','','','','','','','','','../../server/i18n/US.png',1,'',0),
(253,'Unknown or Invalid Region','','','','-- Sin asignar --','ZZ','','','','','','','','','','../../server/i18n/ZZ.png',1,'',0),
(254,'Uruguay','','','','-- Sin asignar --','UY','','','','','','','','','','../../server/i18n/UY.png',1,'',0),
(255,'Uzbekistan','','','','-- Sin asignar --','UZ','','','','','','','','','','../../server/i18n/UZ.png',1,'',0),
(256,'Vanuatu','','','','-- Sin asignar --','VU','','','','','','','','','','../../server/i18n/VU.png',1,'',0),
(257,'Vatican City','','','','-- Sin asignar --','VA','','','','','','','','','','../../server/i18n/VA.png',1,'',0),
(258,'Venezuela','','','','-- Sin asignar --','VE','','','','','','','','','','../../server/i18n/VE.png',1,'',0),
(259,'Vietnam','','','','-- Sin asignar --','VN','','','','','','','','','','../../server/i18n/VN.png',1,'',0),
(260,'Wake Island','','','','-- Sin asignar --','WK','','','','','','','','','','../../server/i18n/WK.png',1,'',0),
(261,'Wallis and Futuna','','','','-- Sin asignar --','WF','','','','','','','','','','../../server/i18n/WF.png',1,'',0),
(262,'Western Sahara','','','','-- Sin asignar --','EH','','','','','','','','','','../../server/i18n/EH.png',1,'',0),
(263,'Yemen','','','','-- Sin asignar --','YE','','','','','','','','','','../../server/i18n/YE.png',1,'',0),
(264,'Zambia','','','','-- Sin asignar --','ZM','','','','','','','','','','../../server/i18n/ZM.png',1,'',0),
(265,'Zimbabwe','','','','-- Sin asignar --','ZW','','','','','','','','','','../../server/i18n/ZW.png',1,'',0),
(266,'Åland Islands','','','','-- Sin asignar --','AX','','','','','','','','','','../../server/i18n/AX.png',1,'',0);
/*!40000 ALTER TABLE `clubes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipos`
--

DROP TABLE IF EXISTS `equipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipos` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Jornada` int(4) NOT NULL,
  `Categorias` varchar(16) NOT NULL DEFAULT '-LMST',
  `Nombre` varchar(255) NOT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  `Miembros` text NOT NULL,
  `DefaultTeam` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Equipos_JornadaNombre` (`Jornada`,`Nombre`),
  KEY `Equipos_Prueba` (`Prueba`),
  KEY `Equipos_Jornada` (`Jornada`),
  CONSTRAINT `Equipos_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Equipos_ibfk_2` FOREIGN KEY (`Jornada`) REFERENCES `jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipos`
--

LOCK TABLES `equipos` WRITE;
/*!40000 ALTER TABLE `equipos` DISABLE KEYS */;
INSERT INTO `equipos` VALUES 
(1,1,1,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 1 JORNADA 1 - Equipo por defecto','BEGIN,END',1),
(2,1,2,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 1 JORNADA 2 - Equipo por defecto','BEGIN,END',1),
(3,1,3,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 1 JORNADA 3 - Equipo por defecto','BEGIN,END',1),
(4,1,4,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 1 JORNADA 4 - Equipo por defecto','BEGIN,END',1),
(5,1,5,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 1 JORNADA 5 - Equipo por defecto','BEGIN,END',1),
(6,1,6,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 1 JORNADA 6 - Equipo por defecto','BEGIN,END',1),
(7,1,7,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 1 JORNADA 7 - Equipo por defecto','BEGIN,END',1),
(8,1,8,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 1 JORNADA 8 - Equipo por defecto','BEGIN,END',1),
(9,2,9,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 2 JORNADA 9 - Default Team','BEGIN,END',1),
(10,2,10,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 2 JORNADA 10 - Default Team','BEGIN,END',1),
(11,2,11,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 2 JORNADA 11 - Default Team','BEGIN,END',1),
(12,2,12,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 2 JORNADA 12 - Default Team','BEGIN,END',1),
(13,2,13,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 2 JORNADA 13 - Default Team','BEGIN,END',1),
(14,2,14,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 2 JORNADA 14 - Default Team','BEGIN,END',1),
(15,2,15,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 2 JORNADA 15 - Default Team','BEGIN,END',1),
(16,2,16,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 2 JORNADA 16 - Default Team','BEGIN,END',1),
(17,3,17,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 3 JORNADA 17 - Default Team','BEGIN,END',1),
(18,3,18,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 3 JORNADA 18 - Default Team','BEGIN,END',1),
(19,3,19,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 3 JORNADA 19 - Default Team','BEGIN,END',1),
(20,3,20,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 3 JORNADA 20 - Default Team','BEGIN,END',1),
(21,3,21,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 3 JORNADA 21 - Default Team','BEGIN,END',1),
(22,3,22,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 3 JORNADA 22 - Default Team','BEGIN,END',1),
(23,3,23,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 3 JORNADA 23 - Default Team','BEGIN,END',1),
(24,3,24,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 3 JORNADA 24 - Default Team','BEGIN,END',1),
(25,4,25,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 4 JORNADA 25 - Default Team','BEGIN,END',1),
(26,4,26,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 4 JORNADA 26 - Default Team','BEGIN,END',1),
(27,4,27,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 4 JORNADA 27 - Default Team','BEGIN,END',1),
(28,4,28,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 4 JORNADA 28 - Default Team','BEGIN,END',1),
(29,4,29,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 4 JORNADA 29 - Default Team','BEGIN,END',1),
(30,4,30,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 4 JORNADA 30 - Default Team','BEGIN,END',1),
(31,4,31,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 4 JORNADA 31 - Default Team','BEGIN,END',1),
(32,4,32,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 4 JORNADA 32 - Default Team','BEGIN,END',1),
(33,5,33,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 5 JORNADA 33 - Default Team','BEGIN,END',1),
(34,5,34,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 5 JORNADA 34 - Default Team','BEGIN,END',1),
(35,5,35,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 5 JORNADA 35 - Default Team','BEGIN,END',1),
(36,5,36,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 5 JORNADA 36 - Default Team','BEGIN,END',1),
(37,5,37,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 5 JORNADA 37 - Default Team','BEGIN,END',1),
(38,5,38,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 5 JORNADA 38 - Default Team','BEGIN,END',1),
(39,5,39,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 5 JORNADA 39 - Default Team','BEGIN,END',1),
(40,5,40,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 5 JORNADA 40 - Default Team','BEGIN,END',1);
/*!40000 ALTER TABLE `equipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventos` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Session` int(4) NOT NULL,
  `Source` varchar(255) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Data` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Eventos_Session` (`Session`),
  CONSTRAINT `Eventos_ibfk_1` FOREIGN KEY (`Session`) REFERENCES `sesiones` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grados_perro`
--

DROP TABLE IF EXISTS `grados_perro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grados_perro` (
  `Grado` varchar(16) NOT NULL,
  `Comentarios` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grados_perro`
--

LOCK TABLES `grados_perro` WRITE;
/*!40000 ALTER TABLE `grados_perro` DISABLE KEYS */;
INSERT INTO `grados_perro` VALUES 
('-','Sin especificar'),
('Baja','Baja temporal'),
('GI','Grado I'),
('GII','Grado II'),
('GIII','Grado III'),
('P.A.','Pre-Agility'),
('P.B.','Perro en Blanco'),
('Ret.','Retirado');
/*!40000 ALTER TABLE `grados_perro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guias`
--

DROP TABLE IF EXISTS `guias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guias` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Telefono` varchar(16) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Club` int(4) NOT NULL DEFAULT '1',
  `Federation` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Guias_Nombre` (`Nombre`),
  KEY `Guias_Club` (`Club`),
  CONSTRAINT `Guias_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `clubes` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guias`
--

LOCK TABLES `guias` WRITE;
/*!40000 ALTER TABLE `guias` DISABLE KEYS */;
INSERT INTO `guias` VALUES 
(1,'-- Sin asignar --',NULL,NULL,1,0,'NO BORRAR. Valor por defecto cuando un perro se define por primera vez');
/*!40000 ALTER TABLE `guias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscripciones`
--

DROP TABLE IF EXISTS `inscripciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inscripciones` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Perro` int(4) NOT NULL,
  `Dorsal` int(4) NOT NULL DEFAULT '0',
  `Celo` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` varchar(255) DEFAULT NULL,
  `Jornadas` int(4) NOT NULL DEFAULT '0',
  `Pagado` int(4) NOT NULL DEFAULT '24',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Inscripciones_PruebaPerro` (`Prueba`,`Perro`),
  KEY `Inscripciones_Perro` (`Perro`),
  KEY `Inscripciones_Prueba` (`Prueba`),
  KEY `Inscripciones_Dorsal` (`Dorsal`),
  CONSTRAINT `Inscripciones_ibfk_1` FOREIGN KEY (`Perro`) REFERENCES `perros` (`ID`),
  CONSTRAINT `Inscripciones_ibfk_2` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscripciones`
--

LOCK TABLES `inscripciones` WRITE;
/*!40000 ALTER TABLE `inscripciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `inscripciones` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`agility_admin`@`localhost`*/ /*!50003 TRIGGER `Increase_Dorsal` BEFORE INSERT ON `inscripciones`
		FOR EACH ROW BEGIN
			select count(*) into @rows from inscripciones where Prueba = NEW.Prueba;
			if @rows>0 then
				select Dorsal + 1 into @newDorsal from inscripciones where Prueba = NEW.Prueba order by Dorsal desc limit 1;
				set NEW.Dorsal = @newDorsal;
			else
				set NEW.Dorsal = 1;
			end if;
		END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `jornadas`
--

DROP TABLE IF EXISTS `jornadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jornadas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Numero` int(4) NOT NULL,
  `SlaveOf` int(4) NOT NULL DEFAULT '0',
  `Default_Team` int(4) NOT NULL DEFAULT '0',
  `Nombre` varchar(255) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL,
  `Grado1` tinyint(1) NOT NULL DEFAULT '0',
  `Grado2` tinyint(1) NOT NULL DEFAULT '0',
  `Grado3` tinyint(1) NOT NULL DEFAULT '0',
  `Open` tinyint(1) NOT NULL DEFAULT '0',
  `Equipos3` tinyint(1) NOT NULL DEFAULT '0',
  `Equipos4` tinyint(1) NOT NULL DEFAULT '0',
  `PreAgility` tinyint(1) NOT NULL DEFAULT '0',
  `KO` tinyint(1) NOT NULL DEFAULT '0',
  `Especial` tinyint(1) NOT NULL DEFAULT '0',
  `PreAgility2` tinyint(1) NOT NULL DEFAULT '0',
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` varchar(255) NOT NULL DEFAULT '',
  `Orden_Tandas` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Jornadas_Prueba` (`Prueba`),
  CONSTRAINT `Jornadas_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jornadas`
--

LOCK TABLES `jornadas` WRITE;
/*!40000 ALTER TABLE `jornadas` DISABLE KEYS */;
INSERT INTO `jornadas` VALUES 
(1,1,1,0,1,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,1,'(sin especificar)','BEGIN,END'),
(2,1,2,0,2,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,1,'(sin especificar)','BEGIN,END'),
(3,1,3,0,3,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,1,'(sin especificar)','BEGIN,END'),
(4,1,4,0,4,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,1,'(sin especificar)','BEGIN,END'),
(5,1,5,0,5,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,1,'(sin especificar)','BEGIN,END'),
(6,1,6,0,6,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,1,'(sin especificar)','BEGIN,END'),
(7,1,7,0,7,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,1,'(sin especificar)','BEGIN,END'),
(8,1,8,0,8,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,1,'(sin especificar)','BEGIN,END'),
(9,2,1,0,0,'Jumping Especial','2015-06-26','09:00:00',0,0,0,0,0,0,0,0,1,0,0,'Jumping Especial',''),
(10,2,2,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(11,2,3,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(12,2,4,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(13,2,5,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(14,2,6,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(15,2,7,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(16,2,8,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(17,3,1,0,0,'Equipos','2015-06-28','09:00:00',0,0,0,0,0,1,0,0,0,0,0,'(sin especificar)',''),
(18,3,2,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(19,3,3,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(20,3,4,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(21,3,5,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(22,3,6,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(23,3,7,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(24,3,8,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(25,4,1,0,0,'Individual','2015-06-27','09:00:00',0,0,0,1,0,0,0,0,0,0,0,'(sin especificar)',''),
(26,4,2,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(27,4,3,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(28,4,4,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(29,4,5,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(30,4,6,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(31,4,7,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(32,4,8,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(33,5,1,0,0,'Jumping','2015-06-26','09:00:00',0,0,0,0,0,0,0,0,1,0,0,'Jumping Individual',''),
(34,5,2,0,0,'Teams','2015-06-28','09:00:00',0,0,0,0,0,1,0,0,0,0,0,'(sin especificar)',''),
(35,5,3,0,0,'Individual','2015-06-27','00:00:00',0,0,0,1,0,0,0,0,0,0,0,'(sin especificar)',''),
(36,5,4,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(37,5,5,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(38,5,6,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(39,5,7,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'',''),
(40,5,8,0,0,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,0,'','');
/*!40000 ALTER TABLE `jornadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jueces`
--

DROP TABLE IF EXISTS `jueces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jueces` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Direccion1` varchar(255) DEFAULT NULL,
  `Direccion2` varchar(255) DEFAULT NULL,
  `Pais` varchar(32) NOT NULL DEFAULT 'España',
  `Telefono` varchar(32) DEFAULT NULL,
  `Internacional` tinyint(1) NOT NULL DEFAULT '0',
  `Practicas` tinyint(1) NOT NULL DEFAULT '0',
  `Email` varchar(255) DEFAULT NULL,
  `Federations` int(4) NOT NULL DEFAULT '1',
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Jueces_Nombre` (`Nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jueces`
--

LOCK TABLES `jueces` WRITE;
/*!40000 ALTER TABLE `jueces` DISABLE KEYS */;
INSERT INTO `jueces` VALUES 
(1,'-- Sin asignar --','--------','--------','ES','--------',1,1,'nobody@nomail.com',7,'NO BORRAR: Asignacion de juez por defecto');
/*!40000 ALTER TABLE `jueces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mangas`
--

DROP TABLE IF EXISTS `mangas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mangas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Jornada` int(4) NOT NULL,
  `Tipo` int(4) NOT NULL DEFAULT '1',
  `Grado` varchar(16) NOT NULL DEFAULT '-',
  `Recorrido` int(4) NOT NULL DEFAULT '0',
  `Dist_L` int(4) NOT NULL DEFAULT '0',
  `Obst_L` int(4) NOT NULL DEFAULT '0',
  `Dist_M` int(4) NOT NULL DEFAULT '0',
  `Obst_M` int(4) NOT NULL DEFAULT '0',
  `Dist_S` int(4) NOT NULL DEFAULT '0',
  `Obst_S` int(4) NOT NULL DEFAULT '0',
  `Dist_T` int(4) NOT NULL DEFAULT '0',
  `Obst_T` int(4) NOT NULL DEFAULT '0',
  `TRS_L_Tipo` int(4) NOT NULL DEFAULT '0',
  `TRS_L_Factor` int(4) NOT NULL DEFAULT '0',
  `TRS_L_Unit` varchar(1) NOT NULL DEFAULT 's',
  `TRM_L_Tipo` int(4) NOT NULL DEFAULT '1',
  `TRM_L_Factor` int(4) NOT NULL DEFAULT '50',
  `TRM_L_Unit` varchar(1) NOT NULL DEFAULT '%',
  `TRS_M_Tipo` int(4) NOT NULL DEFAULT '0',
  `TRS_M_Factor` int(4) NOT NULL DEFAULT '0',
  `TRS_M_Unit` varchar(1) NOT NULL DEFAULT 's',
  `TRM_M_Tipo` int(4) NOT NULL DEFAULT '1',
  `TRM_M_Factor` int(4) NOT NULL DEFAULT '50',
  `TRM_M_Unit` varchar(1) NOT NULL DEFAULT '%',
  `TRS_S_Tipo` int(4) NOT NULL DEFAULT '0',
  `TRS_S_Factor` int(4) NOT NULL DEFAULT '0',
  `TRS_S_Unit` varchar(1) NOT NULL DEFAULT 's',
  `TRM_S_Tipo` int(4) NOT NULL DEFAULT '1',
  `TRM_S_Factor` int(4) NOT NULL DEFAULT '50',
  `TRM_S_Unit` varchar(1) NOT NULL DEFAULT '%',
  `TRS_T_Tipo` int(4) NOT NULL DEFAULT '0',
  `TRS_T_Factor` int(4) NOT NULL DEFAULT '0',
  `TRS_T_Unit` varchar(1) NOT NULL DEFAULT 's',
  `TRM_T_Tipo` int(4) NOT NULL DEFAULT '1',
  `TRM_T_Factor` int(4) NOT NULL DEFAULT '50',
  `TRM_T_Unit` varchar(1) NOT NULL DEFAULT '%',
  `Juez1` int(4) NOT NULL DEFAULT '1',
  `Juez2` int(4) NOT NULL DEFAULT '1',
  `Observaciones` varchar(255) DEFAULT NULL,
  `Orden_Salida` text NOT NULL,
  `Orden_Equipos` text,
  PRIMARY KEY (`ID`),
  KEY `Mangas_Tipo` (`Tipo`),
  KEY `Mangas_Grado` (`Grado`),
  KEY `Mangas_Juez1` (`Juez1`),
  KEY `Mangas_Juez2` (`Juez2`),
  KEY `Mangas_Jornada` (`Jornada`),
  CONSTRAINT `Mangas_ibfk_1` FOREIGN KEY (`Tipo`) REFERENCES `tipo_manga` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `Mangas_ibfk_2` FOREIGN KEY (`Grado`) REFERENCES `grados_perro` (`Grado`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Mangas_ibfk_3` FOREIGN KEY (`Juez1`) REFERENCES `jueces` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `Mangas_ibfk_4` FOREIGN KEY (`Juez2`) REFERENCES `jueces` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `Mangas_ibfk_5` FOREIGN KEY (`Jornada`) REFERENCES `jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mangas`
--

LOCK TABLES `mangas` WRITE;
/*!40000 ALTER TABLE `mangas` DISABLE KEYS */;
INSERT INTO `mangas` VALUES 
(1,9,16,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'Jumping Especial','BEGIN,END','BEGIN,9,END'),
(2,17,9,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'','BEGIN,END','BEGIN,17,END'),
(3,17,14,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'','BEGIN,END','BEGIN,17,END'),
(4,25,7,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'','BEGIN,END','BEGIN,25,END'),
(5,25,12,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'','BEGIN,END','BEGIN,25,END'),
(6,33,16,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'Jumping Individual','BEGIN,END','BEGIN,33,END'),
(7,34,9,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'','BEGIN,END','BEGIN,34,END'),
(8,34,14,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'','BEGIN,END','BEGIN,34,END'),
(9,35,7,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'','BEGIN,END','BEGIN,35,END'),
(10,35,12,'-',0,0,0,0,0,0,0,0,0,0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',0,0,'s',1,50,'%',1,1,'','BEGIN,END','BEGIN,35,END');
/*!40000 ALTER TABLE `mangas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `perroguiaclub`
--

DROP TABLE IF EXISTS `perroguiaclub`;
/*!50001 DROP VIEW IF EXISTS `perroguiaclub`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `perroguiaclub` (
  `ID` tinyint NOT NULL,
  `Federation` tinyint NOT NULL,
  `Nombre` tinyint NOT NULL,
  `Raza` tinyint NOT NULL,
  `Licencia` tinyint NOT NULL,
  `LOE_RRC` tinyint NOT NULL,
  `Categoria` tinyint NOT NULL,
  `NombreCategoria` tinyint NOT NULL,
  `Grado` tinyint NOT NULL,
  `NombreGrado` tinyint NOT NULL,
  `Guia` tinyint NOT NULL,
  `NombreGuia` tinyint NOT NULL,
  `Club` tinyint NOT NULL,
  `NombreClub` tinyint NOT NULL,
  `LogoClub` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `perros`
--

DROP TABLE IF EXISTS `perros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perros` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Raza` varchar(255) DEFAULT NULL,
  `LOE_RRC` varchar(255) DEFAULT NULL,
  `Licencia` varchar(255) DEFAULT '--------',
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Guia` int(4) NOT NULL DEFAULT '1',
  `Federation` tinyint(1) NOT NULL DEFAULT '0',
  `Grado` varchar(16) DEFAULT '-',
  PRIMARY KEY (`ID`),
  KEY `Perros_GuiaNombre` (`Guia`),
  KEY `Perros_Categoria` (`Categoria`),
  KEY `Perros_Grado` (`Grado`),
  CONSTRAINT `Perros_ibfk_1` FOREIGN KEY (`Categoria`) REFERENCES `categorias_perro` (`Categoria`) ON UPDATE CASCADE,
  CONSTRAINT `Perros_ibfk_2` FOREIGN KEY (`Grado`) REFERENCES `grados_perro` (`Grado`) ON UPDATE CASCADE,
  CONSTRAINT `Perros_ibfk_3` FOREIGN KEY (`Guia`) REFERENCES `guias` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perros`
--

LOCK TABLES `perros` WRITE;
/*!40000 ALTER TABLE `perros` DISABLE KEYS */;
/*!40000 ALTER TABLE `perros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provincias`
--

DROP TABLE IF EXISTS `provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provincias` (
  `Provincia` varchar(32) NOT NULL DEFAULT '',
  `Comunidad` varchar(32) DEFAULT NULL,
  `Codigo` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Provincia`),
  UNIQUE KEY `Provincias_Codigo` (`Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provincias`
--

LOCK TABLES `provincias` WRITE;
/*!40000 ALTER TABLE `provincias` DISABLE KEYS */;
INSERT INTO `provincias` VALUES 
('-- Sin asignar --','NO BORRAR: Usado para cuando no ',0),
('Albacete','Castilla - La Mancha',2),
('Alicante/Alacant','Comunitat Valenciana',3),
('Almería','Andalucía',4),
('Araba/Álava','País Vasco',1),
('Asturias','Cantabria',33),
('Ávila','Castilla y León',5),
('Badajoz','Extremadura',6),
('Balears, Illes','Balears, Illes',7),
('Barcelona','Cataluña',8),
('Bizkaia/Vizcaya','País Vasco',48),
('Burgos','Castilla y León',9),
('Cáceres','Extremadura',10),
('Cádiz','Andalucía',11),
('Cantabria','Cantabria',39),
('Castellón/Castelló','Comunitat Valenciana',12),
('Ceuta','Ceuta',51),
('Ciudad Real','Castilla - La Mancha',13),
('Córdoba','Andalucía',14),
('Coruña, A','Galicia',15),
('Cuenca','Castilla - La Mancha',16),
('Gipuzkoa/Guipúzcoa','País Vasco',20),
('Girona/Gerona','Cataluña',17),
('Granada','Andalucía',18),
('Guadalajara','Castilla - La Mancha',19),
('Huelva','Andalucía',21),
('Huesca','Aragón',22),
('Jaén','Andalucía',23),
('León','Castilla y León',24),
('Lleida/Lérida','Cataluña',25),
('Lugo','Galicia',27),
('Madrid','Madrid, Comunidad de',28),
('Málaga','Andalucía',29),
('Melilla','Melilla',52),
('Murcia','Murcia, Región de',30),
('Navarra','Navarra, Comunidad Foral de',31),
('Ourense/Orense','Galicia',32),
('Palencia','Castilla y León',34),
('Palmas, Las','Canarias',35),
('Pontevedra','Galicia',36),
('Rioja, La','Rioja, La',26),
('Salamanca','Castilla y León',37),
('Santa Cruz de Te','Canarias',38),
('Segovia','Castilla y León',40),
('Sevilla','Andalucía',41),
('Soria','Castilla y León',42),
('Tarragona','Cataluña',43),
('Teruel','Aragón',44),
('Toledo','Castilla - La Mancha',45),
('Valencia/Valéncia','Comunitat Valenciana',46),
('Valladolid','Castilla y León',47),
('Zamora','Castilla y León',49),
('Zaragoza','Aragón',50);
/*!40000 ALTER TABLE `provincias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pruebas`
--

DROP TABLE IF EXISTS `pruebas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pruebas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Operador` int(4) DEFAULT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Club` int(4) NOT NULL DEFAULT '1',
  `Ubicacion` varchar(255) DEFAULT NULL,
  `Triptico` longblob,
  `Cartel` longblob,
  `Observaciones` varchar(255) DEFAULT NULL,
  `RSCE` tinyint(1) NOT NULL DEFAULT '0',
  `Selectiva` tinyint(1) NOT NULL DEFAULT '0',
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Pruebas_Club` (`Club`),
  CONSTRAINT `Pruebas_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `clubes` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pruebas`
--

LOCK TABLES `pruebas` WRITE;
/*!40000 ALTER TABLE `pruebas` DISABLE KEYS */;
INSERT INTO `pruebas` VALUES 
(1,NULL,'-- Sin asignar --',1,NULL,NULL,NULL,'NO BORRAR: Prueba por defecto para jornadas huerfanas',0,0,1),
(2,NULL,'L\'Almozara Dog Festival - Jumping',2,'Campo de Futbol Municipal de Zuera','','','',0,0,0),
(3,NULL,'L\'Almozara Dog Festival - Teams',2,'Campo de Futbol municipal de Zuera','','','',0,0,0),
(4,NULL,'L\'Almozara Dog Festival - Individual',2,'Campo de Futbol Municipal de Zuera','','','',0,0,0),
(5,NULL,'Prueba de prueba',2,'Prueba NO PUBLICA: para experimento','','','',0,0,0);
/*!40000 ALTER TABLE `pruebas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultados`
--

DROP TABLE IF EXISTS `resultados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultados` (
  `Prueba` int(4) NOT NULL,
  `Jornada` int(4) NOT NULL,
  `Manga` int(4) NOT NULL,
  `Dorsal` int(4) NOT NULL,
  `Perro` int(4) NOT NULL,
  `Equipo` int(4) NOT NULL DEFAULT '0',
  `Nombre` varchar(255) NOT NULL,
  `Raza` varchar(255) NOT NULL DEFAULT '',
  `Licencia` varchar(255) NOT NULL DEFAULT '--------',
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Grado` varchar(16) NOT NULL DEFAULT '-',
  `Celo` tinyint(1) NOT NULL DEFAULT '0',
  `NombreGuia` varchar(255) NOT NULL DEFAULT '-- Sin asignar --',
  `NombreClub` varchar(255) NOT NULL DEFAULT '-- Sin asignar --',
  `Entrada` timestamp NOT NULL DEFAULT '2014-01-01 00:00:00',
  `Comienzo` timestamp NOT NULL DEFAULT '2014-01-01 00:00:00',
  `Faltas` int(4) NOT NULL DEFAULT '0',
  `Rehuses` int(4) NOT NULL DEFAULT '0',
  `Tocados` int(4) NOT NULL DEFAULT '0',
  `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `NoPresentado` tinyint(1) NOT NULL DEFAULT '0',
  `Tiempo` double NOT NULL DEFAULT '0',
  `Observaciones` varchar(255) NOT NULL DEFAULT '',
  `Pendiente` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Manga`,`Perro`),
  KEY `Resultados_Perro` (`Perro`),
  KEY `Resultados_Manga` (`Manga`),
  KEY `Resultados_Dorsal` (`Dorsal`),
  KEY `Resultados_Jornada` (`Jornada`),
  KEY `Resultados_Prueba` (`Prueba`),
  CONSTRAINT `Resultados_ibfk_1` FOREIGN KEY (`Perro`) REFERENCES `perros` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Resultados_ibfk_2` FOREIGN KEY (`Manga`) REFERENCES `mangas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Resultados_ibfk_3` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Resultados_ibfk_4` FOREIGN KEY (`Jornada`) REFERENCES `jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultados`
--

LOCK TABLES `resultados` WRITE;
/*!40000 ALTER TABLE `resultados` DISABLE KEYS */;
/*!40000 ALTER TABLE `resultados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sesiones`
--

DROP TABLE IF EXISTS `sesiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sesiones` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL DEFAULT '-- Sin asignar --',
  `Comentario` varchar(255) DEFAULT NULL,
  `Operador` int(4) NOT NULL DEFAULT '1',
  `SessionKey` varchar(255) DEFAULT NULL,
  `Prueba` int(4) NOT NULL DEFAULT '0',
  `Jornada` int(4) NOT NULL DEFAULT '0',
  `Manga` int(4) NOT NULL DEFAULT '0',
  `Tanda` int(4) NOT NULL DEFAULT '0',
  `Background` varchar(255) NOT NULL DEFAULT '',
  `LiveStream` varchar(255) DEFAULT NULL,
  `LiveStream2` varchar(255) DEFAULT NULL,
  `LiveStream3` varchar(255) DEFAULT NULL,
  `LastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `Sesiones_Operador` (`Operador`),
  CONSTRAINT `Sesiones_ibfk_1` FOREIGN KEY (`Operador`) REFERENCES `usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sesiones`
--

LOCK TABLES `sesiones` WRITE;
/*!40000 ALTER TABLE `sesiones` DISABLE KEYS */;
INSERT INTO `sesiones` VALUES 
(1,'-- Sin asignar --','Actividades fuera de ring',5,'qIHDXyhc54AZkfWT',2,9,1,153,'','/agility/videos/sample_video.mp4',NULL,NULL,'2015-05-13 12:07:44'),
(2,'Ring 1','Ring 1 / Ring de Honor',5,'JVYba0svX8wUrgNy',12,90,64,1368,'http://192.168.122.168/videostream.cgi','/agility/videos/sample_video.mp4','','','2015-06-11 09:04:03'),
(3,'Ring 2','Mangas a realizar en el segundo ring',5,'RfACb3MEl5ieNkgr',13,97,0,0,'',NULL,NULL,NULL,'2015-06-05 16:38:46'),
(4,'Ring 3','Mangas a realizar en el tercer ring',1,NULL,0,0,0,0,'',NULL,NULL,NULL,'2014-12-05 19:14:34'),
(5,'Ring 4','Mangas a realizar en el cuarto ring',1,NULL,0,0,0,0,'',NULL,NULL,NULL,'2014-12-05 19:14:34'),
(9,'Console','admin - Administrador de la aplicacion',3,'tEBrLYlaWUKTXmns',0,0,0,0,'',NULL,NULL,NULL,'2015-06-24 17:07:57');
/*!40000 ALTER TABLE `sesiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tandas`
--

DROP TABLE IF EXISTS `tandas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tandas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Jornada` int(4) NOT NULL,
  `Sesion` int(4) NOT NULL DEFAULT '1',
  `Orden` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Categoria` varchar(16) NOT NULL,
  `Grado` varchar(16) NOT NULL,
  `Horario` varchar(128) DEFAULT NULL,
  `Tipo` int(4) NOT NULL,
  `Comentario` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Tandas_Prueba` (`Prueba`),
  KEY `Tandas_Jornada` (`Jornada`),
  KEY `Tandas_Sesion` (`Sesion`),
  CONSTRAINT `Tandas_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Tandas_ibfk_2` FOREIGN KEY (`Jornada`) REFERENCES `jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Tandas_ibfk_3` FOREIGN KEY (`Sesion`) REFERENCES `sesiones` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tandas`
--

LOCK TABLES `tandas` WRITE;
/*!40000 ALTER TABLE `tandas` DISABLE KEYS */;
INSERT INTO `tandas` VALUES 
(1,2,9,2,4,'Manga Especial Large','L','-',NULL,38,NULL),
(2,2,9,2,5,'Manga Especial Medium','M','-',NULL,39,NULL),
(3,2,9,2,6,'Manga Especial Small','S','-',NULL,40,NULL),
(4,3,17,2,1,'Ag. Equipos 4 Large','L','-',NULL,21,NULL),
(5,3,17,2,2,'Ag. Equipos 4 Med/Small','MS','-',NULL,22,NULL),
(6,3,17,2,3,'Jp. Equipos 4 Large','L','-',NULL,35,NULL),
(7,3,17,2,4,'Jp. Equipos 4 Med/Small','MS','-',NULL,36,NULL),
(8,4,25,2,1,'Agility Open Large','L','-',NULL,15,NULL),
(9,4,25,2,2,'Agility Open Medium','M','-',NULL,16,NULL),
(10,4,25,2,3,'Agility Open Small','S','-',NULL,17,NULL),
(11,4,25,2,4,'Jumping Open Large','L','-',NULL,29,NULL),
(12,4,25,2,5,'Jumping Open Medium','M','-',NULL,30,NULL),
(13,4,25,2,6,'Jumping Open Small','S','-',NULL,31,NULL),
(14,5,33,2,1,'Manga Especial Large','L','-',NULL,38,NULL),
(15,5,33,2,2,'Manga Especial Medium','M','-',NULL,39,NULL),
(16,5,33,2,3,'Manga Especial Small','S','-',NULL,40,NULL),
(17,5,34,2,1,'Ag. Equipos 4 Large','L','-',NULL,21,NULL),
(18,5,34,2,2,'Ag. Equipos 4 Med/Small','MS','-',NULL,22,NULL),
(19,5,34,2,3,'Jp. Equipos 4 Large','L','-',NULL,35,NULL),
(20,5,34,2,4,'Jp. Equipos 4 Med/Small','MS','-',NULL,36,NULL),
(21,5,35,2,1,'Agility Open Large','L','-',NULL,15,NULL),
(22,5,35,2,2,'Agility Open Medium','M','-',NULL,16,NULL),
(23,5,35,2,3,'Agility Open Small','S','-',NULL,17,NULL),
(24,5,35,2,4,'Jumping Open Large','L','-',NULL,29,NULL),
(25,5,35,2,5,'Jumping Open Medium','M','-',NULL,30,NULL),
(26,5,35,2,6,'Jumping Open Small','S','-',NULL,31,NULL);
/*!40000 ALTER TABLE `tandas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_manga`
--

DROP TABLE IF EXISTS `tipo_manga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_manga` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) DEFAULT NULL,
  `Grado` varchar(16) NOT NULL DEFAULT '-',
  PRIMARY KEY (`ID`),
  KEY `Descripcion` (`Descripcion`),
  KEY `Grado` (`Grado`),
  CONSTRAINT `Tipo_Manga_ibfk_1` FOREIGN KEY (`Grado`) REFERENCES `grados_perro` (`Grado`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_manga`
--

LOCK TABLES `tipo_manga` WRITE;
/*!40000 ALTER TABLE `tipo_manga` DISABLE KEYS */;
INSERT INTO `tipo_manga` VALUES 
(1,'Pre-Agility Manga 1','P.A.'),
(2,'Pre-Agility Manga 2','P.A.'),
(3,'Agility Grado I Manga 1','GI'),
(4,'Agility Grado I Manga 2','GI'),
(5,'Agility Grado II','GII'),
(6,'Agility Grado III','GIII'),
(7,'Agility Abierta','-'),
(8,'Agility Equipos (3 mejores)','-'),
(9,'Agility Equipos (Conjunta)','-'),
(10,'Jumping Grado II','GII'),
(11,'Jumping Grado III','GIII'),
(12,'Jumping Abierta','-'),
(13,'Jumping Equipos (3 mejores)','-'),
(14,'Jumping Equipos (Conjunta)','-'),
(15,'Ronda K.O.','-'),
(16,'Manga Especial','-');
/*!40000 ALTER TABLE `tipo_manga` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Login` varchar(255) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Gecos` varchar(255) NOT NULL DEFAULT '',
  `Phone` varchar(255) NOT NULL DEFAULT '',
  `Email` varchar(255) NOT NULL DEFAULT '',
  `Perms` int(4) NOT NULL DEFAULT '5',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Login` (`Login`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES 
(1,'-- Sin asignar --','--LOCKED--','NO BORRAR: Usuario por defecto para sesiones anonimas','','',5),
(2,'root','JDJ5JDEwJHc2Lm50WFhsQUYuWDl2Zm9JbnNVb09TVEVwcllGaHBCQjFQYk12Yk81VzlJWDd0cTNPRnd5','Usuario Root','','',0),
(3,'admin','JDJ5JDEwJFcwa3B4YUxDVkJ0OVd0NFZVNUhzcXVBTE1yN0x2WWhBTFo4RHQ5TWZZQzgzZGRnMDA1VlVD','Administrador de la aplicacion','','',1),
(4,'operator','JDJ5JDEwJHMyclNoQUtsMlJ0UU5pRG9yUXF3QXUwbEVRdWpUT0daSXJGZmJLR3B4MEVHRzRiOFNYSjdt','Operador de consola','','',2),
(5,'assistant','JDJ5JDEwJHRLL09tT2xJZ1lRRlovNVhsLksxRC52aXo4L1UxNTMub1EwRDRoZ3pCZDcxRHRnSmo0LmE2','Asistente del juez (tablet)','','',3),
(6,'guest','--NULL--','Usuario invitado (anonimo)','','',4);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `versionhistory`
--

DROP TABLE IF EXISTS `versionhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `versionhistory` (
  `Version` varchar(16) NOT NULL DEFAULT '{MINVER}',
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `versionhistory`
--

LOCK TABLES `versionhistory` WRITE;
/*!40000 ALTER TABLE `versionhistory` DISABLE KEYS */;
INSERT INTO `versionhistory` VALUES 
('20150609_1457','2015-06-10 12:19:56'),
('20150611_1445','2015-06-11 12:46:13'),
('20150612_1421','2015-06-17 00:56:52'),
('20150619_1843','2015-06-24 16:50:04');
/*!40000 ALTER TABLE `versionhistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'agility'
--

-- insufficient privileges to SHOW CREATE PROCEDURE `AddColumnUnlessExists`
-- does agility_operator have permissions on mysql.proc?

