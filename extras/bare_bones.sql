-- AgilityContest Version: 2.0.0 Revision: 20150720_0148
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubes`
--

LOCK TABLES `clubes` WRITE;
/*!40000 ALTER TABLE `clubes` DISABLE KEYS */;
INSERT INTO `clubes` VALUES 
(1,'-- Sin asignar --','\"\"','','','-- Sin asignar --','ES','','','','','','','','','','rsce.png',7,'NO BORRAR ESTA ENTRADA. SE USARA PARA AQUELLOS GUIAS QUE NO TENGAN CLUB ASIGNADO',0);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
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
(8,1,8,'-LMST','-- Sin asignar --','NO BORRAR: PRUEBA 1 JORNADA 8 - Equipo por defecto','BEGIN,END',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
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
  CONSTRAINT `Inscripciones_ibfk_1` FOREIGN KEY (`Perro`) REFERENCES `perros` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `Inscripciones_ibfk_2` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
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
(8,1,8,0,8,'-- Sin asignar --','2013-01-01','00:00:00',0,0,0,0,0,0,0,0,0,0,1,'(sin especificar)','BEGIN,END');
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mangas`
--

LOCK TABLES `mangas` WRITE;
/*!40000 ALTER TABLE `mangas` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pruebas`
--

LOCK TABLES `pruebas` WRITE;
/*!40000 ALTER TABLE `pruebas` DISABLE KEYS */;
INSERT INTO `pruebas` VALUES 
(1,NULL,'-- Sin asignar --',1,NULL,NULL,NULL,'NO BORRAR: Prueba por defecto para jornadas huerfanas',0,0,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
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
(5,'Ring 4','Mangas a realizar en el cuarto ring',1,NULL,0,0,0,0,'',NULL,NULL,NULL,'2014-12-05 19:14:34');
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tandas`
--

LOCK TABLES `tandas` WRITE;
/*!40000 ALTER TABLE `tandas` DISABLE KEYS */;
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
('20150612_1421','2015-06-17 00:56:52');
/*!40000 ALTER TABLE `versionhistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'agility'
--

-- insufficient privileges to SHOW CREATE PROCEDURE `AddColumnUnlessExists`
-- does agility_operator have permissions on mysql.proc?

