-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 07-03-2014 a las 22:29:48
-- Versión del servidor: 5.5.35-0ubuntu0.13.10.2
-- Versión de PHP: 5.5.3-1ubuntu2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `agility`
--
CREATE DATABASE IF NOT EXISTS `agility` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `agility`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Categorias_Perro`
--
-- Creación: 07-03-2014 a las 20:31:24
--

DROP TABLE IF EXISTS `Categorias_Perro`;
CREATE TABLE IF NOT EXISTS `Categorias_Perro` (
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Categorias_Perro`
--

INSERT INTO `Categorias_Perro` (`Categoria`, `Observaciones`) VALUES
('-', 'Sin especificar'),
('L', 'Large - Standard'),
('M', 'Medium - Midi'),
('S', 'Small - Mini'),
('T', 'Tiny - Enano');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Provincias`
--
-- Creación: 07-03-2014 a las 20:31:26
--

DROP TABLE IF EXISTS `Provincias`;
CREATE TABLE IF NOT EXISTS `Provincias` (
  `Provincia` varchar(32) NOT NULL DEFAULT '',
  `Comunidad` varchar(32) DEFAULT NULL,
  `Codigo` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Provincia`),
  UNIQUE KEY `Codigo` (`Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `Provincias` (`Provincia`, `Comunidad`, `Codigo`) VALUES
('-- Sin asignar --', 'NO BORRAR: Usado para cuando no hay provincia definida', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Clubes`
--
-- Creación: 07-03-2014 a las 20:31:27
--

DROP TABLE IF EXISTS `Clubes`;
CREATE TABLE IF NOT EXISTS `Clubes` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL DEFAULT '',
  `Direccion1` varchar(255) DEFAULT NULL,
  `Direccion2` varchar(255) DEFAULT NULL,
  `Provincia` varchar(32) NOT NULL DEFAULT '-- Sin asignar --',
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
  `Observaciones` varchar(255) DEFAULT NULL,
  `Baja` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Clubes_Nombre` (`Nombre`),
  KEY `Clubes_Provincia` (`Provincia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Club por defecto. No se deberia borrar nunca
INSERT INTO `Clubes` (`ID`, `Nombre`, `Observaciones`, `Baja`) VALUES
(1, '-- Sin asignar --', 'NO BORRAR ESTA ENTRADA. SE USARA PARA AQUELLOS GUIAS QUE NO TENGAN CLUB ASIGNADO', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Equipos`
--
-- Creación: 07-03-2014 a las 20:31:27
--

DROP TABLE IF EXISTS `Equipos`;
CREATE TABLE IF NOT EXISTS `Equipos` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Nombre` varchar(255) NOT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Prueba` (`Prueba`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Equipos`:
--   `Prueba`
--       `Pruebas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Grados_Perro`
--
-- Creación: 07-03-2014 a las 20:31:25
--

DROP TABLE IF EXISTS `Grados_Perro`;
CREATE TABLE IF NOT EXISTS `Grados_Perro` (
  `Grado` varchar(16) NOT NULL,
  `Comentarios` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Grados_Perro`
--

INSERT INTO `Grados_Perro` (`Grado`, `Comentarios`) VALUES
('-', 'Sin especificar'),
('Baja', 'Baja temporal'),
('GI', 'Grado I'),
('GII', 'Grado II'),
('GIII', 'Grado III'),
('P.A.', 'Pre-Agility'),
('P.B.', 'Perro en Blanco'),
('Ret.', 'Retirado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Guias`
--
-- Creación: 07-03-2014 a las 20:31:27
--

DROP TABLE IF EXISTS `Guias`;
CREATE TABLE IF NOT EXISTS `Guias` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Telefono` varchar(16) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Club` int(4) NOT NULL DEFAULT '1',
  `NombreClub` varchar(255),
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Guias_Nombre` (`Nombre`),
  KEY `Guias_Club` (`Club`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- RELACIONES PARA LA TABLA `Guias`:
--   `Club`
--       `Clubes` -> `Nombre`
--

--
-- Volcado de datos para la tabla `Guias`
--

INSERT INTO `Guias` (`ID`, `Nombre`, `Club`, `Observaciones`) VALUES
(1, '-- Sin asignar --', 1, 'NO BORRAR. Valor por defecto cuando un perro se define por primera vez');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inscripciones`
--
-- Creación: 07-03-2014 a las 20:31:28
--

DROP TABLE IF EXISTS `Inscripciones`;
CREATE TABLE IF NOT EXISTS `Inscripciones` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Perro` int(4) NOT NULL,
  `Dorsal` int(4) NOT NULL DEFAULT '0',
  `Celo` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` varchar(255) DEFAULT NULL,
  `Equipo` int(4) DEFAULT NULL,
  `J1` tinyint(1) NOT NULL DEFAULT '0',
  `J2` tinyint(1) NOT NULL DEFAULT '0',
  `J3` tinyint(1) NOT NULL DEFAULT '0',
  `J4` tinyint(1) NOT NULL DEFAULT '0',
  `J5` tinyint(1) NOT NULL DEFAULT '0',
  `J6` tinyint(1) NOT NULL DEFAULT '0',
  `J7` tinyint(1) NOT NULL DEFAULT '0',
  `J8` tinyint(1) NOT NULL DEFAULT '0',
  `Pagado` int(4) NOT NULL DEFAULT '12',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Inscripciones_PruebaPerro` (`Prueba`,`Perro`),
  KEY `Inscripciones_Perro` (`Perro`),
  KEY `Inscripciones_Prueba` (`Prueba`),
  KEY `Inscripciones_Equipo` (`Equipo`),
  KEY `Inscripciones_Dorsal` (`Dorsal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Inscripciones`:
--   `Perro`
--       `Perros` -> `Perro`
--   `Prueba`
--       `Pruebas` -> `ID`
--   `Equipo`
--       `Equipos` -> `ID`
--

--
-- Disparadores `Inscripciones`
--
DROP TRIGGER IF EXISTS `Increase_Dorsal`;
DELIMITER //
CREATE TRIGGER `Increase_Dorsal` BEFORE INSERT ON `Inscripciones`
 FOR EACH ROW BEGIN
     select count(*) into @rows from Inscripciones where Prueba = NEW.Prueba;
     if @rows>0 then
     select Dorsal + 1 into @newDorsal from Inscripciones where Prueba = NEW.Prueba order by Dorsal desc limit 1;
       set NEW.Dorsal = @newDorsal;
     else
       set NEW.Dorsal = 1;
     end if;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Jornadas`
--
-- Creación: 07-03-2014 a las 20:31:28
--

DROP TABLE IF EXISTS `Jornadas`;
CREATE TABLE IF NOT EXISTS `Jornadas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Numero` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL,
  `Grado1` tinyint(1) NOT NULL DEFAULT '1',
  `Grado2` tinyint(1) NOT NULL DEFAULT '1',
  `Grado3` tinyint(1) NOT NULL DEFAULT '1',
  `Equipos` tinyint(1) NOT NULL DEFAULT '0',
  `PreAgility` tinyint(1) NOT NULL DEFAULT '1',
  `KO` tinyint(1) NOT NULL DEFAULT '0',
  `Exhibicion` tinyint(1) NOT NULL DEFAULT '0',
  `Otras` tinyint(1) NOT NULL DEFAULT '0',
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Prueba` (`Prueba`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Jornadas`:
--   `Prueba`
--       `Pruebas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Jueces`
--
-- Creación: 07-03-2014 a las 20:31:25
--

DROP TABLE IF EXISTS `Jueces`;
CREATE TABLE IF NOT EXISTS `Jueces` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Direccion1` varchar(255) DEFAULT NULL,
  `Direccion2` varchar(255) DEFAULT NULL,
  `Telefono` varchar(32) DEFAULT NULL,
  `Internacional` tinyint(1) NOT NULL DEFAULT '0',
  `Practicas` tinyint(1) NOT NULL DEFAULT '0',
  `Email` varchar(255) DEFAULT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Jueces_Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

--
-- Volcado de datos para la tabla `Jueces`
--

INSERT INTO `Jueces` (`ID`, `Nombre`, `Direccion1`, `Direccion2`, `Telefono`, `Internacional`, `Practicas`, `Email`, `Observaciones`) VALUES
(2, '-- Sin asignar --', NULL, NULL, '--- -- -- --', 0, 0, 'nobody@nomail.com', 'NO BORRAR: Asignacion de juez por defecto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Mangas`
--
-- Creación: 07-03-2014 a las 20:31:28
--

DROP TABLE IF EXISTS `Mangas`;
CREATE TABLE IF NOT EXISTS `Mangas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Jornada` int(4) NOT NULL,
  `Tipo` varchar(16) NOT NULL DEFAULT 'Otras',
  `Grado` varchar(16) NOT NULL DEFAULT '-',
  `Recorrido` int(4) NOT NULL DEFAULT '0',
  `Dist_L` int(4) NOT NULL DEFAULT '0',
  `Obst_L` int(4) NOT NULL DEFAULT '0',
  `Dist_M` int(4) NOT NULL DEFAULT '0',
  `Obst_M` int(4) NOT NULL DEFAULT '0',
  `Dist_S` int(4) NOT NULL DEFAULT '0',
  `Obst_S` int(4) NOT NULL DEFAULT '0',
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
  `Juez1` int(4) NOT NULL DEFAULT '1',
  `Juez2` int(4) NOT NULL DEFAULT '1',
  `Observaciones` varchar(255) DEFAULT NULL,
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  `Orden_Salida` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Tipo` (`Tipo`),
  KEY `Grado` (`Grado`),
  KEY `Juez_Titular` (`Juez1`),
  KEY `Juez_Practicas` (`Juez2`),
  KEY `Jornada` (`Jornada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Mangas`:
--   `Tipo`
--       `Tipo_Manga` -> `Tipo`
--   `Grado`
--       `Grados_Perro` -> `Grado`
--   `Juez1`
--       `Jueces` -> `Nombre`
--   `Juez2`
--       `Jueces` -> `Nombre`
--   `Jornada`
--       `Jornadas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Perros`
--
-- Creación: 07-03-2014 a las 20:31:29
--

DROP TABLE IF EXISTS `Perros`;
CREATE TABLE IF NOT EXISTS `Perros` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Raza` varchar(255) DEFAULT NULL,
  `LOE_RRC` varchar(255) DEFAULT NULL,
  `Licencia` varchar(255) DEFAULT '--------',
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Guia` int(4) NOT NULL DEFAULT '1',
  `NombreGuia` varchar(255) DEFAULT '-- Sin asignar --',
  `Grado` varchar(16) DEFAULT '-',
  PRIMARY KEY (`ID`),
  KEY `Perros_GuiaNombre` (`Guia`),
  KEY `Perros_Categoria` (`Categoria`),
  KEY `Perros_Grado` (`Grado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Perros`:
--   `Categoria`
--       `Categorias_Perro` -> `Categoria`
--   `Grado`
--       `Grados_Perro` -> `Grado`
--   `Guia`
--       `Guias` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Pruebas`
--
-- Creación: 07-03-2014 a las 20:31:29
--

DROP TABLE IF EXISTS `Pruebas`;
CREATE TABLE IF NOT EXISTS `Pruebas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Club` int(4) NOT NULL DEFAULT '1',
  `Ubicacion` varchar(255) DEFAULT NULL,
  `Triptico` longblob,
  `Cartel` longblob,
  `Observaciones` varchar(255) DEFAULT NULL,
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Club` (`Club`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO Pruebas(`ID`,`Nombre`,`Observaciones`,`Cerrada`) 
VALUES(1,'-- Sin asignar --', 'NO BORRAR: Prueba por defecto para jornadas huerfanas', 1);

--
-- RELACIONES PARA LA TABLA `Pruebas`:
--   `Club`
--       `Clubes` -> `Nombre`
--

-- --------------------------------------------------------

--
-- Notice that Guia and Club doesnt refers to pkey, just name, to avoid cascades
-- Estructura de tabla para la tabla `Resultados`
--
-- Creación: 07-03-2014 a las 20:31:29
--

DROP TABLE IF EXISTS `Resultados`;
CREATE TABLE IF NOT EXISTS `Resultados` (
  `Manga` int(4) NOT NULL,
  `Perro` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Licencia` varchar(255) NOT NULL DEFAULT '--------',
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Grado` varchar(16) NOT NULL DEFAULT '-',
  `Guia` varchar(255) NOT NULL DEFAULT '-- Sin asignar --',
  `Club` varchar(255) NOT NULL DEFAULT '-- Sin asignar --',
  `Entrada` timestamp NOT NULL DEFAULT '2014-01-01 00:00:00',
  `Comienzo` timestamp NOT NULL DEFAULT '2014-01-01 00:00:00',
  `Faltas` int(4) NOT NULL DEFAULT '0',
  `Rehuses` int(4) NOT NULL DEFAULT '0',
  `Tocados` int(4) NOT NULL DEFAULT '0',
  `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `NoPresentado` tinyint(1) NOT NULL DEFAULT '0',
  `Tiempo` double NOT NULL DEFAULT '0',
  `Observaciones` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Manga`,`Perro`),
  KEY `Resultados_Perro` (`Perro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `Resultados`:
--   `Perro`
--       `Perros` -> `Perro`
--   `Manga`
--       `Mangas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tipo_Manga`
--
-- Creación: 07-03-2014 a las 20:31:30
--

DROP TABLE IF EXISTS `Tipo_Manga`;
CREATE TABLE IF NOT EXISTS `Tipo_Manga` (
  `Tipo` varchar(16) NOT NULL DEFAULT '',
  `Descripcion` varchar(255) DEFAULT NULL,
  `Grado` varchar(16) NOT NULL DEFAULT '-',
  PRIMARY KEY (`Tipo`),
  KEY `Grado` (`Grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `Tipo_Manga`:
--   `Grado`
--       `Grados_Perro` -> `Grado`
--

--
-- Volcado de datos para la tabla `Tipo_Manga`
--

INSERT INTO `Tipo_Manga` (`Tipo`, `Descripcion`, `Grado`) VALUES
('Agility Equipos', 'Agility por Equipos', '-'),
('Agility GII', 'Agility Grado II', 'GII'),
('Agility GIII', 'Agility Grado III', 'GIII'),
('Agility-1 GI ', 'Agility Grado I Manga 1', 'GI'),
('Agility-2 GI', 'Agility Grado I Manga 2', 'GI'),
('Exhibición', 'Ronda de Exhibición', '-'),
('Jumping Equipos', 'Jumping por Equipos', '-'),
('Jumping GII', 'Jumping Grado II', 'GII'),
('Jumping GIII', 'Jumping Grado III', 'GIII'),
('K.O.', 'Ronda K.O.', '-'),
('Otras', 'Manga sin tipo definido', '-'),
('Pre-Agility', 'Ronda de Pre-Agility', 'P.A.');

-- --------------------------------------------------------

--
-- Estructura para la vista `PerroGuiaClub`
--
DROP TABLE IF EXISTS `PerroGuiaClub`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `PerroGuiaClub` AS select `Perros`.`ID` AS `Perro`,`Perros`.`Nombre` AS `Nombre`,`Perros`.`Raza` AS `Raza`,`Perros`.`Licencia` AS `Licencia`,`Perros`.`LOE_RRC` AS `LOE_RRC`,`Perros`.`Categoria` AS `Categoria`,`Perros`.`Grado` AS `Grado`,`Guias`.`Nombre` AS `Guia`,`Clubes`.`Nombre` AS `Club` from (`Perros` join `Guias` join Clubes) where ( (`Perros`.`Guia` = `Guias`.`ID`) AND ( `Guias`.`Club`= `Clubes`.`ID` ) ) order by `Clubes`.`Nombre`,`Perros`.`Categoria`,`Perros`.`Nombre`;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Clubes`
--
ALTER TABLE `Clubes`
  ADD CONSTRAINT `Clubes_ibfk_1` FOREIGN KEY (`Provincia`) REFERENCES `Provincias` (`Provincia`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Guias`
--
ALTER TABLE `Guias`
  ADD CONSTRAINT `Guias_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `Clubes` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Perros`
--
ALTER TABLE `Perros`
  ADD CONSTRAINT `Perros_ibfk_1` FOREIGN KEY (`Categoria`) REFERENCES `Categorias_Perro` (`Categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Perros_ibfk_2` FOREIGN KEY (`Grado`) REFERENCES `Grados_Perro` (`Grado`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Perros_ibfk_3` FOREIGN KEY (`Guia`) REFERENCES `Guias` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Equipos`
--
ALTER TABLE `Equipos`
  ADD CONSTRAINT `Equipos_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `Pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Pruebas`
--
ALTER TABLE `Pruebas`
  ADD CONSTRAINT `Pruebas_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `Clubes` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Jornadas`
--
ALTER TABLE `Jornadas`
  ADD CONSTRAINT `Jornadas_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `Pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Mangas`
--
ALTER TABLE `Mangas`
  ADD CONSTRAINT `Mangas_ibfk_1` FOREIGN KEY (`Tipo`) REFERENCES `Tipo_Manga` (`Tipo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Mangas_ibfk_2` FOREIGN KEY (`Grado`) REFERENCES `Grados_Perro` (`Grado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Mangas_ibfk_3` FOREIGN KEY (`Juez1`) REFERENCES `Jueces` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Mangas_ibfk_4` FOREIGN KEY (`Juez2`) REFERENCES `Jueces` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Mangas_ibfk_5` FOREIGN KEY (`Jornada`) REFERENCES `Jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Inscripciones`
--
ALTER TABLE `Inscripciones`
  ADD CONSTRAINT `Inscripciones_ibfk_1` FOREIGN KEY (`Perro`) REFERENCES `Perros` (`ID`),
  ADD CONSTRAINT `Inscripciones_ibfk_2` FOREIGN KEY (`Prueba`) REFERENCES `Pruebas` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Inscripciones_ibfk_3` FOREIGN KEY (`Equipo`) REFERENCES `Equipos` (`ID`);

--
-- Filtros para la tabla `Resultados`
--
ALTER TABLE `Resultados`
  ADD CONSTRAINT `Resultados_ibfk_1` FOREIGN KEY (`Perro`) REFERENCES `Perros` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Resultados_ibfk_2` FOREIGN KEY (`Manga`) REFERENCES `Mangas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Tipo_Manga`
--
ALTER TABLE `Tipo_Manga`
  ADD CONSTRAINT `Tipo_Manga_ibfk_1` FOREIGN KEY (`Grado`) REFERENCES `Grados_Perro` (`Grado`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
