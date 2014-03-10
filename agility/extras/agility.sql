-- phpMyAdmin SQL Dump
-- version 4.1.8deb0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 10-03-2014 a las 14:28:30
-- Versión del servidor: 5.5.35-0ubuntu0.12.04.2
-- Versión de PHP: 5.3.10-1ubuntu3.10

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
-- Creación: 10-03-2014 a las 12:45:38
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
-- Estructura de tabla para la tabla `Clubes`
--
-- Creación: 10-03-2014 a las 12:45:40
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=162 ;

--
-- RELACIONES PARA LA TABLA `Clubes`:
--   `Provincia`
--       `Provincias` -> `Provincia`
--

--
-- Volcado de datos para la tabla `Clubes`
--

INSERT INTO `Clubes` (`ID`, `Nombre`, `Direccion1`, `Direccion2`, `Provincia`, `Contacto1`, `Contacto2`, `Contacto3`, `GPS`, `Web`, `Email`, `Facebook`, `Google`, `Twitter`, `Logo`, `Observaciones`, `Baja`) VALUES
(1, '-- Sin asignar --', NULL, NULL, '-- Sin asignar --', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'rsce.png', 'NO BORRAR ESTA ENTRADA. SE USARA PARA AQUELLOS GUIAS QUE NO TENGAN CLUB ASIGNADO', 0),
(82, 'AA Y CIA', '28609 Sevilla La Nueva (Madrid)', '', 'Madrid', '+ 34 619 29 03 98', '', '', '', '', 'arribas.anabel@gmail.com', '', '', '', 'aaycia.png', '', 0),
(83, 'ACADE', 'Salvadas, 41, 2º C', '15705 Santiago de Compostela', 'Coruña, A', '+ 34 620 29 58 31', '+ 34 881 93 95 5', '', '', 'http://www.asociacionacade.com/', 'asociacioncansdeportistas@gmail.com', '', '', '', 'acade.png', '', 0),
(84, 'Agilcan', 'Paseo de los Olivos 10', '28330 San Martin de la Vega', 'Madrid', '634 417 893', '918 946 096', '659 146 878', NULL, 'http://www.agilcan.es/', 'info@agilcan.es', NULL, NULL, NULL, 'agilcan.png', NULL, 0),
(85, 'Alhambra', 'Urano, 24', '18200 Maracena (Granada)', 'Granada', ' + 34 958 42 12 85 ', '', '', '', '', 'agilityalhambra@hotmail.com', '', '', '', 'alhambra.png', '', 0),
(86, 'Askizu', 'Caserio Benta - Barrio Askizu', '20808 Getaria (Guipúzcoa)', 'Gipuzkoa/Guipúzcoa', '+ 34 656 76 60 65', '', '', '', 'http://www.agilityaskizu.com/', 'antonio@agilityaskizu.com', '', '', '', 'askizu.png', '', 0),
(87, 'Badalona', 'Camí del Xiprers, s/n', '08916 Badalona (Barcelona)', 'Barcelona', ' + 34 93 597 23 53 ', ' + 34 676 48 99 40 ', '', '', 'http://www.agilitybadalona.con/', 'info@agilitybadalona.com', '', '', '', 'badalona.png', '', 0),
(88, 'Baix Llobregat', 'Enric Borras, 10', '08820 El Prat de Llobregat (Barcelona)', 'Barcelona', '+34 695 79 42 74', '', '', '', 'http://www.agilitybaixllobregat.com/', 'agilitybaixllobregat@hotmail.com', '', '', '', 'baix_llobregat.png', '', 0),
(89, 'Campo de Gibraltar', 'Arbol de la Paz, 4', '11205 Algeciras (Cádiz)', 'Cádiz', ' + 34 647 80 32 64 ', '', '', '', '', 'villa-zahara@hotmail.com', '', '', '', 'campo_de_gibraltar.png', '', 0),
(90, 'Camu', 'Párraco Pedro Lozano, 26', '30007 Zarandona (Murcia)', 'Murcia', '+ 34 636 25 19 39', '', '', '', '', 'clubagilitymurcia@hotmail.com', '', '', '', 'camu.png', '', 0),
(91, 'Can Natura', 'Peñella Baja, 1', '33310 Cabranes (Asturias)', 'Asturias', '+ 34 696 86 08 63', '', '', '', 'http://www.cannatura.net/', 'cannatura@hotmail.com', '', '', '', 'cannatura.png', '', 0),
(92, 'Canedupo', 'Serra de Ancares, 18', '27004 Lugo', 'Lugo', '636 507 468.', '', '', '', '', 'lugo@canedupo.com', '', '', '', 'canedupo.png', '', 0),
(93, 'Canic', 'Sant Pere, 10', '08392 Llavaneres (Barcelona)', 'Barcelona', '+ 34 93 792 76 55', '', '', '', 'http://www.agilitycanic.cat/', 'info@agilitycanic.cat', '', '', '', 'canic.png', '', 0),
(94, 'Canino Algecireño', 'Los Naranjos, 8', '11380 Tarifa (Cádiz)', 'Cádiz', ' + 34 663 55 86 42 ', '', '', '', '', 'arquikm@gmail.com', '', '', '', 'canino_algecireno.png', '', 0),
(95, 'Castellón', 'Mar Cantábrico, 2, 1º C', '12100 Grao de Castellón (Castellón)', 'Castellón/Castelló', '+ 34 964 28 61 52', '+ 34 625 82 25 35', '', '', 'http://www.agilitycastellon.es/', 'agilitycastellon@gmx.es', '', '', '', 'castellon.png', '', 0),
(96, 'Cinco Huesos', 'Paseo de los Pozos, Km. 1,2', '28813 Torres de la Alameda', 'Madrid', '+ 34 91 832 83 00 ', '+ 34 691 77 75 24 ', '', '', 'http://www.cincohuesos.com/', 'cincohuesos@gmail.com', '', '', '', 'cinco_huesos.png', 'Antiguo club "Apata"', 0),
(97, 'Ciudad de Antequera', '', '', 'Málaga', '', '', '', '', '', '', '', '', '', 'ciudad_de_antequera.png', '', 1),
(98, 'Ciutat Comtal', 'Escultor Llimona, 38-40, Entr. 2ª', '08031 Barcelona', 'Barcelona', ' + 34 645 85 10 06 ', '', '', '', 'http://www.agilitybarcelona.com/', 'info@agilitybarcelona.com', '', '', '', 'ciutat_comtal.png', '', 0),
(99, 'Ciutat de Valencia', 'Sequia de Calvera, 33, Bajo', '46910 Sedaví (Valencia)', 'Valencia/Valéncia', '', '', '', '', 'http://www.bichopeludo.com/ciutat_de_valencia.html', 'ciutatdevalencia@bichopeludo.com', '', '', '', 'ciutat_de_valencia.png', '', 0),
(100, 'Clotet', 'Apdo. de correos 517', '12500 Vinaroz (Castellón)', 'Castellón/Castelló', '+ 34 687 52 38 11', '', '', '', 'http://www.degarrof.com/', 'declotet@hotmail.com', '', '', '', 'clotet.png', '', 0),
(101, 'Cornella', 'Mn. Andreu, 13-19', '08940 Cornellà de Llobregat (Barcelona)', 'Barcelona', '+ 34 638 98 75 91', '', '', '', 'http://www.agilitycornella.com/', 'info@agilitycornella.com', '', '', '', 'cornella.png', '', 0),
(102, 'Correcan', ' Lóndrés, 58, 1º D', '28850 Torrejón de Ardoz (Madrid)', 'Madrid', '+ 34 668 86 76 81', '', '', '', 'http://www.correcan.es/', 'info@correcan.es', '', '', '', 'correcan.png', '', 0),
(103, 'Costa Azahar', 'Camino Caminas, 223 - Apdo. de correos 717', '12080 Castellón', 'Castellón/Castelló', '+ 34 964 76 60 83', '', '', '', 'http://www.mediterraniacentrocanino.com/', 'info@mediterraniacentrocanino.com', '', '', '', 'costa_azahar.png', '', 0),
(104, 'Costa Blanca', 'C/ Baltasar Gracián Nº3, Urb. Montecid', '03670 Monforte del Cid ', 'Alicante/Alacant', NULL, NULL, NULL, NULL, 'http://www.agilitycostablanca.com/', 'agility@agilitycostablanca.com', NULL, NULL, NULL, 'costa_blanca.png', NULL, 0),
(105, 'Cousadecans', 'Lugar de Foxo, s/n - San Vicente de Vigo', '15175 Carral (La Coruña)', 'Coruña, A', '+ 34 652 83 28 34', '+ 34 620 67 61 40', '', '', '', 'cousadecans@gmail.com', '', '', '', 'cousadecans.png', '', 0),
(106, 'Cuatro Patas', 'Club social Urb/ El Socorro', 'Carmona', 'Sevilla', '630 52 72 42 (Isaac) ', '615 45 58 78 (Rafa)', '', 'N 37.43865 - W 5.79858', '', 'agiltiy4patas@hotmail.com', '', '', '', 'cuatro_patas.png', '', 1),
(107, 'Cubas', 'Paseo de los Cipreses s/n', 'Cubas de la Sagra', 'Madrid', '918143395', '619 56 43 49', NULL, NULL, 'http://clubagilitycubas.es/', 'clubagilitycubas@terra.com', NULL, NULL, NULL, 'cubas.png', NULL, 0),
(108, 'Deporcan', 'Jazmín, 16, Entreplanta', '28033 Madrid', 'Madrid', '629 843 681', ' + 34 91 302 44 35', '', '40.32132, -3.41895', 'http://www.clubagilityboadilla.org/', 'agility.deporcan@gmail.com', '', '', '', 'deporcan.png', 'Antiguo "Club Boadilla"', 0),
(109, 'Depordog', 'Avd del Mueble s/n', '11130 Chiclana', 'Cádiz', '652 73 45 17', NULL, NULL, NULL, 'http://www.clubagilitydepordog.es/', 'ildegolo@hotmail.com', NULL, NULL, NULL, 'depordog.png', NULL, 0),
(110, 'Educan', 'Mester de Juglaría, 20', '28978 Cubas de la Sagra (Madrid)', 'Madrid', '617 469 312', '+ 34 676 67 76 38', '', '', 'http://www.madrid.educan.es/', 'agility.madrid@educan.es', '', '', '', 'educan.png', '', 0),
(111, 'El Hechizo del Border C.', 'Ctra. Monserrat, Km. 7''5, nº 26', '46900 Torrent ', 'Valencia/Valéncia', '+ 34 96 156 56 75', NULL, NULL, NULL, 'http://www.elhechizo.com/', 'elhechizobc@gmail.com', NULL, NULL, NULL, 'el_hechizo_del_border_collie.png', NULL, 0),
(112, 'El Nogueral', 'Cami del Camp, 23', '03460 Beneixama', 'Alicante/Alacant', '+ 34 695 45 23 69', NULL, NULL, NULL, 'http://www.clubagility.es/', 'info@clubagility.es', NULL, NULL, NULL, 'el_nogueral.png', NULL, 0),
(113, 'El Tramusser Benifaio', 'Polígono 16 - Cami Prefasic', '46450 Benifaió (Valencia)', 'Valencia/Valéncia', '+ 34 678 57 47 86', '', '', '', 'http://www.escuelacaninavalencia.com/', 'madamagility@hotmail.com', '', '', '', 'el_tramusser_benifaio.png', '', 0),
(114, 'Els Dimonis de Bascara', 'Apartado de correos 241', '17600 Figueres (Gerona)', 'Girona/Gerona', ' + 34 657 20 44 81', '', '', '', 'http://www.dimonisdebascara.cat/', 'dimonisdebascara@hotmail.es', '', '', '', 'els_dimonis_de_bascara.png', '', 0),
(115, 'Eslón', 'Carretera de Carranque s/n', 'Serranillos del Valle', 'Madrid', '657 209 274', '', '', '', 'http://www.agilityeslon.com', 'info@agilityeslon.com', '', '', '', 'eslon.png', '', 0),
(116, 'Euskadi', 'CLUB DE AGILITY EUSKADI Beike Bidea, 2, 2º Dcha', '48950 Asua - Erandio (Vizcaya)', 'Bizkaia/Vizcaya', '619 423 720 - Pedro Martinez', '', '', '', ' www.agilityeuskadi.com', 'info@agilityeuskadi.com', '', '', '', 'euskadi.png', '', 0),
(117, 'Hoop Agility', 'Alberto Conti, 8, 7º C', '28935 Móstoles (Madrid)', 'Madrid', '+ 34 635 65 78 42', '', '', '', 'http://www.agilityclub.es/', 'info@agilityclub.es', '', '', '', 'hoop.png', '', 0),
(118, 'Illa Blanca', 'Washington, 18, 2º', '07820 San Antonio de Portmany (Ibiza)', 'Balears, Illes', '+ 34 672 32 39 22', NULL, NULL, NULL, 'http://www.agilityillablanca.com/', 'info@agilityillablanca.com', NULL, NULL, NULL, 'illa_blanca.png', NULL, 0),
(119, 'Indog Maria de Huerva', 'Calle Orfeón 13  Nave A', '50410  Cuarte de Huerva', 'Zaragoza', '', '', '', '', 'http://www.agilityindog.com/', 'info@agilityindog.com', '', '', '', 'indog.png', '', 0),
(120, 'Junior', 'Calle de la Fuente, nº 8', '16162-Villar del Horno', 'Cuenca', '626389032', NULL, NULL, NULL, 'http://www.agilityjunior.es/', 'agilityjunior@gmail.com, info@agilityjunior.es', NULL, NULL, NULL, 'junior.png', NULL, 0),
(121, 'Kai Argi', 'Oiartzun, 6, Entlo. B', '20110 Pasaia San Pedro (Guipúzcoa)', 'Gipuzkoa/Guipúzcoa', '+ 34 656 71 51 31', '', '', '', 'http://www.kaiargi.com', 'kaiargi@kaiargi.com', '', '', '', 'kai_argi.png', '', 0),
(122, 'L''Almozara', 'Camino de Pinseque, 147-A', '50190 Garrapinillos (Zaragoza)', 'Zaragoza', ' + 34 637 54 15 86', '', '', '', '', '', '', '', '', 'almozara.png', '', 0),
(123, 'L''Horta Nord', 'Vora Vía, 2', '46132 Almassera (Valencia)', 'Valencia/Valéncia', '+ 34 651 30 41 47', '', '', '', 'http://clubagilitylhortanord.blogspot.com.es/', '', 'https://www.facebook.com/agility.lhortanordvalencia', '', '', 'horta_nord.png', '', 0),
(124, 'La Daga', '25230 Mollerussa (Lérida)', '', 'Lleida/Lérida', ' + 34 660 72 04 90 ', '', '', '', 'http://www.clubagilityladaga.blogspot.com/', 'niisia84@hotmail.com', '', '', '', 'la_daga.png', '', 0),
(125, 'La Dama', 'Partida de las Casicas, 5', '03330 Crevillente (Alicante) – España', 'Alicante/Alacant', ' +34 622 109 409', '', '', ' 38° 10′ 44″ N – 0° 48′ 30″ W”', 'http://www.agilityladama.com/ladama/', 'agilityladama@gmail.com', '', '', '', 'la_dama.png', '', 0),
(126, 'La Manada', 'Partida Calvet, 37, Bajo', '46120 Alboraia (Valencia)', 'Valencia/Valéncia', '+ 34 659 89 78 40 (Vicent)', '', '', '39° 29'' 47.55'''' N - 0° 20'' 37.1'''' W', 'http://www.la-manada.org/agility/', 'info@la-manada.org', '', '', '', 'la_manada.png', '', 0),
(127, 'La Palma', 'Paraje Los Pérez de Arriba', '30593 La Palma - Cartagena (Murcia)', 'Murcia', ' + 34 669 23 31 83', '', '', '', 'http://www.agilitycartagena.com/', 'agilitycartagena@gmail.com', '', '', '', 'la_palma.png', '', 0),
(128, 'La Princesa', 'Ocaña, 104, Bajo', '28047 Madrid', 'Madrid', '+ 34 91 465 50 05 ', '', '', '+40° 19'' 35,41", -3° 50'' 50,61" ', 'http://www.agilitylaprincesa.es/', 'agilitylaprincesa@gmail.com', '', '', '', 'la_princesa.png', '', 0),
(129, 'La Ribera', 'Plaza España, 11', '50638 Cabañas de Ebro (Zaragoza)', 'Zaragoza', ' + 34 976 75 86 33', ' + 34 649 58 65 98 ', '', '', 'http://www.agilitylaribera.es/', 'agilitylaribera@hotmail.com', '', '', '', 'la_ribera.png', '', 0),
(130, 'La Selva', 'Carretera Vella de Riudarenes, s/n', '17430 Santa Coloma de Farners (Gerona)', 'Girona/Gerona', '+ 34 606 77 64 65', ' + 34 629 36 37 39', '', '', 'http://www.asscaninalaselva.com/', 'agility@asscaninalaselva.com', '', '', '', 'la_selva.png', '', 0),
(131, 'Lealcan', 'José Luis Sampedro, 14, 2º D', '28529 Rivas Vaciamadrid (Madrid)', 'Madrid', ': + 34 646 44 45 55', '', '', '', 'http://www.lealcan.com/', 'info@lealcan.com', '', '', '', 'lealcan.png', '', 0),
(132, 'Maresme', 'Santiago Rusiñol, 90', '08340 Vilassar de Mar (Barcelona)', 'Barcelona', ' + 34 93 759 70 54 ', '', '', '', '', 'agilitymaresme@telefonica.net', '', '', '', 'maresme.png', '', 0),
(133, 'Marvi', '40 Pins, 36 - Urb. Roca II', '08430 La Roca del Valles (Barcelona)', 'Barcelona', '+ 34 93 842 21 05 ', '', '', '', '', 'Marvistel@hotmail.com', '', '', '', 'marvi.png', '', 0),
(134, 'Mediterráneo', 'Senda Estrecha, 14', '30011 Murcia', 'Murcia', '+ 34 968 25 77 83', '+ 34 677 40 98 57', '', '', '', '', '', '', '', 'mediterraneo.png', '', 0),
(135, 'Mi Perro 10', 'Andalucía, 25', '28750 San Agustín de Guadalix (Madrid)', 'Madrid', '+ 34 651 91 41 46', '', '', '', 'http://www.miperro10.com/', 'info@miperro10.com', '', '', '', 'mi_perro_10.png', '', 0),
(136, 'Miramar', 'Carrer del Llorer, 3', '08789 La Torre de Claramunt (Barcelona)', 'Barcelona', '+ 34 679 27 27 91 ', '', '', '', '', 'jmtorres323@gmail.com', '', '', '', 'miramar.png', '', 0),
(137, 'Negreira', 'Avda. Recinto Ferial, 2, 4º A', '36540 Silleda (Pontevedra)', 'Pontevedra', '+ 34 629 50 71 76', '', '', '', '', '', '', '', '', 'negreira.png', '', 0),
(138, 'Neo Reus', 'Mª Aurelia Campany, 4, 6º, 2º', '43204 Reus (Tarragona)', 'Tarragona', '+ 34 616 44 62 41', '', '', '', '', 'agilityneo@hotmail.com', '', '', '', 'neo_reus.png', '', 0),
(139, 'Palaciego', 'Roble, 7', '41720 Los Palacios y Villafranca (Sevilla)', 'Sevilla', '+ 34 619 12 73 25 ', '', '', '', 'http://www.actiweb.es/palaciego/', '', 'https://www.facebook.com/pages/Club-Deportivo-Agility-Palaciego/192778427478835', '', '', 'palaciego.png', '', 0),
(140, 'Parbayon Cantabria', 'Bº Sorribero Bajo, nº 4', '39470 Renedo de Piélagos (Cantabria)', 'Cantabria', '+ 34 626 79 14 54 ', '', '', '', 'http://www.agilitycantabria.com/', 'agilitycantabria@gmail.com', '', '', '', 'parbayon.png', '', 0),
(141, 'Parque del Alamillo', 'Antonio Machín, 9, 1º Izda.', '41009 Sevilla', 'Sevilla', ' + 34 655 76 46 03', '+ 34 95 443 45 77 ', '', '', 'http://www.clubagilityalamillo.com/', 'clubagilityalamillo@hotmail.com', '', '', '', 'parque_del_alamillo.png', '', 0),
(142, 'Pataplán', ' N-320a carretera de Valencia Km.134.', '', 'Cuenca', ' Juan José González (Presidente del Club) - tfn: 639776502', '	 Javier Martínez (Tesorero y Webmaster) - tfn: 605914763', '', 'N40.033819,W2.11415', 'http://www.agilitypataplan.es/', 'info@agilitypataplan.es', '', '', '', 'pataplan.png', '', 0),
(143, 'Patas', 'Ameixoada, 28C', '36954 Moaña (Pontevedra)', 'Pontevedra', '+ 34 986 31 10 13', '+ 34 659 01 36 68 ', '', '', 'http://patas.blogaliza.org/', 'agilitypatas@hotmail.com', '', '', '', 'patas.png', '', 0),
(144, 'Paterna', 'Cid Campeador, 12-13', '46980 Paterna (Valencia)', 'Valencia/Valéncia', '+ 34 677 72 27 30', '', '', '', 'http://www.agilitypaterna.com/', 'info@agilitypaterna.com', '', '', '', 'paterna.png', '', 0),
(145, 'Pican', 'Marqués de Dos Aguas, 39, p. 8', '46220 Picassent (Valencia)', 'Valencia/Valéncia', '+ 34 687 70 69 90', '', '', '', '', 'clubagilitypican@gmail.com', '', '', '', 'pican.png', '', 0),
(146, 'Pura Vida', 'Almendro, 3, Bl. 2, 1º C', '28710 El Molar (Madrid)', 'Madrid', ' + 34 680 19 08 42', '', '', '', 'http://www.mascotaspuravida.es/', 'agility@mascotaspuravida.es', '', '', '', 'pura_vida.png', '', 0),
(147, 'Santa Quiteria', '08410 Vilanova del Valles (Barcelona)', '', 'Barcelona', '+ 34 651 89 21 07', '', '', '', 'http://www.agilitysantaquiteria.es/', 'info@agilitysantaquiteria.es', '', '', '', 'santa_quiteria.png', '', 0),
(148, 'Star Can', 'Avda. del Vincle, 28', '03560 Campello (Alicante)', 'Alicante/Alacant', '+ 34 96 563 01 60', '(Ana Alonso) anastarcan@hotmail.com', '', 'W 0º 33´ 03" N 38º 22´31"', 'http://www.starcan.es/', 'nsoler@starcan.es', '', '', '', 'star_can.png', '', 0),
(149, 'Talavera', 'Cervera - Local 39', '45600 Talavera de la Reina (Toledo)', 'Toledo', '+ 34 610 01 79 75 ', '', '', '', '', 'reinaboxtalavera@hotmail.com', '', '', '', 'talavera.png', '', 0),
(150, 'Tandem', 'Carretera M413 km 8,5 de Arroyomolinos a Moraleja de Enmedio', 'Madrid.', 'Madrid', '687 964891', '627 964845', '', '', '', 'informacion@agilitytandem.es', '', '', '', 'tandem.png', '', 0),
(151, 'Tercans', 'Reibon, 192-A  -  Meira', '36955 Moaña (Pontevedra)', 'Pontevedra', ' + 34 617 34 07 63 ', '', '', '', '', 'tercans@gmail.com', '', '', '', 'tercans.png', '', 0),
(152, 'Torrevieja', 'Moriones, 43, 3º E', '03182 Torrevieja (Alicante)', 'Alicante/Alacant', '+ 34 635 41 30 31', '', '', '', '', 'info.agilitytorrevieja@gmail.com', '', '', '', 'torrevieja.png', '', 0),
(153, 'Toskahua', 'Cmno Santa Pau, 233 Garrapinillos-ZARAGOZA', '', 'Zaragoza', 'Tel.- 976-780583 movil.-666-436111', '', '', '', 'http://perso.wanadoo.es/vjuan/agility.htm', 'grupotoskahua@eresmas.com', '', '', '', 'toskaua.png', '', 0),
(154, 'Valles Club Cani', '08140 Caldes de Montbui (Barcelona)', '', 'Barcelona', ' telèfon:    619 28 68 82 ', '', '', ' N 41º 37'' 07''''    E 2º 10'' 31.7''''', '', 'info@vallesgrupcani.org', '', '', '', 'valles_club_cani.png', '', 0),
(155, 'Vallgorguina', 'Vila Carina Ctra. C-61, Km. 15,5', '08471 Vallgorguina (Barcelona)', 'Barcelona', '+ 34 93 867 93 18 ', '+ 34 600 00 54 99 ', '', '', '', 'agilityvallgorguina@centrecani.cat', '', '', '', 'vallgorguina.png', '', 0),
(156, 'Vila-Real', 'Padre Lluis María Llop, 54, 1º B', '12540 Vila-real (Castellón)', 'Castellón/Castelló', '+ 34 964 52 40 09', '', '', '', 'http://www.agilityvila-real.es/', 'agilityvilareal@gmail.com', '', '', '', 'vila_real.png', '', 0),
(157, 'Vilcan', 'Nou, 20', '46270 Villanueva de Castellón (Valencia)', 'Valencia/Valéncia', '+ 34 96 245 31 81', '', '', '', '', '', '', '', '', 'vilcan.png', '', 0),
(158, 'Villena', 'Plaza El Rollo, 5', '03400 Villena (Alicante)', 'Alicante/Alacant', '+ 34 636 42 67 13', '', '', '', 'http://clubagilityvillena.blogspot.com.es/', '', '', '', '', 'villena.png', '', 0),
(159, 'W.E.L.P.E.', 'Polideportivo Municipal La Canaleja', 'Alcorcón', 'Madrid', '+ 34 91 619 52 79', NULL, NULL, NULL, 'http://www.grupowelpe.com', 'gwelpe@teleline.es', 'https://www.facebook.com/groups/484854411592829/', NULL, '@gwelpe', 'welpe.png', NULL, 0),
(160, 'Xanastur', ' Baleares, 39, 3º D', '33208 Gijón (Asturias)', 'Asturias', '+ 34 607 11 90 56', '', '', '', 'http://www.xanastur.org/', 'xanasturcentrocanino@gmail.com', '', '', '', 'xanastur.png', '', 0),
(161, 'Zampican', 'Río Navía, 2', '12006 Castellón', 'Castellón/Castelló', '+ 34 629 07 06 75', '', '', '', 'http://www.agilityzampican.es/', '', '', '', '', 'zampican.png', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Equipos`
--
-- Creación: 10-03-2014 a las 12:45:41
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
-- Creación: 10-03-2014 a las 12:45:39
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
-- Creación: 10-03-2014 a las 13:25:46
--

DROP TABLE IF EXISTS `Guias`;
CREATE TABLE IF NOT EXISTS `Guias` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Telefono` varchar(16) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Club` int(4) NOT NULL DEFAULT '1',
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Guias_Nombre` (`Nombre`),
  KEY `Guias_Club` (`Club`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=930 ;

--
-- RELACIONES PARA LA TABLA `Guias`:
--   `Club`
--       `Clubes` -> `ID`
--

--
-- Volcado de datos para la tabla `Guias`
--

INSERT INTO `Guias` (`ID`, `Nombre`, `Telefono`, `Email`, `Club`, `Observaciones`) VALUES
(1, '-- Sin asignar --', NULL, NULL, 1, 'NO BORRAR. Valor por defecto cuando un perro se define por primera vez'),
(466, 'Aaron Laro', NULL, NULL, 107, NULL),
(467, 'Ada Serrano', '', '', 147, ''),
(468, 'Adoración Morales', NULL, NULL, 125, NULL),
(469, 'Adrian Bajo', '', '', 156, ''),
(470, 'Adrian Díaz', NULL, NULL, 111, NULL),
(471, 'Adrian Martínez', '', '', 142, ''),
(472, 'Adrián Soria', '', '', 142, ''),
(473, 'Africa Cabañas', '', '', 102, ''),
(474, 'Agustin Centelles', NULL, NULL, 122, NULL),
(475, 'Agustín González', '', '', 123, ''),
(476, 'Aida Al-Nehlawi', NULL, NULL, 93, NULL),
(477, 'Alaitz Idarraga', '', '', 116, ''),
(478, 'Albert Ulldemolins', NULL, NULL, 87, NULL),
(479, 'Alberto Alonso', NULL, NULL, 104, NULL),
(480, 'Alberto Costas', NULL, NULL, 155, NULL),
(481, 'Alberto González', '', '', 108, ''),
(482, 'Alberto Marugan', '', '', 84, ''),
(483, 'Alberto Mudarra', NULL, NULL, 122, NULL),
(484, 'Alberto Pereda', NULL, NULL, 128, NULL),
(485, 'Alejandra Alvarez', '', '', 111, ''),
(486, 'Alejandro Piñeiro', NULL, NULL, 151, NULL),
(487, 'Alejandro Rodríguez Villalta', '', '', 139, ''),
(488, 'Alejandro Salas', '', '', 160, ''),
(489, 'Alex del Río', NULL, NULL, 98, NULL),
(490, 'Alex Olivera', NULL, NULL, 147, NULL),
(491, 'Alex Sabini', NULL, NULL, 98, NULL),
(492, 'Alfredo Ortíz', NULL, NULL, 120, NULL),
(493, 'Alicia Mejias', NULL, NULL, 141, NULL),
(494, 'Alicia Sanjurjo', NULL, NULL, 154, NULL),
(495, 'Almudena Novo', '', '', 96, ''),
(496, 'Amparo Roig', NULL, NULL, 123, NULL),
(497, 'Ana Alonso', NULL, NULL, 148, NULL),
(498, 'Ana Baeza', NULL, NULL, 120, NULL),
(499, 'Ana Beltran', NULL, NULL, 112, NULL),
(500, 'Ana Isabel Escobar', NULL, NULL, 108, NULL),
(501, 'Ana Mateu', NULL, NULL, 93, NULL),
(502, 'Ana Ontañon', NULL, NULL, 132, NULL),
(503, 'Ana Palet', '', '', 108, ''),
(504, 'Ana Valencia', '', '', 84, ''),
(505, 'Andrea García', NULL, NULL, 156, NULL),
(506, 'Andrés Gimeno', NULL, NULL, 125, NULL),
(507, 'Andrés López', NULL, NULL, 151, NULL),
(508, 'Andres Morillas Sanjuan', '', '', 110, ''),
(509, 'Angel Corroto', '', '', 115, ''),
(510, 'Angel Fernández', '', '', 107, ''),
(511, 'Angel González', '', '', 84, ''),
(512, 'Angel Insa', NULL, NULL, 122, NULL),
(513, 'Angel Puertolas', NULL, NULL, 121, NULL),
(514, 'Angel Rubio', '', '', 119, ''),
(515, 'Angeles Abad', NULL, NULL, 122, NULL),
(516, 'Angelica Castaño', NULL, NULL, 115, NULL),
(517, 'Ankie Kleijberg', NULL, NULL, 148, NULL),
(518, 'Anna Aguilella', NULL, NULL, 104, NULL),
(519, 'Antje Lippold', NULL, NULL, 101, NULL),
(520, 'Antonio Carmona', NULL, NULL, 101, NULL),
(521, 'Antonio Fernández', NULL, NULL, 102, NULL),
(522, 'Antonio López', '', '', 110, ''),
(523, 'Antonio Molina', NULL, NULL, 96, NULL),
(524, 'Antonio Ojeda', NULL, NULL, 95, NULL),
(525, 'Antonio Santos', '', '', 147, ''),
(526, 'Antonio Tovar', '', '', 122, ''),
(527, 'Arabia Vidal', NULL, NULL, 157, NULL),
(528, 'Araceli Montero', NULL, NULL, 87, NULL),
(529, 'Aracelis Rodríguez', NULL, NULL, 98, NULL),
(530, 'Arcadio Nohales', NULL, NULL, 161, NULL),
(531, 'Ariadna Soriano', NULL, NULL, 122, NULL),
(532, 'Arianna Bucci', NULL, NULL, 130, NULL),
(533, 'Arturo Conejera', NULL, NULL, 102, NULL),
(534, 'Asier García', '', '', 116, ''),
(535, 'Astrid Hoffmeister', NULL, NULL, 131, NULL),
(536, 'Barbara Flemming', NULL, NULL, 148, NULL),
(537, 'Beatriz Juan', '', '', 123, ''),
(538, 'Belén de Carvalho', '', '', 110, ''),
(539, 'Berit Kittel', '', '', 119, ''),
(540, 'Carles Fortuny', NULL, NULL, 98, NULL),
(541, 'Carlos Alvarez', NULL, NULL, 151, NULL),
(542, 'Carlos Casado', NULL, NULL, 148, NULL),
(543, 'Carlos Iglesias', NULL, NULL, 155, NULL),
(544, 'Carlos Martínez', NULL, NULL, 115, NULL),
(545, 'Carlos Pérez', '', '', 128, ''),
(546, 'Carlos Pulpón', '', '', 115, ''),
(547, 'Carlos Serra', NULL, NULL, 118, NULL),
(548, 'Carmen Alos', NULL, NULL, 93, NULL),
(549, 'Carmen Antequera', NULL, NULL, 123, NULL),
(550, 'Carmen Briceño', NULL, NULL, 122, NULL),
(551, 'Carmen Sotomayor', NULL, NULL, 115, NULL),
(552, 'Carmen Vázquez', NULL, NULL, 130, NULL),
(553, 'Carolina Verdú', NULL, NULL, 158, NULL),
(554, 'Celeste Zarzosa', NULL, NULL, 128, NULL),
(555, 'Celso Valle', NULL, NULL, 132, NULL),
(556, 'Cesar Losada', NULL, NULL, 115, NULL),
(557, 'Charly Castañer', '', '', 119, ''),
(558, 'Clara Ruisánchez', '', '', 160, ''),
(559, 'Concepción Fernández', NULL, NULL, 128, NULL),
(560, 'Concepción López', NULL, NULL, 129, NULL),
(561, 'Cristian Verde', NULL, NULL, 151, NULL),
(562, 'Cristina Blanco', '', '', 107, ''),
(563, 'Cristina Cortijo', '', '', 115, ''),
(564, 'Cristina García', NULL, NULL, 124, NULL),
(565, 'Cristina González', '', '', 155, ''),
(566, 'Cristina Pedraz', '', '', 82, ''),
(567, 'Cristina Ruill', '', '', 124, ''),
(568, 'Cristofol Albert', NULL, NULL, 123, NULL),
(569, 'Damian Alarcon', NULL, NULL, 110, NULL),
(570, 'Daniel Amigo', NULL, NULL, 87, NULL),
(571, 'Daniel Luna', NULL, NULL, 120, NULL),
(572, 'Daniel Menéndez', NULL, NULL, 111, NULL),
(573, 'David Alique', NULL, NULL, 108, NULL),
(574, 'David Asenjo', '', '', 122, ''),
(575, 'David Escribano', '', '', 107, ''),
(576, 'David Ferrer', NULL, NULL, 87, NULL),
(577, 'David Flix', NULL, NULL, 147, NULL),
(578, 'David Gómez-Calcerrada', '', '', 128, ''),
(579, 'David Gonzalbo', NULL, NULL, 111, NULL),
(580, 'David Molina', '', '', 122, ''),
(581, 'David Parejo', NULL, NULL, 97, NULL),
(582, 'David Sepulveda', NULL, NULL, 87, NULL),
(583, 'Debra Howard', NULL, NULL, 128, NULL),
(584, 'Diana Cozar', NULL, NULL, 93, NULL),
(585, 'Diana García', NULL, NULL, 141, NULL),
(586, 'Diego Rouco', NULL, NULL, 143, NULL),
(587, 'Dolores López', NULL, NULL, 158, NULL),
(588, 'Dolores Sampedro', NULL, NULL, 161, NULL),
(589, 'Eduard Bonet', NULL, NULL, 87, NULL),
(590, 'Eduard Giralt Canadell', '', '', 147, ''),
(591, 'Eduardo Adán', '', '', 119, ''),
(592, 'Efren Lucas', NULL, NULL, 148, NULL),
(593, 'Elena Alberich', NULL, NULL, 93, NULL),
(594, 'Elena Chinchilla', NULL, NULL, 123, NULL),
(595, 'Elena Miguel', '', '', 115, ''),
(596, 'Elena Sin', NULL, NULL, 122, NULL),
(597, 'Elisenda Huidobro', NULL, NULL, 93, NULL),
(598, 'Elvira Pertierra', '', '', 160, ''),
(599, 'Emilio Calvo', NULL, NULL, 111, NULL),
(600, 'Emilio José Pedrazuela', NULL, NULL, 110, NULL),
(601, 'Enma Gutiérrez', '', '', 140, ''),
(602, 'Enric García', NULL, NULL, 161, NULL),
(603, 'Enric Lleixa', NULL, NULL, 87, NULL),
(604, 'Enrique Alonso Queija', '', '', 110, ''),
(605, 'Enrique Camarero', '', '', 115, ''),
(606, 'Enrique Grau', NULL, NULL, 104, NULL),
(607, 'Enrique Herbera', NULL, NULL, 87, NULL),
(608, 'Enrique Lleixa', NULL, NULL, 87, NULL),
(609, 'Enrique Sendra', NULL, NULL, 151, NULL),
(610, 'Ernesto Sorribes', NULL, NULL, 161, NULL),
(611, 'Estefanía Pérez', NULL, NULL, 110, NULL),
(612, 'Estíbaliz Pereda Navarro', '', '', 108, ''),
(613, 'Estíbaliz Pujana', '', '', 123, ''),
(614, 'Eugenio Villares', NULL, NULL, 107, NULL),
(615, 'Eva Grau', NULL, NULL, 144, NULL),
(616, 'Eva Vázquez Morales', '', '', 122, ''),
(617, 'Fermin Gil', NULL, NULL, 123, NULL),
(618, 'Fernando Benet', NULL, NULL, 161, NULL),
(619, 'Fernando Bibián', '', '', 84, ''),
(620, 'Fernando Cardeña', '', '', 160, ''),
(621, 'Fernando De La Fuente', '', '', 122, ''),
(622, 'Fito Rodríguez', '', '', 160, ''),
(623, 'Francisco Aguilera', NULL, NULL, 97, NULL),
(624, 'Francisco de la Cruz', NULL, NULL, 96, NULL),
(625, 'Francisco Esteban', NULL, NULL, 104, NULL),
(626, 'Francisco Javier Jaen', NULL, NULL, 108, NULL),
(627, 'Francisco Javier Luque', NULL, NULL, 141, NULL),
(628, 'Francisco José Sousa', NULL, NULL, 151, NULL),
(629, 'Francisco Maestre', NULL, NULL, 123, NULL),
(630, 'Francisco Martín', NULL, NULL, 154, NULL),
(631, 'Francisco Medina', NULL, NULL, 93, NULL),
(632, 'Francisco Pérez', NULL, NULL, 133, NULL),
(633, 'Francisco Sobral', NULL, NULL, 143, NULL),
(634, 'Gabriel Gómez', '', '', 122, ''),
(635, 'Gabriel Martín', NULL, NULL, 140, NULL),
(636, 'Gema López', '', '', 115, ''),
(637, 'Gemma González', '', '', 160, ''),
(638, 'Gerard Barberá', NULL, NULL, 138, NULL),
(639, 'Gerardo Alvarez', NULL, NULL, 120, NULL),
(640, 'Gerardo González', NULL, NULL, 115, NULL),
(641, 'Gisela Solis', NULL, NULL, 124, NULL),
(642, 'Gorka Pozuelo', '', '', 123, ''),
(643, 'Gregorio Conde', NULL, NULL, 87, NULL),
(644, 'Gustavo Deus', '', '', 111, ''),
(645, 'Iago Sánchez', NULL, NULL, 151, NULL),
(646, 'Iban Cubedo', NULL, NULL, 104, NULL),
(647, 'Imanol López', NULL, NULL, 122, NULL),
(648, 'Iñaki García', '', '', 116, ''),
(649, 'Inmaculada Rubio', NULL, NULL, 115, NULL),
(650, 'Irati Diego', '', '', 123, ''),
(651, 'Irena', '', '', 122, ''),
(652, 'Irene Artacho', NULL, NULL, 96, NULL),
(653, 'Isabel Gómez', NULL, NULL, 109, NULL),
(654, 'Isabel Natera', '', '', 116, ''),
(655, 'Isabel Rodríguez', NULL, NULL, 154, NULL),
(656, 'Isidoro Vázquez', NULL, NULL, 107, NULL),
(657, 'Ismael Pérez', '', '', 93, ''),
(658, 'Israel Díaz', '', '', 102, ''),
(659, 'Israel Fernández', NULL, NULL, 155, NULL),
(660, 'Ivan Amez Alvarez', '', '', 160, ''),
(661, 'Iván Pardo García', '', '', 155, ''),
(662, 'Iván San Antonio', NULL, NULL, 96, NULL),
(663, 'Iván Sánchez García', '', '', 128, ''),
(664, 'Jacqueline Holemans', NULL, NULL, 147, NULL),
(665, 'Jaime Gamir', NULL, NULL, 88, NULL),
(666, 'Jara Pérez', '', '', 84, ''),
(667, 'Jaume Fernández', NULL, NULL, 98, NULL),
(668, 'Javier Gómez', NULL, NULL, 145, NULL),
(669, 'Javier Iniesta', '', '', 155, ''),
(670, 'Javier López', NULL, NULL, 147, NULL),
(671, 'Javier Martín', '', '', 84, ''),
(672, 'Javier Martínez', '', '', 142, ''),
(673, 'Javier Mora Canales', '', '', 102, ''),
(674, 'Javier Ovejero', NULL, NULL, 115, NULL),
(675, 'Javier Sanchis', NULL, NULL, 132, NULL),
(676, 'Javier Santisteban', '', '', 96, ''),
(677, 'Jenifer Tolín', '', '', 128, ''),
(678, 'Jennifer Tolín', NULL, NULL, 128, NULL),
(679, 'Jenny Funcke', NULL, NULL, 87, NULL),
(680, 'Jerónimo Martínez', NULL, NULL, 148, NULL),
(681, 'Jessica Graciano', '', '', 128, ''),
(682, 'Jesús Crespo', NULL, NULL, 135, NULL),
(683, 'Jesús Cuellar', NULL, NULL, 115, NULL),
(684, 'Jesús Gómez', '', '', 84, ''),
(685, 'Jesús Manuel Romero', NULL, NULL, 139, NULL),
(686, 'Joan Castillo', '', '', 132, ''),
(687, 'Joan Wenceslao Pastor', NULL, NULL, 154, NULL),
(688, 'Joaquín Andrés', '', '', 84, ''),
(689, 'Jonathan Guillen', NULL, NULL, 157, NULL),
(690, 'Jordi Boix', NULL, NULL, 93, NULL),
(691, 'Jordi Gómez', NULL, NULL, 93, NULL),
(692, 'Jorge Arcas Perales', '', '', 122, ''),
(693, 'Jorge Bala', '', '', 122, ''),
(694, 'Jorge Muñoz Leal', '', '', 107, ''),
(695, 'Jorge Valero', NULL, NULL, 122, NULL),
(696, 'José Angel Beired', '', '', 153, ''),
(697, 'José Angel Torres', NULL, NULL, 151, NULL),
(698, 'José Antonio Encinas', NULL, NULL, 102, NULL),
(699, 'José Antonio Pascual', NULL, NULL, 122, NULL),
(700, 'José Antonio Vega', NULL, NULL, 84, NULL),
(701, 'José Carlos Iglesias', NULL, NULL, 155, NULL),
(702, 'José Castaño', NULL, NULL, 154, NULL),
(703, 'José Francisco Martorell', NULL, NULL, 145, NULL),
(704, 'José Guix', NULL, NULL, 145, NULL),
(705, 'José Luis García', NULL, NULL, 161, NULL),
(706, 'Jose Luis J. Mori', '', '', 82, ''),
(707, 'José Luis Prieto', NULL, NULL, 118, NULL),
(708, 'José Luis Quiroga', NULL, NULL, 101, NULL),
(709, 'José Luis Romero', NULL, NULL, 107, NULL),
(710, 'José Luis Sogorb', NULL, NULL, 125, NULL),
(711, 'José Mahillo', NULL, NULL, 128, NULL),
(712, 'José Manuel Basco', NULL, NULL, 139, NULL),
(713, 'José Manuel Linares', NULL, NULL, 152, NULL),
(714, 'José Martí', NULL, NULL, 123, NULL),
(715, 'José Mateo Moreno', NULL, NULL, 101, NULL),
(716, 'José Miguel Agustín', NULL, NULL, 84, NULL),
(717, 'José Miguel Morant', NULL, NULL, 128, NULL),
(718, 'José Miguel Paredes', NULL, NULL, 152, NULL),
(719, 'José Moreno', NULL, NULL, 106, NULL),
(720, 'José Pavon', NULL, NULL, 109, NULL),
(721, 'José Peris', NULL, NULL, 144, NULL),
(722, 'Jose Ramón López', '', '', 115, ''),
(723, 'José Santos Luna', NULL, NULL, 120, NULL),
(724, 'José Soliño', NULL, NULL, 151, NULL),
(725, 'Josep Barbera', NULL, NULL, 138, NULL),
(726, 'Josep Mª Pineda', NULL, NULL, 154, NULL),
(727, 'Juan', '', '', 83, ''),
(728, 'Juan Antonio Martinez', NULL, 'juansgaviota@gmail.com', 159, NULL),
(729, 'Juan Campin', NULL, NULL, 128, NULL),
(730, 'Juan Carlos Blas', NULL, NULL, 115, NULL),
(731, 'Juan Carlos Companys', NULL, NULL, 104, NULL),
(732, 'Juan Carlos Hinojal', NULL, NULL, 140, NULL),
(733, 'Juan Carlos Redondo', '', '', 150, ''),
(734, 'Juan Carlos Ruiz', '', '', 115, ''),
(735, 'Juan del Amo', NULL, NULL, 120, NULL),
(736, 'Juan Escos', '', '', 129, ''),
(737, 'Juan Francisco Pelegrin', NULL, NULL, 129, NULL),
(738, 'Juan Francisco Torres', NULL, NULL, 87, NULL),
(739, 'Juan José Espadas', NULL, NULL, 93, NULL),
(740, 'Juan José González', '', '', 142, ''),
(741, 'Juan José Paz', '', '', 92, ''),
(742, 'Juan Luis Colmano', NULL, NULL, 122, NULL),
(743, 'Juan Manuel Caballo', NULL, NULL, 123, NULL),
(744, 'Juan Martín de las Blancas', '', '', 107, ''),
(745, 'Juan Miguel Cifuentes', NULL, NULL, 123, NULL),
(746, 'Juan Pablo Díaz', NULL, NULL, 107, NULL),
(747, 'Juan Pedro Martínez', NULL, NULL, 95, NULL),
(748, 'Juan Rodríguez', '', '', 128, ''),
(749, 'Juan Solanes', NULL, NULL, 157, NULL),
(750, 'Judith Cortes', NULL, NULL, 122, NULL),
(751, 'Judith Franco', '', '', 96, ''),
(752, 'Judith Herms', NULL, NULL, 98, NULL),
(753, 'Julia Faci Green', '', '', 122, ''),
(754, 'Julian Sánchez', NULL, NULL, 154, NULL),
(755, 'Julio Freire', '', '', 140, ''),
(756, 'Katia Moeller', NULL, NULL, 94, NULL),
(757, 'Katy Navarro', NULL, NULL, 140, NULL),
(758, 'Laura Carrasco', NULL, NULL, 98, NULL),
(759, 'Laura Chiva', NULL, NULL, 87, NULL),
(760, 'Laura Monrabal', '', '', 122, ''),
(761, 'Leire Herrera', '', '', 116, ''),
(762, 'Lorena Díez', NULL, NULL, 128, NULL),
(763, 'Lorena García', NULL, NULL, 140, NULL),
(764, 'Lorena Gargoles', NULL, NULL, 93, NULL),
(765, 'Lourdes Giménez', NULL, NULL, 111, NULL),
(766, 'Lourdes Peñarrocha', NULL, NULL, 148, NULL),
(767, 'Lourdes Rivera', '', '', 128, ''),
(768, 'Lucía Montalbán', '', '', 122, ''),
(769, 'Lucía Romero', '', '', 139, ''),
(770, 'Luciano Fernández', '', '', 128, ''),
(771, 'Luis Alberto Pereira', NULL, NULL, 140, NULL),
(772, 'Luis Carlos Sanchez', NULL, NULL, 84, NULL),
(773, 'Luis de Frías', '', '', 128, ''),
(774, 'Luis Ignacio Carazo', NULL, NULL, 154, NULL),
(775, 'Luis Luque', NULL, NULL, 155, NULL),
(776, 'Luis Miguel Rodrigo', '', '', 123, ''),
(777, 'Luis Miguel Rodriguez', '', '', 128, ''),
(778, 'Luisa Fernanda Millan', '', '', 142, ''),
(779, 'Luna Ramírez', NULL, NULL, 132, NULL),
(780, 'Maitane Luengo', '', '', 116, ''),
(781, 'Maite Guerrero', '', '', 115, ''),
(782, 'Manel Martínez', NULL, NULL, 154, NULL),
(783, 'Manuel Basco', NULL, NULL, 139, NULL),
(784, 'Manuel Jesús García', NULL, NULL, 89, NULL),
(785, 'Manuel Lara', NULL, NULL, 98, NULL),
(786, 'Manuel peña', '', '', 160, ''),
(787, 'Manuel Santomé', NULL, NULL, 151, NULL),
(788, 'Mar Bermúdez', NULL, NULL, 90, NULL),
(789, 'Marc Rabada', NULL, NULL, 124, NULL),
(790, 'Marco Maldonado', '', '', 119, ''),
(791, 'Marcos Martínez', NULL, NULL, 145, NULL),
(792, 'María López', NULL, NULL, 154, NULL),
(793, 'Maria Nogueira', '', '', 105, ''),
(794, 'Marina López', NULL, NULL, 98, NULL),
(795, 'Mario Rodríguez', NULL, NULL, 159, NULL),
(796, 'Marisa Jarabo', NULL, NULL, 159, NULL),
(797, 'Marta de la Rosa', NULL, NULL, 161, NULL),
(798, 'Marta Gregorio', NULL, NULL, 151, NULL),
(799, 'Marta Jiménez', '', '', 84, ''),
(800, 'Marta Ponce', '', '', 122, ''),
(801, 'Marta Sánchez', NULL, NULL, 107, NULL),
(802, 'Marta Solar', '', '', 111, ''),
(803, 'Massimiliano Miggiano', NULL, NULL, 155, NULL),
(804, 'Matias Monleón', NULL, NULL, 123, NULL),
(805, 'Matias Rodríguez', NULL, NULL, 155, NULL),
(806, 'Mayte Pérez', '', '', 160, ''),
(807, 'Menchu Melcom', '', '', 108, ''),
(808, 'Mercedes Fernández', NULL, NULL, 148, NULL),
(809, 'Michael Volkert', NULL, NULL, 130, NULL),
(810, 'Miguel Angel Fernández', NULL, NULL, 160, NULL),
(811, 'Miguel Angel García', NULL, NULL, 115, NULL),
(812, 'Miguel Angel Morales', NULL, NULL, 84, NULL),
(813, 'Miguel Angel Soriano', NULL, NULL, 148, NULL),
(814, 'Miguel García Rodríguez', '', '', 151, ''),
(815, 'Mireia Carrascoso', NULL, NULL, 124, NULL),
(816, 'Miriam García', NULL, NULL, 138, NULL),
(817, 'Mónica Muñiz', NULL, NULL, 110, NULL),
(818, 'Mónica Rodríguez', '', '', 82, ''),
(819, 'Mónica Saavedra', NULL, NULL, 135, NULL),
(820, 'Monica Zaballa', '', '', 140, ''),
(821, 'Montserrat Calvet', NULL, NULL, 87, NULL),
(822, 'María José Manzano', NULL, NULL, 122, NULL),
(823, 'Narciso Leita', NULL, NULL, 129, NULL),
(824, 'Natalia Cuadrado', NULL, NULL, 101, NULL),
(825, 'Natividad Ruiz García', '', '', 110, ''),
(826, 'Natividad Soler', NULL, NULL, 148, NULL),
(827, 'Neus Baró', NULL, NULL, 93, NULL),
(828, 'Noelia Gimeno', NULL, NULL, 144, NULL),
(829, 'Noelia Mouchet', '', '', 155, ''),
(830, 'Nuria Alonso', NULL, NULL, 98, NULL),
(831, 'Nuria Costa', NULL, NULL, 154, NULL),
(832, 'Nuria Díez', '', '', 160, ''),
(833, 'Nuria Fortuny', NULL, NULL, 98, NULL),
(834, 'Nuria Morell Nadal', '', '', 154, ''),
(835, 'Olga Palomares', '', '', 108, ''),
(836, 'Oria Micó', NULL, NULL, 158, NULL),
(837, 'Oscar Bravo', NULL, NULL, 101, NULL),
(838, 'Oscar Muñiz', NULL, NULL, 110, NULL),
(839, 'Oscar Reboredo', NULL, NULL, 98, NULL),
(840, 'Oscar Sacristan', NULL, NULL, 128, NULL),
(841, 'Pablo Ballesta', NULL, NULL, 90, NULL),
(842, 'Pablo Miró', NULL, NULL, 161, NULL),
(843, 'Paloma Faci Green', '', '', 122, ''),
(844, 'Pau Serrano Ciratusa', '', '', 122, ''),
(845, 'Paula de Lucas', NULL, NULL, 107, NULL),
(846, 'Paula Rello', NULL, NULL, 107, NULL),
(847, 'Paulino Iranzo', NULL, NULL, 145, NULL),
(848, 'Pedro Delgado Fernandez', '', '', 110, ''),
(849, 'Pedro Jesús Tazón', NULL, NULL, 140, NULL),
(850, 'Pedro Martínez', '', '', 116, ''),
(851, 'pepe', '', 'pepe@pepe.com', 83, ''),
(852, 'pepepepe', '', '', 83, ''),
(853, 'Pilar Collado', '', '', 119, ''),
(854, 'Pilar Matesanz', NULL, NULL, 159, NULL),
(855, 'Pilar Rodríguez', NULL, NULL, 140, NULL),
(856, 'Rachel Stevens', NULL, NULL, 138, NULL),
(857, 'Rafael Altava', NULL, NULL, 104, NULL),
(858, 'Rafael Camacho', NULL, NULL, 155, NULL),
(859, 'Rafael Fernández', NULL, NULL, 145, NULL),
(860, 'Rafael García', NULL, NULL, 156, NULL),
(861, 'Rafael Torregrosa', NULL, NULL, 104, NULL),
(862, 'Ramón Arribas', '', '', 82, ''),
(863, 'Ramón García', '', '', 96, ''),
(864, 'Raquel Frago', NULL, NULL, 129, NULL),
(865, 'Raquel Garrido', NULL, NULL, 107, NULL),
(866, 'Raúl Sánchez', NULL, NULL, 107, NULL),
(867, 'Remedios Torres', NULL, NULL, 148, NULL),
(868, 'Reyes García', NULL, NULL, 111, NULL),
(869, 'Ricardo Benito', NULL, NULL, 159, NULL),
(870, 'Ricardo Martínez', NULL, NULL, 109, NULL),
(871, 'Ricardo Santolaya', NULL, NULL, 122, NULL),
(872, 'Roberto Castro', NULL, NULL, 115, NULL),
(873, 'Roberto Iñigo', NULL, NULL, 96, NULL),
(874, 'Roberto Reina Vega', '', '', 128, ''),
(875, 'Rocio Hermelo', NULL, NULL, 151, NULL),
(876, 'Rocio Santos', NULL, NULL, 141, NULL),
(877, 'Rodrigo González', '', '', 128, ''),
(878, 'Roque Alonso', '', '', 120, ''),
(879, 'Ross Rubio', '', '', 108, ''),
(880, 'Ruben Jurado', NULL, NULL, 124, NULL),
(881, 'Ruben Lopera', NULL, NULL, 133, NULL),
(882, 'Ruben Montero', NULL, NULL, 128, NULL),
(883, 'Sabina González', NULL, NULL, 130, NULL),
(884, 'Salvador Martí', NULL, NULL, 147, NULL),
(885, 'Sandra Gracia', '', '', 122, ''),
(886, 'Sara Bellido', NULL, NULL, 94, NULL),
(887, 'Sara Lara', '', '', 123, ''),
(888, 'Sara Montila', NULL, NULL, 108, NULL),
(889, 'Sara Montoya', NULL, NULL, 115, NULL),
(890, 'Sebastian González', NULL, NULL, 87, NULL),
(891, 'Sergio Casalins', NULL, NULL, 110, NULL),
(892, 'Sergio Colomé', NULL, NULL, 122, NULL),
(893, 'Sergio García', NULL, NULL, 156, NULL),
(894, 'Sergio Martín', NULL, NULL, 122, NULL),
(895, 'Sergio Romeo', '', '', 122, ''),
(896, 'Sergio Ruiz', NULL, NULL, 102, NULL),
(897, 'Sergio Tella', '', '', 129, ''),
(898, 'Sheila Giménez', '', '', 122, ''),
(899, 'Silvia León', NULL, NULL, 161, NULL),
(900, 'Silvia Perea', '', '', 159, ''),
(901, 'Silvia Rodríguez', NULL, NULL, 154, NULL),
(902, 'Sofía Díaz', '', '', 147, ''),
(903, 'Sonia Asensio', NULL, NULL, 90, NULL),
(904, 'Sonia Conejero', NULL, NULL, 158, NULL),
(905, 'Stefan Eggenschwiler', NULL, NULL, 152, NULL),
(906, 'Stina Sandquist', NULL, NULL, 97, NULL),
(907, 'Tamara Vidal', NULL, NULL, 124, NULL),
(908, 'Tomás Pérez', NULL, NULL, 122, NULL),
(909, 'Toni Rios', NULL, NULL, 93, NULL),
(910, 'Ubaldo Delgado', NULL, NULL, 90, NULL),
(911, 'Valentín de la Mesa', NULL, NULL, 104, NULL),
(912, 'Vanessa Calpe', NULL, NULL, 156, NULL),
(913, 'Vanessa Hermoso', NULL, NULL, 89, NULL),
(914, 'Verónica Díez Gómez', '', '', 140, ''),
(915, 'Verónica Fernández', '', '', 160, ''),
(916, 'Verónica Ibañez', NULL, NULL, 97, NULL),
(917, 'Verónica Rodríguez', NULL, NULL, 128, NULL),
(918, 'Vicente Cambra', NULL, NULL, 126, NULL),
(919, 'Vicente Martín', '', '', 84, ''),
(920, 'Vicente Micó', NULL, NULL, 158, NULL),
(921, 'Vicente Villalba', NULL, NULL, 123, NULL),
(922, 'Victor García', NULL, NULL, 133, NULL),
(923, 'Virginia García', '', '', 110, ''),
(924, 'Wladimiro', '', '', 122, ''),
(925, 'Xavier López', NULL, NULL, 147, NULL),
(926, 'Yolanda Larena', NULL, NULL, 138, NULL),
(927, 'Yolanda Moreno', NULL, NULL, 123, NULL),
(928, 'Yolanda Torres', NULL, NULL, 132, NULL),
(929, 'Yulia Morugova', '', '', 112, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inscripciones`
--
-- Creación: 10-03-2014 a las 12:45:42
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
--       `Perros` -> `ID`
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
-- Creación: 10-03-2014 a las 12:45:41
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
-- Creación: 10-03-2014 a las 12:45:39
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49 ;

--
-- Volcado de datos para la tabla `Jueces`
--

INSERT INTO `Jueces` (`ID`, `Nombre`, `Direccion1`, `Direccion2`, `Telefono`, `Internacional`, `Practicas`, `Email`, `Observaciones`) VALUES
(2, '-- Sin asignar --', NULL, NULL, '--- -- -- --', 0, 0, 'nobody@nomail.com', 'NO BORRAR: Asignacion de juez por defecto'),
(26, 'Beltrán Bustamante, Ana', 'Camí del Camp, 23', '03460 Beneixama (Alicante)', '639 67 86 09', 0, 1, 'sadda_874@hotmail.com', NULL),
(27, 'Boix Balaguer, Josep', 'Sant Pere, 10', '08392 San Andreu de Llavaneres (Barcelona)', ' 93 792 76 55', 1, 0, 'josep@agilitycanic.cat', NULL),
(28, 'Conde Delgado, Gregorio', NULL, NULL, ' 93 389 35 83 / 619 39 39 28', 1, 0, 'gconde@xtec.cat', NULL),
(29, 'Correa Arqueros, Mariano', 'Avda. de Moratalaz, 178, 2º A', '28030 Madrid', ' 91 301 47 58', 1, 0, 'mariano.correa@nsn.com', NULL),
(30, 'Diez Pérez, Esteban', 'Ocaña, 104, Bajo', '28047 Madrid', '91 465 50 05', 1, 0, 'estebanagility@gmail.com', NULL),
(31, 'Escalera Salamanca, Javier de la', 'Avda. de San Luis, 95, 2º D', '28033 Madrid', '91 767 07 45 / 607 43 34 13', 1, 0, 'javierdelae@gmail.com', NULL),
(32, 'Felix Fuentes, José', 'Vinaroz, 19', '18100 Armilla (Granada)', '958 55 05 74 / 617 93 54 32', 0, 0, NULL, NULL),
(33, 'García Alvarez, José Luis', 'Río Navia, 2', '12006 Castellón', '629 07 06 75', 1, 0, 'zampican@yahoo.es', NULL),
(34, 'García Rudilla, Juan', 'Rulda, 12', '03400 Villena (Alicante)', ' 96 580 30 63 / 652 83 08 85', 1, 0, NULL, NULL),
(35, 'Garrido Fuentes, Manuel', 'Paseo de los Olivos, 10', '28330 San Martín de la Vega (Madrid)', ' 91 894 60 96', 1, 0, 'mgfcom@telefonica.net', NULL),
(36, 'Gil Solis, Esperanza', 'Paseo de los Olivos, 10', '28330 San Martín de la Vega (Madrid)', ' 91 894 60 96', 1, 0, 'agilcan@telefonica.net', NULL),
(37, 'Guerra Guerra, Juan Pedro', 'Mariano Martín, 9', '10161 Arroyomolinos (Cáceres)', ' 927 28 82 56', 1, 0, 'jaracar@ono.com', NULL),
(38, 'Humanes Almonte, Miguel Angel', 'Zamora, 23, 3º C', '28941 Fuenlabrada (Madrid)', '607 70 55 75', 1, 0, 'agilblack@hotmail.com', NULL),
(39, 'Lanzó, Juan Antonio', 'Recinto Ferial, 2, 4º A', '36540 Silleda (Pontevedra)', ' 629 50 71 76 / 986 57 70 00', 1, 0, 'tonylanzo@gmail.com', NULL),
(40, 'Linares García, José Manuel', 'Carril de los Córdobas, 2', '30161 Llano de Brujas (Murcia)', '968 85 12 60 / 696 75 07 67', 0, 1, 'jomaliga1@gmail.com', NULL),
(41, 'Muñiz Martínez, Oscar', 'León Felipe, 31, 4º, 1ª', '28942 Fuenlabrada (Madrid)', '665 79 86 49', 0, 1, 'agilityzeus@gmail.com', NULL),
(42, 'Navarro Costas, Jordi', 'Santiago Rusiñol, 90', '08340 Vilassar de Mar (Barcelona)', '93 759 70 54 / 609 30 97 10', 1, 0, 'jordinuc@telefonica.net', NULL),
(43, 'Parejo Carregalo, Manuel', 'Ctra. Valle de Abdalajis, Km. 1,7', '29260 La Joya (Málaga)', '95 270 26 04', 0, 1, 'losparejos@hotmail.com', NULL),
(44, 'Pineda Puig, Josep Mª', 'Sant Ramón, 69, B-3', '08140 Caldes de Montbui (Barcelona)', '93 865 46 16 / 678 43 36 45', 0, 0, 'pepagility@movistar.es', NULL),
(45, 'Poble Rosas, Ramón', 'Jaume I, 18', '08140 Caldes de Montbui (Barcelona)', '93 865 20 32', 1, 0, 'pobleramon@gmail.com', NULL),
(46, 'Rodríguez Matesanz, Mario', 'Plaza del Peñón, 10', '28923 Alcorcón (Madrid)', ' 91 619 52 79', 1, 0, 'gwelpe@terra.es', NULL),
(47, 'Santome González, Manuel', 'Fonte da Tella, 133 - A - Moureira - Meira', '36955 Moaña (Pontevedra)', '986 31 27 77 / 607 83 20 53', 0, 1, 'lolosantome@gmail.com', NULL),
(48, 'Ulldemolins Santisteve, Albert', 'Llorer, 28, Casa 4', '08415 Bigues I Riells (Barcelona)', ' 93 865 89 64 / 636 96 33 77', 0, 1, 'albert23m@hotmail.com', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Mangas`
--
-- Creación: 10-03-2014 a las 12:45:42
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
--       `Jueces` -> `ID`
--   `Juez2`
--       `Jueces` -> `ID`
--   `Jornada`
--       `Jornadas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `PerroGuiaClub`
--
DROP VIEW IF EXISTS `PerroGuiaClub`;
CREATE TABLE IF NOT EXISTS `PerroGuiaClub` (
`Perro` int(4)
,`Nombre` varchar(255)
,`Raza` varchar(255)
,`Licencia` varchar(255)
,`LOE_RRC` varchar(255)
,`Categoria` varchar(1)
,`Grado` varchar(16)
,`Guia` varchar(255)
,`Club` varchar(255)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Perros`
--
-- Creación: 10-03-2014 a las 13:25:33
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
  `Grado` varchar(16) DEFAULT '-',
  PRIMARY KEY (`ID`),
  KEY `Perros_GuiaNombre` (`Guia`),
  KEY `Perros_Categoria` (`Categoria`),
  KEY `Perros_Grado` (`Grado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1110 ;

--
-- RELACIONES PARA LA TABLA `Perros`:
--   `Categoria`
--       `Categorias_Perro` -> `Categoria`
--   `Grado`
--       `Grados_Perro` -> `Grado`
--   `Guia`
--       `Guias` -> `ID`
--

--
-- Volcado de datos para la tabla `Perros`
--

INSERT INTO `Perros` (`ID`, `Nombre`, `Raza`, `LOE_RRC`, `Licencia`, `Categoria`, `Guia`, `Grado`) VALUES
(555, 'Yuma', 'P.B.Malinoise', '1936256', 'A330', 'L', 745, 'GIII'),
(556, 'Yuma', 'P.B.Malinoise', '1936256', 'A330', 'L', 745, 'GIII'),
(557, 'Hannibal', '', '1764520', 'A090', 'L', 908, 'GIII'),
(558, 'Ardi', '', '79097', '729', 'L', 843, '-'),
(559, 'William', NULL, '1667920', '920', 'L', 679, '-'),
(560, 'Xonny', NULL, '1317156', '622', 'L', 556, '-'),
(561, 'Indiana Jones', '', '1720531', '987', 'L', 737, 'GIII'),
(562, 'Thelma', '', '1515702', '824', 'L', 871, 'GIII'),
(563, 'Boss', '', '1528991', '797', 'L', 838, 'GIII'),
(564, 'Lee', 'Border Collie', '95245', 'A084', 'L', 523, 'GIII'),
(565, 'Chinouk', NULL, '1390419', '724', 'L', 809, '-'),
(566, 'Angie', NULL, '1370168', '691', 'L', 523, '-'),
(567, 'Burundi', '', '1874262', 'A310', 'L', 594, 'GIII'),
(568, 'Piter', '', '110594', 'A360', 'L', 568, 'GIII'),
(569, 'Napa', '', '1975832', 'A401', 'L', 843, 'GIII'),
(570, 'Gon', '', '1725855', 'A024', 'L', 787, 'GIII'),
(571, 'Valerie', '', '1467667', '786', 'L', 782, 'GIII'),
(572, 'Woman', '', '1866186', 'A206', 'L', 673, 'GIII'),
(573, 'Baloo', NULL, '86974', '991', 'L', 853, '-'),
(574, 'Piter Winers', NULL, '100338', 'A188', 'L', 704, '-'),
(575, 'Lula', NULL, '1891977', 'A344', 'L', 513, '-'),
(576, 'Karen', NULL, '1970258', 'A427', 'L', 496, '-'),
(577, 'Runa', '', '112361', 'A347', 'L', 490, 'GIII'),
(578, 'Chiruca', '', '1635759', '986', 'L', 521, 'GII'),
(579, 'Moss', '', '113891', 'A391', 'L', 873, 'GIII'),
(580, 'Nena', NULL, '1521753', '930', 'L', 753, '-'),
(581, 'Deby', 'BorderCollie', '101610', 'A147', 'L', 556, 'GIII'),
(582, 'Noah', '', '1887262', 'A268', 'L', 921, 'GIII'),
(583, 'Sil', NULL, '1831356', 'A150', 'L', 787, '-'),
(584, 'Furia', '', '1554907', '892', 'L', 833, 'GIII'),
(585, 'Mc Coy', 'Border Collie', '1905162', 'A322', 'L', 551, 'GIII'),
(586, 'Argi', '', '110120', 'A241', 'L', 783, 'GIII'),
(587, 'Lua', NULL, '118441', 'A327', 'L', 713, '-'),
(588, 'Zoe', '', '109748', 'A289', 'L', 801, 'GIII'),
(589, 'Juice', NULL, '117997', 'A387', 'L', 539, '-'),
(590, 'Vega', NULL, '1552296', '855', 'L', 496, '-'),
(591, 'Idris', NULL, '83909', '880', 'L', 823, '-'),
(592, 'Izar', NULL, '1596718', '851', 'L', 630, '-'),
(593, 'Pica', '', '104103', 'A091', 'L', 480, 'GIII'),
(594, 'Nana', '', '1780849', 'A073', 'L', 705, 'GIII'),
(595, 'Tara', NULL, 'No tiene', '1466', 'L', 839, '-'),
(596, 'Finn', '', '2074557', 'A596', 'L', 856, 'GIII'),
(597, 'Rocky', '', '1796496', 'A355', 'L', 533, 'GIII'),
(598, 'Neil', 'Flat Coated Retriever', '122117', 'A417', 'L', 656, '-'),
(599, 'Asia', 'Border Collie', '1958017', 'A364', 'L', 709, 'GIII'),
(600, 'Xodro', 'BorderCollie', '1959124', 'A371', 'L', 544, 'GIII'),
(601, 'Bec', NULL, '87712', '979', 'L', 714, '-'),
(602, 'Bely', NULL, '1370171', '692', 'L', 632, '-'),
(603, 'Laia', '', '1594320', '921', 'L', 659, 'GIII'),
(604, 'Bamba', '', '1887258', 'A242', 'L', 505, 'GII'),
(605, 'Akane', 'Euskal A Txakurra', '101429', 'A249', 'L', 640, 'GIII'),
(606, 'Mister', 'Border Collie', '99971', 'A250', 'L', 516, 'GII'),
(607, 'Spyro', NULL, '89457', 'A155', 'L', 513, '-'),
(608, 'Becho', NULL, '1831350', 'A203', 'L', 486, '-'),
(609, 'Luna', NULL, '1798021', 'A163', 'L', 920, '-'),
(610, 'Buh', '', '1929147', 'A297', 'L', 853, 'GIII'),
(611, 'Aby', 'Border Collie', '105806', 'A204', 'L', 874, 'GIII'),
(612, 'Yuma', 'Border Collie', '113067', 'A385', 'L', 771, 'GIII'),
(613, 'Zak', NULL, '1831579', 'A160', 'L', 842, '-'),
(614, 'Tanga', 'Border Collie', '119678', 'A366', 'L', 889, 'GIII'),
(615, 'Viconte', 'Mudi', '1561797', '813', 'L', 777, 'GII'),
(616, 'Dela', '', '2028765', 'A377', 'L', 775, 'GII'),
(617, 'Brus', NULL, '1763613', 'A140', 'L', 581, '-'),
(618, 'King', '', '1520008', '856', 'L', 817, 'GIII'),
(619, 'Brujostel', 'Border Collie', '1717447', 'A231', 'L', 929, 'GIII'),
(620, 'Kora', 'P.B.Malinoise', '111627', 'A332', 'L', 848, 'GII'),
(621, 'Rubia', NULL, '127474', 'A481', 'L', 776, '-'),
(622, 'Fito', '', '127473', 'A529', 'L', 776, 'GIII'),
(623, 'Visente', NULL, '116863', 'A359', 'L', 847, '-'),
(624, 'Maia', NULL, '1780846', 'A132', 'L', 530, '-'),
(625, 'Mecha', 'P.B.Malinoise', '129549', 'A558', 'L', 698, 'GIII'),
(626, 'Ari', NULL, '1893230', 'A301', 'L', 518, '-'),
(627, 'Aslan', '', '1970223', 'A457', 'L', 646, 'GIII'),
(628, 'Mambo', '', '1392048', '753', 'L', 590, 'GIII'),
(629, 'Nut', '', '120681', 'A430', 'L', 818, 'GII'),
(630, 'Xena', NULL, '1570666', 'A118', 'L', 716, '-'),
(631, 'Fiona', NULL, '2010068', 'A491', 'L', 857, '-'),
(632, 'Winner', 'Border Collie', '127815', 'A497', 'L', 871, 'GII'),
(633, 'Jade', 'skal A Txakurra', '120128', 'A522', 'L', 640, 'GIII'),
(634, 'Dasher', '', '2000291', 'A411', 'L', 737, 'GIII'),
(635, 'Gaston', NULL, '1717449', 'A340', 'L', 632, '-'),
(636, 'Pipo', NULL, '118271', 'A458', 'L', 859, '-'),
(637, 'Grey', NULL, '1717450', 'A066', 'L', 528, '-'),
(638, 'Maty', 'Border Collie', '1964043', 'A333', 'L', 563, 'GII'),
(639, 'Dylan', 'Border Collie', '127444', 'A461', 'L', 663, 'GIII'),
(640, 'Magia', NULL, '1753228', 'A104', 'L', 553, '-'),
(641, 'Lia', NULL, '1842317', 'A389', 'L', 899, '-'),
(642, 'Coma', NULL, '1957039', 'A538', 'L', 584, '-'),
(643, 'Isis', '', '1443353', '838', 'L', 497, 'GIII'),
(644, 'Otto', NULL, '91641', 'A070', 'L', 860, '-'),
(645, 'Nana', '', '1781905', 'A225', 'L', 607, 'GII'),
(646, 'Assucar', 'Border Collie', '1594318', '992', 'L', 733, 'GII'),
(647, 'Fantastico', NULL, '109814', 'A218', 'L', 844, '-'),
(648, 'Panda', 'Border Collie', '1936233', 'A474', 'L', 762, 'GII'),
(649, 'Raissa', 'American Stanford', '131570', 'A563', 'L', 669, 'GII'),
(650, 'Nuka', NULL, '127265', 'A486', 'L', 491, '-'),
(651, 'Halcon', '', '126805', 'A482', 'L', 547, 'GIII'),
(652, 'California', '', '1967653', 'A414', 'L', 692, 'GII'),
(653, 'Astra', NULL, '1551831', '783', 'L', 486, '-'),
(654, 'Fito', '', '1980971', 'A490', 'L', 899, 'GII'),
(655, 'Magic-Black', NULL, '101184', 'A318', 'L', 865, '-'),
(656, 'Blacky', NULL, 'No tiene', '1493', 'L', 794, '-'),
(657, 'Red Magic', 'Border Collie', '1834052', 'A124', 'L', 544, 'GII'),
(658, 'Beauty', '', '120728', 'A452', 'L', 844, 'GIII'),
(659, 'Savannah', NULL, '1627377', '833', 'L', 502, '-'),
(660, 'Ra', '', '2009048', 'A494', 'L', 787, 'GII'),
(661, 'Flay', NULL, '123186', 'A418', 'L', 643, '-'),
(662, 'Bu', NULL, '111416', 'A519', 'L', 861, '-'),
(663, 'Heidy', NULL, '1727593', 'A035', 'L', 894, '-'),
(664, 'Kiko', NULL, '1780843', 'A145', 'L', 610, '-'),
(665, 'Koba', NULL, '1533440', '843', 'L', 647, '-'),
(666, 'Kiwi', NULL, '1908098', 'A267', 'L', 647, '-'),
(667, 'Yun', 'Border Collie', '2001179', 'A484', 'L', 559, 'GII'),
(668, 'Nora', NULL, '80601', '735', 'L', 901, '-'),
(669, 'Liss', NULL, '1779665', 'A212', 'L', 581, '-'),
(670, 'Rayko', 'Border Collie', '2027590', 'A321', 'L', 845, 'GIII'),
(671, 'Nani', NULL, '1838888', 'A238', 'L', 750, '-'),
(672, 'Rasca', '', '2047380', 'A564', 'L', 480, 'GIII'),
(673, 'Abby', '', '2104382', 'A533', 'L', 679, '-'),
(674, 'Merlin', NULL, '1996593', 'A523', 'L', 585, '-'),
(675, 'Rusti', '', '1831356', 'A227', 'L', 628, 'GII'),
(676, 'Cora', '', 'No tiene', '1525', 'L', 512, 'GII'),
(677, 'Dux', '', '1727100', 'A134', 'L', 633, 'GIII'),
(678, 'Urko', NULL, '127390', 'A565', 'L', 543, '-'),
(679, 'Blues', NULL, '1756737', 'A083', 'L', 791, '-'),
(680, 'Lia', NULL, 'No tiene', '1481', 'L', 766, '-'),
(681, 'Broc', NULL, '1596717', '902', 'L', 726, '-'),
(682, 'Bell', NULL, '1831577', 'A205', 'L', 893, '-'),
(683, 'Colombo', NULL, '119957', 'A437', 'L', 623, '-'),
(684, 'Cooper', NULL, '1942046', 'A468', 'L', 683, '-'),
(685, 'Dunja', NULL, '131504', 'A568', 'L', 519, '-'),
(686, 'Nova', NULL, '1472107', '765', 'L', 831, '-'),
(687, 'Nuca', NULL, '105491', 'A431', 'L', 858, '-'),
(688, 'Nora', 'Braco', '113894', 'A356', 'L', 866, 'GII'),
(689, 'Kimi', NULL, '1908103', 'A271', 'L', 571, '-'),
(690, 'Samba', NULL, '131663', 'A608', 'L', 542, '-'),
(691, 'Liss', NULL, '1988114', 'A429', 'L', 588, '-'),
(692, 'Nuka', NULL, 'No tiene', '1418', 'L', 708, '-'),
(693, 'Samy', NULL, '120731', 'A432', 'L', 691, '-'),
(694, 'Kim', NULL, '125522', 'A511', 'L', 489, '-'),
(695, 'Blues', NULL, 'No tiene', '1400', 'L', 735, '-'),
(696, 'Naia', NULL, '1970259', 'A459', 'L', 927, '-'),
(697, 'Aragorn', NULL, '1568043', 'A380', 'L', 922, '-'),
(698, 'Aizu', NULL, '1680441', 'A499', 'L', 757, '-'),
(699, 'Avemar', NULL, '1677435', 'A335', 'L', 784, '-'),
(700, 'Completa', NULL, '95707', 'A121', 'L', 844, '-'),
(701, 'Jayna', '', '1779666', 'A173', 'L', 699, 'GII'),
(702, 'Kira', '', '1710175', 'A116', 'L', 604, 'GIII'),
(703, 'Elfo', NULL, '1496681', '963', 'L', 724, '-'),
(704, 'Choco', NULL, '96941', 'A058', 'L', 541, '-'),
(705, 'Gini', NULL, '1891331', 'A193', 'L', 756, '-'),
(706, 'Alinka', NULL, '1677160', '982', 'L', 916, '-'),
(707, 'Zazpi', NULL, '1779668', 'A278', 'L', 581, '-'),
(708, 'Frasqui', NULL, '1962362', 'A438', 'L', 623, '-'),
(709, 'Yhara', NULL, '1827258', 'A357', 'L', 752, '-'),
(710, 'Lennon', 'A. Foxhound', '111486', 'A336', 'L', 811, 'GII'),
(711, 'Irma', NULL, '1779789', 'A096', 'L', 596, '-'),
(712, 'Yai', NULL, '1879217', 'A475', 'L', 506, '-'),
(713, 'Clara', 'Border Collie', '1936237', 'A462', 'L', 882, 'GII'),
(714, 'Laika', NULL, '93988', 'A224', 'L', 864, '-'),
(715, 'Argon', '', '1926695', '987', 'L', 560, 'GII'),
(716, 'Nube', NULL, '124447', 'A465', 'L', 712, '-'),
(717, 'Blue', '', '1863534', 'A552', 'L', 757, 'GII'),
(718, 'Hana', NULL, 'No tiene', '1526', 'L', 876, '-'),
(719, 'Liss', NULL, '86748', '961', 'L', 701, '-'),
(720, 'Tara', NULL, '123013', 'A442', 'L', 680, '-'),
(721, 'Onis', '', '1478689', 'A498', 'L', 700, 'GII'),
(722, 'Qumba', NULL, '2007589', 'A546', 'L', 739, '-'),
(723, 'Jotave', NULL, '1962366', 'A407', 'L', 743, '-'),
(724, 'Kora', NULL, '124215', 'A585', 'L', 743, '-'),
(725, 'Zana', NULL, '1634512', 'A014', 'L', 894, '-'),
(726, 'Hugo', NULL, '97357', 'A201', 'L', 710, '-'),
(727, 'Gus', NULL, '111335', 'A548', 'L', 918, '-'),
(728, 'Paco', NULL, '85033', 'A550', 'L', 918, '-'),
(729, 'Thor', NULL, '1839333', 'A348', 'L', 721, '-'),
(730, 'Fol', '', '86747', '940', 'L', 814, 'GIII'),
(731, 'Fito', NULL, '1914405', 'A362', 'L', 628, '-'),
(732, 'Troy', '', '98617', 'A044', 'L', 834, 'GII'),
(733, 'Dana', NULL, 'No tiene', '1528', 'L', 805, '-'),
(734, 'Doña Sol', NULL, '1885107', 'A261', 'L', 749, '-'),
(735, 'Martina', NULL, '1833796', 'A545', 'L', 593, '-'),
(736, 'Kiss', NULL, '1626167', '912', 'L', 524, '-'),
(737, 'Nano', NULL, '94485', '964', 'L', 743, '-'),
(738, 'Ariel', NULL, '1835675', 'A213', 'L', 1, '-'),
(739, 'Cristina', NULL, '1929150', 'A352', 'L', 531, '-'),
(740, 'Rulo', NULL, '1676914', '978', 'L', 579, '-'),
(741, 'Hechizada', NULL, '1663984', 'A064', 'L', 599, '-'),
(742, 'Cindy', NULL, '1812230', 'A189', 'L', 470, '-'),
(743, 'Rotten', NULL, 'No tiene', '1518', 'L', 707, '-'),
(744, 'Pixie Moon', '', '1879218', 'A260', 'L', 710, '-'),
(745, 'Timba', '', '119645', 'A403', 'L', 552, 'GIII'),
(746, 'Nuwa', '', '132677', 'A597', 'L', 907, 'GII'),
(747, 'Kora', NULL, '99230', 'A466', 'L', 685, '-'),
(748, 'Kona', NULL, '1986188', 'A524', 'L', 517, '-'),
(749, 'Arwen', NULL, '1753230', 'A151', 'L', 561, '-'),
(750, 'Blas', NULL, '1761207', 'A086', 'L', 718, '-'),
(751, 'Rudy', NULL, '102435', 'A113', 'L', 774, '-'),
(752, 'Ira', 'P.B.Malinoise', '101193', 'A141', 'L', 482, 'GII'),
(753, 'Terra', NULL, '114522', 'A328', 'L', 738, '-'),
(754, 'Kira', NULL, '120733', 'A405', 'L', 476, '-'),
(755, 'Nube', NULL, '97289', 'A515', 'L', 709, '-'),
(756, 'Mara', NULL, '80787', 'A396', 'L', 500, '-'),
(757, 'Trasto', NULL, 'No tiene', '1391', 'L', 470, '-'),
(758, 'Kiria', '', 'No tiene', 'A155', 'L', 695, 'GII'),
(759, 'Wind', 'Border Collie', '1881696', 'A421', 'L', 880, 'GII'),
(760, 'Rocky', NULL, '1756734', 'A156', 'L', 703, '-'),
(761, 'Pepo', NULL, '1756735', 'A200', 'L', 703, '-'),
(762, 'Mina', NULL, '124583', 'A501', 'L', 668, '-'),
(763, 'Gipsy', NULL, 'No tiene', '1542', 'L', 670, '-'),
(764, 'Atril', NULL, '1985153', 'A454', 'L', 798, '-'),
(765, 'Max', NULL, '1945286', 'A487', 'L', 507, '-'),
(766, 'N''Hug', '', '110043', 'A283', 'L', 702, 'GII'),
(767, 'Che', NULL, '1472786', 'A313', 'L', 749, '-'),
(768, 'Chincheta', NULL, '97802', 'A165', 'L', 903, '-'),
(769, 'Nemo', NULL, '1950998', 'A478', 'L', 606, '-'),
(770, 'Tao', NULL, '1807243', 'A554', 'L', 888, '-'),
(771, 'Luna', '', 'No tiene', '1539', 'L', 870, 'GII'),
(772, 'Molsa', NULL, '1710467', 'A103', 'L', 883, '-'),
(773, 'Dina', 'Border Collie', '1798019', 'A136', 'L', 672, 'GII'),
(774, 'Neo', 'Border Collie', '99972', 'A077', 'L', 522, 'GIII'),
(775, 'Zidane', NULL, '2081050', 'A591', 'L', 483, '-'),
(776, 'Poly', NULL, '2060633', 'A587', 'L', 710, '-'),
(777, 'Kinder', '', '101433', 'A223', 'L', 864, 'GII'),
(778, 'Juno', NULL, 'No tiene', '1552', 'L', 532, '-'),
(779, 'Flash', NULL, '109519', 'A420', 'L', 638, '-'),
(780, 'Troya', NULL, '127255', 'A527', 'L', 925, '-'),
(781, 'Pluto', NULL, '80602', '785', 'L', 792, '-'),
(782, 'Poli', NULL, '98654', 'A290', 'L', 901, '-'),
(783, 'Shasta', 'Border Collie', '109487', 'A272', 'L', 795, 'GII'),
(784, 'Kira', NULL, '83038', '789', 'L', 784, '-'),
(785, 'Maya', NULL, '1683945', 'A110', 'L', 747, '-'),
(786, 'Grace', NULL, '127031', 'A575', 'L', 839, '-'),
(787, 'Artemisa', NULL, '1911726', 'A406', 'L', 913, '-'),
(788, 'Ron', NULL, '94045', 'A265', 'L', 596, '-'),
(789, 'Maña', NULL, '1872301', 'A423', 'L', 550, '-'),
(790, 'Blue', NULL, '86743', 'A180', 'L', 586, '-'),
(791, 'Golfa', NULL, '1719195', 'A557', 'L', 664, '-'),
(792, 'Runa', NULL, '2006235', 'A537', 'L', 766, '-'),
(793, 'Fanta', '', '1940535', 'A544', 'L', 826, 'GII'),
(794, 'Xula', NULL, '83896', '844', 'M', 753, '-'),
(795, 'Danko', '', '113906', 'A325', 'M', 694, 'GIII'),
(796, 'Hanna', NULL, '1843070', 'A159', 'M', 833, '-'),
(797, 'Guti', 'Cocker Spaniel', '111666', 'A215', 'M', 674, 'GIII'),
(798, 'Cala', NULL, '1691264', 'A095', 'M', 479, '-'),
(799, 'Milo', NULL, '1399802', '718', 'M', 733, '-'),
(800, 'Gotika', NULL, '1682493', '971', 'M', 531, '-'),
(801, 'Drac', '', '106327', 'A196', 'M', 597, 'GIII'),
(802, 'Neo', NULL, '109522', 'A445', 'M', 638, '-'),
(803, 'Sra. Maruja', NULL, '1695088', '997', 'M', 608, '-'),
(804, 'Duna', '', '93084', '953', 'M', 625, 'GII'),
(805, 'Kiwi', '', '1982934', 'A525', 'M', 914, 'GIII'),
(806, 'Sucre', NULL, '1558711', '938', 'M', 830, '-'),
(807, 'Lass', 'Perro de Aguas Español', '131981', 'A580', 'M', 614, 'GIII'),
(808, 'Tuna', NULL, '1731780', 'A057', 'M', 783, '-'),
(809, 'Norai', NULL, '97039', 'A015', 'M', 618, '-'),
(810, 'Menta', '', '1459964', '767', 'M', 478, 'GIII'),
(811, 'Wirbel', 'Schnnauzer', '1252941', '588', 'M', 795, 'Ret.'),
(812, 'Pepsi', '', '1849505', 'A270', 'M', 540, 'GII'),
(813, 'Kiss', NULL, '1258632', '762', 'M', 570, '-'),
(814, 'Ina', NULL, 'No tiene', '1549', 'M', 910, '-'),
(815, 'Kenia', NULL, '86609', '954', 'M', 867, '-'),
(816, 'Gamma', '', '1988761', 'A307', 'M', 690, 'GIII'),
(817, 'Goku', 'Fox Terrier Wire ', 'No tiene', '1513', 'M', 730, 'GIII'),
(818, 'Coockie', NULL, '120745', 'A434', 'M', 909, '-'),
(819, 'Habana', NULL, '131393', 'A634', 'M', 611, '-'),
(820, 'Dau', NULL, '113713', 'A446', 'M', 830, '-'),
(821, 'Jolie', 'Mudi', '1561798', '808', 'M', 554, 'GII'),
(822, 'Boira', '', '1809307', 'A281', 'M', 564, 'GIII'),
(823, 'Queen', NULL, '93971', '989', 'M', 742, '-'),
(824, 'Legend', NULL, '1606337', '927', 'M', 904, '-'),
(825, 'Koku', NULL, '1394913', '714', 'M', 836, '-'),
(826, 'Luna', '', '103250', 'A240', 'M', 846, 'GII'),
(827, 'Nell', NULL, '1329279', 'A293', 'M', 616, '-'),
(828, 'Lucas', 'Podenco Andaluz', '112275', 'A248', 'M', 484, 'GII'),
(829, 'Benavis', NULL, '1803327', 'A195', 'M', 493, '-'),
(830, 'Paquita', NULL, '1852290', 'A229', 'M', 841, '-'),
(831, 'Fey', NULL, '2063400', 'A440', 'M', 906, '-'),
(832, 'Robbi', NULL, '104953', 'A146', 'M', 912, '-'),
(833, 'Mate', 'Schnauzer', '1930526', 'A469', 'M', 573, 'GII'),
(834, 'Veron', '', '1423605', 'A412', 'M', 616, 'GII'),
(835, 'Lume', NULL, '1985134', 'A416', 'M', 697, '-'),
(836, 'Foska', NULL, '108199', 'A182', 'M', 749, '-'),
(837, 'Bimba', NULL, '1968121', 'A451', 'M', 808, '-'),
(838, 'Chocolate', NULL, '93486', 'A045', 'M', 903, '-'),
(839, 'Tuna', 'Bodeguero Andaliuz', '122113', 'A408', 'M', 694, 'GII'),
(840, 'Naru', NULL, '132678', 'A598', 'M', 907, '-'),
(841, 'Mizar', NULL, '1876817', 'A274', 'M', 609, '-'),
(842, 'Tina', NULL, 'No tiene', '1532', 'M', 665, '-'),
(843, 'Kenya', NULL, '112354', 'A233', 'M', 758, '-'),
(844, 'Duque', NULL, 'No tiene', '1551', 'M', 493, '-'),
(845, 'Harley', NULL, '2025374', 'A428', 'M', 592, '-'),
(846, 'Johnny Cash', '', '2057033', 'A594', 'M', 920, 'GIII'),
(847, 'Striptease', '', '2023589', 'A577', 'M', 548, 'GII'),
(848, 'Tuco', NULL, '2026054', 'A419', 'M', 649, '-'),
(849, 'Mia', NULL, '121232', 'A547', 'M', 804, '-'),
(850, 'Gunilla', NULL, 'No tiene', '1471', 'M', 779, '-'),
(851, 'Alma', NULL, '90313', 'A566', 'M', 765, '-'),
(852, 'Noa', '', '1926077', 'A526', 'M', 855, 'GII'),
(853, 'Magia', '', '2032179', 'A528', 'S', 550, 'GIII'),
(854, 'Saroa', '', '1789456', 'A149', 'S', 928, 'GIII'),
(855, 'Melendi', '', '1842276', 'A164', 'S', 775, 'GIII'),
(856, 'Mims', NULL, '102903', 'A279', 'S', 474, '-'),
(857, 'Hancock', NULL, '131702', 'A609', 'S', 813, '-'),
(858, 'Tris', NULL, '119441', 'A398', 'S', 875, '-'),
(859, 'Lula', NULL, '123192', 'A607', 'S', 582, '-'),
(860, 'Rufo', NULL, '123178', 'A435', 'S', 721, '-'),
(861, 'Sira', '', '106345', 'A168', 'S', 576, 'GIII'),
(862, 'Nit', '', '112007', 'A208', 'S', 821, 'GIII'),
(863, 'Xira', '', '124731', 'A424', 'S', 896, 'GIII'),
(864, 'Che Guevara', 'Caniche', '112448', 'A230', 'S', 929, 'GIII'),
(865, 'Enzo', NULL, '117909', 'A444', 'S', 816, '-'),
(866, 'Nuca', 'Tibetan Spaniel', '109471', 'A181', 'S', 661, 'GIII'),
(867, 'Gismo', NULL, '123726', 'A449', 'S', 905, '-'),
(868, 'Nana', NULL, '103211', 'A277', 'S', 888, '-'),
(869, 'Nikita', '', '123006', 'A443', 'S', 497, 'GIII'),
(870, 'Xena', '', '125524', 'A509', 'S', 602, 'GIII'),
(871, 'Chula', '', '106335', 'A135', 'S', 657, 'GIII'),
(872, 'Dagga', NULL, '127338', 'A507', 'S', 645, '-'),
(873, 'Greta', NULL, '1849716', 'A337', 'S', 536, '-'),
(874, 'Bengel', 'Schnnauzer', '1433208', '760', 'S', 795, 'GII'),
(875, 'Nei', NULL, '1400011', '770', 'S', 655, '-'),
(876, 'Tess', NULL, '102439', 'A245', 'S', 667, '-'),
(877, 'Lia', 'Mestizo', '132000', 'A588', 'S', 652, 'GII'),
(878, 'Taca', '', '128455', 'A589', 'S', 501, 'GII'),
(879, 'Miche', '', '1706141', 'A097', 'S', 785, 'GII'),
(880, 'Manin', NULL, '104120', 'A100', 'S', 875, '-'),
(881, 'Doña Matilde', '', '2005359', 'A514', 'S', 603, 'GIII'),
(882, 'Aqua', NULL, '2056249', 'A513', 'S', 759, '-'),
(883, 'Nuca', 'Schnauzer', '1678476', 'A088', 'S', 466, 'GII'),
(884, 'Pepa', 'Jack Rusell', '1957259', 'A393', 'S', 624, 'GII'),
(885, 'Spyro', '', '131527', 'A569', 'S', 837, 'GIII'),
(886, 'Della', NULL, '2061744', 'A467', 'S', 570, '-'),
(887, 'Lua', NULL, '116884', 'A287', 'S', 731, '-'),
(888, 'Mayo', NULL, 'No tiene', '1419', 'S', 828, '-'),
(889, 'Nala', NULL, '108635', 'A530', 'S', 577, '-'),
(890, 'Lola', NULL, 'No tiene', '1464', 'S', 675, '-'),
(891, 'Noah', NULL, '131506', 'A590', 'S', 691, '-'),
(892, 'Sully', NULL, '116639', 'A520', 'S', 861, '-'),
(893, 'Gus', NULL, '119626', 'A346', 'S', 520, '-'),
(894, 'Lola', NULL, 'No tiene', '1541', 'S', 765, '-'),
(895, 'Thor', '', '1939205', 'A535', 'S', 466, 'GII'),
(896, 'Quillo', 'Rusky Toy', '127443', 'A604', 'S', 565, 'GII'),
(897, 'Lennon', NULL, '103239', 'A144', 'S', 819, '-'),
(898, 'Boira', NULL, 'No tiene', '1554', 'S', 884, '-'),
(899, 'Kyra', NULL, '131481', 'A600', 'S', 494, '-'),
(900, 'Acha', NULL, '123731', 'A483', 'M', 788, '-'),
(901, 'Ada', 'Mestizo', '', '1459', 'S', 862, 'GII'),
(902, 'Aker', 'P.B. Malinoise', '1553051', 'A397', 'L', 626, 'GII'),
(903, 'Akira', NULL, '125877', 'A455', 'L', 719, 'GII'),
(904, 'Dama', 'Fox Terrier Wire', '0131204', 'A641', 'M', 728, 'GIII'),
(905, 'Flai', 'Fox Terrier Wire', '0129738', 'en tramite', 'M', 728, 'GII'),
(906, 'Donna', 'Border Collie', '', '', 'L', 869, 'GII'),
(907, 'fito', 'mestizo', '', '', 'L', 851, 'GIII'),
(908, 'paco', '', '', '', '-', 1, '-'),
(909, 'Akela', 'Border Collie', '', 'A746', 'L', 619, 'GII'),
(910, 'Toska', 'Border Collie', '', '', 'L', 684, 'GI'),
(911, 'Sira', 'P.B.Malinoise', '', 'A584', 'L', 688, 'GII'),
(912, 'Duna', 'P. Aleman', '', 'A586', 'L', 919, 'GII'),
(913, 'Olivia', 'Schnauzer', '', '', 'S', 751, 'GII'),
(914, 'Kyle', 'Schnauzer', '', 'A-539', 'M', 662, 'GII'),
(915, 'Kara', 'Border Collie', '', 'A-541', 'L', 863, 'GII'),
(916, 'Tibet', 'Border Collie', '', '', 'L', 523, 'GII'),
(917, 'Beltxa', 'Schnauzer', '', 'A-622', 'S', 896, 'GII'),
(918, 'Yeni', 'Border Collie', '', '', 'L', 709, 'GI'),
(919, 'Danah', 'P. Australiano', '', '', 'L', 744, 'GI'),
(920, 'Net', 'P. Australiano', '', '', 'L', 656, 'GI'),
(921, 'Sira', 'Boxer', '', '', 'L', 575, 'GI'),
(922, 'Maggie', 'Mestizo', '', '1570', 'S', 866, 'GII'),
(923, 'Kiss', '', '', '', 'L', 666, 'GI'),
(924, 'Milka', '', '', '', 'L', 671, 'GI'),
(925, 'Mitzy', '', '', '', 'L', 511, 'GI'),
(926, 'Sura', '', '', '', 'L', 799, 'GI'),
(927, 'Queen', '', '', '', 'M', 511, 'GI'),
(928, 'Neo', '', '', '', 'L', 684, 'GII'),
(929, 'Sitan', '', '', '795', 'L', 688, 'GII'),
(930, 'Kaiser', '', '', 'A383', 'S', 666, 'GII'),
(931, 'Geha', '', '', 'A162', 'L', 772, 'GIII'),
(932, 'Momo', 'Borde Collie', '', '1593', 'L', 873, 'GII'),
(933, 'Skay', '', '', '', 'L', 676, 'GII'),
(934, 'Keko', '', '', '', 'L', 481, 'GI'),
(935, 'Mambo', 'Border Collie', '', '', 'L', 503, 'GI'),
(936, 'Swing', '', '', '', 'L', 835, 'GI'),
(937, 'Trufa', '', '', '', 'L', 879, 'GI'),
(938, 'Soma', 'Border Collie', '', 'A696', 'L', 807, 'GII'),
(939, 'Phoebe', 'Border Collie', '', 'A555', 'M', 612, 'GII'),
(940, 'Izzie', 'West Higland White Terrier', '', 'A275', 'S', 612, 'GII'),
(941, 'Kyra', 'Border Collie', '', '1607', 'L', 508, 'GII'),
(942, 'Nika', 'Border Collie', '', '', 'L', 538, 'GI'),
(943, 'Dolce', 'Border Collie', '', 'A681', 'L', 508, 'GII'),
(944, 'Dolce', '', '', 'A723', 'L', 825, 'GII'),
(945, 'Gilda', 'Border Collie', '', 'A723', 'L', 825, 'GII'),
(946, 'Noa', 'Border Collie', '', 'A540', 'L', 848, 'GII'),
(947, 'Thor', 'Border Collie', '', '', 'L', 734, 'GI'),
(948, 'Akira Haru', 'BorderCollie', '', 'A311', 'L', 636, 'GII'),
(949, 'Nala', 'BorderCollie', '', 'A142', 'L', 605, 'GIII'),
(950, 'Peka', '', '', 'A615', 'L', 723, 'GII'),
(951, 'Milady', '', '', '', 'L', 871, 'GII'),
(952, 'Yashi', 'Pastor de los Pirineos', '', '', 'M', 917, 'GI'),
(953, 'Dollar', '', '', '911', 'L', 773, 'GII'),
(954, 'Héctor', 'Pastor Vasco', '', '1597', 'L', 578, 'GII'),
(955, 'Ron', 'Border Collie', '', 'A617', 'L', 840, 'GII'),
(956, 'Viconte', '', '', 'A367', 'L', 748, 'GII'),
(957, 'Agran', 'Border Collie', '', 'A752', 'L', 472, 'GII'),
(958, 'Bimba', 'Border Collie', '', 'A288', 'L', 778, 'GII'),
(959, 'Dudy', 'Border Collie', '', 'A753', 'L', 740, 'GII'),
(960, 'Kember', 'Bóxer', '', '1558', 'L', 471, 'GII'),
(961, 'Luca', 'Ratonero Bodeguero Andaluz', '', 'A749', 'M', 565, 'GII'),
(962, 'Kika', 'West Higland White Terrier', '', 'A722', 'S', 829, 'GII'),
(963, 'Olivia', 'Caniche', '', 'A701', 'S', 566, 'GII'),
(964, 'Sombra', 'Border Collie', '', 'A679', 'L', 706, 'GII'),
(965, 'Noah', 'Schnauzer', '', '', 'S', 662, 'GI'),
(966, 'Brea', 'Mestizo', '', '', 'L', 495, 'GI'),
(967, 'Lola', 'Jack Rusell', '', 'A633', 'S', 624, 'GII'),
(968, 'Yeny', 'Border Collie', '', 'A747', 'L', 709, 'GII'),
(969, 'Sonic', 'Perro de Aguas Español', '', 'A495', 'L', 562, 'GII'),
(970, 'Wind', 'Border Collie', '', 'A561', 'L', 891, 'GII'),
(971, 'Crack', 'Border Collie', '', 'A719', 'L', 838, 'GII'),
(972, 'Nitra', 'Caniche', '', 'A743', 'S', 929, 'GII'),
(973, 'Nova', 'Border Collie', '', 'A642', 'L', 878, 'GII'),
(974, 'Momo', 'Border Collie', '', 'A621', 'L', 498, 'GII'),
(975, 'Byron', 'Border Collie', '', '', 'L', 877, 'GI'),
(976, 'Amis', 'Border Collie', '', 'A620', 'L', 711, 'GII'),
(977, 'Noa', 'BorderCollie', '', 'A143', 'L', 677, 'GII'),
(978, 'Vlad', 'Perro de Aguas Español', '', 'A752', 'L', 509, 'GII'),
(979, 'Mou', 'Kelpie Australiano', '', '', 'L', 546, 'GI'),
(980, 'Black', 'Border Collie', '', '', 'L', 722, 'GI'),
(981, 'Kala', 'Yorkshire Terrier', '', '1574', 'S', 563, 'GII'),
(982, 'Andy', 'Border Collie', '', 'A671', 'L', 781, 'GII'),
(983, 'beep', '', '', '', 'L', 856, 'GI'),
(984, 'Ella', '', '', '', 'L', 780, 'GI'),
(985, 'Nya', '', '', '', 'L', 467, 'GI'),
(986, 'Tibet', '', '', '', 'L', 567, 'GI'),
(987, 'Kenzo', '', '', '', 'L', 924, 'GI'),
(988, 'Dafne', '', '', '', 'L', 736, 'GI'),
(989, 'Nut', '', '', '', 'L', 885, 'GI'),
(990, 'Morgan', '', '', '', 'L', 597, 'GI'),
(991, 'Juke', '', '', '', 'L', 800, 'GI'),
(992, 'Argi', '', '', '', 'L', 613, 'GI'),
(993, 'Pi', '', '', '', 'L', 633, 'GI'),
(994, 'Horatio', '', '', '', 'S', 537, 'GI'),
(995, 'Goldie', '', '', '', 'S', 843, 'GI'),
(996, 'Cachirulo', '', '', 'A578', 'L', 580, 'GII'),
(997, 'Lucky', '', '', '1561', 'L', 487, 'GII'),
(998, 'Chika', '', '', 'A518', 'L', 755, 'GII'),
(999, 'Inka', '', '', 'A699', 'L', 564, 'GII'),
(1000, 'Nupsi', '', '', '', 'L', 539, 'GII'),
(1001, 'Inka', '', '', 'A381', 'L', 850, 'GII'),
(1002, 'Tiri', '', '', 'A782', 'L', 705, 'GII'),
(1003, 'Itoitz', '', '', 'A713', 'L', 477, 'GII'),
(1004, 'Xira', '', '', '1499', 'L', 469, 'GII'),
(1005, 'Gala', '', '', 'A500', 'L', 490, 'GII'),
(1006, 'Luna', '', '', '', 'L', 514, 'GII'),
(1007, 'Lorenzo', '', '', 'A666', 'L', 853, 'GII'),
(1008, 'Bombon', '', '', 'A773', 'L', 515, 'GII'),
(1009, 'Lluna', '', '', '1537', 'L', 515, 'GII'),
(1010, 'Ardi de Rioja', '', '', '0729', 'L', 692, 'GII'),
(1011, 'Danko', '', '', '1509', 'L', 574, 'GII'),
(1012, 'Tessa', '', '', 'A317', 'L', 897, 'GII'),
(1013, 'Rex', '', '', '1578', 'L', 902, 'GII'),
(1014, 'Chicaa', '', '', 'A703', 'L', 634, 'GII'),
(1015, 'Sue', '', '', 'A788', 'L', 899, 'GII'),
(1016, 'Kira', '', '', '', 'L', 557, 'GII'),
(1017, 'Heidy', '', '', 'A496', 'L', 746, 'GII'),
(1018, 'Bella', '', '', '1586', 'L', 769, 'GII'),
(1019, 'Hanna', '', '', '1584', 'L', 475, 'GII'),
(1020, 'Eo', '', '', 'A768', 'L', 787, 'GII'),
(1021, 'Panda', '', '', '', 'L', 760, 'GII'),
(1022, 'Venus', '', '', 'A743', 'L', 775, 'GII'),
(1023, 'Charly', '', '', '1592', 'L', 696, 'GII'),
(1024, 'Argi', '', '', '1600', 'L', 650, 'GII'),
(1025, 'Broto', '', '', 'A764', 'L', 895, 'GII'),
(1026, 'Anouk', '', '', 'A480', 'L', 898, 'GII'),
(1027, 'Flecha', '', '', 'A748', 'L', 887, 'GII'),
(1028, 'Heidi', '', '', 'A035', 'L', 894, 'GII'),
(1029, 'Moa', '', '', 'A508', 'L', 628, 'GII'),
(1030, 'Greta', '', '', '', 'L', 693, 'GII'),
(1031, 'Lur', '', '', 'A741', 'L', 642, 'GII'),
(1032, 'Charli', '', '', 'A655', 'L', 527, 'GII'),
(1033, 'Lia', '', '', '1563', 'L', 768, 'GII'),
(1034, 'Nika', '', '', '1590', 'S', 621, 'GII'),
(1035, 'Erinka', '', '', 'A761', 'S', 834, 'GII'),
(1036, 'Ursus', '', '', '', 'S', 648, 'GII'),
(1037, 'Ela', '', '', 'A618', 'S', 827, 'GII'),
(1038, 'Lua', '', '', 'A763', 'S', 525, 'GII'),
(1039, 'Wembley', '', '', 'A769', 'S', 540, 'GII'),
(1040, 'Rikke', '', '', '', 'S', 526, 'GII'),
(1041, 'Imo', '', '', 'A631', 'M', 693, 'GII'),
(1042, 'Alpargata', '', '', 'A606', 'M', 616, 'GII'),
(1043, 'Lucky Luque', '', '', 'A766', 'S', 659, 'GII'),
(1044, 'Salma', '', '', 'A521', 'S', 686, 'GII'),
(1045, 'Dracma', '', '', 'A626', 'S', 797, 'GII'),
(1046, 'Jade', '', '', 'A629', 'L', 689, 'GIII'),
(1047, 'Rinoa', '', '', 'A447', 'L', 789, 'GIII'),
(1048, 'Chus', '', '', '934', 'L', 689, 'GIII'),
(1049, 'Gala', '', '', 'A674', 'L', 591, 'GIII'),
(1050, 'Chamán', '', '', 'A262', 'L', 850, 'GIII'),
(1051, 'Koira', '', '', 'A252', 'L', 790, 'GIII'),
(1052, 'Kobu', '', '', '1564', 'M', 651, 'GIII'),
(1053, 'Zeus', '', '', 'A139', 'M', 789, 'GIII'),
(1054, 'Gea', '', '', 'A175', 'M', 732, 'GIII'),
(1055, 'Sacha', '', '', 'A632', 'M', 631, 'GIII'),
(1056, 'Fran', '', '', 'A028', 'M', 580, 'GIII'),
(1057, 'Peka', '', '', 'A291', 'M', 755, 'GIII'),
(1058, 'Time', '', '', 'A571', 'M', 880, 'GIII'),
(1059, 'Alma', '', '', '874', 'M', 600, 'GIII'),
(1060, 'Kira', '', '', 'A600', 'S', 494, 'GIII'),
(1061, 'Cleo', '', '', 'A152', 'S', 549, 'GIII'),
(1062, 'Luna', '', '', '0956', 'S', 549, 'GIII'),
(1063, 'Nika', '', '', 'A082', 'S', 789, 'GII'),
(1064, 'Quenn', 'Palleiro', '', '', 'M', 511, 'GI'),
(1065, 'Gala', 'Pastor Alemán', '', '', 'L', 504, 'GI'),
(1066, 'Moli', 'Jack Rusell', '', '', 'S', 658, 'GI'),
(1067, 'Vali', 'Border Collie', '', '', 'L', 473, 'GI'),
(1068, 'Isis', 'Border Collie', '', '', 'L', 521, 'GI'),
(1069, 'Darco', 'Beagle', '', '', 'M', 510, 'GI'),
(1070, 'Bruce', 'Ratonero Bodeguero Andaluz', '', '', 'M', 694, 'GI'),
(1071, 'Nut', 'Pastor Belga Malinoise', '', '', 'L', 626, 'GI'),
(1072, 'Lluna', 'Border Collie', '', '', 'L', 923, 'GI'),
(1073, 'Dunah', 'Cocker Spaniel', '', 'A635', 'M', 492, 'GII'),
(1074, 'Aska', 'Border Collie', '', '', 'L', 770, 'GI'),
(1075, 'Buck', 'Border Collie', '', '', 'L', 545, 'GI'),
(1076, 'Merche', 'Schnauzer Gigante', '', '', 'L', 767, 'GI'),
(1077, 'Bambú', 'Border Collie', '', '', 'L', 681, 'GI'),
(1078, 'King', '', '', 'A683', 'S', 727, 'GII'),
(1079, 'Toxo', '', '', 'A677', 'S', 741, 'GII'),
(1080, 'Newton', '', '', '', 'L', 601, 'GI'),
(1081, 'Cala', '', '', 'A697', 'L', 757, 'GII'),
(1082, 'Onna', '', '', '', 'L', 820, 'GII'),
(1083, 'Trasgo', '', '', 'A650', 'L', 635, 'GII'),
(1084, 'Samba', '', '', 'A702', 'M', 849, 'GII'),
(1085, 'Charlie', '', '', 'A532', 'S', 849, 'GIII'),
(1086, 'Alan', '', '', '', 'L', 654, 'GI'),
(1087, 'Moly', '', '', '', 'L', 534, 'GI'),
(1088, 'Pipa', '', '', 'A718', 'S', 761, 'GII'),
(1089, 'Chola', '', '', 'A691', 'M', 793, 'GII'),
(1090, 'Blue', '', '', '', 'S', 485, 'GI'),
(1091, 'Fly', '', '', '', 'L', 644, 'GII'),
(1092, 'Sira', '', '', '', 'L', 802, 'GII'),
(1093, 'Xhyla', '', '', 'A711', 'L', 572, 'GII'),
(1094, 'Lua', '', '', 'A689', 'M', 572, 'GII'),
(1095, 'Deva', '', '', '', 'L', 915, 'GI'),
(1096, 'Duna', '', '', '', 'L', 622, 'GI'),
(1097, 'Jefrelú', '', '', '', 'L', 558, 'GI'),
(1098, 'Lumy', '', '', '', 'L', 488, 'GI'),
(1099, 'Magui', '', '', '', 'L', 558, 'GI'),
(1100, 'Trenty', '', '', '', 'L', 786, 'GI'),
(1101, 'Xana', '', '', '', 'L', 832, 'GI'),
(1102, 'Zoe', '', '', '', 'L', 598, 'GI'),
(1103, 'Zoe', '', '', '', 'L', 620, 'GI'),
(1104, 'Eria', '', '', '', 'S', 637, 'GI'),
(1105, 'Tinka', '', '', '', 'S', 806, 'GI'),
(1106, 'Blue', '', '', '1572', 'L', 810, 'GII'),
(1107, 'Dana', '', '', 'A688', 'L', 810, 'GII'),
(1108, 'Phoebe', '', '', 'A690', 'L', 660, 'GII'),
(1109, 'Sella', '', '', '', 'L', 558, 'GII');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Provincias`
--
-- Creación: 10-03-2014 a las 12:45:38
--

DROP TABLE IF EXISTS `Provincias`;
CREATE TABLE IF NOT EXISTS `Provincias` (
  `Provincia` varchar(32) NOT NULL DEFAULT '',
  `Comunidad` varchar(32) DEFAULT NULL,
  `Codigo` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Provincia`),
  UNIQUE KEY `Codigo` (`Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Provincias`
--

INSERT INTO `Provincias` (`Provincia`, `Comunidad`, `Codigo`) VALUES
('-- Sin asignar --', 'NO BORRAR: Usado para cuando no ', 0),
('Albacete', 'Castilla - La Mancha', 2),
('Alicante/Alacant', 'Comunitat Valenciana', 3),
('Almería', 'Andalucía', 4),
('Araba/Álava', 'País Vasco', 1),
('Asturias', 'Cantabria', 33),
('Ávila', 'Castilla y León', 5),
('Badajoz', 'Extremadura', 6),
('Balears, Illes', 'Balears, Illes', 7),
('Barcelona', 'Cataluña', 8),
('Bizkaia/Vizcaya', 'País Vasco', 48),
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
('Gipuzkoa/Guipúzcoa', 'País Vasco', 20),
('Girona/Gerona', 'Cataluña', 17),
('Granada', 'Andalucía', 18),
('Guadalajara', 'Castilla - La Mancha', 19),
('Huelva', 'Andalucía', 21),
('Huesca', 'Aragón', 22),
('Jaén', 'Andalucía', 23),
('León', 'Castilla y León', 24),
('Lleida/Lérida', 'Cataluña', 25),
('Lugo', 'Galicia', 27),
('Madrid', 'Madrid, Comunidad de', 28),
('Málaga', 'Andalucía', 29),
('Melilla', 'Melilla', 52),
('Murcia', 'Murcia, Región de', 30),
('Navarra', 'Navarra, Comunidad Foral de', 31),
('Ourense/Orense', 'Galicia', 32),
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
-- Creación: 10-03-2014 a las 12:45:41
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- RELACIONES PARA LA TABLA `Pruebas`:
--   `Club`
--       `Clubes` -> `ID`
--

--
-- Volcado de datos para la tabla `Pruebas`
--

INSERT INTO `Pruebas` (`ID`, `Nombre`, `Club`, `Ubicacion`, `Triptico`, `Cartel`, `Observaciones`, `Cerrada`) VALUES
(1, '-- Sin asignar --', 1, NULL, NULL, NULL, 'NO BORRAR: Prueba por defecto para jornadas huerfanas', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Resultados`
--
-- Creación: 10-03-2014 a las 12:45:42
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
--       `Perros` -> `ID`
--   `Manga`
--       `Mangas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tipo_Manga`
--
-- Creación: 10-03-2014 a las 12:45:42
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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `PerroGuiaClub` AS select `Perros`.`ID` AS `Perro`,`Perros`.`Nombre` AS `Nombre`,`Perros`.`Raza` AS `Raza`,`Perros`.`Licencia` AS `Licencia`,`Perros`.`LOE_RRC` AS `LOE_RRC`,`Perros`.`Categoria` AS `Categoria`,`Perros`.`Grado` AS `Grado`,`Guias`.`Nombre` AS `Guia`,`Clubes`.`Nombre` AS `Club` from ((`Perros` join `Guias`) join `Clubes`) where ((`Perros`.`Guia` = `Guias`.`ID`) and (`Guias`.`Club` = `Clubes`.`ID`)) order by `Clubes`.`Nombre`,`Perros`.`Categoria`,`Perros`.`Nombre`;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Clubes`
--
ALTER TABLE `Clubes`
  ADD CONSTRAINT `Clubes_ibfk_1` FOREIGN KEY (`Provincia`) REFERENCES `Provincias` (`Provincia`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Equipos`
--
ALTER TABLE `Equipos`
  ADD CONSTRAINT `Equipos_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `Pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Guias`
--
ALTER TABLE `Guias`
  ADD CONSTRAINT `Guias_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `Clubes` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Inscripciones`
--
ALTER TABLE `Inscripciones`
  ADD CONSTRAINT `Inscripciones_ibfk_1` FOREIGN KEY (`Perro`) REFERENCES `Perros` (`ID`),
  ADD CONSTRAINT `Inscripciones_ibfk_2` FOREIGN KEY (`Prueba`) REFERENCES `Pruebas` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Inscripciones_ibfk_3` FOREIGN KEY (`Equipo`) REFERENCES `Equipos` (`ID`);

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
-- Filtros para la tabla `Perros`
--
ALTER TABLE `Perros`
  ADD CONSTRAINT `Perros_ibfk_1` FOREIGN KEY (`Categoria`) REFERENCES `Categorias_Perro` (`Categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Perros_ibfk_2` FOREIGN KEY (`Grado`) REFERENCES `Grados_Perro` (`Grado`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Perros_ibfk_3` FOREIGN KEY (`Guia`) REFERENCES `Guias` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Pruebas`
--
ALTER TABLE `Pruebas`
  ADD CONSTRAINT `Pruebas_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `Clubes` (`ID`) ON UPDATE CASCADE;

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
