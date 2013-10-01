-- phpMyAdmin SQL Dump
-- version 4.0.7deb0ubuntu1ppa1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 30-09-2013 a las 15:48:26
-- Versión del servidor: 5.5.32-0ubuntu0.12.04.1
-- Versión de PHP: 5.3.10-1ubuntu3.8

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
CREATE DATABASE IF NOT EXISTS `agility` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `agility`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Categorias_Perro`
--
-- Creación: 30-09-2013 a las 06:07:31
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
('S', 'Small - Mini');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Clubes`
--
-- Creación: 30-09-2013 a las 06:07:33
--

DROP TABLE IF EXISTS `Clubes`;
CREATE TABLE IF NOT EXISTS `Clubes` (
  `Nombre` varchar(255) NOT NULL DEFAULT '',
  `Direccion1` varchar(255) DEFAULT NULL,
  `Direccion2` varchar(255) DEFAULT NULL,
  `Provincia` varchar(32) DEFAULT NULL,
  `Contacto1` varchar(255) DEFAULT NULL,
  `Contacto2` varchar(255) DEFAULT NULL,
  `Contacto3` varchar(255) DEFAULT NULL,
  `GPS` varchar(255) DEFAULT NULL,
  `Web` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Facebook` varchar(255) DEFAULT NULL,
  `Google` varchar(255) DEFAULT NULL,
  `Twitter` varchar(255) DEFAULT NULL,
  `Logo` mediumblob,
  `Observaciones` varchar(255) DEFAULT NULL,
  `Baja` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Nombre`),
  KEY `Provincia` (`Provincia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `Clubes`:
--   `Provincia`
--       `Provincias` -> `Provincia`
--

--
-- Volcado de datos para la tabla `Clubes`
--

INSERT INTO `Clubes` (`Nombre`, `Direccion1`, `Direccion2`, `Provincia`, `Contacto1`, `Contacto2`, `Contacto3`, `GPS`, `Web`, `Email`, `Facebook`, `Google`, `Twitter`, `Logo`, `Observaciones`, `Baja`) VALUES
(' Sin asignar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NO BORRAR ESTA ENTRADA. SE USARA PARA AQUELLOS GUIAS QUE NO TENGAN CLUB ASIGNADO', 1),
('ACADE', 'Salvadas, 41, 2º C', '15705 Santiago de Compostela', 'Coruña, A', '+ 34 620 29 58 31', '+ 34 881 93 95 5', NULL, NULL, 'http://www.asociacionacade.com/', 'asociacioncansdeportistas@gmail.com', NULL, NULL, NULL, NULL, NULL, 0),
('Agilcan', 'Paseo de los Olivos 10', '28330 San Martin de la Vega', 'Madrid', '634 417 893', '918 946 096', '659 146 878', NULL, 'http://www.agilcan.es/', 'info@agilcan.es', NULL, NULL, NULL, NULL, NULL, 0),
('Badalona', NULL, NULL, 'Barcelona', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Baix Llobregat', NULL, NULL, 'Barcelona', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Campo de Gibraltar', NULL, NULL, 'Cádiz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Camu', NULL, NULL, 'Murcia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Canic', NULL, NULL, 'Barcelona', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Canino Algecireño', NULL, NULL, 'Cádiz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Castellón', NULL, NULL, 'Castellón/Castelló', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Cinco Huesos', 'Paseo de los Pozos, Km. 1,2', '28813 Torres de la Alameda', 'Madrid', '+ 34 91 832 83 00 ', '+ 34 691 77 75 24 ', '', '', 'http://www.cincohuesos.com/', 'cincohuesos@gmail.com', '', '', '', NULL, 'Antiguo club "Apata"', 0),
('Ciudad de Antequera', NULL, NULL, 'Málaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Ciutat Comtal', NULL, NULL, 'Barcelona', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Cornella', NULL, NULL, 'Barcelona', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Correcan', NULL, NULL, 'Madrid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Costa Blanca', 'C/ Baltasar Gracián Nº3, Urb. Montecid', '03670 Monforte del Cid ', 'Alicante/Alacant', NULL, NULL, NULL, NULL, 'http://www.agilitycostablanca.com/', 'agility@agilitycostablanca.com', NULL, NULL, NULL, NULL, NULL, 0),
('Cuatro Patas', 'Club social Urb/ El Socorro', 'Carmona', 'Sevilla', '630 52 72 42 (Isaac) ', '615 45 58 78 (Rafa)', NULL, 'N 37.43865 - W 5.79858', NULL, 'agiltiy4patas@hotmail.com', NULL, NULL, NULL, 0x74727565, NULL, 0),
('Cubas', 'Paseo de los Cipreses s/n', 'Cubas de la Sagra', 'Madrid', '918143395', '619 56 43 49', NULL, NULL, 'http://clubagilitycubas.es/', 'clubagilitycubas@terra.com', NULL, NULL, NULL, NULL, NULL, 0),
('Deporcan', NULL, NULL, 'Madrid', '629 843 681', NULL, NULL, '40.32132, -3.41895', NULL, 'agility.deporcan@gmail.com', NULL, NULL, NULL, 0x416e746967756f2022436c756220426f6164696c6c6122, NULL, 0),
('Depordog', 'Avd del Mueble s/n', '11130 Chiclana', 'Cádiz', '652 73 45 17', NULL, NULL, NULL, 'http://www.clubagilitydepordog.es/', 'ildegolo@hotmail.com', NULL, NULL, NULL, NULL, NULL, 0),
('Educan', NULL, NULL, 'Madrid', '617 469 312', NULL, NULL, NULL, 'http://www.madrid.educan.es/', 'agility.madrid@educan.es', NULL, NULL, NULL, NULL, NULL, 0),
('El Hechizo del Border C.', 'Ctra. Monserrat, Km. 7''5, nº 26', '46900 Torrent ', 'Valencia/Valéncia', '+ 34 96 156 56 75', NULL, NULL, NULL, 'http://www.elhechizo.com/', 'elhechizobc@gmail.com', NULL, NULL, NULL, NULL, NULL, 0),
('El Nogueral', 'Cami del Camp, 23', '03460 Beneixama', 'Alicante/Alacant', '+ 34 695 45 23 69', NULL, NULL, NULL, 'http://www.clubagility.es/', 'info@clubagility.es', NULL, NULL, NULL, NULL, NULL, 0),
('Eslon', 'Carretera de Carranque s/n', 'Serranillos del Valle', 'Madrid', '657 209 274', NULL, NULL, NULL, 'http://www.agilityeslon.com', 'info@agilityeslon.com', NULL, NULL, NULL, NULL, NULL, 0),
('Illa Blanca', 'Washington, 18, 2º', '07820 San Antonio de Portmany (Ibiza)', 'Balears, Illes', '+ 34 672 32 39 22', NULL, NULL, NULL, 'http://www.agilityillablanca.com/', 'info@agilityillablanca.com', NULL, NULL, NULL, NULL, NULL, 0),
('Junior', 'Calle de la Fuente, nº 8', '16162-Villar del Horno', 'Cuenca', '626389032', NULL, NULL, NULL, 'http://www.agilityjunior.es/', 'agilityjunior@gmail.com, info@agilityjunior.es', NULL, NULL, NULL, NULL, NULL, 0),
('Kai Argi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('L''Almozara', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('L''Horta Nord', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('La Daga', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('La Dama', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('La Manada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('La Princesa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('La Ribera', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('La Selva', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Lealcan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Maresme', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Marvi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Mi Perro 10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Neo Reus', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Palaciego', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Parbayon Cantabria', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Parque del Alamillo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Patas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Paterna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Pican', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Pura Vida', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Santa Quiteria', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Star Can', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Talavera', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Tercans', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Torrevieja', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Valles Club Cani', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Vallgorguina', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Vila-Real', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Vilcan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Villena', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('W.E.L.P.E.', 'Polideportivo Municipal La Canaleja', 'Alcorcón', 'Madrid', '+ 34 91 619 52 79', NULL, NULL, NULL, 'http://www.grupowelpe.com', 'gwelpe@teleline.es', 'https://www.facebook.com/groups/484854411592829/', NULL, '@gwelpe', NULL, NULL, 0),
('Xanastur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
('Zampican', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Equipos`
--
-- Creación: 30-09-2013 a las 06:07:33
--

DROP TABLE IF EXISTS `Equipos`;
CREATE TABLE IF NOT EXISTS `Equipos` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Jornada` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Jornada` (`Jornada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Equipos`:
--   `Jornada`
--       `Jornadas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Grados_Perro`
--
-- Creación: 30-09-2013 a las 06:07:32
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
-- Creación: 30-09-2013 a las 06:07:33
--

DROP TABLE IF EXISTS `Guias`;
CREATE TABLE IF NOT EXISTS `Guias` (
  `Nombre` varchar(255) NOT NULL,
  `Telefono` varchar(16) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Club` varchar(255) DEFAULT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Nombre`),
  UNIQUE KEY `Nombre` (`Nombre`),
  KEY `Guias_ibfk_1` (`Club`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `Guias`:
--   `Club`
--       `Clubes` -> `Nombre`
--

--
-- Volcado de datos para la tabla `Guias`
--

INSERT INTO `Guias` (`Nombre`, `Telefono`, `Email`, `Club`, `Observaciones`) VALUES
(' Sin asignar', '', '', ' Sin asignar', 'NO BORRAR. Valor por defecto cuando un perro se define por primera vez'),
('Aaron Laro', NULL, NULL, 'Cubas', NULL),
('Adoración Morales', NULL, NULL, 'La Dama', NULL),
('Adrian Díaz', NULL, NULL, 'El Hechizo del Border C.', NULL),
('Adrian Martínez', NULL, NULL, 'Eslon', NULL),
('Agustin Centelles', NULL, NULL, 'L''Almozara', NULL),
('Aida Al-Nehlawi', NULL, NULL, 'Canic', NULL),
('Albert Ulldemolins', NULL, NULL, 'Badalona', NULL),
('Alberto Alonso', NULL, NULL, 'Costa Blanca', NULL),
('Alberto Costas', NULL, NULL, 'Vallgorguina', NULL),
('Alberto Marugan', NULL, NULL, 'Agilcan', NULL),
('Alberto Mudarra', NULL, NULL, 'L''Almozara', NULL),
('Alberto Pereda', NULL, NULL, 'La Princesa', NULL),
('Alejandro Piñeiro', NULL, NULL, 'Tercans', NULL),
('Alejandro Rodríguez', NULL, NULL, 'Depordog', NULL),
('Alex del Río', NULL, NULL, 'Ciutat Comtal', NULL),
('Alex Olivera', NULL, NULL, 'Santa Quiteria', NULL),
('Alex Sabini', NULL, NULL, 'Ciutat Comtal', NULL),
('Alfredo Ortíz', NULL, NULL, 'Junior', NULL),
('Alicia Mejias', NULL, NULL, 'Parque del Alamillo', NULL),
('Alicia Sanjurjo', NULL, NULL, 'Valles Club Cani', NULL),
('Amparo Roig', NULL, NULL, 'L''Horta Nord', NULL),
('Ana Alonso', NULL, NULL, 'Star Can', NULL),
('Ana Baeza', NULL, NULL, 'Junior', NULL),
('Ana Beltran', NULL, NULL, 'El Nogueral', NULL),
('Ana Isabel Escobar', NULL, NULL, 'Deporcan', NULL),
('Ana Mateu', NULL, NULL, 'Canic', NULL),
('Ana Ontañon', NULL, NULL, 'Maresme', NULL),
('Andrea García', NULL, NULL, 'Vila-Real', NULL),
('Andrés Gimeno', NULL, NULL, 'La Dama', NULL),
('Andrés López', NULL, NULL, 'Tercans', NULL),
('Angel Insa', NULL, NULL, 'L''Almozara', NULL),
('Angel Puertolas', NULL, NULL, 'Kai Argi', NULL),
('Angeles Abad', NULL, NULL, 'L''Almozara', NULL),
('Angelica Castaño', NULL, NULL, 'Eslon', NULL),
('Ankie Kleijberg', NULL, NULL, 'Star Can', NULL),
('Anna Aguilella', NULL, NULL, 'Costa Blanca', NULL),
('Antje Lippold', NULL, NULL, 'Cornella', NULL),
('Antonio Carmona', NULL, NULL, 'Cornella', NULL),
('Antonio Fernández', NULL, NULL, 'Correcan', NULL),
('Antonio López', NULL, NULL, 'Junior', NULL),
('Antonio Molina', NULL, NULL, 'Cinco Huesos', NULL),
('Antonio Ojeda', NULL, NULL, 'Castellón', NULL),
('Arabia Vidal', NULL, NULL, 'Vilcan', NULL),
('Araceli Montero', NULL, NULL, 'Badalona', NULL),
('Aracelis Rodríguez', NULL, NULL, 'Ciutat Comtal', NULL),
('Arcadio Nohales', NULL, NULL, 'Zampican', NULL),
('Ariadna Soriano', NULL, NULL, 'L''Almozara', NULL),
('Arianna Bucci', NULL, NULL, 'La Selva', NULL),
('Arturo Conejera', NULL, NULL, 'Correcan', NULL),
('Astrid Hoffmeister', NULL, NULL, 'Lealcan', NULL),
('Barbara Flemming', NULL, NULL, 'Star Can', NULL),
('Berit Kittel', NULL, NULL, 'Parbayon Cantabria', NULL),
('Carles Fortuny', NULL, NULL, 'Ciutat Comtal', NULL),
('Carlos Alvarez', NULL, NULL, 'Tercans', NULL),
('Carlos Casado', NULL, NULL, 'Star Can', NULL),
('Carlos Iglesias', NULL, NULL, 'Vallgorguina', NULL),
('Carlos Martínez', NULL, NULL, 'Eslon', NULL),
('Carlos Serra', NULL, NULL, 'Illa Blanca', NULL),
('Carmen Alos', NULL, NULL, 'Canic', NULL),
('Carmen Antequera', NULL, NULL, 'L''Horta Nord', NULL),
('Carmen Briceño', NULL, NULL, 'L''Almozara', NULL),
('Carmen Sotomayor', NULL, NULL, 'Eslon', NULL),
('Carmen Vázquez', NULL, NULL, 'La Selva', NULL),
('Carolina Verdú', NULL, NULL, 'Villena', NULL),
('Celeste Zarzosa', NULL, NULL, 'La Princesa', NULL),
('Celso Valle', NULL, NULL, 'Maresme', NULL),
('Cesar Losada', NULL, NULL, 'Eslon', NULL),
('Concepción Fernández', NULL, NULL, 'La Princesa', NULL),
('Concepción López', NULL, NULL, 'La Ribera', NULL),
('Cristian Verde', NULL, NULL, 'Tercans', NULL),
('Cristina García', NULL, NULL, 'La Daga', NULL),
('Cristina González', NULL, NULL, 'Lealcan', NULL),
('Cristofol Albert', NULL, NULL, 'L''Horta Nord', NULL),
('Damian Alarcon', NULL, NULL, 'Educan', NULL),
('Daniel Amigo', NULL, NULL, 'Badalona', NULL),
('Daniel Luna', NULL, NULL, 'Junior', NULL),
('Daniel Menéndez', NULL, NULL, 'El Hechizo del Border C.', NULL),
('David Alique', NULL, NULL, 'Deporcan', NULL),
('David Ferrer', NULL, NULL, 'Badalona', NULL),
('David Flix', NULL, NULL, 'Santa Quiteria', NULL),
('David Gonzalbo', NULL, NULL, 'El Hechizo del Border C.', NULL),
('David Parejo', NULL, NULL, 'Ciudad de Antequera', NULL),
('David Sepulveda', NULL, NULL, 'Badalona', NULL),
('Debra Howard', NULL, NULL, 'La Princesa', NULL),
('Diana Cozar', NULL, NULL, 'Canic', NULL),
('Diana García', NULL, NULL, 'Parque del Alamillo', NULL),
('Diego Rouco', NULL, NULL, 'Patas', NULL),
('Dolores López', NULL, NULL, 'Villena', NULL),
('Dolores Sampedro', NULL, NULL, 'Zampican', NULL),
('Eduard Bonet', NULL, NULL, 'Badalona', NULL),
('Eduard Giralt', NULL, NULL, 'Maresme', NULL),
('Efren Lucas', NULL, NULL, 'Star Can', NULL),
('Elena Alberich', NULL, NULL, 'Canic', NULL),
('Elena Chinchilla', NULL, NULL, 'L''Horta Nord', NULL),
('Elena Sin', NULL, NULL, 'L''Almozara', NULL),
('Elisenda Huidobro', NULL, NULL, 'Canic', NULL),
('Emilio Calvo', NULL, NULL, 'El Hechizo del Border C.', NULL),
('Emilio José Pedrazuela', NULL, NULL, 'Educan', NULL),
('Enric García', NULL, NULL, 'Zampican', NULL),
('Enric Lleixa', NULL, NULL, 'Badalona', NULL),
('Enrique Alonso', NULL, NULL, 'Educan', NULL),
('Enrique Grau', NULL, NULL, 'Costa Blanca', NULL),
('Enrique Herbera', NULL, NULL, 'Badalona', NULL),
('Enrique Lleixa', NULL, NULL, 'Badalona', NULL),
('Enrique Sendra', NULL, NULL, 'Tercans', NULL),
('Ernesto Sorribes', NULL, NULL, 'Zampican', NULL),
('Estefanía Pérez', NULL, NULL, 'Educan', NULL),
('Eugenio Villares', NULL, NULL, 'Cubas', NULL),
('Eva Grau', NULL, NULL, 'Paterna', NULL),
('Eva Vázquez', NULL, NULL, 'L''Almozara', NULL),
('Fermin Gil', NULL, NULL, 'L''Horta Nord', NULL),
('Fernando Benet', NULL, NULL, 'Zampican', NULL),
('Francisco Aguilera', NULL, NULL, 'Ciudad de Antequera', NULL),
('Francisco de la Cruz', NULL, NULL, 'Cinco Huesos', NULL),
('Francisco Esteban', NULL, NULL, 'Costa Blanca', NULL),
('Francisco Javier Jaen', NULL, NULL, 'Deporcan', NULL),
('Francisco Javier Luque', NULL, NULL, 'Parque del Alamillo', NULL),
('Francisco José Sousa', NULL, NULL, 'Tercans', NULL),
('Francisco Maestre', NULL, NULL, 'L''Horta Nord', NULL),
('Francisco Martín', NULL, NULL, 'Valles Club Cani', NULL),
('Francisco Medina', NULL, NULL, 'Canic', NULL),
('Francisco Pérez', NULL, NULL, 'Marvi', NULL),
('Francisco Sobral', NULL, NULL, 'Patas', NULL),
('Gabriel Martín', NULL, NULL, 'Parbayon Cantabria', NULL),
('Gerard Barberá', NULL, NULL, 'Neo Reus', NULL),
('Gerardo Alvarez', NULL, NULL, 'Junior', NULL),
('Gerardo González', NULL, NULL, 'Eslon', NULL),
('Gisela Solis', NULL, NULL, 'La Daga', NULL),
('Gregorio Conde', NULL, NULL, 'Badalona', NULL),
('Iago Sánchez', NULL, NULL, 'Tercans', NULL),
('Iban Cubedo', NULL, NULL, 'Costa Blanca', NULL),
('Imanol López', NULL, NULL, 'L''Almozara', NULL),
('Inmaculada Rubio', NULL, NULL, 'Eslon', NULL),
('Irene Artacho', NULL, NULL, 'Cinco Huesos', NULL),
('Isabel Gómez', NULL, NULL, 'Depordog', NULL),
('Isabel Rodríguez', NULL, NULL, 'Valles Club Cani', NULL),
('Isidoro Vázquez', NULL, NULL, 'Cubas', NULL),
('Ismael Pérez', NULL, NULL, 'Canic', NULL),
('Israel Fernández', NULL, NULL, 'Vallgorguina', NULL),
('Iván Pardo', NULL, NULL, 'Lealcan', NULL),
('Iván San Antonio', NULL, NULL, 'Cinco Huesos', NULL),
('Iván Sánchez', NULL, NULL, 'La Princesa', NULL),
('Jacqueline Holemans', NULL, NULL, 'Santa Quiteria', NULL),
('Jaime Gamir', NULL, NULL, 'Baix Llobregat', NULL),
('Jaume Fernández', NULL, NULL, 'Ciutat Comtal', NULL),
('Javier Gómez', NULL, NULL, 'Pican', NULL),
('Javier Iniesta', NULL, NULL, 'Lealcan', NULL),
('Javier López', NULL, NULL, 'Santa Quiteria', NULL),
('Javier Martínez', NULL, NULL, 'Eslon', NULL),
('Javier Mora', NULL, NULL, 'Correcan', NULL),
('Javier Ovejero', NULL, NULL, 'Eslon', NULL),
('Javier Sanchis', NULL, NULL, 'Maresme', NULL),
('Jennifer Tolín', NULL, NULL, 'La Princesa', NULL),
('Jenny Funcke', NULL, NULL, 'Badalona', NULL),
('Jerónimo Martínez', NULL, NULL, 'Star Can', NULL),
('Jesús Crespo', NULL, NULL, 'Mi Perro 10', NULL),
('Jesús Cuellar', NULL, NULL, 'Eslon', NULL),
('Jesús Manuel Romero', NULL, NULL, 'Palaciego', NULL),
('Joan Wenceslao Pastor', NULL, NULL, 'Valles Club Cani', NULL),
('Joaquín Andres', NULL, NULL, 'Agilcan', NULL),
('Jonathan Guillen', NULL, NULL, 'Vilcan', NULL),
('Jordi Boix', NULL, NULL, 'Canic', NULL),
('Jordi Gómez', NULL, NULL, 'Canic', NULL),
('Jorge Arcas', NULL, NULL, 'L''Almozara', NULL),
('Jorge Muñoz', NULL, NULL, 'Cubas', NULL),
('Jorge Valero', NULL, NULL, 'L''Almozara', NULL),
('José Angel Torres', NULL, NULL, 'Tercans', NULL),
('José Antonio Encinas', NULL, NULL, 'Correcan', NULL),
('José Antonio Pascual', NULL, NULL, 'L''Almozara', NULL),
('José Antonio Vega', NULL, NULL, 'Agilcan', NULL),
('José Carlos Iglesias', NULL, NULL, 'Vallgorguina', NULL),
('José Castaño', NULL, NULL, 'Valles Club Cani', NULL),
('José Francisco Martorell', NULL, NULL, 'Pican', NULL),
('José Guix', NULL, NULL, 'Pican', NULL),
('José Luis García', NULL, NULL, 'Zampican', NULL),
('José Luis Prieto', NULL, NULL, 'Illa Blanca', NULL),
('José Luis Quiroga', NULL, NULL, 'Cornella', NULL),
('José Luis Romero', NULL, NULL, 'Cubas', NULL),
('José Luis Sogorb', NULL, NULL, 'La Dama', NULL),
('José Mahillo', NULL, NULL, 'La Princesa', NULL),
('José Manuel Basco', NULL, NULL, 'Palaciego', NULL),
('José Manuel Linares', NULL, NULL, 'Torrevieja', NULL),
('José Martí', NULL, NULL, 'L''Horta Nord', NULL),
('José Mateo Moreno', NULL, NULL, 'Cornella', NULL),
('José Miguel Agustín', NULL, NULL, 'Agilcan', NULL),
('José Miguel Morant', NULL, NULL, 'La Princesa', NULL),
('José Miguel Paredes', NULL, NULL, 'Torrevieja', NULL),
('José Moreno', NULL, NULL, 'Cuatro Patas', NULL),
('José Pavon', NULL, NULL, 'Depordog', NULL),
('José Peris', NULL, NULL, 'Paterna', NULL),
('José Santos Luna', NULL, NULL, 'Junior', NULL),
('José Soliño', NULL, NULL, 'Tercans', NULL),
('Josep Barbera', NULL, NULL, 'Neo Reus', NULL),
('Josep Mª Pineda', NULL, NULL, 'Valles Club Cani', NULL),
('Juan Antonio Martinez', NULL, 'juansgaviota@gmail.com', 'W.E.L.P.E.', NULL),
('Juan Campin', NULL, NULL, 'La Princesa', NULL),
('Juan Carlos Blas', NULL, NULL, 'Eslon', NULL),
('Juan Carlos Companys', NULL, NULL, 'Costa Blanca', NULL),
('Juan Carlos Hinojal', NULL, NULL, 'Parbayon Cantabria', NULL),
('Juan Carlos Redondo', NULL, NULL, 'Talavera', NULL),
('Juan del Amo', NULL, NULL, 'Junior', NULL),
('Juan Francisco Pelegrin', NULL, NULL, 'La Ribera', NULL),
('Juan Francisco Torres', NULL, NULL, 'Badalona', NULL),
('Juan José Espadas', NULL, NULL, 'Canic', NULL),
('Juan Luis Colmano', NULL, NULL, 'L''Almozara', NULL),
('Juan Manuel Caballo', NULL, NULL, 'L''Horta Nord', NULL),
('Juan Miguel Cifuentes', NULL, NULL, 'L''Horta Nord', NULL),
('Juan Pablo Díaz', NULL, NULL, 'Cubas', NULL),
('Juan Pedro Martínez', NULL, NULL, 'Castellón', NULL),
('Juan Solanes', NULL, NULL, 'Vilcan', NULL),
('Judith Cortes', NULL, NULL, 'L''Almozara', NULL),
('Judith Herms', NULL, NULL, 'Ciutat Comtal', NULL),
('Julia Faci', NULL, NULL, 'L''Almozara', NULL),
('Julia Morugova', NULL, NULL, 'El Nogueral', NULL),
('Julian Sánchez', NULL, NULL, 'Valles Club Cani', NULL),
('Katia Moeller', NULL, NULL, 'Canino Algecireño', NULL),
('Katy Navarro', NULL, NULL, 'Parbayon Cantabria', NULL),
('Laura Carrasco', NULL, NULL, 'Ciutat Comtal', NULL),
('Laura Chiva', NULL, NULL, 'Badalona', NULL),
('Lorena Díez', NULL, NULL, 'La Princesa', NULL),
('Lorena García', NULL, NULL, 'Parbayon Cantabria', NULL),
('Lorena Gargoles', NULL, NULL, 'Canic', NULL),
('Lourdes Giménez', NULL, NULL, 'El Hechizo del Border C.', NULL),
('Lourdes Peñarrocha', NULL, NULL, 'Star Can', NULL),
('Luis Alberto Pereira', NULL, NULL, 'Parbayon Cantabria', NULL),
('Luis Carlos Sanchez', NULL, NULL, 'Agilcan', NULL),
('Luis Ignacio Carazo', NULL, NULL, 'Valles Club Cani', NULL),
('Luis Luque', NULL, NULL, 'Vallgorguina', NULL),
('Luis Miguel Rodrigo', NULL, NULL, 'Lealcan', NULL),
('Luisa Fernanda Millan', NULL, NULL, 'Eslon', NULL),
('Luna Ramírez', NULL, NULL, 'Maresme', NULL),
('Manel Martínez', NULL, NULL, 'Valles Club Cani', NULL),
('Manuel Basco', NULL, NULL, 'Palaciego', NULL),
('Manuel Jesús García', NULL, NULL, 'Campo de Gibraltar', NULL),
('Manuel Lara', NULL, NULL, 'Ciutat Comtal', NULL),
('Manuel Santomé', NULL, NULL, 'Tercans', NULL),
('Mar Bermúdez', NULL, NULL, 'Camu', NULL),
('Marc Rabada', NULL, NULL, 'La Daga', NULL),
('Marcos Martínez', NULL, NULL, 'Pican', NULL),
('María López', NULL, NULL, 'Valles Club Cani', NULL),
('Marina López', NULL, NULL, 'Ciutat Comtal', NULL),
('Mario Rodríguez', NULL, NULL, 'W.E.L.P.E.', NULL),
('Marisa Jarabo', NULL, NULL, 'W.E.L.P.E.', NULL),
('Marta de la Rosa', NULL, NULL, 'Zampican', NULL),
('Marta Gregorio', NULL, NULL, 'Tercans', NULL),
('Marta Sánchez', NULL, NULL, 'Cubas', NULL),
('Massimiliano Miggiano', NULL, NULL, 'Vallgorguina', NULL),
('Matias Monleón', NULL, NULL, 'L''Horta Nord', NULL),
('Matias Rodríguez', NULL, NULL, 'Vallgorguina', NULL),
('Mercedes Fernández', NULL, NULL, 'Star Can', NULL),
('Michael Volkert', NULL, NULL, 'La Selva', NULL),
('Miguel Angel Fernández', NULL, NULL, 'Xanastur', NULL),
('Miguel Angel García', NULL, NULL, 'Eslon', NULL),
('Miguel Angel Morales', NULL, NULL, 'Agilcan', NULL),
('Miguel Angel Soriano', NULL, NULL, 'Star Can', NULL),
('Miguel García', NULL, NULL, 'Tercans', NULL),
('Mireia Carrascoso', NULL, NULL, 'La Daga', NULL),
('Miriam García', NULL, NULL, 'Neo Reus', NULL),
('Mónica Muñiz', NULL, NULL, 'Educan', NULL),
('Mónica Rodríguez', NULL, NULL, 'Pura Vida', NULL),
('Mónica Saavedra', NULL, NULL, 'Mi Perro 10', NULL),
('Montserrat Calvet', NULL, NULL, 'Badalona', NULL),
('Mª José Manzano', NULL, NULL, 'L''Almozara', NULL),
('Narciso Leita', NULL, NULL, 'La Ribera', NULL),
('Natalia Cuadrado', NULL, NULL, 'Cornella', NULL),
('Natividad Soler', NULL, NULL, 'Star Can', NULL),
('Neus Baró', NULL, NULL, 'Canic', NULL),
('Noelia Gimeno', NULL, NULL, 'Paterna', NULL),
('Nuria Alonso', NULL, NULL, 'Ciutat Comtal', NULL),
('Nuria Costa', NULL, NULL, 'Valles Club Cani', NULL),
('Nuria Fortuny', NULL, NULL, 'Ciutat Comtal', NULL),
('Nuria Morell', NULL, NULL, 'Valles Club Cani', NULL),
('Oria Micó', NULL, NULL, 'Villena', NULL),
('Oscar Bravo', NULL, NULL, 'Cornella', NULL),
('Oscar Muñiz', NULL, NULL, 'Educan', NULL),
('Oscar Reboredo', NULL, NULL, 'Ciutat Comtal', NULL),
('Oscar Sacristan', NULL, NULL, 'La Princesa', NULL),
('Pablo Ballesta', NULL, NULL, 'Camu', NULL),
('Pablo Miró', NULL, NULL, 'Zampican', NULL),
('Paloma Faci', NULL, NULL, 'L''Almozara', NULL),
('Pau Serrano', NULL, NULL, 'Illa Blanca', NULL),
('Paula de Lucas', NULL, NULL, 'Cubas', NULL),
('Paula Rello', NULL, NULL, 'Cubas', NULL),
('Paulino Iranzo', NULL, NULL, 'Pican', NULL),
('Pedro Delgado', NULL, NULL, 'Mi Perro 10', NULL),
('Pedro Jesús Tazón', NULL, NULL, 'Parbayon Cantabria', NULL),
('pepe', '', '', ' Sin Asignar', ''),
('Pilar Collado', NULL, NULL, 'Parbayon Cantabria', NULL),
('Pilar Matesanz', NULL, NULL, 'W.E.L.P.E.', NULL),
('Pilar Rodríguez', NULL, NULL, 'Parbayon Cantabria', NULL),
('Rachel Stevens', NULL, NULL, 'Neo Reus', NULL),
('Rafael Altava', NULL, NULL, 'Costa Blanca', NULL),
('Rafael Camacho', NULL, NULL, 'Vallgorguina', NULL),
('Rafael Fernández', NULL, NULL, 'Pican', NULL),
('Rafael García', NULL, NULL, 'Vila-Real', NULL),
('Rafael Torregrosa', NULL, NULL, 'Costa Blanca', NULL),
('Ramón Arribas', NULL, NULL, 'Pura Vida', NULL),
('Raquel Frago', NULL, NULL, 'La Ribera', NULL),
('Raquel Garrido', NULL, NULL, 'Cubas', NULL),
('Raúl Sánchez', NULL, NULL, 'Cubas', NULL),
('Remedios Torres', NULL, NULL, 'Star Can', NULL),
('Reyes García', NULL, NULL, 'El Hechizo del Border C.', NULL),
('Ricardo Benito', NULL, NULL, 'W.E.L.P.E.', NULL),
('Ricardo Martínez', NULL, NULL, 'Depordog', NULL),
('Ricardo Santolaya', NULL, NULL, 'L''Almozara', NULL),
('Roberto Castro', NULL, NULL, 'Eslon', NULL),
('Roberto Iñigo', NULL, NULL, 'Cinco Huesos', NULL),
('Roberto Reina', NULL, NULL, 'La Princesa', NULL),
('Rocio Hermelo', NULL, NULL, 'Tercans', NULL),
('Rocio Santos', NULL, NULL, 'Parque del Alamillo', NULL),
('Ruben Jurado', NULL, NULL, 'La Daga', NULL),
('Ruben Lopera', NULL, NULL, 'Marvi', NULL),
('Ruben Montero', NULL, NULL, 'La Princesa', NULL),
('Sabina González', NULL, NULL, 'La Selva', NULL),
('Salvador Martí', NULL, NULL, 'Santa Quiteria', NULL),
('Sara Bellido', NULL, NULL, 'Canino Algecireño', NULL),
('Sara Montila', NULL, NULL, 'Deporcan', NULL),
('Sara Montoya', NULL, NULL, 'Eslon', NULL),
('Sebastian González', NULL, NULL, 'Badalona', NULL),
('Sergio Casalins', NULL, NULL, 'Educan', NULL),
('Sergio Colomé', NULL, NULL, 'L''Almozara', NULL),
('Sergio García', NULL, NULL, 'Vila-Real', NULL),
('Sergio Martín', NULL, NULL, 'L''Almozara', NULL),
('Sergio Ruiz', NULL, NULL, 'Correcan', NULL),
('Silvia León', NULL, NULL, 'Zampican', NULL),
('Silvia Perea', NULL, NULL, 'W.E.L.P.E.', NULL),
('Silvia Rodríguez', NULL, NULL, 'Valles Club Cani', NULL),
('Sonia Asensio', NULL, NULL, 'Camu', NULL),
('Sonia Conejero', NULL, NULL, 'Villena', NULL),
('Stefan Eggenschwiler', NULL, NULL, 'Torrevieja', NULL),
('Stina Sandquist', NULL, NULL, 'Ciudad de Antequera', NULL),
('Tamara Vidal', NULL, NULL, 'La Daga', NULL),
('Tomás Pérez', NULL, NULL, 'L''Almozara', NULL),
('Toni Rios', NULL, NULL, 'Canic', NULL),
('Ubaldo Delgado', NULL, NULL, 'Camu', NULL),
('Valentín de la Mesa', NULL, NULL, 'Costa Blanca', NULL),
('Vanessa Calpe', NULL, NULL, 'Vila-Real', NULL),
('Vanessa Hermoso', NULL, NULL, 'Campo de Gibraltar', NULL),
('Verónica Díez', NULL, NULL, 'Parbayon Cantabria', NULL),
('Verónica Ibañez', NULL, NULL, 'Ciudad de Antequera', NULL),
('Verónica Rodríguez', NULL, NULL, 'La Princesa', NULL),
('Vicente Cambra', NULL, NULL, 'La Manada', NULL),
('Vicente Micó', NULL, NULL, 'Villena', NULL),
('Vicente Villalba', NULL, NULL, 'L''Horta Nord', NULL),
('Victor García', NULL, NULL, 'Marvi', NULL),
('Xavier López', NULL, NULL, 'Santa Quiteria', NULL),
('Yolanda Larena', NULL, NULL, 'Neo Reus', NULL),
('Yolanda Moreno', NULL, NULL, 'L''Horta Nord', NULL),
('Yolanda Torres', NULL, NULL, 'Maresme', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inscripciones`
--
-- Creación: 30-09-2013 a las 06:07:34
--

DROP TABLE IF EXISTS `Inscripciones`;
CREATE TABLE IF NOT EXISTS `Inscripciones` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Jornada` int(4) NOT NULL,
  `Dorsal` int(4) NOT NULL,
  `Celo` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` varchar(255) DEFAULT NULL,
  `Equipo` int(4) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Dorsal` (`Dorsal`),
  KEY `Jornada` (`Jornada`),
  KEY `Equipo` (`Equipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Inscripciones`:
--   `Dorsal`
--       `Perros` -> `Dorsal`
--   `Equipo`
--       `Equipos` -> `ID`
--   `Jornada`
--       `Jornadas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Jornadas`
--
-- Creación: 30-09-2013 a las 12:45:56
--

DROP TABLE IF EXISTS `Jornadas`;
CREATE TABLE IF NOT EXISTS `Jornadas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` varchar(255) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  `A1GI` tinyint(1) NOT NULL DEFAULT '1',
  `A2GI` tinyint(1) NOT NULL DEFAULT '1',
  `AGII` tinyint(1) NOT NULL DEFAULT '1',
  `JGII` tinyint(1) NOT NULL DEFAULT '1',
  `AGIII` tinyint(1) NOT NULL DEFAULT '1',
  `JGIII` tinyint(1) NOT NULL DEFAULT '1',
  `AEq` tinyint(1) NOT NULL DEFAULT '0',
  `JEq` tinyint(1) NOT NULL DEFAULT '0',
  `PreA` tinyint(1) NOT NULL DEFAULT '0',
  `K.O.` tinyint(1) NOT NULL DEFAULT '0',
  `Show` tinyint(1) NOT NULL DEFAULT '0',
  `Otras` tinyint(1) NOT NULL DEFAULT '0',
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Prueba` (`Prueba`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Jornadas`:
--   `Prueba`
--       `Pruebas` -> `Nombre`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Jueces`
--
-- Creación: 30-09-2013 a las 06:07:32
--

DROP TABLE IF EXISTS `Jueces`;
CREATE TABLE IF NOT EXISTS `Jueces` (
  `Nombre` varchar(255) NOT NULL,
  `Direccion1` varchar(255) DEFAULT NULL,
  `Direccion2` varchar(255) DEFAULT NULL,
  `Telefono` varchar(32) DEFAULT NULL,
  `Internacional` tinyint(1) NOT NULL DEFAULT '0',
  `Practicas` tinyint(1) NOT NULL DEFAULT '0',
  `Email` varchar(255) DEFAULT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Nombre`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Jueces`
--

INSERT INTO `Jueces` (`Nombre`, `Direccion1`, `Direccion2`, `Telefono`, `Internacional`, `Practicas`, `Email`, `Observaciones`) VALUES
('Beltrán Bustamante, Ana', 'Camí del Camp, 23', '03460 Beneixama (Alicante)', '639 67 86 09', 0, 1, 'sadda_874@hotmail.com', NULL),
('Boix Balaguer, Josep', 'Sant Pere, 10', '08392 San Andreu de Llavaneres (Barcelona)', ' 93 792 76 55', 1, 0, 'josep@agilitycanic.cat', NULL),
('Conde Delgado, Gregorio', NULL, NULL, ' 93 389 35 83 / 619 39 39 28', 1, 0, 'gconde@xtec.cat', NULL),
('Correa Arqueros, Mariano', 'Avda. de Moratalaz, 178, 2º A', '28030 Madrid', ' 91 301 47 58', 1, 0, 'mariano.correa@nsn.com', NULL),
('Diez Pérez, Esteban', 'Ocaña, 104, Bajo', '28047 Madrid', '91 465 50 05', 1, 0, 'estebanagility@gmail.com', NULL),
('Escalera Salamanca, Javier de la', 'Avda. de San Luis, 95, 2º D', '28033 Madrid', '91 767 07 45 / 607 43 34 13', 1, 0, 'javierdelae@gmail.com', NULL),
('Felix Fuentes, José', 'Vinaroz, 19', '18100 Armilla (Granada)', '958 55 05 74 / 617 93 54 32', 0, 0, NULL, NULL),
('García Alvarez, José Luis', 'Río Navia, 2', '12006 Castellón', '629 07 06 75', 1, 0, 'zampican@yahoo.es', NULL),
('García Rudilla, Juan', 'Rulda, 12', '03400 Villena (Alicante)', ' 96 580 30 63 / 652 83 08 85', 1, 0, NULL, NULL),
('Garrido Fuentes, Manuel', 'Paseo de los Olivos, 10', '28330 San Martín de la Vega (Madrid)', ' 91 894 60 96', 1, 0, 'mgfcom@telefonica.net', NULL),
('Gil Solis, Esperanza', 'Paseo de los Olivos, 10', '28330 San Martín de la Vega (Madrid)', ' 91 894 60 96', 1, 0, 'agilcan@telefonica.net', NULL),
('Guerra Guerra, Juan Pedro', 'Mariano Martín, 9', '10161 Arroyomolinos (Cáceres)', ' 927 28 82 56', 1, 0, 'jaracar@ono.com', NULL),
('Humanes Almonte, Miguel Angel', 'Zamora, 23, 3º C', '28941 Fuenlabrada (Madrid)', '607 70 55 75', 1, 0, 'agilblack@hotmail.com', NULL),
('Lanzó, Juan Antonio', 'Recinto Ferial, 2, 4º A', '36540 Silleda (Pontevedra)', ' 629 50 71 76 / 986 57 70 00', 1, 0, 'tonylanzo@gmail.com', NULL),
('Linares García, José Manuel', 'Carril de los Córdobas, 2', '30161 Llano de Brujas (Murcia)', '968 85 12 60 / 696 75 07 67', 0, 1, 'jomaliga1@gmail.com', NULL),
('Muñiz Martínez, Oscar', 'León Felipe, 31, 4º, 1ª', '28942 Fuenlabrada (Madrid)', '665 79 86 49', 0, 1, 'agilityzeus@gmail.com', NULL),
('Navarro Costas, Jordi', 'Santiago Rusiñol, 90', '08340 Vilassar de Mar (Barcelona)', '93 759 70 54 / 609 30 97 10', 1, 0, 'jordinuc@telefonica.net', NULL),
('Parejo Carregalo, Manuel', 'Ctra. Valle de Abdalajis, Km. 1,7', '29260 La Joya (Málaga)', '95 270 26 04', 0, 1, 'losparejos@hotmail.com', NULL),
('Pineda Puig, Josep Mª', 'Sant Ramón, 69, B-3', '08140 Caldes de Montbui (Barcelona)', '93 865 46 16 / 678 43 36 45', 0, 0, 'pepagility@movistar.es', NULL),
('Poble Rosas, Ramón', 'Jaume I, 18', '08140 Caldes de Montbui (Barcelona)', '93 865 20 32', 1, 0, 'pobleramon@gmail.com', NULL),
('Rodríguez Matesanz, Mario', 'Plaza del Peñón, 10', '28923 Alcorcón (Madrid)', ' 91 619 52 79', 1, 0, 'gwelpe@terra.es', NULL),
('Santome González, Manuel', 'Fonte da Tella, 133 - A - Moureira - Meira', '36955 Moaña (Pontevedra)', '986 31 27 77 / 607 83 20 53', 0, 1, 'lolosantome@gmail.com', NULL),
('Ulldemolins Santisteve, Albert', 'Llorer, 28, Casa 4', '08415 Bigues I Riells (Barcelona)', ' 93 865 89 64 / 636 96 33 77', 0, 1, 'albert23m@hotmail.com', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Mangas`
--
-- Creación: 30-09-2013 a las 06:07:34
--

DROP TABLE IF EXISTS `Mangas`;
CREATE TABLE IF NOT EXISTS `Mangas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Jornada` int(4) NOT NULL,
  `Tipo` varchar(16) NOT NULL,
  `Distancia` int(4) NOT NULL,
  `Obstaculos` int(4) NOT NULL,
  `Velocidad` double DEFAULT NULL,
  `TRS` int(4) DEFAULT NULL,
  `TRM` int(11) DEFAULT NULL,
  `Juez Titular` varchar(255) NOT NULL,
  `Juez Practicas` varchar(255) DEFAULT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Tipo` (`Tipo`),
  KEY `Juez Titular` (`Juez Titular`),
  KEY `Juez Practicas` (`Juez Practicas`),
  KEY `Jornada` (`Jornada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Mangas`:
--   `Jornada`
--       `Jornadas` -> `ID`
--   `Juez Practicas`
--       `Jueces` -> `Nombre`
--   `Juez Titular`
--       `Jueces` -> `Nombre`
--   `Tipo`
--       `Tipo_Manga` -> `Tipo`
--

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `PerroGuiaClub`
--
DROP VIEW IF EXISTS `PerroGuiaClub`;
CREATE TABLE IF NOT EXISTS `PerroGuiaClub` (
`Dorsal` int(4)
,`Nombre` varchar(255)
,`Categoria` varchar(1)
,`Grado` varchar(16)
,`Guia` varchar(255)
,`Club` varchar(255)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Perros`
--
-- Creación: 30-09-2013 a las 06:07:34
--

DROP TABLE IF EXISTS `Perros`;
CREATE TABLE IF NOT EXISTS `Perros` (
  `Dorsal` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Raza` varchar(255) DEFAULT NULL,
  `LOE_RRC` varchar(255) DEFAULT NULL,
  `Licencia` varchar(255) DEFAULT NULL,
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Guia` varchar(255) DEFAULT 'Sin guia',
  `Grado` varchar(16) DEFAULT '-',
  PRIMARY KEY (`Dorsal`),
  KEY `Perros_ibfk_3` (`Guia`),
  KEY `Perros_ibfk_1` (`Categoria`),
  KEY `Perros_ibfk_2` (`Grado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=568 ;

--
-- RELACIONES PARA LA TABLA `Perros`:
--   `Categoria`
--       `Categorias_Perro` -> `Categoria`
--   `Grado`
--       `Grados_Perro` -> `Grado`
--   `Guia`
--       `Guias` -> `Nombre`
--

--
-- Volcado de datos para la tabla `Perros`
--

INSERT INTO `Perros` (`Dorsal`, `Nombre`, `Raza`, `LOE_RRC`, `Licencia`, `Categoria`, `Guia`, `Grado`) VALUES
(10, 'Yuma', 'P.B.Malinoise', '1936256', 'A330', 'L', 'Juan Miguel Cifuentes', 'GIII'),
(11, 'Hannibal', NULL, '1764520', 'A090', 'L', 'Tomás Pérez', '-'),
(12, 'Ardi', '', '79097', '729', 'L', 'Paloma Faci', '-'),
(13, 'William', NULL, '1667920', '920', 'L', 'Jenny Funcke', '-'),
(14, 'Xonny', NULL, '1317156', '622', 'L', 'Cesar Losada', '-'),
(15, 'Indiana Jones', NULL, '1720531', '987', 'L', 'Juan Francisco Pelegrin', '-'),
(16, 'Thelma', NULL, '1515702', '824', 'L', 'Ricardo Santolaya', '-'),
(17, 'Boss', NULL, '1528991', '797', 'L', 'Oscar Muñiz', '-'),
(18, 'Lee', NULL, '95245', 'A084', 'L', 'Antonio Molina', '-'),
(19, 'Chinouk', NULL, '1390419', '724', 'L', 'Michael Volkert', '-'),
(20, 'Angie', NULL, '1370168', '691', 'L', 'Antonio Molina', '-'),
(21, 'Burundi', NULL, '1874262', 'A310', 'L', 'Elena Chinchilla', '-'),
(22, 'Piter', NULL, '110594', 'A360', 'L', 'Cristofol Albert', '-'),
(23, 'Napa', NULL, '1975832', 'A401', 'L', 'Paloma Faci', '-'),
(24, 'Gon', NULL, '1725855', 'A024', 'L', 'Manuel Santomé', '-'),
(25, 'Valerie', NULL, '1467667', '786', 'L', 'Manel Martínez', '-'),
(26, 'Woman', NULL, '1866186', 'A206', 'L', 'Javier Mora', '-'),
(27, 'Baloo', NULL, '86974', '991', 'L', 'Pilar Collado', '-'),
(28, 'Piter Winers', NULL, '100338', 'A188', 'L', 'José Guix', '-'),
(29, 'Lula', NULL, '1891977', 'A344', 'L', 'Angel Puertolas', '-'),
(30, 'Karen', NULL, '1970258', 'A427', 'L', 'Amparo Roig', '-'),
(31, 'Runa', NULL, '112361', 'A347', 'L', 'Alex Olivera', '-'),
(33, 'Chiruca', NULL, '1635759', '986', 'L', 'Antonio Fernández', '-'),
(35, 'Moss', NULL, '113891', 'A391', 'L', 'Roberto Iñigo', '-'),
(36, 'Nena', NULL, '1521753', '930', 'L', 'Julia Faci', '-'),
(38, 'Deby', NULL, '101610', 'A147', 'L', 'Cesar Losada', '-'),
(39, 'Noah', NULL, '1887262', 'A268', 'L', 'Vicente Villalba', '-'),
(40, 'Sil', NULL, '1831356', 'A150', 'L', 'Manuel Santomé', '-'),
(41, 'Furia', NULL, '1554907', '892', 'L', 'Nuria Fortuny', '-'),
(42, 'Mc Coy', NULL, '1905162', 'A322', 'L', 'Carmen Sotomayor', '-'),
(43, 'Argi', NULL, '110120', 'A241', 'L', 'Manuel Basco', '-'),
(44, 'Lua', NULL, '118441', 'A327', 'L', 'José Manuel Linares', '-'),
(45, 'Zoe', NULL, '109748', 'A289', 'L', 'Marta Sánchez', '-'),
(47, 'Juice', NULL, '117997', 'A387', 'L', 'Berit Kittel', '-'),
(48, 'Vega', NULL, '1552296', '855', 'L', 'Amparo Roig', '-'),
(49, 'Idris', NULL, '83909', '880', 'L', 'Narciso Leita', '-'),
(51, 'Izar', NULL, '1596718', '851', 'L', 'Francisco Martín', '-'),
(52, 'Pica', NULL, '104103', 'A091', 'L', 'Alberto Costas', '-'),
(53, 'Nana', NULL, '1780849', 'A073', 'L', 'José Luis García', '-'),
(54, 'Tara', NULL, 'No tiene', '1466', 'L', 'Oscar Reboredo', '-'),
(56, 'Finn', NULL, '2074557', 'A596', 'L', 'Rachel Stevens', '-'),
(57, 'Rocky', NULL, '1796496', 'A355', 'L', 'Arturo Conejera', '-'),
(58, 'Neil', NULL, '122117', 'A417', 'L', 'Isidoro Vázquez', '-'),
(59, 'Asia', NULL, '1958017', 'A364', 'L', 'José Luis Romero', '-'),
(60, 'Xodro', NULL, '1959124', 'A371', 'L', 'Carlos Martínez', '-'),
(61, 'Bec', NULL, '87712', '979', 'L', 'José Martí', '-'),
(62, 'Bely', NULL, '1370171', '692', 'L', 'Francisco Pérez', '-'),
(63, 'Laia', NULL, '1594320', '921', 'L', 'Israel Fernández', '-'),
(64, 'Bamba', NULL, '1887258', 'A242', 'L', 'Andrea García', '-'),
(65, 'Akane', NULL, '101429', 'A249', 'L', 'Gerardo González', '-'),
(66, 'Mister', NULL, '99971', 'A250', 'L', 'Angelica Castaño', '-'),
(67, 'Spyro', NULL, '89457', 'A155', 'L', 'Angel Puertolas', '-'),
(68, 'Becho', NULL, '1831350', 'A203', 'L', 'Alejandro Piñeiro', '-'),
(69, 'Luna', NULL, '1798021', 'A163', 'L', 'Vicente Micó', '-'),
(70, 'Buh', NULL, '1929147', 'A297', 'L', 'Pilar Collado', '-'),
(71, 'Aby', NULL, '105806', 'A204', 'L', 'Roberto Reina', '-'),
(72, 'Yuma', NULL, '113067', 'A385', 'L', 'Luis Alberto Pereira', '-'),
(73, 'Zak', NULL, '1831579', 'A160', 'L', 'Pablo Miró', '-'),
(75, 'Tanga', NULL, '119678', 'A366', 'L', 'Sara Montoya', '-'),
(76, 'Viconte', NULL, '1561797', '813', 'L', 'Verónica Rodríguez', '-'),
(77, 'Dela', NULL, '2028765', 'A377', 'L', 'Luis Luque', '-'),
(78, 'Brus', NULL, '1763613', 'A140', 'L', 'David Parejo', '-'),
(80, 'King', NULL, '1520008', '856', 'L', 'Mónica Muñiz', '-'),
(81, 'Brujostel', NULL, '1717447', 'A231', 'L', 'Julia Morugova', '-'),
(83, 'Kora', NULL, '111627', 'A332', 'L', 'Pedro Delgado', '-'),
(84, 'Rubia', NULL, '127474', 'A481', 'L', 'Luis Miguel Rodrigo', '-'),
(85, 'Fito', NULL, '127473', 'A529', 'L', 'Luis Miguel Rodrigo', '-'),
(86, 'Visente', NULL, '116863', 'A359', 'L', 'Paulino Iranzo', '-'),
(87, 'Maia', NULL, '1780846', 'A132', 'L', 'Arcadio Nohales', '-'),
(88, 'Mecha', NULL, '129549', 'A558', 'L', 'José Antonio Encinas', '-'),
(89, 'Ari', NULL, '1893230', 'A301', 'L', 'Anna Aguilella', '-'),
(90, 'Aslan', NULL, '1970223', 'A457', 'L', 'Iban Cubedo', '-'),
(91, 'Mambo', NULL, '1392048', '753', 'L', 'Eduard Giralt', '-'),
(93, 'Nut', NULL, '120681', 'A430', 'L', 'Mónica Rodríguez', '-'),
(94, 'Xena', NULL, '1570666', 'A118', 'L', 'José Miguel Agustín', '-'),
(95, 'Fiona', NULL, '2010068', 'A491', 'L', 'Rafael Altava', '-'),
(96, 'Winner', 'Border Collie', '127815', 'A497', 'L', 'Ricardo Santolaya', 'Baja'),
(97, 'Jade', NULL, '120128', 'A522', 'L', 'Gerardo González', '-'),
(98, 'Dasher', NULL, '2000291', 'A411', 'L', 'Juan Francisco Pelegrin', '-'),
(99, 'Gaston', NULL, '1717449', 'A340', 'L', 'Francisco Pérez', '-'),
(100, 'Pipo', NULL, '118271', 'A458', 'L', 'Rafael Fernández', '-'),
(101, 'Grey', NULL, '1717450', 'A066', 'L', 'Araceli Montero', '-'),
(102, 'Maty', NULL, '1964043', 'A333', 'L', 'Roberto Castro', '-'),
(104, 'Dylan', NULL, '127444', 'A461', 'L', 'Iván Sánchez', '-'),
(106, 'Magia', NULL, '1753228', 'A104', 'L', 'Carolina Verdú', '-'),
(107, 'Lia', NULL, '1842317', 'A389', 'L', 'Silvia León', '-'),
(108, 'Coma', NULL, '1957039', 'A538', 'L', 'Diana Cozar', '-'),
(109, 'Isis', NULL, '1443353', '838', 'L', 'Ana Alonso', '-'),
(110, 'Otto', NULL, '91641', 'A070', 'L', 'Rafael García', '-'),
(111, 'Nana', NULL, '1781905', 'A225', 'L', 'Enrique Herbera', '-'),
(113, 'Assucar', NULL, '1594318', '992', 'L', 'Juan Carlos Redondo', '-'),
(114, 'Fantastico', NULL, '109814', 'A218', 'L', 'Pau Serrano', '-'),
(115, 'Panda', NULL, '1936233', 'A474', 'L', 'Lorena Díez', '-'),
(116, 'Raissa', NULL, '131570', 'A563', 'L', 'Javier Iniesta', '-'),
(119, 'Nuka', NULL, '127265', 'A486', 'L', 'Alex Sabini', '-'),
(120, 'Halcon', NULL, '126805', 'A482', 'L', 'Carlos Serra', '-'),
(121, 'California', NULL, '1967653', 'A414', 'L', 'Jorge Arcas', '-'),
(122, 'Astra', NULL, '1551831', '783', 'L', 'Alejandro Piñeiro', '-'),
(123, 'Fito', NULL, '1980971', 'A490', 'L', 'Silvia León', '-'),
(124, 'Magic-Black', NULL, '101184', 'A318', 'L', 'Raquel Garrido', '-'),
(125, 'Blacky', NULL, 'No tiene', '1493', 'L', 'Marina López', '-'),
(126, 'Red Magic', NULL, '1834052', 'A124', 'L', 'Carlos Martínez', '-'),
(127, 'Beauty', NULL, '120728', 'A452', 'L', 'Pau Serrano', '-'),
(128, 'Savannah', NULL, '1627377', '833', 'L', 'Ana Ontañon', '-'),
(129, 'Ra', NULL, '2009048', 'A494', 'L', 'Manuel Santomé', '-'),
(130, 'Flay', NULL, '123186', 'A418', 'L', 'Gregorio Conde', '-'),
(131, 'Bu', NULL, '111416', 'A519', 'L', 'Rafael Torregrosa', '-'),
(132, 'Heidy', NULL, '1727593', 'A035', 'L', 'Sergio Martín', '-'),
(133, 'Kiko', NULL, '1780843', 'A145', 'L', 'Ernesto Sorribes', '-'),
(134, 'Koba', NULL, '1533440', '843', 'L', 'Imanol López', '-'),
(135, 'Kiwi', NULL, '1908098', 'A267', 'L', 'Imanol López', '-'),
(136, 'Yun', NULL, '2001179', 'A484', 'L', 'Concepción Fernández', '-'),
(137, 'Nora', NULL, '80601', '735', 'L', 'Silvia Rodríguez', '-'),
(139, 'Liss', NULL, '1779665', 'A212', 'L', 'David Parejo', '-'),
(141, 'Rayko', NULL, '2027590', 'A321', 'L', 'Paula de Lucas', '-'),
(143, 'Nani', NULL, '1838888', 'A238', 'L', 'Judith Cortes', '-'),
(146, 'Rasca', NULL, '2047380', 'A564', 'L', 'Alberto Costas', '-'),
(147, 'Abby', NULL, '2104382', 'A533', 'L', 'Jenny Funcke', '-'),
(150, 'Merlin', NULL, '1996593', 'A523', 'L', 'Diana García', '-'),
(151, 'Rusti', NULL, '1831356', 'A227', 'L', 'Francisco José Sousa', '-'),
(153, 'Cora', NULL, 'No tiene', '1525', 'L', 'Angel Insa', '-'),
(154, 'Dux', NULL, '1727100', 'A134', 'L', 'Francisco Sobral', '-'),
(155, 'Urko', NULL, '127390', 'A565', 'L', 'Carlos Iglesias', '-'),
(156, 'Blues', NULL, '1756737', 'A083', 'L', 'Marcos Martínez', '-'),
(157, 'Lia', NULL, 'No tiene', '1481', 'L', 'Lourdes Peñarrocha', '-'),
(158, 'Broc', NULL, '1596717', '902', 'L', 'Josep Mª Pineda', '-'),
(159, 'Bell', NULL, '1831577', 'A205', 'L', 'Sergio García', '-'),
(160, 'Colombo', NULL, '119957', 'A437', 'L', 'Francisco Aguilera', '-'),
(163, 'Cooper', NULL, '1942046', 'A468', 'L', 'Jesús Cuellar', '-'),
(164, 'Dunja', NULL, '131504', 'A568', 'L', 'Antje Lippold', '-'),
(165, 'Nova', NULL, '1472107', '765', 'L', 'Nuria Costa', '-'),
(166, 'Nuca', NULL, '105491', 'A431', 'L', 'Rafael Camacho', '-'),
(168, 'Nora', NULL, '113894', 'A356', 'L', 'Raúl Sánchez', '-'),
(170, 'Kimi', NULL, '1908103', 'A271', 'L', 'Daniel Luna', '-'),
(174, 'Samba', NULL, '131663', 'A608', 'L', 'Carlos Casado', '-'),
(176, 'Liss', NULL, '1988114', 'A429', 'L', 'Dolores Sampedro', '-'),
(178, 'Nuka', NULL, 'No tiene', '1418', 'L', 'José Luis Quiroga', '-'),
(183, 'Samy', NULL, '120731', 'A432', 'L', 'Jordi Gómez', '-'),
(184, 'Kim', NULL, '125522', 'A511', 'L', 'Alex del Río', '-'),
(186, 'Blues', NULL, 'No tiene', '1400', 'L', 'Juan del Amo', '-'),
(187, 'Naia', NULL, '1970259', 'A459', 'L', 'Yolanda Moreno', '-'),
(188, 'Aragorn', NULL, '1568043', 'A380', 'L', 'Victor García', '-'),
(189, 'Aizu', NULL, '1680441', 'A499', 'L', 'Katy Navarro', '-'),
(191, 'Avemar', NULL, '1677435', 'A335', 'L', 'Manuel Jesús García', '-'),
(193, 'Completa', NULL, '95707', 'A121', 'L', 'Pau Serrano', '-'),
(194, 'Jayna', NULL, '1779666', 'A173', 'L', 'José Antonio Pascual', '-'),
(197, 'Kira', NULL, '1710175', 'A116', 'L', 'Enrique Alonso', '-'),
(198, 'Elfo', NULL, '1496681', '963', 'L', 'José Soliño', '-'),
(199, 'Choco', NULL, '96941', 'A058', 'L', 'Carlos Alvarez', '-'),
(200, 'Gini', NULL, '1891331', 'A193', 'L', 'Katia Moeller', '-'),
(201, 'Alinka', NULL, '1677160', '982', 'L', 'Verónica Ibañez', '-'),
(202, 'Zazpi', NULL, '1779668', 'A278', 'L', 'David Parejo', '-'),
(203, 'Frasqui', NULL, '1962362', 'A438', 'L', 'Francisco Aguilera', '-'),
(204, 'Yhara', NULL, '1827258', 'A357', 'L', 'Judith Herms', '-'),
(209, 'Lennon', NULL, '111486', 'A336', 'L', 'Miguel Angel García', '-'),
(210, 'Irma', NULL, '1779789', 'A096', 'L', 'Elena Sin', '-'),
(212, 'Yai', NULL, '1879217', 'A475', 'L', 'Andrés Gimeno', '-'),
(215, 'Clara', NULL, '1936237', 'A462', 'L', 'Ruben Montero', '-'),
(216, 'Laika', NULL, '93988', 'A224', 'L', 'Raquel Frago', '-'),
(218, 'Argon', NULL, '1926695', 'A488', 'L', 'Concepción López', '-'),
(219, 'Nube', NULL, '124447', 'A465', 'L', 'José Manuel Basco', '-'),
(221, 'Blue', NULL, '1863534', 'A552', 'L', 'Katy Navarro', '-'),
(222, 'Hana', NULL, 'No tiene', '1526', 'L', 'Rocio Santos', '-'),
(223, 'Liss', NULL, '86748', '961', 'L', 'José Carlos Iglesias', '-'),
(224, 'Tara', NULL, '123013', 'A442', 'L', 'Jerónimo Martínez', '-'),
(227, 'Onis', NULL, '1478689', 'A498', 'L', 'José Antonio Vega', '-'),
(228, 'Qumba', NULL, '2007589', 'A546', 'L', 'Juan José Espadas', '-'),
(229, 'Jotave', NULL, '1962366', 'A407', 'L', 'Juan Manuel Caballo', '-'),
(230, 'Kora', NULL, '124215', 'A585', 'L', 'Juan Manuel Caballo', '-'),
(233, 'Zana', NULL, '1634512', 'A014', 'L', 'Sergio Martín', '-'),
(234, 'Hugo', NULL, '97357', 'A201', 'L', 'José Luis Sogorb', '-'),
(235, 'Gus', NULL, '111335', 'A548', 'L', 'Vicente Cambra', '-'),
(236, 'Paco', NULL, '85033', 'A550', 'L', 'Vicente Cambra', '-'),
(237, 'Thor', NULL, '1839333', 'A348', 'L', 'José Peris', '-'),
(238, 'Fol', NULL, '86747', '940', 'L', 'Miguel García', '-'),
(239, 'Fito', NULL, '1914405', 'A362', 'L', 'Francisco José Sousa', '-'),
(240, 'Troy', NULL, '98617', 'A044', 'L', 'Nuria Morell', '-'),
(241, 'Dana', NULL, 'No tiene', '1528', 'L', 'Matias Rodríguez', '-'),
(242, 'Doña Sol', NULL, '1885107', 'A261', 'L', 'Juan Solanes', '-'),
(244, 'Martina', NULL, '1833796', 'A545', 'L', 'Elena Alberich', '-'),
(245, 'Kiss', NULL, '1626167', '912', 'L', 'Antonio Ojeda', '-'),
(246, 'Nano', NULL, '94485', '964', 'L', 'Juan Manuel Caballo', '-'),
(247, 'Ariel', NULL, '1835675', 'A213', 'L', 'Mª José Manzano', '-'),
(248, 'Cristina', NULL, '1929150', 'A352', 'L', 'Ariadna Soriano', '-'),
(253, 'Rulo', NULL, '1676914', '978', 'L', 'David Gonzalbo', '-'),
(254, 'Hechizada', NULL, '1663984', 'A064', 'L', 'Emilio Calvo', '-'),
(255, 'Cindy', NULL, '1812230', 'A189', 'L', 'Adrian Díaz', '-'),
(257, 'Rotten', NULL, 'No tiene', '1518', 'L', 'José Luis Prieto', '-'),
(260, 'Pixie Moon', NULL, '1879218', 'A260', 'L', 'José Luis Sogorb', '-'),
(262, 'Timba', NULL, '119645', 'A403', 'L', 'Carmen Vázquez', '-'),
(263, 'Nuwa', NULL, '132677', 'A597', 'L', 'Tamara Vidal', '-'),
(264, 'Kora', NULL, '99230', 'A466', 'L', 'Jesús Manuel Romero', '-'),
(265, 'Kona', NULL, '1986188', 'A524', 'L', 'Ankie Kleijberg', '-'),
(266, 'Arwen', NULL, '1753230', 'A151', 'L', 'Cristian Verde', '-'),
(267, 'Blas', NULL, '1761207', 'A086', 'L', 'José Miguel Paredes', '-'),
(268, 'Rudy', NULL, '102435', 'A113', 'L', 'Luis Ignacio Carazo', '-'),
(272, 'Ira', NULL, '101193', 'A141', 'L', 'Alberto Marugan', '-'),
(273, 'Terra', NULL, '114522', 'A328', 'L', 'Juan Francisco Torres', '-'),
(274, 'Kira', NULL, '120733', 'A405', 'L', 'Aida Al-Nehlawi', '-'),
(278, 'Nube', NULL, '97289', 'A515', 'L', 'José Luis Romero', '-'),
(279, 'Mara', NULL, '80787', 'A396', 'L', 'Ana Isabel Escobar', '-'),
(281, 'Trasto', NULL, 'No tiene', '1391', 'L', 'Adrian Díaz', '-'),
(286, 'Kiria', NULL, 'No tiene', '1553', 'L', 'Jorge Valero', '-'),
(287, 'Wind', NULL, '1881696', 'A421', 'L', 'Ruben Jurado', '-'),
(291, 'Rocky', NULL, '1756734', 'A156', 'L', 'José Francisco Martorell', '-'),
(292, 'Pepo', NULL, '1756735', 'A200', 'L', 'José Francisco Martorell', '-'),
(293, 'Mina', NULL, '124583', 'A501', 'L', 'Javier Gómez', '-'),
(294, 'Gipsy', NULL, 'No tiene', '1542', 'L', 'Javier López', '-'),
(296, 'Atril', NULL, '1985153', 'A454', 'L', 'Marta Gregorio', '-'),
(297, 'Max', NULL, '1945286', 'A487', 'L', 'Andrés López', '-'),
(299, 'N''Hug', NULL, '110043', 'A283', 'L', 'José Castaño', '-'),
(301, 'Che', NULL, '1472786', 'A313', 'L', 'Juan Solanes', '-'),
(305, 'Chincheta', NULL, '97802', 'A165', 'L', 'Sonia Asensio', '-'),
(314, 'Nemo', NULL, '1950998', 'A478', 'L', 'Enrique Grau', '-'),
(316, 'Tao', NULL, '1807243', 'A554', 'L', 'Sara Montila', '-'),
(317, 'Luna', NULL, 'No tiene', '1539', 'L', 'Ricardo Martínez', '-'),
(322, 'Molsa', NULL, '1710467', 'A103', 'L', 'Sabina González', '-'),
(323, 'Dina', NULL, '1798019', 'A136', 'L', 'Javier Martínez', '-'),
(325, 'Neo', NULL, '99972', 'A077', 'L', 'Antonio López', '-'),
(329, 'Zidane', NULL, '2081050', 'A591', 'L', 'Alberto Mudarra', '-'),
(332, 'Poly', NULL, '2060633', 'A587', 'L', 'José Luis Sogorb', '-'),
(333, 'Kinder', NULL, '101433', 'A223', 'L', 'Raquel Frago', '-'),
(334, 'Juno', NULL, 'No tiene', '1552', 'L', 'Arianna Bucci', '-'),
(335, 'Flash', NULL, '109519', 'A420', 'L', 'Gerard Barberá', '-'),
(337, 'Troya', NULL, '127255', 'A527', 'L', 'Xavier López', '-'),
(341, 'Pluto', NULL, '80602', '785', 'L', 'María López', '-'),
(342, 'Poli', NULL, '98654', 'A290', 'L', 'Silvia Rodríguez', '-'),
(345, 'Shasta', 'Border Collie', '109487', 'A272', 'L', 'Silvia Perea', 'GII'),
(349, 'Kira', NULL, '83038', '789', 'L', 'Manuel Jesús García', '-'),
(353, 'Maya', NULL, '1683945', 'A110', 'L', 'Juan Pedro Martínez', '-'),
(356, 'Grace', NULL, '127031', 'A575', 'L', 'Oscar Reboredo', '-'),
(358, 'Artemisa', NULL, '1911726', 'A406', 'L', 'Vanessa Hermoso', '-'),
(361, 'Ron', NULL, '94045', 'A265', 'L', 'Elena Sin', '-'),
(362, 'Maña', NULL, '1872301', 'A423', 'L', 'Carmen Briceño', '-'),
(368, 'Blue', NULL, '86743', 'A180', 'L', 'Diego Rouco', '-'),
(369, 'Golfa', NULL, '1719195', 'A557', 'L', 'Jacqueline Holemans', '-'),
(370, 'Runa', NULL, '2006235', 'A537', 'L', 'Lourdes Peñarrocha', '-'),
(371, 'Fanta', NULL, '1940535', 'A544', 'L', 'Natividad Soler', '-'),
(373, 'Xula', NULL, '83896', '844', 'M', 'Julia Faci', '-'),
(374, 'Danko', NULL, '113906', 'A325', 'M', 'Jorge Muñoz', '-'),
(375, 'Hanna', NULL, '1843070', 'A159', 'M', 'Nuria Fortuny', '-'),
(376, 'Guti', NULL, '111666', 'A215', 'M', 'Javier Ovejero', '-'),
(377, 'Cala', NULL, '1691264', 'A095', 'M', 'Alberto Alonso', '-'),
(379, 'Milo', NULL, '1399802', '718', 'M', 'Juan Carlos Redondo', '-'),
(380, 'Gotika', NULL, '1682493', '971', 'M', 'Ariadna Soriano', '-'),
(381, 'Drac', NULL, '106327', 'A196', 'M', 'Elisenda Huidobro', '-'),
(382, 'Neo', NULL, '109522', 'A445', 'M', 'Gerard Barberá', '-'),
(383, 'Sra. Maruja', NULL, '1695088', '997', 'M', 'Enrique Lleixa', '-'),
(384, 'Duna', NULL, '93084', '953', 'M', 'Francisco Esteban', '-'),
(385, 'Kiwi', NULL, '1982934', 'A525', 'M', 'Verónica Díez', '-'),
(386, 'Sucre', NULL, '1558711', '938', 'M', 'Nuria Alonso', '-'),
(387, 'Lass', NULL, '131981', 'A580', 'M', 'Eugenio Villares', '-'),
(389, 'Tuna', NULL, '1731780', 'A057', 'M', 'Manuel Basco', '-'),
(390, 'Norai', NULL, '97039', 'A015', 'M', 'Fernando Benet', '-'),
(391, 'Menta', NULL, '1459964', '767', 'M', 'Albert Ulldemolins', '-'),
(393, 'Wirbel', 'Schnnauzer', '1252941', '588', 'M', 'Mario Rodríguez', 'Ret.'),
(394, 'Pepsi', NULL, '1849505', 'A270', 'M', 'Carles Fortuny', '-'),
(395, 'Kiss', NULL, '1258632', '762', 'M', 'Daniel Amigo', '-'),
(396, 'Ina', NULL, 'No tiene', '1549', 'M', 'Ubaldo Delgado', '-'),
(397, 'Kenia', NULL, '86609', '954', 'M', 'Remedios Torres', '-'),
(398, 'Gamma', NULL, '1988761', 'A307', 'M', 'Jordi Boix', '-'),
(399, 'Goku', NULL, 'No tiene', '1513', 'M', 'Juan Carlos Blas', '-'),
(400, 'Coockie', NULL, '120745', 'A434', 'M', 'Toni Rios', '-'),
(401, 'Habana', NULL, '131393', 'A634', 'M', 'Estefanía Pérez', '-'),
(402, 'Dau', NULL, '113713', 'A446', 'M', 'Nuria Alonso', '-'),
(404, 'Jolie', NULL, '1561798', '808', 'M', 'Celeste Zarzosa', '-'),
(406, 'Boira', NULL, '1809307', 'A281', 'M', 'Cristina García', '-'),
(407, 'Queen', NULL, '93971', '989', 'M', 'Juan Luis Colmano', '-'),
(410, 'Legend', NULL, '1606337', '927', 'M', 'Sonia Conejero', '-'),
(412, 'Koku', NULL, '1394913', '714', 'M', 'Oria Micó', '-'),
(414, 'Luna', NULL, '103250', 'A240', 'M', 'Paula Rello', '-'),
(415, 'Nell', NULL, '1329279', 'A293', 'M', 'Eva Vázquez', '-'),
(416, 'Lucas', NULL, '112275', 'A248', 'M', 'Alberto Pereda', '-'),
(417, 'Benavis', NULL, '1803327', 'A195', 'M', 'Alicia Mejias', '-'),
(419, 'Paquita', NULL, '1852290', 'A229', 'M', 'Pablo Ballesta', '-'),
(420, 'Fey', NULL, '2063400', 'A440', 'M', 'Stina Sandquist', '-'),
(422, 'Robbi', NULL, '104953', 'A146', 'M', 'Vanessa Calpe', '-'),
(424, 'Mate', NULL, '1930526', 'A469', 'M', 'David Alique', '-'),
(425, 'Veron', NULL, '1423605', 'A412', 'M', 'Eva Vázquez', '-'),
(426, 'Lume', NULL, '1985134', 'A416', 'M', 'José Angel Torres', '-'),
(429, 'Foska', NULL, '108199', 'A182', 'M', 'Juan Solanes', '-'),
(431, 'Bimba', NULL, '1968121', 'A451', 'M', 'Mercedes Fernández', '-'),
(432, 'Chocolate', NULL, '93486', 'A045', 'M', 'Sonia Asensio', '-'),
(433, 'Tuna', 'Bodeguero Andaliuz', '122113', 'A408', 'M', 'Jorge Muñoz', 'GII'),
(434, 'Naru', NULL, '132678', 'A598', 'M', 'Tamara Vidal', '-'),
(437, 'Mizar', NULL, '1876817', 'A274', 'M', 'Enrique Sendra', '-'),
(439, 'Tina', NULL, 'No tiene', '1532', 'M', 'Jaime Gamir', '-'),
(440, 'Kenya', NULL, '112354', 'A233', 'M', 'Laura Carrasco', '-'),
(444, 'Duque', NULL, 'No tiene', '1551', 'M', 'Alicia Mejias', '-'),
(446, 'Harley', NULL, '2025374', 'A428', 'M', 'Efren Lucas', '-'),
(447, 'Johnny Cash', NULL, '2057033', 'A594', 'M', 'Vicente Micó', '-'),
(452, 'Striptease', NULL, '2023589', 'A577', 'M', 'Carmen Alos', '-'),
(454, 'Tuco', NULL, '2026054', 'A419', 'M', 'Inmaculada Rubio', '-'),
(456, 'Mia', NULL, '121232', 'A547', 'M', 'Matias Monleón', '-'),
(457, 'Gunilla', NULL, 'No tiene', '1471', 'M', 'Luna Ramírez', '-'),
(463, 'Alma', NULL, '90313', 'A566', 'M', 'Lourdes Giménez', '-'),
(464, 'Noa', NULL, '1926077', 'A526', 'M', 'Pilar Rodríguez', '-'),
(465, 'Magia', NULL, '2032179', 'A528', 'S', 'Carmen Briceño', '-'),
(466, 'Saroa', NULL, '1789456', 'A149', 'S', 'Yolanda Torres', '-'),
(467, 'Melendi', NULL, '1842276', 'A164', 'S', 'Luis Luque', '-'),
(468, 'Mims', NULL, '102903', 'A279', 'S', 'Agustin Centelles', '-'),
(469, 'Hancock', NULL, '131702', 'A609', 'S', 'Miguel Angel Soriano', '-'),
(473, 'Tris', NULL, '119441', 'A398', 'S', 'Rocio Hermelo', '-'),
(474, 'Lula', NULL, '123192', 'A607', 'S', 'David Sepulveda', '-'),
(475, 'Rufo', NULL, '123178', 'A435', 'S', 'José Peris', '-'),
(477, 'Sira', NULL, '106345', 'A168', 'S', 'David Ferrer', '-'),
(478, 'Nit', NULL, '112007', 'A208', 'S', 'Montserrat Calvet', '-'),
(479, 'Xira', NULL, '124731', 'A424', 'S', 'Sergio Ruiz', '-'),
(482, 'Che Guevara', NULL, '112448', 'A230', 'S', 'Julia Morugova', '-'),
(484, 'Enzo', NULL, '117909', 'A444', 'S', 'Miriam García', '-'),
(485, 'Nuca', NULL, '109471', 'A181', 'S', 'Iván Pardo', '-'),
(487, 'Gismo', NULL, '123726', 'A449', 'S', 'Stefan Eggenschwiler', '-'),
(488, 'Nana', NULL, '103211', 'A277', 'S', 'Sara Montila', '-'),
(489, 'Nikita', NULL, '123006', 'A443', 'S', 'Ana Alonso', '-'),
(490, 'Xena', NULL, '125524', 'A509', 'S', 'Enric García', '-'),
(491, 'Chula', NULL, '106335', 'A135', 'S', 'Ismael Pérez', '-'),
(492, 'Dagga', NULL, '127338', 'A507', 'S', 'Iago Sánchez', '-'),
(495, 'Greta', NULL, '1849716', 'A337', 'S', 'Barbara Flemming', '-'),
(496, 'Bengel', 'Schnnauzer', '1433208', '760', 'S', 'Mario Rodríguez', 'GII'),
(497, 'Nei', NULL, '1400011', '770', 'S', 'Isabel Rodríguez', '-'),
(498, 'Tess', NULL, '102439', 'A245', 'S', 'Jaume Fernández', '-'),
(500, 'Lia', NULL, '132000', 'A588', 'S', 'Irene Artacho', '-'),
(504, 'Taca', NULL, '128455', 'A589', 'S', 'Ana Mateu', '-'),
(505, 'Miche', NULL, '1706141', 'A097', 'S', 'Manuel Lara', '-'),
(506, 'Manin', NULL, '104120', 'A100', 'S', 'Rocio Hermelo', '-'),
(507, 'Doña Matilde', NULL, '2005359', 'A514', 'S', 'Enric Lleixa', '-'),
(508, 'Aqua', NULL, '2056249', 'A513', 'S', 'Laura Chiva', '-'),
(512, 'Nuca', NULL, '1678476', 'A088', 'S', 'Aaron Laro', '-'),
(514, 'Pepa', NULL, '1957259', 'A393', 'S', 'Francisco de la Cruz', '-'),
(515, 'Spyro', NULL, '131527', 'A569', 'S', 'Oscar Bravo', '-'),
(516, 'Della', NULL, '2061744', 'A467', 'S', 'Daniel Amigo', '-'),
(518, 'Lua', NULL, '116884', 'A287', 'S', 'Juan Carlos Companys', '-'),
(519, 'Mayo', NULL, 'No tiene', '1419', 'S', 'Noelia Gimeno', '-'),
(520, 'Nala', NULL, '108635', 'A530', 'S', 'David Flix', '-'),
(522, 'Lola', NULL, 'No tiene', '1464', 'S', 'Javier Sanchis', '-'),
(525, 'Noah', NULL, '131506', 'A590', 'S', 'Jordi Gómez', '-'),
(526, 'Sully', NULL, '116639', 'A520', 'S', 'Rafael Torregrosa', '-'),
(531, 'Gus', NULL, '119626', 'A346', 'S', 'Antonio Carmona', '-'),
(534, 'Lola', NULL, 'No tiene', '1541', 'S', 'Lourdes Giménez', '-'),
(540, 'Thor', NULL, '1939205', 'A535', 'S', 'Aaron Laro', '-'),
(541, 'Quillo', NULL, '127443', 'A604', 'S', 'Cristina González', '-'),
(542, 'Lennon', NULL, '103239', 'A144', 'S', 'Mónica Saavedra', '-'),
(543, 'Boira', NULL, 'No tiene', '1554', 'S', 'Salvador Martí', '-'),
(544, 'Kyra', NULL, '131481', 'A600', 'S', 'Alicia Sanjurjo', '-'),
(556, 'Acha', NULL, '123731', 'A483', 'M', 'Mar Bermúdez', '-'),
(557, 'Ada', NULL, NULL, '1459', 'S', 'Ramón Arribas', 'GII'),
(558, 'Aker', 'P.B. Malinoise', '1553051', 'A397', 'L', 'Francisco Javier Jaen', 'GII'),
(559, 'Akira', NULL, '125877', 'A455', 'L', 'José Moreno', 'GII'),
(563, 'Dama', 'Fox Terrier Wire', '0131204', 'A641', 'M', 'Juan Antonio Martinez', 'GII'),
(564, 'Flai', 'Fox Terrier Wire', '0129738', '', 'M', 'Juan Antonio Martinez', 'P.A.'),
(567, 'Donna', 'Border Collie', '', '', 'L', 'Ricardo Benito', 'GI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Provincias`
--
-- Creación: 30-09-2013 a las 06:07:32
--

DROP TABLE IF EXISTS `Provincias`;
CREATE TABLE IF NOT EXISTS `Provincias` (
  `Provincia` varchar(32) NOT NULL DEFAULT '',
  `Comunidad` varchar(32) DEFAULT NULL,
  `Codigo` int(2) NOT NULL,
  PRIMARY KEY (`Provincia`),
  UNIQUE KEY `Codigo` (`Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Provincias`
--

INSERT INTO `Provincias` (`Provincia`, `Comunidad`, `Codigo`) VALUES
('Albacete', 'Castilla - La Mancha', 2),
('Alicante/Alacant', 'Comunitat Valenciana', 3),
('Almería', 'Andalucía', 4),
('Araba/Álava', 'País Vasco', 1),
('Asturias', 'Cantabria', 33),
('Ávila', 'Castilla y León', 5),
('Badajoz', 'Extremadura', 6),
('Balears, Illes', 'Balears, Illes', 7),
('Barcelona', 'Cataluña', 8),
('Bizkaia', 'País Vasco', 48),
('Burgos', 'Castilla y León', 9),
('Cáceres', 'Extremadura', 10),
('Cádiz', 'Andalucía', 11),
('Cantabria', 'Cantabria', 39),
('Castellón/Castelló', 'Comunitat Valenciana', 12),
('Ceuta', 'Ceuta', 51),
('Ciudad Real', 'Castilla - La Mancha', 13),
('Córdoba', 'Andalucía', 14),
('Coruña, A', 'Galicia', 15),
('Cuenca', 'Castilla - La Mancha', 16),
('Gipuzkoa', 'País Vasco', 20),
('Girona', 'Cataluña', 17),
('Granada', 'Andalucía', 18),
('Guadalajara', 'Castilla - La Mancha', 19),
('Huelva', 'Andalucía', 21),
('Huesca', 'Aragón', 22),
('Jaén', 'Andalucía', 23),
('León', 'Castilla y León', 24),
('Lleida', 'Cataluña', 25),
('Lugo', 'Galicia', 27),
('Madrid', 'Madrid, Comunidad de', 28),
('Málaga', 'Andalucía', 29),
('Melilla', 'Melilla', 52),
('Murcia', 'Murcia, Región de', 30),
('Navarra', 'Navarra, Comunidad Foral de', 31),
('Ourense', 'Galicia', 32),
('Palencia', 'Castilla y León', 34),
('Palmas, Las', 'Canarias', 35),
('Pontevedra', 'Galicia', 36),
('Rioja, La', 'Rioja, La', 26),
('Salamanca', 'Castilla y León', 37),
('Santa Cruz de Te', 'Canarias', 38),
('Segovia', 'Castilla y León', 40),
('Sevilla', 'Andalucía', 41),
('Soria', 'Castilla y León', 42),
('Tarragona', 'Cataluña', 43),
('Teruel', 'Aragón', 44),
('Toledo', 'Castilla - La Mancha', 45),
('Valencia/Valéncia', 'Comunitat Valenciana', 46),
('Valladolid', 'Castilla y León', 47),
('Zamora', 'Castilla y León', 49),
('Zaragoza', 'Aragón', 50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Pruebas`
--
-- Creación: 30-09-2013 a las 06:07:35
--

DROP TABLE IF EXISTS `Pruebas`;
CREATE TABLE IF NOT EXISTS `Pruebas` (
  `Nombre` varchar(255) NOT NULL,
  `Club` varchar(255) NOT NULL,
  `Ubicación` varchar(255) DEFAULT NULL,
  `Triptico` longblob,
  `Cartel` longblob,
  `Observaciones` varchar(255) DEFAULT NULL,
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Nombre`),
  KEY `Club` (`Club`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `Pruebas`:
--   `Club`
--       `Clubes` -> `Nombre`
--

--
-- Volcado de datos para la tabla `Pruebas`
--

INSERT INTO `Pruebas` (`Nombre`, `Club`, `Ubicación`, `Triptico`, `Cartel`, `Observaciones`, `Cerrada`) VALUES
('test', 'W.E.L.P.E.', 'Polideportivo "La Canaleja"', NULL, NULL, 'Esto es una prueba', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Resultados`
--
-- Creación: 30-09-2013 a las 06:07:35
--

DROP TABLE IF EXISTS `Resultados`;
CREATE TABLE IF NOT EXISTS `Resultados` (
  `Orden de Salida` int(4) DEFAULT NULL,
  `Dorsal` int(4) NOT NULL,
  `Entrada a pista` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Faltas` int(4) NOT NULL DEFAULT '0',
  `Rehuses` int(4) NOT NULL DEFAULT '0',
  `Tocados` int(4) NOT NULL DEFAULT '0',
  `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `No presentado` tinyint(1) NOT NULL DEFAULT '0',
  `Tiempo` double DEFAULT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  `Manga` int(4) NOT NULL,
  KEY `Dorsal` (`Dorsal`),
  KEY `Manga` (`Manga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `Resultados`:
--   `Dorsal`
--       `Perros` -> `Dorsal`
--   `Manga`
--       `Mangas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tipo_Manga`
--
-- Creación: 30-09-2013 a las 06:07:33
--

DROP TABLE IF EXISTS `Tipo_Manga`;
CREATE TABLE IF NOT EXISTS `Tipo_Manga` (
  `Tipo` varchar(16) NOT NULL DEFAULT '',
  `Descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Tipo_Manga`
--

INSERT INTO `Tipo_Manga` (`Tipo`, `Descripcion`) VALUES
('Agility Equipos', 'Agility por Equipos'),
('Agility GII', 'Agility Grado II'),
('Agility GIII', 'Agility Grado III'),
('Agility-1 GI ', 'Agility Grado I Manga 1'),
('Agility-2 GI', 'Agility Grado I Manga 2'),
('Exhibición', 'Ronda de Exhibición'),
('Jumping Equipos', 'Jumping por Equipos'),
('Jumping GII', 'Jumping Grado II'),
('Jumping GIII', 'Jumping Grado III'),
('K.O.', 'Ronda K.O.'),
('Pre-Agility', 'Ronda de Pre-Agility');

-- --------------------------------------------------------

--
-- Estructura para la vista `PerroGuiaClub`
--
DROP TABLE IF EXISTS `PerroGuiaClub`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `PerroGuiaClub` AS select `Perros`.`Dorsal` AS `Dorsal`,`Perros`.`Nombre` AS `Nombre`,`Perros`.`Categoria` AS `Categoria`,`Perros`.`Grado` AS `Grado`,`Perros`.`Guia` AS `Guia`,`Guias`.`Club` AS `Club` from (`Perros` join `Guias`) where (`Perros`.`Guia` = `Guias`.`Nombre`) order by `Guias`.`Club`,`Perros`.`Categoria`,`Perros`.`Nombre`;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Clubes`
--
ALTER TABLE `Clubes`
  ADD CONSTRAINT `Clubes_ibfk_1` FOREIGN KEY (`Provincia`) REFERENCES `Provincias` (`Provincia`);

--
-- Filtros para la tabla `Equipos`
--
ALTER TABLE `Equipos`
  ADD CONSTRAINT `Equipos_ibfk_1` FOREIGN KEY (`Jornada`) REFERENCES `Jornadas` (`ID`);

--
-- Filtros para la tabla `Guias`
--
ALTER TABLE `Guias`
  ADD CONSTRAINT `Guias_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `Clubes` (`Nombre`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `Inscripciones`
--
ALTER TABLE `Inscripciones`
  ADD CONSTRAINT `Inscripciones_ibfk_1` FOREIGN KEY (`Dorsal`) REFERENCES `Perros` (`Dorsal`),
  ADD CONSTRAINT `Inscripciones_ibfk_2` FOREIGN KEY (`Jornada`) REFERENCES `Jornadas` (`ID`),
  ADD CONSTRAINT `Inscripciones_ibfk_3` FOREIGN KEY (`Equipo`) REFERENCES `Equipos` (`ID`);

--
-- Filtros para la tabla `Jornadas`
--
ALTER TABLE `Jornadas`
  ADD CONSTRAINT `Jornadas_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `Pruebas` (`Nombre`);

--
-- Filtros para la tabla `Mangas`
--
ALTER TABLE `Mangas`
  ADD CONSTRAINT `Mangas_ibfk_1` FOREIGN KEY (`Tipo`) REFERENCES `Tipo_Manga` (`Tipo`),
  ADD CONSTRAINT `Mangas_ibfk_2` FOREIGN KEY (`Juez Titular`) REFERENCES `Jueces` (`Nombre`),
  ADD CONSTRAINT `Mangas_ibfk_3` FOREIGN KEY (`Juez Practicas`) REFERENCES `Jueces` (`Nombre`),
  ADD CONSTRAINT `Mangas_ibfk_4` FOREIGN KEY (`Jornada`) REFERENCES `Jornadas` (`ID`);

--
-- Filtros para la tabla `Perros`
--
ALTER TABLE `Perros`
  ADD CONSTRAINT `Perros_ibfk_3` FOREIGN KEY (`Guia`) REFERENCES `Guias` (`Nombre`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Perros_ibfk_1` FOREIGN KEY (`Categoria`) REFERENCES `Categorias_Perro` (`Categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Perros_ibfk_2` FOREIGN KEY (`Grado`) REFERENCES `Grados_Perro` (`Grado`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Pruebas`
--
ALTER TABLE `Pruebas`
  ADD CONSTRAINT `Pruebas_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `Clubes` (`Nombre`);

--
-- Filtros para la tabla `Resultados`
--
ALTER TABLE `Resultados`
  ADD CONSTRAINT `Resultados_ibfk_1` FOREIGN KEY (`Dorsal`) REFERENCES `Perros` (`Dorsal`),
  ADD CONSTRAINT `Resultados_ibfk_2` FOREIGN KEY (`Manga`) REFERENCES `Mangas` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
