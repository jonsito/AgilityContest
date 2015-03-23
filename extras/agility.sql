-- phpMyAdmin SQL Dump
-- version 4.3.7
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 11-02-2015 a las 13:05:33
-- Versión del servidor: 5.5.41-MariaDB
-- Versión de PHP: 5.5.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Estructura de tabla para la tabla `categorias_perro`
--
-- Creación: 06-02-2015 a las 13:03:55
--

DROP TABLE IF EXISTS `categorias_perro`;
CREATE TABLE IF NOT EXISTS `categorias_perro` (
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `categorias_perro`:
--

--
-- Volcado de datos para la tabla `categorias_perro`
--

INSERT INTO `categorias_perro` (`Categoria`, `Observaciones`) VALUES
('-', 'Sin especificar'),
('L', 'Large - Standard'),
('M', 'Medium - Midi'),
('S', 'Small - Mini'),
('T', 'Tiny - Enano');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clubes`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `clubes`;
CREATE TABLE IF NOT EXISTS `clubes` (
  `ID` int(4) NOT NULL,
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
  `Baja` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `clubes`:
--   `Provincia`
--       `provincias` -> `Provincia`
--

--
-- Volcado de datos para la tabla `clubes`
--

INSERT INTO `clubes` (`ID`, `Nombre`, `Direccion1`, `Direccion2`, `Provincia`, `Contacto1`, `Contacto2`, `Contacto3`, `GPS`, `Web`, `Email`, `Facebook`, `Google`, `Twitter`, `Logo`, `Observaciones`, `Baja`) VALUES
(1, '-- Sin asignar --', '', '', '-- Sin asignar --', '', '', '', '', '', '', '', '', '', 'rsce.png', 'NO BORRAR ESTA ENTRADA. SE USARA PARA AQUELLOS GUIAS QUE NO TENGAN CLUB ASIGNADO', 0),
(2, 'AA Y CIA', '28609 Sevilla La Nueva (Madrid)', '', 'Madrid', '+ 34 619 29 03 98', '', '', '', '', 'arribas.anabel@gmail.com', '', '', '', 'aaycia.png', '', 0),
(3, 'ACADE / Canesport', 'Salvadas, 41, 2º C', '15705 Santiago de Compostela', 'Coruña, A', '+ 34 620 29 58 31', '+ 34 881 93 95 5', '', '', 'http://www.asociacionacade.com/', 'asociacioncansdeportistas@gmail.com', '', '', '', 'acade.png', '', 0),
(4, 'Agilcan', 'Paseo de los Olivos 10', '28330 San Martin de la Vega', 'Madrid', '634 417 893', '918 946 096', '659 146 878', NULL, 'http://www.agilcan.es/', 'info@agilcan.es', NULL, NULL, NULL, 'agilcan.png', NULL, 0),
(5, 'Alhambra', 'Urano, 24', '18200 Maracena (Granada)', 'Granada', ' + 34 958 42 12 85 ', '', '', '', '', 'agilityalhambra@hotmail.com', '', '', '', 'alhambra.png', '', 0),
(6, 'Askizu', 'Caserio Benta - Barrio Askizu', '20808 Getaria (Guipúzcoa)', 'Gipuzkoa/Guipúzcoa', '+ 34 656 76 60 65', '', '', '', 'http://www.agilityaskizu.com/', 'antonio@agilityaskizu.com', '', '', '', 'askizu.png', '', 0),
(7, 'Badalona', 'Camí del Xiprers, s/n', '08916 Badalona (Barcelona)', 'Barcelona', ' + 34 93 597 23 53 ', ' + 34 676 48 99 40 ', '', '', 'http://www.agilitybadalona.con/', 'info@agilitybadalona.com', '', '', '', 'badalona.png', '', 0),
(8, 'Baix Llobregat', 'Enric Borras, 10', '08820 El Prat de Llobregat (Barcelona)', 'Barcelona', '+34 695 79 42 74', '', '', '', 'http://www.agilitybaixllobregat.com/', 'agilitybaixllobregat@hotmail.com', '', '', '', 'baix_llobregat.png', '', 0),
(9, 'Campo de Gibraltar', 'Arbol de la Paz, 4', '11205 Algeciras (Cádiz)', 'Cádiz', ' + 34 647 80 32 64 ', '', '', '', '', 'villa-zahara@hotmail.com', '', '', '', 'campo_de_gibraltar.png', '', 0),
(10, 'Camu', 'Párraco Pedro Lozano, 26', '30007 Zarandona (Murcia)', 'Murcia', '+ 34 636 25 19 39', '', '', '', '', 'clubagilitymurcia@hotmail.com', '', '', '', 'camu.png', '', 0),
(11, 'Can Natura', 'Peñella Baja, 1', '33310 Cabranes (Asturias)', 'Asturias', '+ 34 696 86 08 63', '', '', '', 'http://www.cannatura.net/', 'cannatura@hotmail.com', '', '', '', 'cannatura.png', '', 0),
(12, 'Canedupo', 'Serra de Ancares, 18', '27004 Lugo', 'Lugo', '636 507 468.', '', '', '', '', 'lugo@canedupo.com', '', '', '', 'canedupo.png', '', 0),
(13, 'Canic', 'Sant Pere, 10', '08392 Llavaneres (Barcelona)', 'Barcelona', '+ 34 93 792 76 55', '', '', '', 'http://www.agilitycanic.cat/', 'info@agilitycanic.cat', '', '', '', 'canic.png', '', 0),
(14, 'Canino Algecireño', 'Los Naranjos, 8', '11380 Tarifa (Cádiz)', 'Cádiz', ' + 34 663 55 86 42 ', '', '', '', '', 'arquikm@gmail.com', '', '', '', 'canino_algecireno.png', '', 0),
(15, 'Castellón', 'Mar Cantábrico, 2, 1º C', '12100 Grao de Castellón (Castellón)', 'Castellón/Castelló', '+ 34 964 28 61 52', '+ 34 625 82 25 35', '', '', 'http://www.agilitycastellon.es/', 'agilitycastellon@gmx.es', '', '', '', 'castellon.png', '', 0),
(16, 'Cinco Huesos', 'Paseo de los Pozos, Km. 1,2', '28813 Torres de la Alameda', 'Madrid', '+ 34 91 832 83 00 ', '+ 34 691 77 75 24 ', '', '', 'http://www.cincohuesos.com/', 'cincohuesos@gmail.com', '', '', '', 'cinco_huesos.png', 'Antiguo club "Apata"', 0),
(17, 'Ciudad de Antequera', '', '', 'Málaga', '', '', '', '', '', '', '', '', '', 'ciudad_de_antequera.png', 'Baja 28-enero-2014', 1),
(18, 'Ciutat Comtal', 'Escultor Llimona, 38-40, Entr. 2ª', '08031 Barcelona', 'Barcelona', ' + 34 645 85 10 06 ', '', '', '', 'http://www.agilitybarcelona.com/', 'info@agilitybarcelona.com', '', '', '', 'ciutat_comtal.png', '', 0),
(19, 'Ciutat de Valencia', 'Sequia de Calvera, 33, Bajo', '46910 Sedaví (Valencia)', 'Valencia/Valéncia', '', '', '', '', 'http://www.bichopeludo.com/ciutat_de_valencia.html', 'ciutatdevalencia@bichopeludo.com', '', '', '', 'ciutat_de_valencia.png', '', 0),
(20, 'Clotet', 'Apdo. de correos 517', '12500 Vinaroz (Castellón)', 'Castellón/Castelló', '+ 34 687 52 38 11', '', '', '', 'http://www.degarrof.com/', 'declotet@hotmail.com', '', '', '', 'clotet.png', '', 0),
(21, 'Cornella', 'Mn. Andreu, 13-19', '08940 Cornellà de Llobregat (Barcelona)', 'Barcelona', '+ 34 638 98 75 91', '', '', '', 'http://www.agilitycornella.com/', 'info@agilitycornella.com', '', '', '', 'cornella.png', '', 0),
(22, 'Correcan', ' Lóndrés, 58, 1º D', '28850 Torrejón de Ardoz (Madrid)', 'Madrid', '+ 34 668 86 76 81', '', '', '', 'http://www.correcan.es/', 'info@correcan.es', '', '', '', 'correcan.png', '', 0),
(23, 'Costa Azahar', 'Camino Caminas, 223 - Apdo. de correos 717', '12080 Castellón', 'Castellón/Castelló', '+ 34 964 76 60 83', '', '', '', 'http://www.mediterraniacentrocanino.com/', 'info@mediterraniacentrocanino.com', '', '', '', 'costa_azahar.png', '', 0),
(24, 'Costa Blanca', 'C/ Baltasar Gracián Nº3, Urb. Montecid', '03670 Monforte del Cid ', 'Alicante/Alacant', NULL, NULL, NULL, NULL, 'http://www.agilitycostablanca.com/', 'agility@agilitycostablanca.com', NULL, NULL, NULL, 'costa_blanca.png', NULL, 0),
(25, 'Cousadecans', 'Lugar de Foxo, s/n - San Vicente de Vigo', '15175 Carral (La Coruña)', 'Coruña, A', '+ 34 652 83 28 34', '+ 34 620 67 61 40', '', '', '', 'cousadecans@gmail.com', '', '', '', 'cousadecans.png', '', 0),
(26, 'Cuatro Patas', 'Club social Urb/ El Socorro', 'Carmona', 'Sevilla', '630 52 72 42 (Isaac) ', '615 45 58 78 (Rafa)', '', 'N 37.43865 - W 5.79858', '', 'agiltiy4patas@hotmail.com', '', '', '', 'cuatro_patas.png', '', 1),
(27, 'Cubas', 'Paseo de los Cipreses s/n', 'Cubas de la Sagra', 'Madrid', '918143395', '619 56 43 49', NULL, NULL, 'http://clubagilitycubas.es/', 'clubagilitycubas@terra.com', NULL, NULL, NULL, 'cubas.png', NULL, 0),
(28, 'Deporcan', 'Jazmín, 16, Entreplanta', '28033 Madrid', 'Madrid', '629 843 681', ' + 34 91 302 44 35', '', '40.32132, -3.41895', 'http://www.clubagilityboadilla.org/', 'agility.deporcan@gmail.com', '', '', '', 'deporcan.png', 'Antiguo "Club Boadilla"', 0),
(29, 'Depordog', 'Avd del Mueble s/n', '11130 Chiclana', 'Cádiz', '652 73 45 17', NULL, NULL, NULL, 'http://www.clubagilitydepordog.es/', 'ildegolo@hotmail.com', NULL, NULL, NULL, 'depordog.png', NULL, 0),
(30, 'Educan', 'Mester de Juglaría, 20', '28978 Cubas de la Sagra (Madrid)', 'Madrid', '617 469 312', '+ 34 676 67 76 38', '', '', 'http://www.madrid.educan.es/', 'agility.madrid@educan.es', '', '', '', 'educan.png', '', 0),
(31, 'El Hechizo del Border C.', 'Ctra. Monserrat, Km. 7''5, nº 26', '46900 Torrent ', 'Valencia/Valéncia', '+ 34 96 156 56 75', NULL, NULL, NULL, 'http://www.elhechizo.com/', 'elhechizobc@gmail.com', NULL, NULL, NULL, 'el_hechizo_del_border_collie.png', NULL, 0),
(32, 'El Nogueral', 'Cami del Camp, 23', '03460 Beneixama', 'Alicante/Alacant', '+ 34 695 45 23 69', NULL, NULL, NULL, 'http://www.clubagility.es/', 'info@clubagility.es', NULL, NULL, NULL, 'el_nogueral.png', NULL, 0),
(33, 'El Tramusser Benifaio', 'Polígono 16 - Cami Prefasic', '46450 Benifaió (Valencia)', 'Valencia/Valéncia', '+ 34 678 57 47 86', '', '', '', 'http://www.escuelacaninavalencia.com/', 'madamagility@hotmail.com', '', '', '', 'el_tramusser_benifaio.png', '', 0),
(34, 'Els Dimonis de Bascara', 'Apartado de correos 241', '17600 Figueres (Gerona)', 'Girona/Gerona', ' + 34 657 20 44 81', '', '', '', 'http://www.dimonisdebascara.cat/', 'dimonisdebascara@hotmail.es', '', '', '', 'els_dimonis_de_bascara.png', '', 0),
(35, 'Eslón', 'Carretera de Carranque s/n', 'Serranillos del Valle', 'Madrid', '657 209 274', '', '', '', 'http://www.agilityeslon.com', 'info@agilityeslon.com', '', '', '', 'eslon.png', '', 0),
(36, 'Euskadi', 'CLUB DE AGILITY EUSKADI Beike Bidea, 2, 2º Dcha', '48950 Asua - Erandio (Vizcaya)', 'Bizkaia/Vizcaya', '619 423 720 - Pedro Martinez', '', '', '', ' www.agilityeuskadi.com', 'info@agilityeuskadi.com', '', '', '', 'euskadi.png', '', 0),
(37, 'Hoop Agility', 'Alberto Conti, 8, 7º C', '28935 Móstoles (Madrid)', 'Madrid', '+ 34 635 65 78 42', '', '', '', 'http://www.agilityclub.es/', 'info@agilityclub.es', '', '', '', 'hoop.png', '', 0),
(38, 'Illa Blanca', 'Washington, 18, 2º', '07820 San Antonio de Portmany (Ibiza)', 'Balears, Illes', '+ 34 672 32 39 22', NULL, NULL, NULL, 'http://www.agilityillablanca.com/', 'info@agilityillablanca.com', NULL, NULL, NULL, 'illa_blanca.png', NULL, 0),
(39, 'Indog Maria de Huerva', 'Calle Orfeón 13  Nave A', '50410  Cuarte de Huerva', 'Zaragoza', '', '', '', '', 'http://www.agilityindog.com/', 'info@agilityindog.com', '', '', '', 'indog.png', '', 0),
(40, 'Junior', 'Calle de la Fuente, nº 8', '16162-Villar del Horno', 'Cuenca', '626389032', NULL, NULL, NULL, 'http://www.agilityjunior.es/', 'agilityjunior@gmail.com, info@agilityjunior.es', NULL, NULL, NULL, 'junior.png', NULL, 0),
(41, 'Kai Argi', 'Oiartzun, 6, Entlo. B', '20110 Pasaia San Pedro (Guipúzcoa)', 'Gipuzkoa/Guipúzcoa', '+ 34 656 71 51 31', '', '', '', 'http://www.kaiargi.com', 'kaiargi@kaiargi.com', '', '', '', 'kai_argi.png', '', 0),
(42, 'L''Almozara', 'Camino de Pinseque, 147-A', '50190 Garrapinillos (Zaragoza)', 'Zaragoza', ' + 34 637 54 15 86', '', '', '', '', '', '', '', '', 'almozara.png', '', 0),
(43, 'L''Horta Nord', 'Vora Vía, 2', '46132 Almassera (Valencia)', 'Valencia/Valéncia', '+ 34 651 30 41 47', '', '', '', 'http://clubagilitylhortanord.blogspot.com.es/', '', 'https://www.facebook.com/agility.lhortanordvalencia', '', '', 'horta_nord.png', '', 0),
(44, 'La Daga', '25230 Mollerussa (Lérida)', '', 'Lleida/Lérida', ' + 34 660 72 04 90 ', '', '', '', 'http://www.clubagilityladaga.blogspot.com/', 'niisia84@hotmail.com', '', '', '', 'la_daga.png', '', 0),
(45, 'La Dama', 'Partida de las Casicas, 5', '03330 Crevillente (Alicante) – España', 'Alicante/Alacant', ' +34 622 109 409', '', '', ' 38° 10′ 44″ N – 0° 48′ 30″ W”', 'http://www.agilityladama.com/ladama/', 'agilityladama@gmail.com', '', '', '', 'la_dama.png', '', 0),
(46, 'La Manada', 'Partida Calvet, 37, Bajo', '46120 Alboraia (Valencia)', 'Valencia/Valéncia', '+ 34 659 89 78 40 (Vicent)', '', '', '39° 29'' 47.55'''' N - 0° 20'' 37.1'''' W', 'http://www.la-manada.org/agility/', 'info@la-manada.org', '', '', '', 'la_manada.png', '', 0),
(47, 'La Palma', 'Paraje Los Pérez de Arriba', '30593 La Palma - Cartagena (Murcia)', 'Murcia', ' + 34 669 23 31 83', '', '', '', 'http://www.agilitycartagena.com/', 'agilitycartagena@gmail.com', '', '', '', 'la_palma.png', '', 0),
(48, 'La Princesa', 'Ocaña, 104, Bajo', '28047 Madrid', 'Madrid', '+ 34 91 465 50 05 ', '', '', '+40° 19'' 35,41", -3° 50'' 50,61" ', 'http://www.agilitylaprincesa.es/', 'agilitylaprincesa@gmail.com', '', '', '', 'la_princesa.png', '', 0),
(49, 'La Ribera', 'Plaza España, 11', '50638 Cabañas de Ebro (Zaragoza)', 'Zaragoza', ' + 34 976 75 86 33', ' + 34 649 58 65 98 ', '', '', 'http://www.agilitylaribera.es/', 'agilitylaribera@hotmail.com', '', '', '', 'la_ribera.png', '', 0),
(50, 'La Selva', 'Carretera Vella de Riudarenes, s/n', '17430 Santa Coloma de Farners (Gerona)', 'Girona/Gerona', '+ 34 606 77 64 65', ' + 34 629 36 37 39', '', '', 'http://www.asscaninalaselva.com/', 'agility@asscaninalaselva.com', '', '', '', 'la_selva.png', '', 0),
(51, 'Lealcan', 'José Luis Sampedro, 14, 2º D', '28529 Rivas Vaciamadrid (Madrid)', 'Madrid', ': + 34 646 44 45 55', '', '', '', 'http://www.lealcan.com/', 'info@lealcan.com', '', '', '', 'lealcan.png', '', 0),
(52, 'Maresme', 'Santiago Rusiñol, 90', '08340 Vilassar de Mar (Barcelona)', 'Barcelona', ' + 34 93 759 70 54 ', '', '', '', '', 'agilitymaresme@telefonica.net', '', '', '', 'maresme.png', '', 0),
(53, 'Marvi', '40 Pins, 36 - Urb. Roca II', '08430 La Roca del Valles (Barcelona)', 'Barcelona', '+ 34 93 842 21 05 ', '', '', '', '', 'Marvistel@hotmail.com', '', '', '', 'marvi.png', '', 0),
(54, 'Mediterráneo', 'Senda Estrecha, 14', '30011 Murcia', 'Murcia', '+ 34 968 25 77 83', '+ 34 677 40 98 57', '', '', '', '', '', '', '', 'mediterraneo.png', '', 0),
(55, 'Mi Perro 10', 'Andalucía, 25', '28750 San Agustín de Guadalix (Madrid)', 'Madrid', '+ 34 651 91 41 46', '', '', '', 'http://www.miperro10.com/', 'info@miperro10.com', '', '', '', 'mi_perro_10.png', '', 0),
(56, 'Miramar', 'Carrer del Llorer, 3', '08789 La Torre de Claramunt (Barcelona)', 'Barcelona', '+ 34 679 27 27 91 ', '', '', '', '', 'jmtorres323@gmail.com', '', '', '', 'miramar.png', '', 0),
(57, 'Negreira', 'Avda. Recinto Ferial, 2, 4º A', '36540 Silleda (Pontevedra)', 'Pontevedra', '+ 34 629 50 71 76', '', '', '', '', '', '', '', '', 'negreira.png', '', 0),
(58, 'Neo Reus', 'Mª Aurelia Campany, 4, 6º, 2º', '43204 Reus (Tarragona)', 'Tarragona', '+ 34 616 44 62 41', '', '', '', '', 'agilityneo@hotmail.com', '', '', '', 'neo_reus.png', '', 0),
(59, 'Palaciego', 'Roble, 7', '41720 Los Palacios y Villafranca (Sevilla)', 'Sevilla', '+ 34 619 12 73 25 ', '', '', '', 'http://www.actiweb.es/palaciego/', '', 'https://www.facebook.com/pages/Club-Deportivo-Agility-Palaciego/192778427478835', '', '', 'palaciego.png', '', 0),
(60, 'Parbayon Cantabria', 'Bº Sorribero Bajo, nº 4', '39470 Renedo de Piélagos (Cantabria)', 'Cantabria', '+ 34 626 79 14 54 ', '', '', '', 'http://www.agilitycantabria.com/', 'agilitycantabria@gmail.com', '', '', '', 'parbayon.png', '', 0),
(61, 'Parque del Alamillo', 'Antonio Machín, 9, 1º Izda.', '41009 Sevilla', 'Sevilla', ' + 34 655 76 46 03', '+ 34 95 443 45 77 ', '', '', 'http://www.clubagilityalamillo.com/', 'clubagilityalamillo@hotmail.com', '', '', '', 'parque_del_alamillo.png', '', 0),
(62, 'Pataplán', ' N-320a carretera de Valencia Km.134.', '', 'Cuenca', ' Juan José González (Presidente del Club) - tfn: 639776502', '	 Javier Martínez (Tesorero y Webmaster) - tfn: 605914763', '', 'N40.033819,W2.11415', 'http://www.agilitypataplan.es/', 'info@agilitypataplan.es', '', '', '', 'pataplan.png', '', 0),
(63, 'Patas', 'Ameixoada, 28C', '36954 Moaña (Pontevedra)', 'Pontevedra', '+ 34 986 31 10 13', '+ 34 659 01 36 68 ', '', '', 'http://patas.blogaliza.org/', 'agilitypatas@hotmail.com', '', '', '', 'patas.png', '', 0),
(64, 'Paterna', 'Cid Campeador, 12-13', '46980 Paterna (Valencia)', 'Valencia/Valéncia', '+ 34 677 72 27 30', '', '', '', 'http://www.agilitypaterna.com/', 'info@agilitypaterna.com', '', '', '', 'paterna.png', '', 0),
(65, 'Pican', 'Marqués de Dos Aguas, 39, p. 8', '46220 Picassent (Valencia)', 'Valencia/Valéncia', '+ 34 687 70 69 90', '', '', '', '', 'clubagilitypican@gmail.com', '', '', '', 'pican.png', '', 0),
(66, 'Pura Vida', 'Almendro, 3, Bl. 2, 1º C', '28710 El Molar (Madrid)', 'Madrid', ' + 34 680 19 08 42', '', '', '', 'http://www.mascotaspuravida.es/', 'agility@mascotaspuravida.es', '', '', '', 'pura_vida.png', '', 0),
(67, 'Santa Quiteria', '08410 Vilanova del Valles (Barcelona)', '', 'Barcelona', '+ 34 651 89 21 07', '', '', '', 'http://www.agilitysantaquiteria.es/', 'info@agilitysantaquiteria.es', '', '', '', 'santa_quiteria.png', '', 0),
(68, 'Star Can', 'Avda. del Vincle, 28', '03560 Campello (Alicante)', 'Alicante/Alacant', '+ 34 96 563 01 60', '(Ana Alonso) anastarcan@hotmail.com', '', 'W 0º 33´ 03" N 38º 22´31"', 'http://www.starcan.es/', 'nsoler@starcan.es', '', '', '', 'star_can.png', '', 0),
(69, 'Talavera', 'Cervera - Local 39', '45600 Talavera de la Reina (Toledo)', 'Toledo', '+ 34 610 01 79 75 ', '', '', '', '', 'reinaboxtalavera@hotmail.com', '', '', '', 'talavera.png', '', 0),
(70, 'Tandem', 'Carretera M413 km 8,5 de Arroyomolinos a Moraleja de Enmedio', 'Madrid.', 'Madrid', '687 964891', '627 964845', '', '', '', 'informacion@agilitytandem.es', '', '', '', 'tandem.png', '', 0),
(71, 'Tercans', 'Reibon, 192-A  -  Meira', '36955 Moaña (Pontevedra)', 'Pontevedra', ' + 34 617 34 07 63 ', '', '', '', '', 'tercans@gmail.com', '', '', '', 'tercans.png', '', 0),
(72, 'Torrevieja', 'Moriones, 43, 3º E', '03182 Torrevieja (Alicante)', 'Alicante/Alacant', '+ 34 635 41 30 31', '', '', '', '', 'info.agilitytorrevieja@gmail.com', '', '', '', 'torrevieja.png', '', 0),
(73, 'Toskahua', 'Cmno Santa Pau, 233 Garrapinillos-ZARAGOZA', '', 'Zaragoza', 'Tel.- 976-780583 movil.-666-436111', '', '', '', 'http://perso.wanadoo.es/vjuan/agility.htm', 'grupotoskahua@eresmas.com', '', '', '', 'toskaua.png', '', 0),
(74, 'Valles Club Cani', '08140 Caldes de Montbui (Barcelona)', '', 'Barcelona', ' telèfon:    619 28 68 82 ', '', '', ' N 41º 37'' 07''''    E 2º 10'' 31.7''''', '', 'info@vallesgrupcani.org', '', '', '', 'valles_club_cani.png', '', 0),
(75, 'Vallgorguina', 'Vila Carina Ctra. C-61, Km. 15,5', '08471 Vallgorguina (Barcelona)', 'Barcelona', '+ 34 93 867 93 18 ', '+ 34 600 00 54 99 ', '', '', '', 'agilityvallgorguina@centrecani.cat', '', '', '', 'vallgorguina.png', '', 0),
(76, 'Vila-Real', 'Padre Lluis María Llop, 54, 1º B', '12540 Vila-real (Castellón)', 'Castellón/Castelló', '+ 34 964 52 40 09', '', '', '', 'http://www.agilityvila-real.es/', 'agilityvilareal@gmail.com', '', '', '', 'vila_real.png', '', 0),
(77, 'Vilcan', 'Nou, 20', '46270 Villanueva de Castellón (Valencia)', 'Valencia/Valéncia', '+ 34 96 245 31 81', '', '', '', '', '', '', '', '', 'vilcan.png', '', 0),
(78, 'Villena', 'Plaza El Rollo, 5', '03400 Villena (Alicante)', 'Alicante/Alacant', '+ 34 636 42 67 13', '', '', '', 'http://clubagilityvillena.blogspot.com.es/', '', '', '', '', 'villena.png', '', 0),
(79, 'W.E.L.P.E.', 'Polideportivo Municipal La Canaleja', 'Alcorcón', 'Madrid', '+ 34 91 619 52 79', NULL, NULL, NULL, 'http://www.grupowelpe.com', 'gwelpe@teleline.es', 'https://www.facebook.com/groups/484854411592829/', NULL, '@gwelpe', 'welpe.png', NULL, 0),
(80, 'Xanastur', ' Baleares, 39, 3º D', '33208 Gijón (Asturias)', 'Asturias', '+ 34 607 11 90 56', '', '', '', 'http://www.xanastur.org/', 'xanasturcentrocanino@gmail.com', '', '', '', 'xanastur.png', '', 0),
(81, 'Zampican', 'Río Navía, 2', '12006 Castellón', 'Castellón/Castelló', '+ 34 629 07 06 75', '', '', '', 'http://www.agilityzampican.es/', '', '', '', '', 'zampican.png', '', 0),
(82, 'Buscans', '07810 Cala de San Vicente (Ibiza)', '', 'Balears, Illes', ' + 34 661 02 12 6', '+34 637 13 62 73', '', '', '', 'buscans@hotmail.com', '', '', '', 'buscans.png', '', 0),
(83, 'Eivissa', 'Elx, s/n, B. 1, nº 4', '07820 Sant Agusti - Sant Josep (Ibiza)', 'Balears, Illes', '+ 34 971 34 58 28', '+34 609 35 40 00', '', '', 'http://www.agilityeivissa.com/', 'info@agilityeivissa.com', '', '', '', 'eivissa.png', '', 0),
(84, 'Insular', 'Pablo Picasso', '07820 San Antonio (Baleares)', 'Balears, Illes', '+34 655 76 10 94', '', '', '', 'http://www.agilityinsular.com/', 'agilityinsular@gmail.com', '', '', '', 'insular.png', '', 0),
(85, 'Teocan', 'Rarís, s/n', '15883 Teo (La Coruña)', 'Coruña, A', '+ 34 627 93 72 81', '', '', '', '', 'adteocan@gmail.com', '', '', '', 'teocan.png', '', 0),
(86, 'Los Angeles de San Antón', 'Camino del Tiro Pichón, 1', '11500 El Puerto de Santa María', 'Cádiz', '+ 34 667 48 61 06', '+ 34 617 41 06 52', '', '', '', 'cdagilityladsa@hotmail.com', '', '', '', 'ladsa.png', '', 0),
(87, 'A-0', 'Cº de los Santos, s/n - Avda. de la Vega, s/n', '28500 Arganda del Rey', 'Madrid', '+ 34 626 43 68 18', '+ 34 646 79 51 55', '', '', '', '', '', '', '', 'logo_87.png', '', 0),
(88, 'A. D. A. Pozuelo', 'Felipe de la Guerra, 7', '28224 Pozuelo de Alarcón', 'Madrid', '', '', '', '', '', '', '', '', '', 'logo_88.png', '', 0),
(89, 'El Campet de Pobla Llarga', 'Partida Codona, Pol. 1 parc. 78', '46670 La Poble Llarga', 'Valencia/Valéncia', '669 088 200', '606 428 438 ', '', '', '', 'agilityelcampet@gmail.com', '', '', '', 'logo_89.png', '', 0),
(90, 'La Huella', 'Miguel Servet 80', '46540 El Puig de Santa Maria', 'Valencia/Valéncia', '', '', '', '', '', 'agilitylahuella@hotmail.com', '', '', '', 'rsce.png', '', 0),
(91, 'Almussafes', 'Camí Burriaga s/n (Parque Rural de Almussafes)', '46440 Almussafes', 'Valencia/Valéncia', '', '', '', '', '', '', '', '', '', 'logo_91.png', '', 0),
(92, 'Tinerfe', 'Los Adernos s/n', '38441 Santo Domingo - La Guancha', 'Santa Cruz de Te', '', '', '', '', '', '', '', '', '', 'logo_92.png', '', 0),
(93, 'Tolouse Veto Agility', '', '', '-- Sin asignar --', '', '', '', '', '', '', '', '', '', 'rsce.png', '', 0),
(94, 'Avila', 'Camino de Avila, 4', '05289 San Esteban de los Patos', 'Ávila', '619 36 47 21', '', '', '', '', 'agilityavila@gmail.com', 'https://www.facebook.com/profile.php?id=100008197471949', '', '', 'logo_94.png', '', 0),
(95, 'Can Roja', 'Ctra. C-1415a, Km. 31,400 Can Ramoneda', '08181 Senmenat', 'Barcelona', '93 864 36 71', '656 35 97 95', '', '', 'http://www.canroja.com/', 'canroja@canroja.com', '', '', '', 'logo_95.png', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `equipos`;
CREATE TABLE IF NOT EXISTS `equipos` (
  `ID` int(4) NOT NULL,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Nombre` varchar(255) NOT NULL,
  `Observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `equipos`:
--   `Prueba`
--       `pruebas` -> `ID`
--

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`ID`, `Prueba`, `Nombre`, `Observaciones`) VALUES
(5, 6, '-- Sin asignar --', 'NO BORRAR: PRUEBA 6 - Equipo por defecto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `eventos`;
CREATE TABLE IF NOT EXISTS `eventos` (
  `ID` int(4) NOT NULL,
  `Session` int(4) NOT NULL,
  `Source` varchar(255) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Data` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `eventos`:
--   `Session`
--       `sesiones` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados_perro`
--
-- Creación: 06-02-2015 a las 13:03:56
--

DROP TABLE IF EXISTS `grados_perro`;
CREATE TABLE IF NOT EXISTS `grados_perro` (
  `Grado` varchar(16) NOT NULL,
  `Comentarios` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `grados_perro`:
--

--
-- Volcado de datos para la tabla `grados_perro`
--

INSERT INTO `grados_perro` (`Grado`, `Comentarios`) VALUES
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
-- Estructura de tabla para la tabla `guias`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `guias`;
CREATE TABLE IF NOT EXISTS `guias` (
  `ID` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Telefono` varchar(16) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Club` int(4) NOT NULL DEFAULT '1',
  `Observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=588 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `guias`:
--   `Club`
--       `clubes` -> `ID`
--

--
-- Volcado de datos para la tabla `guias`
--

INSERT INTO `guias` (`ID`, `Nombre`, `Telefono`, `Email`, `Club`, `Observaciones`) VALUES
(1, '-- Sin asignar --', NULL, NULL, 1, 'NO BORRAR. Valor por defecto cuando un perro se define por primera vez'),
(2, 'Aaron Laro', '', '', 27, ''),
(3, 'Ada Serrano', '', '', 67, ''),
(5, 'Adrian Bajo', '', '', 76, ''),
(6, 'Adrian Díaz', NULL, NULL, 31, NULL),
(7, 'Adrian Martínez', '', '', 62, ''),
(8, 'Adrián Soria', '', '', 62, ''),
(9, 'Africa Cabañas', '', '', 22, ''),
(10, 'Agustin Centelles', '', '', 23, ''),
(11, 'Agustín González', '', '', 43, ''),
(12, 'Aida Al-Nehlawi', NULL, NULL, 13, NULL),
(13, 'Alaitz Idarraga', '', '', 36, ''),
(14, 'Albert Ulldemolins', NULL, NULL, 7, NULL),
(15, 'Alberto Alonso Gutierrez', '', '', 24, ''),
(16, 'Alberto Costas', NULL, NULL, 75, NULL),
(17, 'Alberto González', '', '', 75, ''),
(18, 'Alberto Marugan', '', '', 4, ''),
(19, 'Alberto Mudarra', '', '', 49, ''),
(20, 'Alberto Pereda', NULL, NULL, 48, NULL),
(21, 'Alejandra Alvarez', '', '', 31, ''),
(22, 'Alejandro Piñeiro', NULL, NULL, 71, NULL),
(23, 'Alejandro Rodríguez Villalta', '', '', 59, ''),
(24, 'Alejandro Salas', '', '', 80, ''),
(25, 'Alex del Río', NULL, NULL, 18, NULL),
(26, 'Alex Olivera', NULL, NULL, 67, NULL),
(27, 'Alex Sabini', NULL, NULL, 18, NULL),
(28, 'Alfredo Ortíz', '', '', 10, ''),
(29, 'Alicia Mejias', NULL, NULL, 61, NULL),
(30, 'Alicia Sanjurjo', NULL, NULL, 74, NULL),
(31, 'Almudena Novo', '', '', 16, ''),
(32, 'Amparo Roig', NULL, NULL, 43, NULL),
(33, 'Ana Alonso', NULL, NULL, 68, NULL),
(34, 'Ana Baeza', NULL, NULL, 40, NULL),
(35, 'Ana Beltran Bustamante', '', '', 32, ''),
(36, 'Ana Isabel Escobar', '', '', 75, ''),
(37, 'Ana Mateu', NULL, NULL, 13, NULL),
(38, 'Ana Ontañon', NULL, NULL, 52, NULL),
(39, 'Ana Palet', '', '', 75, ''),
(40, 'Ana Valencia', '', '', 4, ''),
(41, 'Andrea García', NULL, NULL, 76, NULL),
(42, 'Andrés Gimeno', NULL, NULL, 45, NULL),
(43, 'Andrés López', NULL, NULL, 71, NULL),
(44, 'Andres Morillas Sanjuan', '', '', 87, ''),
(45, 'Angel Corroto', '', '', 35, ''),
(46, 'Angel Fernández', '', '', 27, ''),
(47, 'Angel González', '', '', 4, ''),
(48, 'Angel Insa', NULL, NULL, 42, NULL),
(49, 'Angel Puertolas', NULL, NULL, 41, NULL),
(50, 'Angel Rubio', '', '', 39, ''),
(51, 'Angeles Abad', NULL, NULL, 42, NULL),
(52, 'Angelica Castaño', NULL, NULL, 35, NULL),
(53, 'Ankie Kleijberg', NULL, NULL, 68, NULL),
(54, 'Anna Aguilella', NULL, NULL, 24, NULL),
(55, 'Antje Lippold', NULL, NULL, 21, NULL),
(56, 'Antonio Carmona', NULL, NULL, 21, NULL),
(57, 'Antonio Fernández Moreno', '', '', 22, ''),
(58, 'Antonio López', '', '', 87, ''),
(59, 'Antonio Molina', '', '', 79, ''),
(60, 'Antonio Ojeda', NULL, NULL, 15, NULL),
(61, 'Antonio Santos', '', '', 67, ''),
(62, 'Antonio Tovar', '', '', 42, ''),
(63, 'Arabia Vidal', NULL, NULL, 77, NULL),
(64, 'Araceli Montero', NULL, NULL, 7, NULL),
(65, 'Aracelis Rodríguez', NULL, NULL, 18, NULL),
(66, 'Arcadio Nohales', NULL, NULL, 81, NULL),
(67, 'Ariadna Soriano', NULL, NULL, 42, NULL),
(68, 'Arianna Bucci', NULL, NULL, 50, NULL),
(69, 'Arturo Conejera', NULL, NULL, 22, NULL),
(70, 'Asier García', '', '', 36, ''),
(71, 'Astrid Hoffmeister', NULL, NULL, 51, NULL),
(72, 'Barbara Flemming', NULL, NULL, 68, NULL),
(73, 'Beatriz Juan', '', '', 87, ''),
(74, 'Belén de Carvalho', '', '', 30, ''),
(75, 'Berit Kittel', '', '', 39, ''),
(76, 'Carles Fortuny', NULL, NULL, 18, NULL),
(77, 'Carlos Alvarez', NULL, NULL, 71, NULL),
(78, 'Carlos Casado', NULL, NULL, 68, NULL),
(80, 'Carlos Martínez R.', '', '', 35, ''),
(81, 'Carlos Pérez', '', '', 48, ''),
(82, 'Carlos Pulpón', '', '', 35, ''),
(83, 'Carlos Serra', NULL, NULL, 38, NULL),
(84, 'Carmen Alos', NULL, NULL, 13, NULL),
(85, 'Mari Carmen Antequera', '', '', 23, ''),
(86, 'Carmen Briceño', NULL, NULL, 42, NULL),
(87, 'Carmen Sotomayor', NULL, NULL, 35, NULL),
(88, 'Carmen Vázquez', NULL, NULL, 50, NULL),
(89, 'Carolina Verdú', NULL, NULL, 78, NULL),
(90, 'Celeste Zarzosa', NULL, NULL, 48, NULL),
(91, 'Celso Valle', NULL, NULL, 52, NULL),
(92, 'Cesar Losada Mera', '', '', 35, ''),
(93, 'Charly Castañer', '', '', 39, ''),
(94, 'Clara Ruisánchez', '', '', 80, ''),
(95, 'Conchi Fernández', '', '', 48, ''),
(96, 'Concepción López', NULL, NULL, 49, NULL),
(97, 'Cristian Verde', NULL, NULL, 71, NULL),
(98, 'Cristina Blanco', '', '', 27, ''),
(99, 'Cristina Cortijo', '', '', 35, ''),
(100, 'Cristina García', NULL, NULL, 44, NULL),
(101, 'Cristina González', '', '', 75, ''),
(102, 'Cristina Pedraz', '', '', 2, ''),
(103, 'Cristina Ruill', '', '', 44, ''),
(104, 'Cristofol Albert', NULL, NULL, 43, NULL),
(105, 'Damian Alarcon', NULL, NULL, 30, NULL),
(106, 'Daniel Amigo', NULL, NULL, 7, NULL),
(107, 'Daniel Luna', NULL, NULL, 40, NULL),
(108, 'Daniel Menéndez', NULL, NULL, 31, NULL),
(109, 'David Alique', NULL, NULL, 28, NULL),
(110, 'David Asenjo', '', '', 42, ''),
(111, 'David Escribano', '', '', 27, ''),
(112, 'David Ferrer', NULL, NULL, 7, NULL),
(113, 'David Flix Bermejo', '', '', 67, ''),
(114, 'David Gómez-Calcerrada', '', '', 48, ''),
(115, 'David Gonzalbo', NULL, NULL, 31, NULL),
(116, 'David Molina', '', '', 42, ''),
(117, 'David Parejo', NULL, NULL, 17, NULL),
(118, 'David Sepulveda', NULL, NULL, 7, NULL),
(119, 'Debra Howard', NULL, NULL, 48, NULL),
(120, 'Diana Cozar', NULL, NULL, 13, NULL),
(121, 'Diana García Romero', '', '', 61, ''),
(122, 'Diego Rouco', NULL, NULL, 63, NULL),
(123, 'Dolores López', NULL, NULL, 78, NULL),
(124, 'Dolores Sampedro', NULL, NULL, 81, NULL),
(125, 'Eduard Bonet', NULL, NULL, 7, NULL),
(126, 'Eduard Giralt Canadell', '', '', 67, ''),
(127, 'Eduardo Adán', '', '', 39, ''),
(128, 'Efren Lucas', NULL, NULL, 68, NULL),
(129, 'Elena Alberich', NULL, NULL, 13, NULL),
(130, 'Elena Chinchilla', NULL, NULL, 43, NULL),
(131, 'Elena Miguel', '', '', 35, ''),
(132, 'Elena Sin', NULL, NULL, 42, NULL),
(133, 'Elisenda Huidobro', NULL, NULL, 13, NULL),
(134, 'Elvira Pertierra', '', '', 80, ''),
(135, 'Emilio Calvo', NULL, NULL, 31, NULL),
(136, 'Emilio José Pedrazuela', '', '', 75, ''),
(137, 'Enma Gutiérrez', '', '', 60, ''),
(138, 'Enric García', NULL, NULL, 81, NULL),
(139, 'Enric Lleixa', NULL, NULL, 7, NULL),
(140, 'Enrique Alonso Queija', '', '', 66, ''),
(141, 'Enrique Camarero', '', '', 35, ''),
(142, 'Enrique Grau', NULL, NULL, 24, NULL),
(143, 'Enrique Herbera', NULL, NULL, 7, NULL),
(145, 'Enrique Sendra Ortega', '', '', 71, ''),
(146, 'Ernesto Sorribes', NULL, NULL, 81, NULL),
(147, 'Estefanía Pérez', NULL, NULL, 30, NULL),
(148, 'Estíbaliz Pereda Navarro', '', '', 75, ''),
(149, 'Estíbaliz Pujana', '', '', 43, ''),
(150, 'Eugenio Villares', NULL, NULL, 27, NULL),
(151, 'Eva Grau', NULL, NULL, 64, NULL),
(152, 'Eva Vázquez Morales', '', '', 42, ''),
(153, 'Fermin Gil', NULL, NULL, 43, NULL),
(154, 'Fernando Benet', NULL, NULL, 81, NULL),
(155, 'Fernando Bibián', '', '', 4, ''),
(156, 'Fernando Cardeña', '', '', 80, ''),
(157, 'Fernando De La Fuente', '', '', 49, ''),
(158, 'Fito Rodríguez', '', '', 80, ''),
(159, 'Francisco Aguilera', NULL, NULL, 17, NULL),
(160, 'Francisco de la Cruz', '', '', 88, ''),
(161, 'Francisco Esteban', NULL, NULL, 24, NULL),
(162, 'Francisco Javier Jaen', '', '', 75, ''),
(163, 'Francisco Javier Luque', NULL, NULL, 61, NULL),
(164, 'Francisco José Sousa', NULL, NULL, 71, NULL),
(165, 'Francisco Maestre', NULL, NULL, 43, NULL),
(166, 'Francisco Martín', NULL, NULL, 74, NULL),
(167, 'Francisco Medina', NULL, NULL, 13, NULL),
(168, 'Francisco Pérez', NULL, NULL, 53, NULL),
(169, 'Francisco Sobral', NULL, NULL, 63, NULL),
(170, 'Gabriel Gómez', '', '', 42, ''),
(171, 'Gabriel Martín', NULL, NULL, 60, NULL),
(172, 'Gema López', '', '', 35, ''),
(173, 'Gemma González', '', '', 80, ''),
(174, 'Gerard Barberá', NULL, NULL, 58, NULL),
(175, 'Gerardo Alvarez', '', '', 62, ''),
(176, 'Gerardo González', NULL, NULL, 35, NULL),
(177, 'Gisela Solis', NULL, NULL, 44, NULL),
(178, 'Gorka Pozuelo', '', '', 43, ''),
(179, 'Gregorio Conde', NULL, NULL, 7, NULL),
(180, 'Gustavo Deus', '', '', 25, ''),
(181, 'Iago Sánchez', NULL, NULL, 71, NULL),
(182, 'Iban Cubedo', '', '', 23, ''),
(183, 'Imanol López', NULL, NULL, 42, NULL),
(184, 'Iñaki García', '', '', 36, ''),
(185, 'Inmaculada Rubio', '', '', 62, ''),
(186, 'Irati Diego', '', '', 43, ''),
(187, 'Irena Montalvo', '', '', 22, ''),
(188, 'Irene Artacho', NULL, NULL, 16, NULL),
(189, 'Isabel Gómez', NULL, NULL, 29, NULL),
(190, 'Isabel Natera', '', '', 36, ''),
(191, 'Isabel Rodríguez', NULL, NULL, 74, NULL),
(192, 'Isidoro Vázquez', NULL, NULL, 27, NULL),
(193, 'Ismael Pérez', '', '', 13, ''),
(194, 'Israel Díaz', '', '', 22, ''),
(195, 'Israel Fernández', NULL, NULL, 75, NULL),
(196, 'Ivan Amez Alvarez', '', '', 80, ''),
(197, 'Iván Pardo García', '', '', 75, ''),
(198, 'Iván San Antonio', NULL, NULL, 16, NULL),
(199, 'Iván Sánchez García', '', '', 35, ''),
(200, 'Jacqueline Holemans', NULL, NULL, 67, NULL),
(201, 'Jaime Gamir', NULL, NULL, 8, NULL),
(202, 'Jara Pérez', '', '', 4, ''),
(203, 'Jaume Fernández', NULL, NULL, 18, NULL),
(204, 'Javier Gómez', NULL, NULL, 65, NULL),
(205, 'Javier Iniesta', '', '', 75, ''),
(206, 'Javier López', NULL, NULL, 67, NULL),
(207, 'Javier Martín', '', '', 4, ''),
(208, 'Javier Martínez', '', '', 62, ''),
(209, 'Javier Mora Canales', '', '', 87, ''),
(210, 'Javier Ovejero', NULL, NULL, 35, NULL),
(211, 'Javier Sanchis', NULL, NULL, 52, NULL),
(212, 'Javier Santisteban', '', '', 16, ''),
(213, 'Jennifer Tolín del Castillo', '', '', 48, ''),
(215, 'Jenny Funcke', NULL, NULL, 7, NULL),
(216, 'Jerónimo Martínez', NULL, NULL, 68, NULL),
(217, 'Jessica Graciano', '', '', 35, ''),
(218, 'Jesús Crespo', NULL, NULL, 55, NULL),
(219, 'Jesús Cuellar', NULL, NULL, 35, NULL),
(220, 'Jesús Gómez', '', '', 4, ''),
(221, 'Jesús Manuel Romero', NULL, NULL, 59, NULL),
(222, 'Joan Castillo', '', '', 52, ''),
(223, 'Joan Wenceslao Pastor', NULL, NULL, 74, NULL),
(224, 'Joaquín Andrés', '', '', 4, ''),
(225, 'Jonathan Guillem', '', '', 77, ''),
(226, 'Jordi Boix', NULL, NULL, 13, NULL),
(227, 'Jordi Gómez', NULL, NULL, 13, NULL),
(228, 'Jorge Arcas Perales', '', '', 42, ''),
(229, 'Jorge Bala', '', '', 42, ''),
(230, 'Jorge Muñoz Leal', '', '', 27, ''),
(231, 'Jorge Valero', NULL, NULL, 42, NULL),
(232, 'José Angel Beired', '', '', 49, ''),
(233, 'José Angel Torres', NULL, NULL, 71, NULL),
(234, 'José Antonio Encinas', '', '', 87, ''),
(235, 'José Antonio Pascual', NULL, NULL, 42, NULL),
(236, 'José Antonio Vega', NULL, NULL, 4, NULL),
(237, 'Juan Carlos Iglesias', '', '', 75, ''),
(238, 'José Castaño', '', '', 95, ''),
(239, 'José Francisco Martorell', NULL, NULL, 65, NULL),
(240, 'José Guix', NULL, NULL, 65, NULL),
(241, 'José Luis García', NULL, NULL, 81, NULL),
(242, 'Jose Luis J. Mori', '', '', 2, ''),
(243, 'José Luis Prieto', NULL, NULL, 38, NULL),
(244, 'José Luis Quiroga', NULL, NULL, 21, NULL),
(245, 'José Luis Romero', '', '', 88, ''),
(246, 'José Luis Sogorb Nicolás', '', '', 45, ''),
(247, 'José Mahillo', NULL, NULL, 48, NULL),
(249, 'José Manuel Linares', NULL, NULL, 72, NULL),
(250, 'José Martí', NULL, NULL, 43, NULL),
(251, 'José Mateo Moreno', '', '', 44, ''),
(252, 'José Miguel Agustín', NULL, NULL, 4, NULL),
(253, 'José Miguel Morant', NULL, NULL, 48, NULL),
(254, 'José Miguel Paredes', NULL, NULL, 72, NULL),
(255, 'José Moreno', NULL, NULL, 26, NULL),
(256, 'José Pavon', NULL, NULL, 29, NULL),
(257, 'José Peris', NULL, NULL, 64, NULL),
(258, 'Jose Ramón López', '', '', 35, ''),
(259, 'José Santos Luna', '', '', 27, ''),
(260, 'José Soliño Caride', '', '', 71, ''),
(261, 'Josep Barbera', NULL, NULL, 58, NULL),
(262, 'Josep Mª Pineda', NULL, NULL, 74, NULL),
(263, 'Juan Muñiz', '', '', 3, ''),
(264, 'Juan Antonio Martinez', '', 'juansgaviota@gmail.com', 35, ''),
(265, 'Juan Campin', NULL, NULL, 48, NULL),
(266, 'Juan Carlos Blas', NULL, NULL, 35, NULL),
(267, 'Juan Carlos Companys', NULL, NULL, 24, NULL),
(268, 'Juan Carlos Hinojal', NULL, NULL, 60, NULL),
(269, 'Juan Carlos Redondo', '', '', 70, ''),
(270, 'Juan Carlos Ruiz', '', '', 35, ''),
(271, 'Juan del Amo', NULL, NULL, 40, NULL),
(272, 'Juan Escos', '', '', 49, ''),
(273, 'Juan Francisco Pelegrin', NULL, NULL, 49, NULL),
(274, 'Juan Francisco Torres', NULL, NULL, 7, NULL),
(275, 'Juan José Espadas', NULL, NULL, 13, NULL),
(276, 'Juan José González', '', '', 62, ''),
(277, 'Juan José Paz Nieto', '', '', 71, ''),
(278, 'Juan Luis Colmano', NULL, NULL, 42, NULL),
(279, 'Juan Manuel Caballo', NULL, NULL, 43, NULL),
(280, 'Juan Martín de las Blancas', '', '', 27, ''),
(281, 'Juan Miguel Cifuentes', NULL, NULL, 43, NULL),
(282, 'Juan Pablo Díaz', NULL, NULL, 27, NULL),
(283, 'Juan Pedro Martínez', NULL, NULL, 15, NULL),
(284, 'Juan Rodríguez', '', '', 48, ''),
(285, 'Juan Solanes', NULL, NULL, 77, NULL),
(286, 'Judith Cortes', NULL, NULL, 42, NULL),
(287, 'Judith Franco', '', '', 16, ''),
(288, 'Judith Herms', NULL, NULL, 18, NULL),
(289, 'Julia Faci Green', '', '', 42, ''),
(290, 'Julian Sánchez', NULL, NULL, 74, NULL),
(291, 'Julio Freire Nogueira', '', '', 63, ''),
(292, 'Katia Moeller', NULL, NULL, 14, NULL),
(293, 'Katy Navarro', NULL, NULL, 60, NULL),
(294, 'Laura Carrasco', NULL, NULL, 18, NULL),
(295, 'Laura Chiva', NULL, NULL, 7, NULL),
(296, 'Laura Monrabal', '', '', 42, ''),
(297, 'Leire Herrera', '', '', 36, ''),
(298, 'Lorena Díez', '', '', 87, ''),
(299, 'Lorena García', NULL, NULL, 60, NULL),
(300, 'Lorena Gargoles', NULL, NULL, 13, NULL),
(301, 'Lourdes Giménez', NULL, NULL, 31, NULL),
(302, 'Lourdes Peñarrocha', NULL, NULL, 68, NULL),
(303, 'Lourdes Rivera', '', '', 48, ''),
(304, 'Lucía Montalbán', '', '', 42, ''),
(305, 'Lucía Romero', '', '', 59, ''),
(306, 'Luciano Fernández', '', '', 48, ''),
(307, 'Luis Alberto Pereira', NULL, NULL, 60, NULL),
(308, 'Luis Carlos Sanchez', NULL, NULL, 4, NULL),
(309, 'Luis de Frías', '', '', 48, ''),
(310, 'Luis Ignacio Carazo', NULL, NULL, 74, NULL),
(311, 'Luis Luque', NULL, NULL, 75, NULL),
(312, 'Luis Miguel Rodrigo', '', '', 87, ''),
(313, 'Luis Miguel Rodriguez', '', '', 48, ''),
(314, 'Luisa Fernanda Millan', '', '', 62, ''),
(315, 'Luna Ramírez', NULL, NULL, 52, NULL),
(316, 'Maitane Luengo', '', '', 36, ''),
(317, 'Maite Guerrero', '', '', 35, ''),
(318, 'Manel Martínez', NULL, NULL, 74, NULL),
(319, 'José Manuel Basco', '', '', 59, ''),
(320, 'Manuel Jesús García', NULL, NULL, 9, NULL),
(321, 'Manuel Lara', NULL, NULL, 18, NULL),
(322, 'Manuel peña', '', '', 80, ''),
(323, 'Manuel Santomé', NULL, NULL, 71, NULL),
(324, 'Mar Bermúdez', NULL, NULL, 10, NULL),
(325, 'Marc Rabada', NULL, NULL, 44, NULL),
(326, 'Marco Maldonado', '', '', 39, ''),
(327, 'Marcos Martínez', NULL, NULL, 65, NULL),
(328, 'María López', NULL, NULL, 74, NULL),
(329, 'Maria Nogueira', '', '', 25, ''),
(330, 'Marina López', NULL, NULL, 18, NULL),
(331, 'Mario Rodríguez', NULL, NULL, 79, NULL),
(332, 'Marisa Jarabo', NULL, NULL, 79, NULL),
(333, 'Marta de la Rosa', NULL, NULL, 81, NULL),
(334, 'Marta Gregorio', NULL, NULL, 71, NULL),
(335, 'Marta Jiménez', '', '', 4, ''),
(336, 'Marta Ponce', '', '', 42, ''),
(337, 'Marta Sánchez', '', '', 87, ''),
(338, 'Marta Solar', '', '', 31, ''),
(339, 'Massimiliano Miggiano', NULL, NULL, 75, NULL),
(340, 'Matias Monleón', NULL, NULL, 43, NULL),
(341, 'Matias Rodríguez', NULL, NULL, 75, NULL),
(342, 'Mayte Pérez', '', '', 80, ''),
(343, 'Menchu Melcom', '', '', 28, ''),
(344, 'Mercedes Fernández', NULL, NULL, 68, NULL),
(345, 'Michael Volkert', NULL, NULL, 50, NULL),
(346, 'Miguel Angel Fernández', NULL, NULL, 80, NULL),
(347, 'Miguel Angel García', NULL, NULL, 35, NULL),
(348, 'Miguel Angel Morales', NULL, NULL, 4, NULL),
(349, 'Miguel Angel Soriano', NULL, NULL, 68, NULL),
(350, 'Miguel García Rodríguez', '', '', 71, ''),
(351, 'Mireia Carrascoso', NULL, NULL, 44, NULL),
(352, 'Miriam García', NULL, NULL, 58, NULL),
(353, 'Mónica Muñiz', NULL, NULL, 30, NULL),
(354, 'Mónica Rodríguez', '', '', 2, ''),
(355, 'Mónica Saavedra', NULL, NULL, 55, NULL),
(356, 'Monica Zaballa', '', '', 60, ''),
(357, 'Montserrat Calvet', NULL, NULL, 7, NULL),
(358, 'María José Manzano', NULL, NULL, 42, NULL),
(359, 'Narciso Leita', NULL, NULL, 49, NULL),
(360, 'Natalia Cuadrado', '', '', 67, ''),
(361, 'Natividad Ruiz García', '', '', 87, ''),
(362, 'Natividad Soler', NULL, NULL, 68, NULL),
(363, 'Neus Baró', NULL, NULL, 13, NULL),
(364, 'Noelia Gimeno', NULL, NULL, 64, NULL),
(365, 'Noelia Mouchet', '', '', 75, ''),
(366, 'Nuria Alonso', NULL, NULL, 18, NULL),
(367, 'Nuria Costa', NULL, NULL, 74, NULL),
(368, 'Nuria Díez', '', '', 80, ''),
(369, 'Nuria Fortuny', NULL, NULL, 18, NULL),
(370, 'Nuria Morell Nadal', '', '', 74, ''),
(371, 'Olga Palomares', '', '', 28, ''),
(372, 'Oria Micó', NULL, NULL, 78, NULL),
(373, 'Oscar Bravo', NULL, NULL, 21, NULL),
(374, 'Oscar Muñiz', '', '', 87, ''),
(375, 'Oscar Reboredo', NULL, NULL, 18, NULL),
(376, 'Oscar Sacristan', NULL, NULL, 48, NULL),
(377, 'Pablo Ballesta', NULL, NULL, 10, NULL),
(378, 'Pablo Miró', NULL, NULL, 81, NULL),
(379, 'Paloma Faci Green', '', '', 42, ''),
(380, 'Pau Serrano Ciratusa', '', '', 42, ''),
(381, 'Paula de Lucas', NULL, NULL, 27, NULL),
(382, 'Paula Rello', '', '', 87, ''),
(383, 'Paulino Iranzo', NULL, NULL, 65, NULL),
(384, 'Pedro Delgado Fernandez', '', '', 87, ''),
(385, 'Pedro Jesús Tazón', NULL, NULL, 60, NULL),
(386, 'Pedro Martínez', '', '', 36, ''),
(387, 'pepe', '', 'pepe@pepe.com', 3, ''),
(388, 'pepepepe', '', '', 3, ''),
(389, 'Pilar Collado', '', '', 39, ''),
(390, 'Pilar Matesanz', NULL, NULL, 79, NULL),
(391, 'Pilar Rodríguez', NULL, NULL, 60, NULL),
(392, 'Rachel Stevens', NULL, NULL, 58, NULL),
(393, 'Rafael Altava', NULL, NULL, 24, NULL),
(394, 'Rafael Camacho', NULL, NULL, 75, NULL),
(395, 'Rafael Fernández', NULL, NULL, 65, NULL),
(396, 'Rafael García', NULL, NULL, 76, NULL),
(397, 'Rafael Torregrosa', NULL, NULL, 24, NULL),
(398, 'Ramón Arribas', '', '', 2, ''),
(399, 'Ramón García Maroto', '', '', 16, ''),
(400, 'Raquel Frago', NULL, NULL, 49, NULL),
(401, 'Raquel Garrido', '', '', 70, ''),
(402, 'Raúl Sánchez', NULL, NULL, 27, NULL),
(403, 'Remedios Torres', NULL, NULL, 68, NULL),
(404, 'Reyes García', NULL, NULL, 31, NULL),
(405, 'Ricardo Benito', NULL, NULL, 79, NULL),
(406, 'Ricardo Martínez', NULL, NULL, 29, NULL),
(407, 'Ricardo Santolaya', '', '', 88, ''),
(408, 'Roberto Castro', NULL, NULL, 35, NULL),
(409, 'Roberto Iñigo', NULL, NULL, 16, NULL),
(410, 'Roberto Reina Vega', '', '', 48, ''),
(411, 'Rocio Hermelo Martínez', '', '', 71, ''),
(412, 'Rocio Santos', NULL, NULL, 61, NULL),
(413, 'Rodrigo González', '', '', 48, ''),
(414, 'Roque Alonso', '', '', 40, ''),
(415, 'Rosa Rubio', '', '', 28, ''),
(416, 'Ruben Jurado Márquez', '', '', 44, ''),
(417, 'Ruben Lopera', NULL, NULL, 53, NULL),
(418, 'Ruben Montero', NULL, NULL, 48, NULL),
(419, 'Sabina González', NULL, NULL, 50, NULL),
(420, 'Salvador Martí', NULL, NULL, 67, NULL),
(421, 'Sandra Gracia', '', '', 42, ''),
(422, 'Sara Bellido', NULL, NULL, 14, NULL),
(423, 'Sara Lara', '', '', 43, ''),
(424, 'Sara Montila', NULL, NULL, 28, NULL),
(425, 'Sara Montoya', NULL, NULL, 35, NULL),
(426, 'Sebastian González', NULL, NULL, 7, NULL),
(427, 'Sergio Casalins', '', '', 66, ''),
(428, 'Sergio Colomé', NULL, NULL, 42, NULL),
(429, 'Sergio García', NULL, NULL, 76, NULL),
(430, 'Sergio Martín', NULL, NULL, 42, NULL),
(431, 'Sergio Romeo', '', '', 42, ''),
(432, 'Sergio Ruiz', NULL, NULL, 22, NULL),
(433, 'Sergio Tella', '', '', 49, ''),
(434, 'Sheila Giménez', '', '', 42, ''),
(435, 'Silvia León', NULL, NULL, 81, NULL),
(436, 'Silvia Perea', '', '', 79, ''),
(437, 'Silvia Rodríguez', NULL, NULL, 74, NULL),
(438, 'Sofía Díaz', '', '', 67, ''),
(439, 'Sonia Asensio', NULL, NULL, 10, NULL),
(440, 'Sonia Conejero', NULL, NULL, 78, NULL),
(441, 'Stefan Eggenschwiler', '', '', 68, ''),
(442, 'Stina Sandquist', NULL, NULL, 17, NULL),
(443, 'Tamara Vidal', NULL, NULL, 44, NULL),
(444, 'Tomás Pérez Ayuso', '', '', 88, ''),
(445, 'Toni Rios', NULL, NULL, 13, NULL),
(446, 'Ubaldo Delgado', NULL, NULL, 10, NULL),
(447, 'Valentín de la Mesa', NULL, NULL, 24, NULL),
(448, 'Vanessa Calpe', NULL, NULL, 76, NULL),
(449, 'Vanessa Hermoso', NULL, NULL, 9, NULL),
(450, 'Verónica Díez Gómez', '', '', 60, ''),
(451, 'Verónica Fernández', '', '', 80, ''),
(452, 'Verónica Ibañez', NULL, NULL, 17, NULL),
(453, 'Verónica Rodríguez', NULL, NULL, 48, NULL),
(454, 'Vicente Cambra', NULL, NULL, 46, NULL),
(455, 'Vicente Martín', '', '', 4, ''),
(456, 'Vicente Micó Cánovas', '', '', 78, ''),
(457, 'Vicente Villalba', NULL, NULL, 43, NULL),
(458, 'Victor García', NULL, NULL, 53, NULL),
(459, 'Virginia García', '', '', 30, ''),
(460, 'Wladimiro', '', '', 42, ''),
(461, 'Xavier López', NULL, NULL, 67, NULL),
(462, 'Yolanda Larena', NULL, NULL, 58, NULL),
(463, 'Yolanda Moreno', NULL, NULL, 43, NULL),
(464, 'Yolanda Torres', NULL, NULL, 52, NULL),
(465, 'Yulia Morugova', '', '', 32, ''),
(466, 'Maria Jorge', '', '', 2, ''),
(467, 'Carmen Gutiérrez', '', '', 2, ''),
(468, 'Veronica Roda', '', '', 87, ''),
(469, 'Elena Cardenal', '', '', 4, ''),
(470, 'Natalia Peral', '', '', 2, ''),
(471, 'Antonio Fernandez Ortiz', '', '', 22, ''),
(472, 'Oscar Uceda', '', '', 27, ''),
(473, 'Iván García Puebla', '', '', 42, ''),
(474, 'Ainhoa de Frias', '', '', 48, ''),
(475, 'Sonia García', '', '', 48, ''),
(476, 'Jesus Perea', '', '', 48, ''),
(477, 'Dolores Rosas', '', '', 87, ''),
(478, 'Pablo Martínez', '', '', 87, ''),
(479, 'Sara María Lara', '', '', 43, ''),
(480, 'Fabian Santolaya', '', '', 88, ''),
(481, 'David Calviño', '', '', 35, ''),
(482, 'Yaiza Caballero Fernández', '', '', 80, ''),
(483, 'Carlos Escribano', '', '', 35, ''),
(484, 'Rosa Maria Cañadillas', '', '', 35, ''),
(485, 'Arancha Ruipérez Moslares', '', '', 35, ''),
(486, 'Rubén García', '', '', 35, ''),
(487, 'Carlos Martínez S.', '', '', 35, ''),
(488, 'Sandra Rodrigo', '', '', 87, ''),
(489, 'Sonia Gil', '', '', 16, ''),
(490, 'Beatriz Sánchez Casares', '', '', 16, ''),
(491, 'Isabel Fernández', '', '', 16, ''),
(492, 'Mari Carmen Martí Sanz', '', '', 16, ''),
(493, 'Irene Escribano', '', '', 22, ''),
(494, 'Cynthia Sánchez', '', '', 22, ''),
(496, 'Irene Blanco', '', '', 28, ''),
(497, 'Oscar López', '', '', 28, ''),
(498, 'Virginia Pastor', '', '', 28, ''),
(499, 'Ruben López', '', '', 48, ''),
(500, 'Ana Belén Ondategui Casas', '', '', 80, ''),
(501, 'Jose Luis de la Vara', '', '', 16, ''),
(502, 'Yaiza', '', '', 16, ''),
(504, 'Luis Miguel Jiménez', '', '', 2, ''),
(505, 'Susana Martín', '', '', 28, ''),
(506, 'Javier Perez', '', '', 28, ''),
(507, 'Beatriz Gómez', '', '', 30, ''),
(508, 'Rodrigo García-Vidal', '', '', 37, ''),
(509, 'Pablo Parra', '', '', 55, ''),
(510, 'Daniel Zamora', '', '', 62, ''),
(511, 'Gregory Bielle-Bidalot', '', '', 93, ''),
(512, 'Ludiwine Dabezies', '', '', 93, ''),
(513, 'Alberto Pérez', '', '', 80, ''),
(514, 'Rómulo Parrilla', '', '', 35, ''),
(515, 'Javier González', '', '', 35, ''),
(516, 'Rafael Arjona', '', '', 9, ''),
(517, 'Nina Causevic', '', '', 9, ''),
(518, 'Eli Brandsaeter', '', '', 28, ''),
(519, 'Nicolas Alcaide', '', '', 28, ''),
(520, 'Patricia Sevillano García', '', '', 80, ''),
(521, 'Daniel Colás Avilés', '', '', 80, ''),
(522, 'Marta Acero', '', '', 75, ''),
(523, 'Jesús Fernández Crespo', '', '', 55, ''),
(524, 'Susana Calvelo', '', '', 16, ''),
(525, 'Laura', '', '', 80, ''),
(526, 'Beatriz', '', '', 80, ''),
(527, 'Miriam Villar', '', '', 70, ''),
(528, 'Miguel Angel Manzaneda', '', '', 2, ''),
(529, 'Mercedes Prieto', '', '', 4, ''),
(530, 'Álvaro Muñiz', '', '', 87, ''),
(531, 'Andres Morilla Sánchez', '', '', 87, ''),
(532, 'Abraham Romera', '', '', 7, ''),
(533, 'David Martínez', '', '', 23, ''),
(534, 'Rola Gamarra', '', '', 13, ''),
(535, 'Juan Carlos Vázquez', '', '', 85, ''),
(536, 'Gloria Domínguez Meira', '', '', 57, ''),
(537, 'Cristina Terrón', '', '', 31, ''),
(538, 'Irene Cortés', '', '', 57, ''),
(539, 'Yolanda García Sánchez', '', '', 61, ''),
(540, 'Diego', '', '', 85, ''),
(541, 'Antonio Pereiro', '', '', 85, ''),
(542, 'José Manuel Montero', '', '', 85, ''),
(543, 'Carlos Souto', '', '', 85, ''),
(544, 'Clara Rodríguez', '', '', 3, ''),
(545, 'Jaime Otero', '', '', 3, ''),
(546, 'Jordi Marco Rey', '', '', 71, ''),
(547, 'Carlos Collado', '', '', 71, ''),
(548, 'Dori Moreno Morales', '', '', 23, ''),
(549, 'Sergio Gamarra', '', '', 63, ''),
(550, 'Alaitz Argüello', '', '', 60, ''),
(551, 'Sara López', '', '', 25, ''),
(552, 'Rafael García', '', '', 28, ''),
(553, 'Lorenzo Buzón', '', '', 61, ''),
(554, 'Rosa María Espinosa', '', '', 62, ''),
(555, 'Ricardo de la Cruz', '', '', 88, ''),
(556, 'Amaia Peña Gómez', '', '', 88, ''),
(557, 'Miriam Fraile Gutiérrez', '', '', 10, ''),
(558, 'Alberto Garrido', '', '', 8, ''),
(559, 'Noelia Lobato', '', '', 39, ''),
(560, 'Hur Ayo', '', '', 36, ''),
(561, 'Izaskun Garitaonaindia', '', '', 36, ''),
(562, 'Alfredo Tuset', '', '', 7, ''),
(563, 'David Martinez', '', '', 42, ''),
(564, 'Beatriz Cascón', '', '', 42, ''),
(565, 'Rosana Cerón', '', '', 42, ''),
(566, 'David Chinchilla', '', '', 43, ''),
(567, 'Pablo Oria Tardío', '', '', 60, ''),
(568, 'Mar López', '', '', 42, ''),
(569, 'José Manuel Torres', '', '', 56, ''),
(570, 'Loli Gil', '', '', 49, ''),
(571, 'Sara Marques', '', '', 67, ''),
(572, 'Manu García', '', '', 36, ''),
(573, 'Adrian Ozaeta', '', '', 36, ''),
(574, 'Carlos Cruz', '', '', 7, ''),
(575, 'Carolina Cuadros', '', '', 7, ''),
(576, 'Adrian Buil', '', '', 42, ''),
(577, 'Inmaculada López', '', '', 42, ''),
(578, 'Paki Vinuesa', '', '', 60, ''),
(579, 'Ariadna Sieres', '', '', 61, ''),
(580, 'Claudia Martínez', '', '', 7, ''),
(581, 'Beatriz Virto', '', '', 42, ''),
(582, 'Vanessa Navarro', '', '', 23, ''),
(583, 'Pilar Sánchez', '', '', 23, ''),
(584, 'Carolina Gaggero', '', '', 3, ''),
(585, 'Julen Lázaro', '', '', 36, ''),
(586, 'Domingo López', '', '', 43, ''),
(587, 'Eduardo Font', '', '', 42, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `inscripciones`;
CREATE TABLE IF NOT EXISTS `inscripciones` (
  `ID` int(4) NOT NULL,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Perro` int(4) NOT NULL,
  `Dorsal` int(4) NOT NULL DEFAULT '0',
  `Celo` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` varchar(255) DEFAULT NULL,
  `Equipo` int(4) DEFAULT NULL,
  `Jornadas` int(4) NOT NULL DEFAULT '0',
  `Pagado` int(4) NOT NULL DEFAULT '24'
) ENGINE=InnoDB AUTO_INCREMENT=426 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `inscripciones`:
--   `Perro`
--       `perros` -> `ID`
--   `Prueba`
--       `pruebas` -> `ID`
--   `Equipo`
--       `equipos` -> `ID`
--

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`ID`, `Prueba`, `Perro`, `Dorsal`, `Celo`, `Observaciones`, `Equipo`, `Jornadas`, `Pagado`) VALUES
(295, 6, 602, 110, 0, '', 5, 3, 0),
(296, 6, 148, 93, 0, '', 5, 1, 0),
(297, 6, 416, 92, 0, '', 5, 1, 0),
(298, 6, 519, 35, 0, '', 5, 2, 0),
(299, 6, 585, 3, 1, '', 5, 3, 0),
(300, 6, 584, 12, 0, '', 5, 3, 0),
(301, 6, 391, 5, 0, '', 5, 3, 0),
(302, 6, 440, 11, 0, '', 5, 3, 0),
(303, 6, 68, 14, 0, '', 5, 3, 0),
(304, 6, 633, 1, 0, 'Test', 5, 3, 0),
(305, 6, 560, 10, 0, '', 5, 2, -12),
(306, 6, 576, 115, 0, '', 5, 1, 0),
(307, 6, 577, 116, 0, '', 5, 1, 0),
(308, 6, 571, 44, 0, '', 5, 1, 0),
(309, 6, 383, 46, 0, '', 5, 1, 0),
(310, 6, 589, 42, 0, '', 5, 3, 0),
(311, 6, 314, 51, 0, '', 5, 3, 0),
(312, 6, 384, 50, 0, '', 5, 1, 0),
(313, 6, 617, 47, 0, '', 5, 1, 0),
(314, 6, 616, 49, 0, '', 5, 1, 0),
(315, 6, 573, 48, 0, '', 5, 1, 0),
(316, 6, 382, 45, 0, '', 5, 1, 0),
(317, 6, 593, 75, 0, '', 5, 1, -12),
(318, 6, 57, 85, 0, '', 5, 3, 0),
(319, 6, 113, 86, 0, '', 5, 3, 0),
(322, 6, 575, 80, 0, '', 5, 1, 0),
(323, 6, 522, 79, 0, '', 5, 1, 0),
(324, 6, 400, 77, 0, '', 5, 3, 0),
(325, 6, 401, 82, 0, '', 5, 2, 0),
(326, 6, 423, 81, 0, '', 5, 2, 0),
(327, 6, 267, 83, 0, '', 5, 2, 0),
(328, 6, 274, 84, 0, '', 5, 2, 0),
(329, 6, 521, 76, 0, '', 5, 2, 0),
(330, 6, 398, 74, 0, '', 5, 2, 0),
(331, 6, 634, 78, 0, '', 5, 3, 0),
(332, 6, 623, 90, 0, '', 5, 2, 0),
(333, 6, 405, 89, 0, '', 5, 2, 0),
(334, 6, 404, 88, 0, '', 5, 2, 0),
(335, 6, 635, 91, 0, '', 5, 3, 0),
(336, 6, 229, 112, 0, '', 5, 3, 0),
(337, 6, 352, 111, 0, '', 5, 2, 0),
(338, 6, 10, 114, 0, '', 5, 1, 0),
(339, 6, 636, 113, 0, '', 5, 1, 0),
(340, 6, 25, 38, 0, '', 5, 3, 0),
(341, 6, 361, 36, 0, '', 5, 3, 0),
(342, 6, 379, 37, 0, '', 5, 2, 0),
(343, 6, 101, 95, 0, '', 5, 3, 0),
(344, 6, 637, 94, 0, 'Test', 5, 3, 0),
(345, 6, 595, 87, 0, '', 5, 2, 0),
(346, 6, 639, 40, 0, '', 5, 3, 0),
(347, 6, 253, 41, 0, '', 5, 3, 0),
(348, 6, 75, 26, 0, '', 5, 1, 0),
(349, 6, 410, 27, 0, '', 5, 1, 0),
(351, 6, 640, 25, 0, 'Test', 5, 3, 0),
(352, 6, 3, 23, 0, '', 5, 3, 0),
(353, 6, 414, 19, 1, '', 5, 3, 0),
(354, 6, 45, 22, 0, '', 5, 3, 0),
(355, 6, 413, 20, 0, '', 5, 3, 0),
(356, 6, 330, 21, 0, '', 5, 3, 0),
(357, 6, 78, 24, 0, '', 5, 3, 0),
(358, 6, 8, 18, 0, '', 5, 3, 0),
(359, 6, 397, 17, 0, '', 5, 3, 0),
(361, 6, 355, 32, 0, '', 5, 1, 0),
(362, 6, 357, 33, 0, '', 5, 1, 0),
(363, 6, 510, 31, 0, '', 5, 1, 0),
(364, 6, 356, 30, 0, '', 5, 1, 0),
(365, 6, 371, 28, 0, '', 5, 1, 0),
(366, 6, 641, 29, 0, '', 5, 1, 0),
(367, 6, 622, 97, 0, '', 5, 3, 0),
(368, 6, 601, 96, 0, '', 5, 3, 0),
(369, 6, 407, 105, 0, '', 5, 3, 0),
(370, 6, 342, 108, 0, '', 5, 3, 0),
(371, 6, 408, 107, 0, '', 5, 3, 0),
(372, 6, 202, 101, 0, '', 5, 3, 0),
(373, 6, 386, 106, 0, '', 5, 3, 0),
(374, 6, 95, 103, 0, '', 5, 3, 0),
(375, 6, 348, 99, 0, '', 5, 3, 0),
(376, 6, 517, 102, 0, '', 5, 3, 0),
(377, 6, 380, 100, 0, '', 5, 3, 0),
(379, 6, 312, 109, 1, '', 5, 1, 0),
(381, 6, 642, 98, 0, '', 5, 3, 0),
(383, 6, 643, 104, 0, '', 5, 1, 0),
(384, 6, 220, 15, 0, '', 5, 3, 0),
(385, 6, 392, 7, 0, '', 5, 3, 0),
(386, 6, 272, 9, 0, '', 5, 3, 0),
(387, 6, 94, 8, 1, '', 5, 2, 0),
(388, 6, 34, 16, 0, '', 5, 2, 0),
(389, 6, 387, 6, 0, '', 5, 2, 0),
(390, 6, 389, 4, 0, '', 5, 2, 0),
(391, 6, 644, 2, 0, '', 5, 2, 0),
(392, 6, 604, 55, 0, '', 5, 3, 0),
(394, 6, 427, 67, 0, '', 5, 2, -12),
(395, 6, 609, 61, 0, '', 5, 3, 0),
(396, 6, 607, 59, 0, '', 5, 3, 0),
(397, 6, 27, 68, 0, '', 5, 3, 0),
(398, 6, 103, 63, 0, '', 5, 3, 0),
(399, 6, 52, 62, 0, '', 5, 3, 0),
(400, 6, 394, 56, 0, '', 5, 3, 0),
(401, 6, 156, 60, 0, '', 5, 3, 0),
(402, 6, 424, 66, 0, '', 5, 3, 0),
(403, 6, 393, 65, 0, '', 5, 3, 0),
(404, 6, 608, 64, 0, '', 5, 3, 0),
(405, 6, 610, 58, 0, '', 5, 3, 0),
(406, 6, 350, 72, 0, '', 5, 3, 0),
(408, 6, 31, 70, 0, '', 5, 3, 0),
(409, 6, 46, 71, 0, '', 5, 3, 0),
(410, 6, 85, 69, 0, '', 5, 3, 0),
(411, 6, 605, 54, 0, '', 5, 1, 0),
(412, 6, 428, 57, 0, '', 5, 1, 0),
(413, 6, 498, 39, 0, '', 5, 3, 0),
(415, 6, 418, 53, 0, '', 5, 3, 0),
(416, 6, 624, 52, 0, 'Test', 5, 3, 0),
(418, 6, 588, 43, 0, 'Test', 5, 2, 0),
(419, 6, 417, 13, 0, '', 5, 3, 0),
(421, 6, 189, 73, 0, '', 5, 3, 0),
(424, 6, 18, 117, 0, '', 5, 1, 0),
(425, 6, 646, 118, 0, '', 5, 2, 0);

--
-- Disparadores `inscripciones`
--
DROP TRIGGER IF EXISTS `Increase_Dorsal`;
DELIMITER $$
CREATE TRIGGER `Increase_Dorsal` BEFORE INSERT ON `inscripciones`
 FOR EACH ROW BEGIN
     select count(*) into @rows from Inscripciones where Prueba = NEW.Prueba;
     if @rows>0 then
     select Dorsal + 1 into @newDorsal from Inscripciones where Prueba = NEW.Prueba order by Dorsal desc limit 1;
       set NEW.Dorsal = @newDorsal;
     else
       set NEW.Dorsal = 1;
     end if;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jornadas`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `jornadas`;
CREATE TABLE IF NOT EXISTS `jornadas` (
  `ID` int(4) NOT NULL,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Numero` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL,
  `Grado1` tinyint(1) NOT NULL DEFAULT '1',
  `Grado2` tinyint(1) NOT NULL DEFAULT '1',
  `Grado3` tinyint(1) NOT NULL DEFAULT '1',
  `Open` tinyint(1) NOT NULL DEFAULT '0',
  `Equipos3` tinyint(1) NOT NULL DEFAULT '0',
  `Equipos4` tinyint(1) NOT NULL DEFAULT '0',
  `PreAgility` tinyint(1) NOT NULL DEFAULT '1',
  `KO` tinyint(1) NOT NULL DEFAULT '0',
  `Especial` tinyint(1) NOT NULL DEFAULT '0',
  `PreAgility2` tinyint(1) NOT NULL DEFAULT '0',
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` varchar(255) NOT NULL DEFAULT '',
  `Orden_Tandas` varchar(255) NOT NULL DEFAULT 'BEGIN,END'
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `jornadas`:
--   `Prueba`
--       `pruebas` -> `ID`
--

--
-- Volcado de datos para la tabla `jornadas`
--

INSERT INTO `jornadas` (`ID`, `Prueba`, `Numero`, `Nombre`, `Fecha`, `Hora`, `Grado1`, `Grado2`, `Grado3`, `Open`, `Equipos3`, `Equipos4`, `PreAgility`, `KO`, `Especial`, `PreAgility2`, `Cerrada`, `Observaciones`, `Orden_Tandas`) VALUES
(33, 6, 1, 'Sábado', '2015-01-31', '00:00:00', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '(sin especificar)', 'BEGIN,3,4,5,6,7,8,9,10,11,23,24,25,12,13,14,26,27,28,END'),
(34, 6, 2, 'Domingo', '2015-02-01', '00:00:00', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '(sin especificar)', 'BEGIN,3,4,5,6,7,8,9,10,11,23,24,25,12,13,14,26,27,28,END'),
(35, 6, 3, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, '', 'BEGIN,1,3,4,5,6,7,8,9,10,11,23,24,25,12,13,14,26,27,28,END'),
(36, 6, 4, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, '', 'BEGIN,1,3,4,5,6,7,8,9,10,11,23,24,25,12,13,14,26,27,28,END'),
(37, 6, 5, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, '', 'BEGIN,1,3,4,5,6,7,8,9,10,11,23,24,25,12,13,14,26,27,28,END'),
(38, 6, 6, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, '', 'BEGIN,1,3,4,5,6,7,8,9,10,11,23,24,25,12,13,14,26,27,28,END'),
(39, 6, 7, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, '', 'BEGIN,1,3,4,5,6,7,8,9,10,11,23,24,25,12,13,14,26,27,28,END'),
(40, 6, 8, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, '', 'BEGIN,1,3,4,5,6,7,8,9,10,11,23,24,25,12,13,14,26,27,28,END');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jueces`
--
-- Creación: 06-02-2015 a las 13:03:59
--

DROP TABLE IF EXISTS `jueces`;
CREATE TABLE IF NOT EXISTS `jueces` (
  `ID` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Direccion1` varchar(255) DEFAULT NULL,
  `Direccion2` varchar(255) DEFAULT NULL,
  `Telefono` varchar(32) DEFAULT NULL,
  `Internacional` tinyint(1) NOT NULL DEFAULT '0',
  `Practicas` tinyint(1) NOT NULL DEFAULT '0',
  `Email` varchar(255) DEFAULT NULL,
  `Observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `jueces`:
--

--
-- Volcado de datos para la tabla `jueces`
--

INSERT INTO `jueces` (`ID`, `Nombre`, `Direccion1`, `Direccion2`, `Telefono`, `Internacional`, `Practicas`, `Email`, `Observaciones`) VALUES
(1, '-- Sin asignar --', '--------', '--------', '--------', 1, 1, 'nobody@nomail.com', 'NO BORRAR: Asignacion de juez por defecto'),
(2, 'Beltrán Bustamante, Ana', 'Camí del Camp, 23', '03460 Beneixama (Alicante)', '639 67 86 09', 0, 0, 'sadda\\_874@hotmail.com', ''),
(3, 'Boix Balaguer, Josep', 'Sant Pere, 10', '08392 San Andreu de Llavaneres (Barcelona)', ' 93 792 76 55', 1, 0, 'josep@agilitycanic.cat', NULL),
(4, 'Conde Delgado, Gregorio', NULL, NULL, ' 93 389 35 83 / 619 39 39 28', 1, 0, 'gconde@xtec.cat', NULL),
(5, 'Correa Arqueros, Mariano', 'Avda. de Moratalaz, 178, 2º A', '28030 Madrid', ' 91 301 47 58', 1, 0, 'mariano.correa@nsn.com', NULL),
(6, 'Diez Pérez, Esteban', 'Ocaña, 104, Bajo', '28047 Madrid', '91 465 50 05', 1, 0, 'estebanagility@gmail.com', NULL),
(7, 'Escalera Salamanca, Javier de la', 'Avda. de San Luis, 95, 2º D', '28033 Madrid', '91 767 07 45 / 607 43 34 13', 1, 0, 'javierdelae@gmail.com', NULL),
(8, 'Felix Fuentes, José', 'Vinaroz, 19', '18100 Armilla (Granada)', '958 55 05 74 / 617 93 54 32', 0, 0, NULL, NULL),
(9, 'García Alvarez, José Luis', 'Río Navia, 2', '12006 Castellón', '629 07 06 75', 1, 0, 'zampican@yahoo.es', NULL),
(10, 'García Rudilla, Juan', 'Rulda, 12', '03400 Villena (Alicante)', ' 96 580 30 63 / 652 83 08 85', 1, 0, NULL, NULL),
(11, 'Garrido Fuentes, Manuel', 'Paseo de los Olivos, 10', '28330 San Martín de la Vega (Madrid)', ' 91 894 60 96', 1, 0, 'mgfcom@telefonica.net', NULL),
(12, 'Gil Solis, Esperanza', 'Paseo de los Olivos, 10', '28330 San Martín de la Vega (Madrid)', ' 91 894 60 96', 1, 0, 'agilcan@telefonica.net', NULL),
(13, 'Guerra Guerra, Juan Pedro', 'Mariano Martín, 9', '10161 Arroyomolinos (Cáceres)', ' 927 28 82 56', 1, 0, 'jaracar@ono.com', NULL),
(14, 'Humanes Almonte, Miguel Angel', 'Zamora, 23, 3º C', '28941 Fuenlabrada (Madrid)', '607 70 55 75', 1, 0, 'agilblack@hotmail.com', NULL),
(15, 'Lanzó, Juan Antonio', 'Recinto Ferial, 2, 4º A', '36540 Silleda (Pontevedra)', ' 629 50 71 76 / 986 57 70 00', 1, 0, 'tonylanzo@gmail.com', NULL),
(16, 'Linares García, José Manuel', 'Carril de los Córdobas, 2', '30161 Llano de Brujas (Murcia)', '968 85 12 60 / 696 75 07 67', 0, 1, 'jomaliga1@gmail.com', NULL),
(17, 'Muñiz Martínez, Oscar', 'León Felipe, 31, 4º, 1ª', '28942 Fuenlabrada (Madrid)', '665 79 86 49', 1, 0, 'agilityzeus@gmail.com', ''),
(18, 'Navarro Costas, Jordi', 'Santiago Rusiñol, 90', '08340 Vilassar de Mar (Barcelona)', '93 759 70 54 / 609 30 97 10', 1, 0, 'jordinuc@telefonica.net', NULL),
(19, 'Parejo Carregalo, Manuel', 'Ctra. Valle de Abdalajis, Km. 1,7', '29260 La Joya (Málaga)', '95 270 26 04', 0, 1, 'losparejos@hotmail.com', NULL),
(20, 'Pineda Puig, Josep Mª', 'Sant Ramón, 69, B-3', '08140 Caldes de Montbui (Barcelona)', '93 865 46 16 / 678 43 36 45', 0, 0, 'pepagility@movistar.es', NULL),
(21, 'Poble Rosas, Ramón', 'Jaume I, 18', '08140 Caldes de Montbui (Barcelona)', '93 865 20 32', 1, 0, 'pobleramon@gmail.com', NULL),
(22, 'Rodríguez Matesanz, Mario', 'Plaza del Peñón, 10', '28923 Alcorcón (Madrid)', ' 91 619 52 79', 1, 0, 'gwelpe@terra.es', NULL),
(23, 'Santome González, Manuel', 'Fonte da Tella, 133 - A - Moureira - Meira', '36955 Moaña (Pontevedra)', '986 31 27 77 / 607 83 20 53', 0, 0, 'lolosantome@gmail.com', ''),
(24, 'Ulldemolins Santisteve, Albert', 'Llorer, 28, Casa 4', '08415 Bigues I Riells (Barcelona)', ' 93 865 89 64 / 636 96 33 77', 0, 1, 'albert23m@hotmail.com', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mangas`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `mangas`;
CREATE TABLE IF NOT EXISTS `mangas` (
  `ID` int(4) NOT NULL,
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
  `TRS_L_Factor` int(4) NOT NULL DEFAULT '100',
  `TRS_L_Unit` varchar(1) NOT NULL DEFAULT 's',
  `TRM_L_Tipo` int(4) NOT NULL DEFAULT '1',
  `TRM_L_Factor` int(4) NOT NULL DEFAULT '50',
  `TRM_L_Unit` varchar(1) NOT NULL DEFAULT '%',
  `TRS_M_Tipo` int(4) NOT NULL DEFAULT '0',
  `TRS_M_Factor` int(4) NOT NULL DEFAULT '100',
  `TRS_M_Unit` varchar(1) NOT NULL DEFAULT 's',
  `TRM_M_Tipo` int(4) NOT NULL DEFAULT '1',
  `TRM_M_Factor` int(4) NOT NULL DEFAULT '50',
  `TRM_M_Unit` varchar(1) NOT NULL DEFAULT '%',
  `TRS_S_Tipo` int(4) NOT NULL DEFAULT '0',
  `TRS_S_Factor` int(4) NOT NULL DEFAULT '100',
  `TRS_S_Unit` varchar(1) NOT NULL DEFAULT 's',
  `TRM_S_Tipo` int(4) NOT NULL DEFAULT '1',
  `TRM_S_Factor` int(4) NOT NULL DEFAULT '50',
  `TRM_S_Unit` varchar(1) NOT NULL DEFAULT '%',
  `TRS_T_Tipo` int(4) NOT NULL DEFAULT '0',
  `TRS_T_Factor` int(4) NOT NULL DEFAULT '100',
  `TRS_T_Unit` varchar(1) NOT NULL DEFAULT 's',
  `TRM_T_Tipo` int(4) NOT NULL DEFAULT '1',
  `TRM_T_Factor` int(4) NOT NULL DEFAULT '50',
  `TRM_T_Unit` varchar(1) NOT NULL DEFAULT '%',
  `Juez1` int(4) NOT NULL DEFAULT '1',
  `Juez2` int(4) NOT NULL DEFAULT '1',
  `Observaciones` varchar(255) DEFAULT NULL,
  `Orden_Salida` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `mangas`:
--   `Tipo`
--       `tipo_manga` -> `ID`
--   `Grado`
--       `grados_perro` -> `Grado`
--   `Juez1`
--       `jueces` -> `ID`
--   `Juez2`
--       `jueces` -> `ID`
--   `Jornada`
--       `jornadas` -> `ID`
--

--
-- Volcado de datos para la tabla `mangas`
--

INSERT INTO `mangas` (`ID`, `Jornada`, `Tipo`, `Grado`, `Recorrido`, `Dist_L`, `Obst_L`, `Dist_M`, `Obst_M`, `Dist_S`, `Obst_S`, `Dist_T`, `Obst_T`, `TRS_L_Tipo`, `TRS_L_Factor`, `TRS_L_Unit`, `TRM_L_Tipo`, `TRM_L_Factor`, `TRM_L_Unit`, `TRS_M_Tipo`, `TRS_M_Factor`, `TRS_M_Unit`, `TRM_M_Tipo`, `TRM_M_Factor`, `TRM_M_Unit`, `TRS_S_Tipo`, `TRS_S_Factor`, `TRS_S_Unit`, `TRM_S_Tipo`, `TRM_S_Factor`, `TRM_S_Unit`, `TRS_T_Tipo`, `TRS_T_Factor`, `TRS_T_Unit`, `TRM_T_Tipo`, `TRM_T_Factor`, `TRM_T_Unit`, `Juez1`, `Juez2`, `Observaciones`, `Orden_Salida`) VALUES
(59, 33, 3, 'GI', 1, 150, 17, 150, 17, 150, 17, 0, 0, 0, 53, 's', 0, 80, 's', 0, 56, 's', 0, 85, 's', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,576,577,589,639,356,371,641,622,601,605,633,637,624,604,TAG_L1,TAG_M0,571,617,382,510,383,TAG_M1,TAG_S0,616,573,642,640,TAG_S1,TAG_T0,TAG_T1,END'),
(60, 33, 4, 'GI', 1, 150, 17, 150, 17, 150, 17, 0, 0, 0, 53, 's', 0, 80, '%', 3, 56, 's', 0, 85, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,381,639,604,633,601,589,605,641,622,356,624,577,576,371,637,TAG_L1,TAG_M0,510,383,571,617,382,TAG_M1,TAG_S0,640,642,616,573,TAG_S1,TAG_T0,TAG_T1,END'),
(61, 33, 5, 'GII', 1, 167, 18, 167, 18, 167, 18, 0, 0, 0, 47, 's', 0, 70, 's', 3, 49, 's', 0, 73, 's', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,400,361,522,416,355,428,75,52,410,8,609,394,390,392,348,593,608,357,156,602,607,202,393,384,189,380,643,391,397,103,517,610,575,634,95,424,TAG_L1,414,585,TAG_M0,636,407,272,TAG_M1,TAG_S0,584,330,386,440,408,342,418,413,TAG_S1,TAG_T0,TAG_T1,END'),
(62, 33, 10, 'GII', 1, 151, 18, 151, 18, 151, 18, 0, 0, 0, 39, 's', 1, 50, '%', 0, 42, 's', 1, 50, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,390,410,634,428,394,609,575,593,517,156,8,75,361,610,602,202,397,384,424,608,522,416,348,643,95,355,189,52,400,380,357,103,393,607,391,392,TAG_L1,414,585,TAG_M0,636,272,407,TAG_M1,TAG_S0,330,418,584,408,413,440,342,386,TAG_S1,TAG_T0,TAG_T1,END'),
(63, 33, 6, 'GIII', 1, 181, 20, 181, 20, 181, 20, 0, 0, 2, 10, '%', 1, 50, '%', 2, 10, '%', 1, 50, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,220,78,3,417,25,57,45,27,113,10,148,46,85,101,31,68,18,229,TAG_L1,TAG_M0,635,498,253,350,TAG_M1,TAG_S0,314,TAG_S1,312,TAG_T0,TAG_T1,END'),
(64, 33, 11, 'GIII', 1, 138, 20, 138, 20, 138, 20, 0, 0, 2, 10, '%', 1, 50, '%', 2, 10, '%', 1, 50, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,10,113,27,31,85,220,148,101,57,417,3,18,68,46,45,25,78,229,TAG_L1,TAG_M0,350,498,635,253,TAG_M1,TAG_S0,314,TAG_S1,312,TAG_T0,TAG_T1,END'),
(65, 34, 3, 'GI', 1, 161, 17, 161, 17, 161, 17, 0, 0, 0, 54, 's', 1, 50, '%', 0, 56, 's', 1, 50, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,601,624,604,639,622,514,644,589,637,633,TAG_L1,TAG_M0,588,398,595,645,TAG_M1,TAG_S0,640,642,TAG_S1,TAG_T0,TAG_T1,END'),
(66, 34, 4, 'GI', 1, 161, 17, 161, 17, 161, 17, 0, 0, 0, 54, 's', 1, 50, '%', 0, 56, 's', 1, 50, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,514,639,644,601,633,589,637,622,604,624,TAG_L1,TAG_M0,595,398,588,TAG_M1,TAG_S0,640,642,TAG_S1,TAG_T0,TAG_T1,END'),
(67, 34, 5, 'GII', 1, 158, 18, 158, 18, 158, 18, 0, 0, 0, 44, 's', 1, 50, '%', 0, 47, 's', 1, 50, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,623,401,387,608,610,521,609,103,156,602,517,397,423,394,393,361,424,646,202,95,189,380,391,404,8,400,405,52,389,392,634,607,352,348,379,TAG_L1,585,94,414,TAG_M0,274,267,407,272,519,TAG_M1,TAG_S0,413,560,342,408,427,584,386,440,418,330,TAG_S1,TAG_T0,TAG_T1,END'),
(68, 34, 10, 'GII', 1, 154, 19, 154, 19, 154, 19, 0, 0, 0, 42, 's', 1, 50, '%', 0, 45, 's', 1, 50, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,397,609,602,380,404,391,394,623,103,348,95,607,389,400,8,521,424,156,610,202,517,52,387,393,646,379,392,608,423,401,189,634,405,352,361,TAG_L1,585,94,414,TAG_M0,274,519,272,267,407,TAG_M1,TAG_S0,330,427,560,584,418,408,440,413,342,386,TAG_S1,TAG_T0,TAG_T1,END'),
(69, 34, 6, 'GIII', 1, 158, 20, 158, 20, 158, 20, 0, 0, 2, 10, '%', 1, 50, '%', 2, 10, '%', 1, 50, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,34,85,229,417,25,113,57,46,27,3,31,45,101,68,220,78,TAG_L1,TAG_M0,498,350,635,253,TAG_M1,TAG_S0,314,TAG_S1,TAG_T0,TAG_T1,END'),
(70, 34, 11, 'GIII', 1, 146, 18, 146, 18, 146, 18, 0, 0, 2, 10, '%', 1, 50, '%', 2, 10, '%', 1, 50, '%', 4, 0, 's', 1, 50, '%', 0, 0, 's', 0, 0, 's', 2, 1, '', 'BEGIN,TAG_-0,TAG_-1,TAG_L0,220,101,78,113,229,25,3,68,45,34,27,31,46,57,85,417,TAG_L1,TAG_M0,253,350,635,498,TAG_M1,TAG_S0,314,TAG_S1,TAG_T0,TAG_T1,END');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `perroguiaclub`
--
DROP VIEW IF EXISTS `perroguiaclub`;
CREATE TABLE IF NOT EXISTS `perroguiaclub` (
`ID` int(4)
,`Nombre` varchar(255)
,`Raza` varchar(255)
,`Licencia` varchar(255)
,`LOE_RRC` varchar(255)
,`Categoria` varchar(1)
,`NombreCategoria` varchar(255)
,`Grado` varchar(16)
,`NombreGrado` varchar(255)
,`Guia` int(4)
,`NombreGuia` varchar(255)
,`Club` int(4)
,`NombreClub` varchar(255)
,`LogoClub` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perros`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `perros`;
CREATE TABLE IF NOT EXISTS `perros` (
  `ID` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Raza` varchar(255) DEFAULT NULL,
  `LOE_RRC` varchar(255) DEFAULT NULL,
  `Licencia` varchar(255) DEFAULT '--------',
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Guia` int(4) NOT NULL DEFAULT '1',
  `Grado` varchar(16) DEFAULT '-'
) ENGINE=InnoDB AUTO_INCREMENT=744 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `perros`:
--   `Categoria`
--       `categorias_perro` -> `Categoria`
--   `Grado`
--       `grados_perro` -> `Grado`
--   `Guia`
--       `guias` -> `ID`
--

--
-- Volcado de datos para la tabla `perros`
--

INSERT INTO `perros` (`ID`, `Nombre`, `Raza`, `LOE_RRC`, `Licencia`, `Categoria`, `Guia`, `Grado`) VALUES
(2, 'Yuma', 'P.B.Malinoise', '1936256', 'A330', 'L', 281, 'GIII'),
(3, 'Hannibal Lecter XIII', 'Border Collie', '1764520', 'A090', 'L', 444, 'GIII'),
(4, 'Ardi', '', '79097', '729', 'L', 379, '-'),
(5, 'William', NULL, '1667920', '920', 'L', 215, '-'),
(6, 'Xonny', NULL, '1317156', '622', 'L', 92, '-'),
(7, 'Indiana Jones', '', '1720531', '987', 'L', 273, 'GIII'),
(8, 'Thelma', 'Border Collie', '1515702', '824', 'L', 480, 'GII'),
(9, 'Boss', '', '1528991', '797', 'L', 374, 'GIII'),
(10, 'Lee', 'Border Collie', '95245', 'A084', 'L', 59, 'GIII'),
(11, 'Chinouk', NULL, '1390419', '724', 'L', 345, '-'),
(12, 'Angie', NULL, '1370168', '691', 'L', 59, '-'),
(13, 'Burundi', 'Perro de Aguas Español', '1874262', 'A310', 'L', 130, 'GIII'),
(14, 'Piter', '', '110594', 'A360', 'L', 104, 'GIII'),
(15, 'Napa', '', '1975832', 'A401', 'L', 228, 'GIII'),
(16, 'Gon', 'Border Collie', '1725855', 'A024', 'L', 323, 'GII'),
(17, 'Valerie', '', '1467667', '786', 'L', 318, 'GIII'),
(18, 'Woman', 'Border Collie', '1866186', 'A206', 'L', 209, 'GIII'),
(19, 'Baloo', NULL, '86974', '991', 'L', 389, '-'),
(20, 'Piter Winers', NULL, '100338', 'A188', 'L', 240, '-'),
(21, 'Lula', NULL, '1891977', 'A344', 'L', 49, '-'),
(22, 'Karen', NULL, '1970258', 'A427', 'L', 32, '-'),
(23, 'Runa', '', '112361', 'A347', 'L', 26, 'GIII'),
(24, 'Chiruca', 'Border Collie', '1635759', '986', 'L', 57, 'GII'),
(25, 'Moss', 'Border Collie', '113891', 'A391', 'L', 409, 'GIII'),
(26, 'Nena', NULL, '1521753', '930', 'L', 289, '-'),
(27, 'Deby', 'BorderCollie', '101610', 'A147', 'L', 92, 'GIII'),
(28, 'Noah', 'Border Collie', '1887262', 'A268', 'L', 457, 'GIII'),
(29, 'Sil', NULL, '1831356', 'A150', 'L', 323, '-'),
(30, 'Furia', '', '1554907', '892', 'L', 369, 'GIII'),
(31, 'Mc Coy', 'Border Collie', '1905162', 'A322', 'L', 87, 'GIII'),
(32, 'Argi', 'Border Collie', '110120', 'A241', 'L', 319, 'GIII'),
(33, 'Lua', NULL, '118441', 'A327', 'L', 249, '-'),
(34, 'Zoe', 'Border Collie', '109748', 'A289', 'L', 337, 'GIII'),
(35, 'Juice', NULL, '117997', 'A387', 'L', 75, '-'),
(36, 'Vega', NULL, '1552296', '855', 'L', 32, '-'),
(37, 'Idris', NULL, '83909', '880', 'L', 359, '-'),
(38, 'Izar', NULL, '1596718', '851', 'L', 166, '-'),
(39, 'Pica', 'Perro de Aguas Español', '104103', 'A091', 'L', 16, 'GIII'),
(40, 'Nana', 'Border Collie', '1780849', 'A073', 'L', 241, 'GIII'),
(41, 'Tara', NULL, 'No tiene', '1466', 'L', 375, '-'),
(42, 'Finn', '', '2074557', 'A596', 'L', 392, 'GIII'),
(43, 'Rocky', '', '1796496', 'A355', 'L', 69, 'GIII'),
(44, 'Neil', 'Flat Coated Retriever', '122117', 'A417', 'L', 192, '-'),
(45, 'Asia', 'Border Collie', '1958017', 'A364', 'L', 245, 'GIII'),
(46, 'Xodro', 'BorderCollie', '1959124', 'A371', 'L', 80, 'GIII'),
(47, 'Bec', NULL, '87712', '979', 'L', 250, '-'),
(48, 'Bely', NULL, '1370171', '692', 'L', 168, '-'),
(49, 'Laia', 'Border Collie', '1594320', '921', 'L', 195, 'GIII'),
(50, 'Bamba', '', '1887258', 'A242', 'L', 41, 'GII'),
(51, 'Akane', 'Euskal A Txakurra', '101429', 'A249', 'L', 176, 'GIII'),
(52, 'Mister', 'Border Collie', '99971', 'A250', 'L', 486, 'GII'),
(53, 'Spyro', NULL, '89457', 'A155', 'L', 49, '-'),
(54, 'Becho', NULL, '1831350', 'A203', 'L', 22, '-'),
(55, 'Luna', NULL, '1798021', 'A163', 'L', 456, '-'),
(56, 'Buh', '', '1929147', 'A297', 'L', 389, 'GIII'),
(57, 'Aby', 'Border Collie', '105806', 'A204', 'L', 410, 'GIII'),
(58, 'Yuma', 'Border Collie', '113067', 'A385', 'L', 307, 'GIII'),
(59, 'Zak', NULL, '1831579', 'A160', 'L', 378, '-'),
(60, 'Tanga', 'Border Collie', '119678', 'A366', 'L', 425, 'GIII'),
(61, 'Viconte', 'Mudi', '1561797', '813', 'L', 313, 'GII'),
(62, 'Dela', 'P.B. Malinoise', '2028765', 'A377', 'L', 311, 'GII'),
(63, 'Brus', NULL, '1763613', 'A140', 'L', 117, '-'),
(64, 'King', '', '1520008', '856', 'L', 353, 'GIII'),
(65, 'Brujostel', 'Border Collie', '1717447', 'A231', 'L', 465, 'GIII'),
(66, 'Kora', 'P.B.Malinoise', '111627', 'A332', 'L', 384, 'GII'),
(67, 'Rubia', NULL, '127474', 'A481', 'L', 312, '-'),
(68, 'Fito', 'Borde Collie', '127473', 'A529', 'L', 312, 'GIII'),
(69, 'Visente', NULL, '116863', 'A359', 'L', 383, '-'),
(70, 'Maia', NULL, '1780846', 'A132', 'L', 66, '-'),
(71, 'Mecha', 'P.B.Malinoise', '129549', 'A558', 'L', 234, 'GIII'),
(72, 'Ari', NULL, '1893230', 'A301', 'L', 54, '-'),
(73, 'Aslan', 'Border Collie', '1970223', 'A457', 'L', 182, 'GIII'),
(74, 'Mambo', '', '1392048', '753', 'L', 126, 'GIII'),
(75, 'Nut', 'Sabueso Anglo-Francés', '120681', 'A430', 'L', 354, 'GII'),
(76, 'Xena', NULL, '1570666', 'A118', 'L', 252, '-'),
(77, 'Fiona', NULL, '2010068', 'A491', 'L', 393, '-'),
(78, 'Winner', 'Border Collie', '127815', 'A497', 'L', 407, 'GIII'),
(79, 'Jade', 'Euskal A Txakurra', '', '', 'L', 484, 'GI'),
(80, 'Dasher', '', '2000291', 'A411', 'L', 273, 'GIII'),
(81, 'Gaston', NULL, '1717449', 'A340', 'L', 168, '-'),
(82, 'Pipo', NULL, '118271', 'A458', 'L', 395, '-'),
(83, 'Grey', 'Border Collie', '1717450', 'A066', 'L', 64, 'GIII'),
(84, 'Maty', 'Border Collie', '1964043', 'A333', 'L', 99, 'GII'),
(85, 'Dylan', 'Border Collie', '127444', 'A461', 'L', 199, 'GIII'),
(86, 'Magia', NULL, '1753228', 'A104', 'L', 89, '-'),
(87, 'Lia', NULL, '1842317', 'A389', 'L', 435, '-'),
(88, 'Coma', NULL, '1957039', 'A538', 'L', 120, '-'),
(89, 'Isis', '', '1443353', '838', 'L', 33, 'GIII'),
(90, 'Otto', NULL, '91641', 'A070', 'L', 396, '-'),
(91, 'Nana', '', '1781905', 'A225', 'L', 143, 'GII'),
(92, 'Assucar', 'Border Collie', '1594318', '992', 'L', 269, 'GII'),
(93, 'Fantastico', NULL, '109814', 'A218', 'L', 380, '-'),
(94, 'Panda', 'Border Collie', '1936233', 'A474', 'L', 298, 'GII'),
(95, 'Raissa', 'American Stanford', '131570', 'A563', 'L', 205, 'GII'),
(96, 'Nuka', NULL, '127265', 'A486', 'L', 27, '-'),
(97, 'Halcon', '', '126805', 'A482', 'L', 83, 'GIII'),
(98, 'California', '', '1967653', 'A414', 'L', 228, 'GII'),
(99, 'Astra', NULL, '1551831', '783', 'L', 22, '-'),
(100, 'Fito', '', '1980971', 'A490', 'L', 435, 'GIII'),
(101, 'Magic-Black', 'Border Collie', '101184', 'A318', 'L', 401, 'GIII'),
(102, 'Blacky', NULL, 'No tiene', '1493', 'L', 330, '-'),
(103, 'Red Magic', 'Border Collie', '1834052', 'A124', 'L', 131, 'GII'),
(104, 'Beauty', '', '120728', 'A452', 'L', 380, 'GIII'),
(105, 'Savannah', NULL, '1627377', '833', 'L', 38, '-'),
(106, 'Ra', 'P.B. Malinoise', '2009048', 'A494', 'L', 323, 'GII'),
(107, 'Flay', NULL, '123186', 'A418', 'L', 179, '-'),
(108, 'Bu', NULL, '111416', 'A519', 'L', 397, '-'),
(109, 'Heidy', '', '1727593', 'A035', 'L', 430, 'GII'),
(110, 'Kiko', NULL, '1780843', 'A145', 'L', 146, '-'),
(111, 'Koba', NULL, '1533440', '843', 'L', 183, '-'),
(112, 'Kiwi', NULL, '1908098', 'A267', 'L', 183, '-'),
(113, 'Yun', 'Border Collie', '2001179', 'A484', 'L', 95, 'GIII'),
(114, 'Nora', NULL, '80601', '735', 'L', 437, '-'),
(115, 'Liss', NULL, '1779665', 'A212', 'L', 117, '-'),
(116, 'Rayko', 'Border Collie', '2027590', 'A321', 'L', 381, 'GIII'),
(117, 'Nani', '', '1838888', 'A238', 'L', 286, 'GII'),
(118, 'Rasca', '', '2047380', 'A564', 'L', 16, 'GIII'),
(119, 'Abby', 'P. B. Groenendael', '2104382', 'A533', 'L', 215, 'GII'),
(120, 'Merlin', 'Border Collie', '1996593', 'A523', 'L', 121, 'GIII'),
(121, 'Rusti', 'Border Collie', '1831356', 'A227', 'L', 164, 'GIII'),
(122, 'Cora', '', 'No tiene', '1525', 'L', 48, 'GII'),
(123, 'Dux', '', '1727100', 'A134', 'L', 169, 'GIII'),
(124, 'Urko', 'BorderCollie', '127390', 'A565', 'L', 237, 'GIII'),
(125, 'Blues', NULL, '1756737', 'A083', 'L', 327, '-'),
(126, 'Lia', NULL, 'No tiene', '1481', 'L', 302, '-'),
(127, 'Broc', NULL, '1596717', '902', 'L', 262, '-'),
(128, 'Bell', NULL, '1831577', 'A205', 'L', 429, '-'),
(129, 'Colombo', NULL, '119957', 'A437', 'L', 159, '-'),
(130, 'Cooper', NULL, '1942046', 'A468', 'L', 219, '-'),
(131, 'Dunja', NULL, '131504', 'A568', 'L', 55, '-'),
(132, 'Nova', NULL, '1472107', '765', 'L', 367, '-'),
(133, 'Nuca', NULL, '105491', 'A431', 'L', 394, '-'),
(134, 'Nora', 'Braco', '113894', 'A356', 'L', 402, 'GII'),
(135, 'Kimi', NULL, '1908103', 'A271', 'L', 107, '-'),
(136, 'Samba', NULL, '131663', 'A608', 'L', 78, '-'),
(137, 'Liss', NULL, '1988114', 'A429', 'L', 124, '-'),
(138, 'Nuka', NULL, 'No tiene', '1418', 'L', 244, '-'),
(139, 'Samy', NULL, '120731', 'A432', 'L', 227, '-'),
(140, 'Kim', NULL, '125522', 'A511', 'L', 25, '-'),
(141, 'Blues', NULL, 'No tiene', '1400', 'L', 271, '-'),
(142, 'Naia', NULL, '1970259', 'A459', 'L', 463, '-'),
(143, 'Aragorn', NULL, '1568043', 'A380', 'L', 458, '-'),
(144, 'Aizu', NULL, '1680441', 'A499', 'L', 293, '-'),
(145, 'Avemar', NULL, '1677435', 'A335', 'L', 320, '-'),
(146, 'Completa', NULL, '95707', 'A121', 'L', 380, '-'),
(147, 'Jayna', 'Border Collie', '1779666', 'A173', 'L', 235, 'GII'),
(148, 'Kira', 'Border Collie', '1710175', 'A116', 'L', 140, 'GIII'),
(149, 'Elfo', 'P.B. Malinoise', '1496681', '963', 'L', 260, 'GII'),
(150, 'Choco', NULL, '96941', 'A058', 'L', 77, '-'),
(151, 'Gini', NULL, '1891331', 'A193', 'L', 292, '-'),
(152, 'Alinka', NULL, '1677160', '982', 'L', 452, '-'),
(153, 'Zazpi', NULL, '1779668', 'A278', 'L', 117, '-'),
(154, 'Frasqui', NULL, '1962362', 'A438', 'L', 159, '-'),
(155, 'Yhara', NULL, '1827258', 'A357', 'L', 288, '-'),
(156, 'Lennon', 'A. Foxhound', '111486', 'A336', 'L', 347, 'GII'),
(157, 'Irma', NULL, '1779789', 'A096', 'L', 132, '-'),
(158, 'Yai', '', '1879217', 'A475', 'L', 42, '-'),
(159, 'Clara', 'Border Collie', '1936237', 'A462', 'L', 418, 'GII'),
(160, 'Laika', NULL, '93988', 'A224', 'L', 400, '-'),
(161, 'Argon', '', '1926695', '987', 'L', 96, 'GII'),
(162, 'Nube', '', '124447', 'A465', 'L', 319, '-'),
(163, 'Blue', 'Bearded Collie', '1863534', 'A552', 'L', 293, 'GIII'),
(164, 'Hana', NULL, 'No tiene', '1526', 'L', 412, '-'),
(165, 'Liss', NULL, '86748', '961', 'L', 237, '-'),
(166, 'Tara', NULL, '123013', 'A442', 'L', 216, '-'),
(167, 'Onis', '', '1478689', 'A498', 'L', 236, 'GII'),
(168, 'Qumba', NULL, '2007589', 'A546', 'L', 275, '-'),
(169, 'Jotave', NULL, '1962366', 'A407', 'L', 279, '-'),
(170, 'Kora', NULL, '124215', 'A585', 'L', 279, '-'),
(171, 'Zana', NULL, '1634512', 'A014', 'L', 430, '-'),
(172, 'Hugo', NULL, '97357', 'A201', 'L', 246, '-'),
(173, 'Gus', NULL, '111335', 'A548', 'L', 454, '-'),
(174, 'Paco', NULL, '85033', 'A550', 'L', 454, '-'),
(175, 'Thor', NULL, '1839333', 'A348', 'L', 257, '-'),
(176, 'Fol', '', '86747', '940', 'L', 350, 'GIII'),
(177, 'Fito', NULL, '1914405', 'A362', 'L', 164, '-'),
(178, 'Troy', '', '98617', 'A044', 'L', 238, 'GII'),
(179, 'Dana', NULL, 'No tiene', '1528', 'L', 341, '-'),
(180, 'Doña Sol', NULL, '1885107', 'A261', 'L', 285, '-'),
(181, 'Martina', NULL, '1833796', 'A545', 'L', 129, '-'),
(182, 'Kiss', NULL, '1626167', '912', 'L', 60, '-'),
(183, 'Nano', NULL, '94485', '964', 'L', 279, '-'),
(184, 'Ariel', NULL, '1835675', 'A213', 'L', 1, '-'),
(185, 'Cristina', NULL, '1929150', 'A352', 'L', 67, '-'),
(186, 'Rulo', NULL, '1676914', '978', 'L', 115, '-'),
(187, 'Hechizada', NULL, '1663984', 'A064', 'L', 135, '-'),
(188, 'Cindy', NULL, '1812230', 'A189', 'L', 6, '-'),
(189, 'Rotten', 'Carea Leonés', '', '1518', 'L', 243, 'GII'),
(190, 'Pixie Moon', '', '1879218', 'A260', 'L', 246, '-'),
(191, 'Timba', 'Canaan Dog', '119645', 'A403', 'L', 88, 'GIII'),
(192, 'Nuwa', '', '132677', 'A597', 'L', 443, 'GII'),
(193, 'Kora', NULL, '99230', 'A466', 'L', 221, '-'),
(194, 'Kona', NULL, '1986188', 'A524', 'L', 53, '-'),
(195, 'Arwen', 'Border Collie', '1753230', 'A151', 'L', 97, 'GII'),
(196, 'Blas', NULL, '1761207', 'A086', 'L', 254, '-'),
(197, 'Rudy', NULL, '102435', 'A113', 'L', 310, '-'),
(198, 'Ira', 'P.B.Malinoise', '101193', 'A141', 'L', 18, 'GII'),
(199, 'Terra', NULL, '114522', 'A328', 'L', 274, '-'),
(200, 'Kira', NULL, '120733', 'A405', 'L', 12, '-'),
(201, 'Nube', NULL, '97289', 'A515', 'L', 245, '-'),
(202, 'Mara', '', '80787', 'A396', 'L', 36, 'GII'),
(203, 'Trasto', NULL, 'No tiene', '1391', 'L', 6, '-'),
(204, 'Kiria', '', 'No tiene', 'A155', 'L', 231, 'GII'),
(205, 'Wind', 'Border Collie', '1881696', 'A421', 'L', 416, 'GIII'),
(206, 'Rocky', NULL, '1756734', 'A156', 'L', 239, '-'),
(207, 'Pepo', NULL, '1756735', 'A200', 'L', 239, '-'),
(208, 'Mina', NULL, '124583', 'A501', 'L', 204, '-'),
(209, 'Gipsy', NULL, 'No tiene', '1542', 'L', 206, '-'),
(210, 'Atril', NULL, '1985153', 'A454', 'L', 334, '-'),
(211, 'Max', 'Perro de Aguas Español', '1945286', 'A487', 'L', 43, 'GII'),
(212, 'N''Hug', '', '110043', 'A283', 'L', 238, 'GII'),
(213, 'Che', NULL, '1472786', 'A313', 'L', 285, '-'),
(214, 'Chincheta', NULL, '97802', 'A165', 'L', 439, '-'),
(215, 'Nemo', NULL, '1950998', 'A478', 'L', 142, '-'),
(216, 'Tao', NULL, '1807243', 'A554', 'L', 424, '-'),
(217, 'Luna', '', 'No tiene', '1539', 'L', 406, 'GII'),
(218, 'Molsa', NULL, '1710467', 'A103', 'L', 419, '-'),
(219, 'Dina', 'Border Collie', '1798019', 'A136', 'L', 208, 'GII'),
(220, 'Neo', 'Border Collie', '99972', 'A077', 'L', 58, 'GIII'),
(221, 'Zidanne', '', '2081050', 'A591', 'L', 19, 'GIII'),
(222, 'Poly de Terra de Gosos', '', '2060633', 'A587', 'L', 246, '-'),
(223, 'Kinder', '', '101433', 'A223', 'L', 400, 'GII'),
(224, 'Juno', '', 'No tiene', '1552', 'L', 68, 'GII'),
(225, 'Flash', NULL, '109519', 'A420', 'L', 174, '-'),
(226, 'Troya', NULL, '127255', 'A527', 'L', 461, '-'),
(227, 'Pluto', NULL, '80602', '785', 'L', 328, '-'),
(228, 'Poli', NULL, '98654', 'A290', 'L', 437, '-'),
(229, 'Shasta', 'Border Collie', '109487', 'A272', 'L', 331, 'GIII'),
(230, 'Kira', NULL, '83038', '789', 'L', 320, '-'),
(231, 'Maya', NULL, '1683945', 'A110', 'L', 283, '-'),
(232, 'Grace', NULL, '127031', 'A575', 'L', 375, '-'),
(233, 'Artemisa', NULL, '1911726', 'A406', 'L', 449, '-'),
(234, 'Ron', NULL, '94045', 'A265', 'L', 132, '-'),
(235, 'Maña', NULL, '1872301', 'A423', 'L', 86, '-'),
(236, 'Blue', NULL, '86743', 'A180', 'L', 122, '-'),
(237, 'Golfa', NULL, '1719195', 'A557', 'L', 200, '-'),
(238, 'Runa', NULL, '2006235', 'A537', 'L', 302, '-'),
(239, 'Fanta', '', '1940535', 'A544', 'L', 362, 'GII'),
(240, 'Xula', NULL, '83896', '844', 'M', 289, '-'),
(241, 'Danko', 'Ratonero Bodeguero Andaluz', '113906', 'A325', 'M', 230, 'GIII'),
(242, 'Hanna', NULL, '1843070', 'A159', 'M', 369, '-'),
(243, 'Guti', 'Cocker Spaniel', '111666', 'A215', 'M', 210, 'GIII'),
(244, 'Cala', 'Sheetland', '1691264', 'A095', 'M', 15, 'GIII'),
(245, 'Milo', NULL, '1399802', '718', 'M', 269, '-'),
(246, 'Gotika', NULL, '1682493', '971', 'M', 67, '-'),
(247, 'Drac', 'Cocker Spaniel', '106327', 'A196', 'M', 133, 'GIII'),
(248, 'Neo', NULL, '109522', 'A445', 'M', 174, '-'),
(249, 'Sra. Maruja', '', '1695088', '997', 'M', 139, '-'),
(250, 'Duna', '', '93084', '953', 'M', 161, 'GII'),
(251, 'Kiwi', '', '1982934', 'A525', 'M', 450, 'GIII'),
(252, 'Sucre', NULL, '1558711', '938', 'M', 366, '-'),
(253, 'Lass', 'Perro de Aguas Español', '131981', 'A580', 'M', 150, 'GIII'),
(254, 'Tuna', NULL, '1731780', 'A057', 'M', 319, '-'),
(255, 'Norai', NULL, '97039', 'A015', 'M', 154, '-'),
(256, 'Menta', 'Welsh Terrier', '1459964', '767', 'M', 14, 'GIII'),
(257, 'Wirbel', 'Schnnauzer', '1252941', '588', 'M', 331, 'Ret.'),
(258, 'Pepsi', '', '1849505', 'A270', 'M', 76, 'GII'),
(259, 'Kiss', NULL, '1258632', '762', 'M', 106, '-'),
(260, 'Ina', NULL, 'No tiene', '1549', 'M', 446, '-'),
(261, 'Kenia', NULL, '86609', '954', 'M', 403, '-'),
(262, 'Gamma', 'Sheetland', '1988761', 'A307', 'M', 226, 'GIII'),
(263, 'Goku', 'Fox Terrier Wire ', 'No tiene', '1513', 'M', 266, 'GIII'),
(264, 'Coockie', NULL, '120745', 'A434', 'M', 445, '-'),
(265, 'Habana', NULL, '131393', 'A634', 'M', 147, '-'),
(266, 'Dau', NULL, '113713', 'A446', 'M', 366, '-'),
(267, 'Jolie', 'Mudi', '1561798', '808', 'M', 90, 'GII'),
(268, 'Boira', '', '1809307', 'A281', 'M', 100, 'GIII'),
(269, 'Queen', '', '93971', '989', 'M', 278, 'GIII'),
(270, 'Legend', NULL, '1606337', '927', 'M', 440, '-'),
(271, 'Koku', NULL, '1394913', '714', 'M', 372, '-'),
(272, 'Luna', 'Fox Terrier Wire', '103250', 'A240', 'M', 382, 'GII'),
(273, 'Nell', NULL, '1329279', 'A293', 'M', 152, '-'),
(274, 'Lucas', 'Podenco Andaluz', '112275', 'A248', 'M', 20, 'GII'),
(275, 'Benavis', NULL, '1803327', 'A195', 'M', 29, '-'),
(276, 'Paquita', NULL, '1852290', 'A229', 'M', 377, '-'),
(277, 'Fey', NULL, '2063400', 'A440', 'M', 442, '-'),
(278, 'Robbi', NULL, '104953', 'A146', 'M', 448, '-'),
(279, 'Mate', 'Schnauzer', '1930526', 'A469', 'M', 109, 'GII'),
(280, 'Veron', '', '1423605', 'A412', 'M', 152, 'GII'),
(281, 'Lume', 'Sheetlan SheepDog', '1985134', 'A416', 'M', 233, 'GII'),
(282, 'Foska', NULL, '108199', 'A182', 'M', 285, '-'),
(283, 'Bimba', NULL, '1968121', 'A451', 'M', 344, '-'),
(284, 'Chocolate', NULL, '93486', 'A045', 'M', 439, '-'),
(285, 'Tuna', 'Bodeguero Andaliuz', '122113', 'A408', 'M', 230, 'GII'),
(286, 'Naru', NULL, '132678', 'A598', 'M', 443, '-'),
(287, 'Mizar', 'Schnauzer Miniatura', '1876817', 'A274', 'M', 145, 'GII'),
(288, 'Tina', NULL, 'No tiene', '1532', 'M', 201, '-'),
(289, 'Kenya', NULL, '112354', 'A233', 'M', 294, '-'),
(290, 'Duque', NULL, 'No tiene', '1551', 'M', 29, '-'),
(291, 'Harley', NULL, '2025374', 'A428', 'M', 128, '-'),
(292, 'Johnny Cash', 'Sheetland', '2057033', 'A594', 'M', 456, 'GIII'),
(293, 'Striptease', '', '2023589', 'A577', 'M', 84, 'GII'),
(294, 'Tuco', 'Shetland', '2026054', 'A419', 'M', 185, 'GII'),
(295, 'Mia', NULL, '121232', 'A547', 'M', 340, '-'),
(296, 'Gunilla', NULL, 'No tiene', '1471', 'M', 315, '-'),
(297, 'Alma', NULL, '90313', 'A566', 'M', 301, '-'),
(298, 'Noa', '', '1926077', 'A526', 'M', 391, 'GII'),
(299, 'Magia', 'Sheetland', '2032179', 'A528', 'S', 86, 'GIII'),
(300, 'Saroa', '', '1789456', 'A149', 'S', 464, 'GIII'),
(301, 'Melendi', 'Caniche', '1842276', 'A164', 'S', 311, 'GIII'),
(302, 'Mims', 'Caniche', '102903', 'A279', 'S', 10, 'GIII'),
(303, 'Hancock', NULL, '131702', 'A609', 'S', 349, '-'),
(304, 'Tris', 'Schnauzer', '119441', 'A398', 'S', 411, 'GII'),
(305, 'Lula', 'Pinscher', '123192', 'A607', 'S', 118, 'GIII'),
(306, 'Rufo', '', '123178', 'A435', 'S', 257, 'GII'),
(307, 'Sira', '', '106345', 'A168', 'S', 112, 'GIII'),
(308, 'Nit', 'Schnauzer Mini', '112007', 'A208', 'S', 357, 'GIII'),
(309, 'Xira', 'Schnauzer', '124731', 'A424', 'S', 432, 'GIII'),
(310, 'Che Guevara', 'Caniche', '112448', 'A230', 'S', 465, 'GIII'),
(311, 'Enzo', NULL, '117909', 'A444', 'S', 352, '-'),
(312, 'Nuca', 'Tibetan Spaniel', '109471', 'A181', 'S', 197, 'GIII'),
(313, 'Gismo', '', '123726', 'A449', 'S', 441, 'GIII'),
(314, 'Nana', 'Caniche', '103211', 'A277', 'S', 424, 'GIII'),
(315, 'Nikita', '', '123006', 'A443', 'S', 33, 'GIII'),
(316, 'Xena', '', '125524', 'A509', 'S', 138, 'GIII'),
(317, 'Chula', '', '106335', 'A135', 'S', 193, 'GIII'),
(318, 'Dagga', 'Tibetan Spaniel', '127338', 'A507', 'S', 181, 'GII'),
(319, 'Greta', '', '1849716', 'A337', 'S', 72, 'GII'),
(320, 'Bengel', 'Schnnauzer', '1433208', '760', 'S', 331, 'GII'),
(321, 'Nei', NULL, '1400011', '770', 'S', 191, '-'),
(322, 'Tess', NULL, '102439', 'A245', 'S', 203, '-'),
(323, 'Lia', 'Brazillian Terrier', '132000', 'A588', 'S', 188, 'GII'),
(324, 'Taca', '', '128455', 'A589', 'S', 37, 'GII'),
(325, 'Miche', '', '1706141', 'A097', 'S', 321, 'GII'),
(326, 'Manin', 'Schnauzer miniatura', '104120', 'A100', 'S', 411, 'GII'),
(327, 'Doña Matilde', 'Caniche', '2005359', 'A514', 'S', 139, 'GIII'),
(328, 'Aqua', NULL, '2056249', 'A513', 'S', 295, '-'),
(329, 'Nuca', 'Schnauzer', '1678476', 'A088', 'S', 2, 'GII'),
(330, 'Pepa', 'Jack Rusell', '1957259', 'A393', 'S', 160, 'GII'),
(331, 'Spyro', '', '131527', 'A569', 'S', 373, 'GIII'),
(332, 'Della', NULL, '2061744', 'A467', 'S', 106, '-'),
(333, 'Lua', '', '116884', 'A287', 'S', 582, 'GII'),
(334, 'Mayo', NULL, 'No tiene', '1419', 'S', 364, '-'),
(335, 'Nala', '', '108635', 'A530', 'S', 113, 'GIII'),
(336, 'Lola', NULL, 'No tiene', '1464', 'S', 211, '-'),
(337, 'Noah', NULL, '131506', 'A590', 'S', 227, '-'),
(338, 'Sully', NULL, '116639', 'A520', 'S', 397, '-'),
(339, 'Gus', NULL, '119626', 'A346', 'S', 56, '-'),
(340, 'Lola', NULL, 'No tiene', '1541', 'S', 301, '-'),
(341, 'Thor', 'Schnauzer', '1939205', 'A535', 'S', 2, 'GII'),
(342, 'Quillo', 'Rusky Toy', '127443', 'A604', 'S', 101, 'GII'),
(343, 'Lennon', NULL, '103239', 'A144', 'S', 355, '-'),
(344, 'Boira', NULL, 'No tiene', '1554', 'S', 420, '-'),
(345, 'Kyra', NULL, '131481', 'A600', 'S', 30, '-'),
(346, 'Acha', '', '123731', 'A483', 'M', 324, '-'),
(347, 'Ada', 'Mestizo', '', '1459', 'S', 398, 'GII'),
(348, 'Aker', 'P.B. Malinoise', '1553051', 'A397', 'L', 162, 'GII'),
(349, 'Akira', NULL, '125877', 'A455', 'L', 255, 'GII'),
(350, 'Dama', 'Fox Terrier Wire', '0131204', 'A641', 'M', 264, 'GIII'),
(351, 'Flai', 'Fox Terrier Wire', '0129738', 'A815', 'M', 264, 'GII'),
(352, 'Donna', 'Border Collie', '', 'A795', 'L', 405, 'GII'),
(353, 'Fito', 'mestizo', '', '', 'T', 387, 'GII'),
(354, 'paco', '', '', '', 'T', 1, 'GII'),
(355, 'Akela', 'Border Collie', '', 'A746', 'L', 155, 'GII'),
(356, 'Toska', 'Border Collie', '', '', 'L', 220, 'GI'),
(357, 'Sira', 'P.B.Malinoise', '', 'A584', 'L', 224, 'GII'),
(358, 'Duna', 'P. Aleman', '', 'A586', 'L', 455, 'GII'),
(359, 'Olivia', 'Schnauzer', '', 'A742', 'S', 287, 'GII'),
(360, 'Kyle', 'Schnauzer', '', 'A-539', 'M', 198, 'GII'),
(361, 'Kara', 'Border Collie', '', 'A-541', 'L', 399, 'GII'),
(362, 'Tibet', 'Border Collie', '', '', 'L', 59, 'GII'),
(363, 'Beltxa', 'Schnauzer', '', 'A622', 'S', 432, 'GIII'),
(365, 'Danah', 'P. Australiano', '', 'A889', 'L', 280, 'GII'),
(366, 'Net', 'P. Australiano', '', '', 'L', 192, 'GI'),
(367, 'Sira', 'Boxer', '', '', 'L', 111, 'GI'),
(368, 'Maggie', 'Mestizo', '', '1570', 'S', 402, 'GII'),
(369, 'Kiss', '', '', '', 'L', 202, 'GI'),
(370, 'Milka', '', '', '', 'L', 207, 'GI'),
(371, 'Mitzy', 'Braco', '', '', 'L', 47, 'GI'),
(372, 'Sura', 'Shar Pei', '', '', 'L', 335, 'GI'),
(374, 'Neo', '', '', '', 'L', 220, 'GII'),
(375, 'Sitan', '', '', '795', 'L', 224, 'GII'),
(376, 'Kaiser', '', '', 'A383', 'S', 202, 'GII'),
(377, 'Geha', 'Border Collie', '', 'A162', 'L', 308, 'GII'),
(378, 'Momo', 'Borde Collie', '', 'A391', 'L', 409, 'GII'),
(379, 'Skay', 'Perro de Aguas Español', '', 'A605', 'L', 212, 'GII'),
(380, 'Keko', '', '', '', 'L', 17, 'GII'),
(381, 'Mambo', 'Border Collie', '', '', 'L', 39, 'GI'),
(382, 'Swing', '', '', '', 'M', 371, 'GI'),
(383, 'Trufa', 'Perro de Aguas Español', '', '', 'M', 415, 'GI'),
(384, 'Soma', 'Labrador Retriever', '', 'A696', 'L', 343, 'GII'),
(385, 'Phoebe', 'Border Collie', '', 'A555', 'M', 148, 'GII'),
(386, 'Izzie', 'West Higland White Terrier', '', 'A275', 'S', 148, 'GII'),
(387, 'Kyra', 'Border Collie', '', '1607', 'L', 531, 'GII'),
(388, 'Nika', 'Border Collie', '', '', 'L', 74, 'GI'),
(389, 'Dolce', 'Border Collie', '', 'A681', 'L', 531, 'GII'),
(390, 'Dolce', '', '', '', 'L', 361, 'GII'),
(391, 'Gilda', 'Border Collie', '', 'A723', 'L', 361, 'GII'),
(392, 'Noa', 'Border Collie', '', 'A540', 'L', 384, 'GII'),
(393, 'Thor', 'Border Collie', '', 'A835', 'L', 270, 'GII'),
(394, 'Akira Haru', 'BorderCollie', '', 'A311', 'L', 172, 'GII'),
(395, 'Nala', 'BorderCollie', '', 'A142', 'L', 141, 'GIII'),
(396, 'Peka', '', '', 'A615', 'L', 259, 'GII'),
(397, 'Milady', 'Border Collie', '', 'A791', 'L', 407, 'GII'),
(398, 'Yashi', 'Pastor de los Pirineos', '', '', 'M', 453, 'GI'),
(399, 'Dollar', 'Border Collie', '', '911', 'L', 309, 'GII'),
(400, 'Héctor', 'Pastor Vasco', '', '1597', 'L', 114, 'GII'),
(401, 'Ron', 'Border Collie', '', 'A617', 'L', 376, 'GII'),
(402, 'Viconte', '', '', 'A367', 'L', 284, 'GII'),
(403, 'Agran', 'Border Collie', '', 'A752', 'L', 8, 'GII'),
(404, 'Bimba', 'Border Collie', '', 'A288', 'L', 314, 'GII'),
(405, 'Dudy', 'Border Collie', '', 'A753', 'L', 276, 'GII'),
(406, 'Kember', 'Bóxer', '', '1558', 'L', 7, 'GII'),
(407, 'Luca', 'Ratonero Bodeguero Andaluz', '', 'A749', 'M', 101, 'GII'),
(408, 'Kika', 'West Higland White Terrier', '', 'A722', 'S', 365, 'GII'),
(409, 'Olivia', 'Caniche', '', 'A701', 'S', 102, 'GII'),
(410, 'Sombra', 'Border Collie', '', 'A679', 'L', 242, 'GII'),
(411, 'Noah', 'Schnauzer', '', '', 'S', 198, 'GI'),
(412, 'Brea', 'Mestizo', '', '', 'L', 31, 'GII'),
(413, 'Lola', 'Jack Rusell', '', 'A633', 'S', 160, 'GII'),
(414, 'Yeny', 'Border Collie', '', 'A747', 'L', 245, 'GII'),
(415, 'Sonic', 'Perro de Aguas Español', '', 'A495', 'L', 98, 'GII'),
(416, 'Wind', 'Border Collie', '', 'A561', 'L', 427, 'GII'),
(417, 'Crak', 'Border Collie', '', 'A719', 'L', 374, 'GIII'),
(418, 'Nitra', 'Caniche', '', 'A743', 'S', 465, 'GII'),
(419, 'Nova', 'Border Collie', '', 'A642', 'L', 414, 'GII'),
(420, 'Momo', 'Border Collie', '', 'A621', 'L', 34, 'GII'),
(421, 'Byron', 'Border Collie', '', '', 'L', 413, 'GI'),
(422, 'Amis', 'Border Collie', '', 'A620', 'L', 247, 'GII'),
(423, 'Noa', 'BorderCollie', '', 'A143', 'L', 213, 'GII'),
(424, 'Vlad', 'Perro de Aguas Español', '', 'A752', 'L', 45, 'GII'),
(425, 'Mou', 'Kelpie Australiano', '', '', 'L', 82, 'GI'),
(426, 'Black', 'Border Collie', '', '', 'L', 258, 'GI'),
(427, 'Kala', 'Yorkshire Terrier', '', '1574', 'S', 99, 'GII'),
(428, 'Andy', 'Border Collie', '', 'A671', 'L', 317, 'GII'),
(429, 'beep', '', '', '', 'L', 392, 'GI'),
(430, 'Ella', '', '', '', 'L', 316, 'GI'),
(431, 'Nya', '', '', '', 'L', 3, 'GI'),
(432, 'Tibet', '', '', '', 'L', 103, 'GI'),
(433, 'Kenzo', '', '', '', 'L', 460, 'GI'),
(434, 'Dafne', '', '', 'A870', 'L', 272, 'GI'),
(435, 'Nut', '', '', '', 'L', 421, 'GI'),
(436, 'Morgan', '', '', '', 'L', 133, 'GI'),
(437, 'Juke', '', '', '', 'L', 336, 'GI'),
(438, 'Argi', '', '', '', 'L', 149, 'GI'),
(439, 'Pi', '', '', '', 'L', 169, 'GI'),
(440, 'Horatio', 'Bulldog Frances', '', 'A847', 'S', 73, 'GII'),
(441, 'Goldie', '', '', '', 'S', 228, 'GII'),
(442, 'Cachirulo', 'Perro de Aguas Español', '', 'A578', 'L', 116, 'GIII'),
(443, 'Lucky', '', '', '1561', 'L', 23, 'GII'),
(444, 'Chika', 'Border Collie', '', 'A518', 'L', 291, 'GII'),
(445, 'Inka', '', '', 'A699', 'L', 100, 'GII'),
(446, 'Nupsi', '', '', 'A694', 'L', 75, 'GII'),
(447, 'Inka', 'Border Collie', '', 'A381', 'L', 386, 'GII'),
(448, 'Tiri', '', '', 'A782', 'L', 241, 'GIII'),
(449, 'Itoitz', '', '', 'A713', 'L', 13, 'GII'),
(450, 'Xira', '', '', '1499', 'L', 5, 'GII'),
(451, 'Gala', '', '', 'A500', 'L', 26, 'GII'),
(452, 'Luna', '', '', 'A809', 'L', 50, 'GII'),
(453, 'Lorenzo', 'Border Collie', '', 'A666', 'L', 389, 'GII'),
(454, 'Bombon', '', '', 'A773', 'L', 51, 'GII'),
(455, 'Lluna', '', '', '1537', 'L', 51, 'GII'),
(456, 'Ardi de Rioja', '', '', '0729', 'L', 228, 'GII'),
(457, 'Danko', '', '', '1509', 'L', 110, 'GII'),
(458, 'Tessa', '', '', 'A317', 'L', 433, 'GII'),
(459, 'Rex', '', '', '1578', 'L', 438, 'GII'),
(460, 'Chica', '', '', 'A703', 'L', 170, 'GII'),
(461, 'Sue', '', '', 'A788', 'L', 435, 'GII'),
(462, 'Kira', '', '', '', 'L', 93, 'GII'),
(463, 'Heidy', '', '', 'A496', 'L', 282, 'GII'),
(464, 'Bella', '', '', '1586', 'L', 305, 'GII'),
(465, 'Hanna', '', '', '1584', 'L', 11, 'GII'),
(466, 'Eo', 'Border Collie', '', 'A768', 'L', 323, 'GII'),
(467, 'Panda', '', '', 'A797', 'L', 296, 'GII'),
(468, 'Venus', '', '', 'A743', 'L', 311, 'GII'),
(469, 'Charly', '', '', '1592', 'L', 232, 'GII'),
(470, 'Argi', '', '', '1600', 'L', 186, 'GII'),
(471, 'Broto', '', '', 'A764', 'L', 431, 'GII'),
(472, 'Anouk', '', '', 'A480', 'L', 434, 'GII'),
(473, 'Flecha', '', '', 'A748', 'L', 423, 'GII'),
(475, 'Moa', 'Stanfordshire', '', 'A508', 'L', 164, 'GII'),
(476, 'Greta', '', '', '', 'L', 229, 'GII'),
(477, 'Lur', '', '', 'A741', 'L', 178, 'GII'),
(478, 'Charli', '', '', 'A655', 'L', 63, 'GII'),
(479, 'Lia', '', '', '1563', 'L', 304, 'GII'),
(480, 'Nica', '', '', 'A875', 'S', 157, 'GII'),
(481, 'Erinka', '', '', 'A761', 'S', 370, 'GII'),
(482, 'Ursus', '', '', '', 'S', 184, 'GII'),
(483, 'Ela', '', '', 'A618', 'S', 363, 'GII'),
(484, 'Lua', '', '', 'A763', 'S', 61, 'GII'),
(485, 'Wembley', '', '', 'A769', 'S', 76, 'GII'),
(486, 'Rikke', '', '', '', 'S', 62, 'GII'),
(487, 'Imo', '', '', 'A631', 'M', 229, 'GII'),
(488, 'Alpargata', 'Perro de Aguas Español', '', 'A606', 'M', 152, 'GII'),
(489, 'Lucky Luque', 'Caniche', '', 'A766', 'S', 195, 'GIII'),
(490, 'Salma', '', '', 'A521', 'S', 222, 'GII'),
(491, 'Dracma', '', '', 'A626', 'S', 138, 'GII'),
(492, 'Jade', 'Border Collie', '', 'A629', 'L', 225, 'GIII'),
(493, 'Rinoa', '', '', 'A447', 'L', 325, 'GIII'),
(494, 'Chus', '', '', '934', 'L', 225, 'GIII'),
(495, 'Gala', 'Border Collie', '', 'A674', 'L', 127, 'GIII'),
(496, 'Chamán', 'Border Collie', '', 'A262', 'L', 386, 'GIII'),
(497, 'Koira', '', '', 'A252', 'L', 326, 'GIII'),
(498, 'Kobu', '', '', 'A806', 'M', 187, 'GIII'),
(499, 'Zeus', 'Cocker Americano', '', 'A139', 'M', 325, 'GIII'),
(500, 'Gea', '', '', 'A175', 'M', 268, 'GIII'),
(501, 'Sacha', 'Perro de Aguas Español', '', 'A632', 'M', 167, 'GIII'),
(502, 'Fran', 'Perro de Aguas Español', '', 'A028', 'M', 116, 'GIII'),
(503, 'Peka', 'Border Collie', '', 'A291', 'M', 291, 'GIII'),
(504, 'Time', '', '', 'A571', 'M', 416, 'GIII'),
(505, 'Alma', 'Pastor de los Pirineos', '', '874', 'M', 136, 'GIII'),
(506, 'Kira', '', '', 'A600', 'S', 30, 'GIII'),
(507, 'Cleo', 'Spitz Aleman Enano', '', 'A152', 'S', 85, 'GIII'),
(508, 'Luna', 'Spitz Enano', '', '0956', 'S', 85, 'GIII'),
(509, 'Nika', 'Cocker Americano', '', 'A082', 'S', 325, 'GIII'),
(510, 'Quenn', 'Palleiro', '', '', 'M', 47, 'GI'),
(511, 'Gala', 'Pastor Alemán', '', '', 'L', 40, 'GI'),
(512, 'Moly', 'Jack Rusell', '', '1633', 'S', 194, 'GII'),
(513, 'Vali', 'Border Collie', '', 'A811', 'L', 9, 'GII'),
(514, 'Isis', 'Border Collie', '', '', 'L', 471, 'GI'),
(515, 'Darco', 'Beagle', '', '', 'M', 46, 'GI'),
(516, 'Bruce', 'Ratonero Bodeguero Andaluz', '', 'A813', 'M', 230, 'GII'),
(517, 'Nut', 'Pastor Belga Malinoise', '', 'A-886', 'L', 162, 'GII'),
(518, 'Lluna', 'Border Collie', '', '', 'L', 459, 'GI'),
(519, 'Dunah', 'Cocker Spaniel', '', 'A635', 'M', 28, 'GII'),
(520, 'Aska', 'Border Collie', '', 'A899', 'L', 306, 'GII'),
(521, 'Buck', 'Border Collie', '', 'A877', 'L', 81, 'GII'),
(522, 'Merche', 'Schnauzer Gigante', '', 'A989', 'L', 303, 'GII'),
(523, 'Bambú', 'Border Collie', '', '', 'L', 217, 'GI'),
(524, 'King', 'Caniche Toy', '', 'A683', 'S', 263, 'GIII'),
(525, 'Toxo', '', '', 'A677', 'S', 277, 'GII'),
(526, 'Newton', '', '', '', 'L', 137, 'GI'),
(527, 'Cala', 'Bearded Collie', '', 'A697', 'L', 293, 'GII'),
(528, 'Onna', '', '', '', 'L', 356, 'GII'),
(529, 'Trasgo', '', '', 'A650', 'L', 171, 'GII'),
(530, 'Samba', '', '', 'A702', 'M', 385, 'GII'),
(531, 'Charlie', '', '', 'A532', 'S', 385, 'GIII'),
(532, 'Alan', '', '', '', 'L', 190, 'GI'),
(533, 'Moly', '', '', '', 'L', 70, 'GI'),
(534, 'Pipa', '', '', 'A718', 'S', 297, 'GII'),
(535, 'Chola', 'Cocker Spaniel', '', 'A691', 'M', 329, 'GII'),
(536, 'Blue', '', '', '', 'S', 21, 'GI'),
(537, 'Fly', 'Border Collie', '', 'A826', 'L', 180, 'GIII'),
(538, 'Sira', '', '', '', 'L', 338, 'GII'),
(539, 'Xhyla', '', '', 'A711', 'L', 108, 'GII'),
(540, 'Lua', '', '', 'A689', 'M', 108, 'GII'),
(541, 'Deva', '', '', '', 'L', 451, 'GI'),
(542, 'Duna', '', '', '', 'L', 158, 'GI'),
(543, 'Jefrelú', '', '', '', 'L', 94, 'GI'),
(544, 'Lumy', '', '', '', 'L', 24, 'GI'),
(545, 'Magui', '', '', '', 'L', 94, 'GI'),
(546, 'Trenty', '', '', '', 'L', 322, 'GI'),
(547, 'Xana', '', '', '', 'L', 368, 'GI'),
(548, 'Zoe', '', '', '', 'L', 134, 'GI'),
(549, 'Zoe', '', '', '', 'L', 156, 'GI'),
(550, 'Eria', '', '', '', 'S', 173, 'GI'),
(551, 'Tinka', '', '', '', 'S', 342, 'GI'),
(552, 'Blue', '', '', '1572', 'L', 346, 'GII'),
(553, 'Dana', '', '', 'A688', 'L', 346, 'GII'),
(554, 'Phoebe', 'Border Collie', '', 'A690', 'L', 196, 'GII'),
(555, 'Sella', '', '', '', 'L', 94, 'GII'),
(556, 'botinera', '', '', '', 'L', 466, 'GI'),
(558, 'Rumba', '', '', '', 'L', 467, 'GI'),
(559, 'Perla', 'Yorkshire', '', '', 'S', 488, 'GII'),
(560, 'Arturo', 'Mestizo', '', '', 'S', 477, 'GII'),
(561, 'Yara', '', '', '', 'L', 489, 'GI'),
(562, 'Arya', '', '', '', 'L', 399, 'P.A.'),
(563, 'Sasha', '', '', '', 'L', 490, 'P.A.'),
(564, 'Zar', '', '', '', 'L', 491, 'P.A.'),
(565, 'Lillo', 'Jack Russel', '', '', 'S', 492, 'GI'),
(566, 'Amy', '', '', '', 'L', 493, 'P.A.'),
(567, 'Dana', '', '', '', 'L', 493, 'P.A.'),
(568, 'Golfo', '', '', '', 'L', 494, 'P.A.'),
(569, 'Ron', '', '', '', 'L', 494, 'P.A.'),
(570, 'Putt', '', '', '', 'L', 371, 'P.A.'),
(571, 'Onza', 'PDAE', '', '', 'M', 496, 'GI'),
(572, 'Sancho', '', '', '', 'M', 497, 'P.A.'),
(573, 'Manzanillo', '', '', '', 'S', 498, 'GI'),
(574, 'Arwen', '', '', '', 'L', 473, 'GI'),
(575, 'Nashira', 'Border Collie', '', 'A903', 'L', 499, 'GII'),
(576, 'Chuli', 'Border Collie', '', '', 'L', 482, 'GI'),
(577, 'Skye', 'Border Collie', '', '', 'L', 500, 'GI'),
(581, 'Lady', '', '', '', 'L', 501, 'P.A.'),
(582, 'Zoe', '', '', '', 'L', 502, 'P.A.'),
(583, 'Tabu', '', '', '', 'L', 491, 'P.A.'),
(584, 'Ursula', 'Jack Russel', '', 'A840', 'S', 478, 'GII'),
(585, 'Ari', 'Border Collie', '', 'A894', 'L', 468, 'GII'),
(586, 'Hippie', 'Ratonero Valenciano', '', '', 'M', 504, 'GI'),
(587, 'Mara', 'Schnauzer', '', '', 'S', 472, 'GI'),
(588, 'Greta', 'Cocker Spaniel', '', '', 'M', 505, 'GI'),
(589, 'Iris', 'Border Collie', '', '', 'L', 424, 'GI'),
(590, 'Bruni', 'Border Collie', '', '', 'L', 506, 'GI'),
(591, 'Dana', 'Border Collie', '', '', 'L', 507, 'GI'),
(592, 'Lua', 'Mestizo', '', '', 'L', 508, 'GI'),
(593, 'Ada', 'Mestizo', '', '1647', 'L', 475, 'GII'),
(594, 'Mashel', 'Jack Russel', '', 'A931', 'S', 474, 'GII'),
(595, 'Lola', 'Whippet', '', '', 'M', 509, 'GI'),
(596, 'Moly', 'Border Collie', '', '', 'L', 7, 'GI'),
(597, 'Coco', 'Labrador Retriever', '', '', 'L', 510, 'GI'),
(598, 'Nika', 'Border Collie', '', 'A821', 'L', 140, 'GII'),
(599, 'Cayenne', 'Border Collie', '', '28123', 'L', 511, 'GIII'),
(600, 'Etna', 'Border Collie', '', '34873', 'L', 512, 'GIII'),
(601, 'Lee-Ann', 'Border Colliie', '', '', 'L', 197, 'GII'),
(602, 'Sucre', 'Border Collie', '', 'A920', 'L', 5, 'GII'),
(603, 'Killa', 'Mestizo', '', 'Pendiente', 'L', 513, 'GII'),
(604, 'Mizar', 'Border Collie', '', '', 'L', 514, 'GI'),
(605, 'Hera', 'Border Collie', '', '', 'L', 483, 'GI'),
(606, 'Nora', 'P.B. Malinoise', '', 'A305', 'L', 515, 'GII'),
(607, 'Hela', 'Border Collie', '', 'A836', 'L', 92, 'GII'),
(608, 'Sendy', 'Border Collie', '', 'A926', 'L', 487, 'GII'),
(609, 'Lumbre', 'Border Collie', '', 'A932', 'L', 485, 'GII'),
(610, 'Baddy', 'Border Collie', '', '', 'L', 481, 'GII'),
(612, 'Ginn', 'Pastor del Pirineo', '', '', 'M', 516, 'GI'),
(613, 'Quillo', 'Pastor del Pirineo', '', 'A904', 'M', 516, 'GII'),
(614, 'Ben', 'Caniche Enano', '', 'A928', 'S', 517, 'GIII'),
(615, 'Winnie', 'Pastor del Pirineo', '', 'A929', 'M', 517, 'GIII'),
(616, 'Mika', 'Mestizo', '', '', 'S', 518, 'GI'),
(617, 'Wendy', 'Mestizo', '', '', 'M', 519, 'GI'),
(618, 'Thais', 'Labrador', '', '1640', 'L', 469, 'GII'),
(620, 'Arya', '', '', '', 'L', 520, 'P.A.'),
(621, 'Iris', '', '', '', 'L', 521, 'P.A.'),
(622, 'Truco', 'Border Collie', '', '', 'L', 522, 'GI'),
(623, 'Nubia', 'Pastor Alemán', '', 'A-309', 'L', 276, 'GII'),
(624, 'Bora', 'Border Collie', '', '', 'L', 465, 'GI'),
(625, 'Knut', '', '', '', 'L', 523, 'GI'),
(626, 'Hera', '', '', '', 'L', 523, 'GII'),
(627, 'Arashi', '', '', '', 'L', 207, 'P.A.'),
(628, 'Shiva', '', '', '', 'L', 40, 'P.A.'),
(629, 'Vash', '', '', '', 'L', 220, 'P.A.'),
(630, 'Coba', 'Border Collie', '', '', 'L', 524, 'P.A.'),
(631, 'Yoda', '', '', '', 'L', 526, 'P.A.'),
(632, 'Parche', '', '', '', 'L', 525, 'P.A.'),
(633, 'Asics', 'Pastor Belga Malinois', '', '', 'L', 312, 'GI'),
(634, 'Menta', 'Border Collie', '', 'A450', 'L', 476, 'GII'),
(635, 'Turco', 'Perro de Agua Español', '', 'A643', 'M', 175, 'GIII'),
(636, 'Witzig', 'Pastor Pirineos', '', '', 'M', 331, 'GII'),
(637, 'Lis', 'Border Collie', '', '', 'L', 527, 'GI'),
(639, 'Boss', 'Border Collie', '', '', 'L', 111, 'GI'),
(640, 'Nueve', 'West Highland', '', '', 'S', 528, 'GI'),
(641, 'Neo', 'Border Collie', '', '', 'L', 529, 'GI'),
(642, 'Charly', 'Jack Rusell', '', '', 'S', 101, 'GI'),
(643, 'Wiki', 'Borde Collie', '', 'A941', 'L', 39, 'GII'),
(644, 'Boss', 'Border Collie', '', '797', 'L', 530, 'GI'),
(646, 'Nora', 'Border Coliie', '', 'A624', 'L', 265, 'GII'),
(647, 'Maggie', 'Border Collie', '', 'A817', 'L', 406, 'GIII'),
(648, 'karioka', '', '', 'A534', 'L', 35, 'GIII'),
(649, 'Nikita', 'P.B. Malinoise', '', 'A087', 'L', 532, 'GIII'),
(651, 'Laila', 'P. B. Groenendael', '', 'A592', 'L', 292, 'GIII'),
(652, 'Panza-Ulima', 'Perro de Aguas Español', '', 'A614', 'M', 189, 'GIII'),
(653, 'Mixta', 'Chihuahua', '', 'A842', 'S', 533, 'GIII'),
(654, 'Bandido', 'Perro de Aguas Español', '', '', 'L', 534, 'GII'),
(655, 'Ringo', '', '', '', 'L', 535, 'GI'),
(656, 'Flipi', '', '', '', 'L', 536, 'GI'),
(657, 'kas', 'Border Collie', '', '', 'L', 323, 'GI'),
(658, 'ChanChan', 'Border Collie', '', '', 'L', 77, 'GI'),
(659, 'Cherry de Ydre', 'P.B. Malinoise', '', '', 'L', 32, 'GI'),
(660, 'Uma', '', '', '', 'L', 537, 'GI'),
(661, 'Olivia', '', '', '', 'L', 538, 'GI'),
(662, 'Cinza', 'Border Collie', '', '', 'L', 411, 'GI'),
(663, 'Turka', 'Perro de Aguas Español', '', '', 'M', 539, 'GI'),
(664, 'Merak', 'Schnauzer miniatura', '', '', 'M', 145, 'GI'),
(665, 'Nixon', '', '', '', 'M', 540, 'GI'),
(666, 'Coco', '', '', '', 'S', 541, 'GI'),
(667, 'Chispa', '', '', '', 'S', 542, 'GI'),
(668, 'Ammy', 'Parson Russel Terrier', '', 'A953', 'S', 112, 'GII'),
(669, 'Hachi', 'Shetland SheepDog', '', 'A805', 'S', 543, 'GII'),
(670, 'Mambo', 'Caniche', '', 'A892', 'S', 544, 'GII'),
(671, 'Coco', 'Schnauzer', '', '941', 'S', 64, 'GII'),
(672, 'Match', 'Sheetland SheepDog', '', 'A665', 'M', 233, 'GII'),
(673, 'Mía', 'Perro de Aguas Español', '', '', 'M', 545, 'GII'),
(674, 'Ness', 'Border Collie', '', 'A800', 'L', 546, 'GII'),
(675, 'Roxo', 'Labrador Retriever', '', '1644', 'L', 547, 'GII'),
(676, 'Blue', 'Border Collie', '', 'A630', 'L', 548, 'GII'),
(677, 'Zaila', 'Border Collie', '', 'A897', 'L', 549, 'GII'),
(678, 'Indi', 'Border Collie', '', 'A801', 'L', 543, 'GII'),
(679, 'Bela', 'Border Collie', '', 'A890', 'L', 233, 'GII'),
(680, 'Noara', 'Labrador Retriever', '', 'A924', 'L', 550, 'GII'),
(681, 'Xesta', 'Border Collie', '', 'A948', 'L', 277, 'GII'),
(682, 'Ska', 'Border Collie', '', 'A888', 'L', 181, 'GII'),
(683, 'Luke', 'Border Collie', '', 'A935', 'L', 551, 'GII'),
(684, 'Blue Jazz', 'Border Collie', '', 'A854', 'L', 182, 'GII'),
(685, 'Hanna', '', '', '', 'M', 424, 'P.A.'),
(686, 'Ella', '', '', '', 'S', 552, 'GI'),
(687, 'Mía', '', '', '', 'S', 552, 'P.A.'),
(688, 'Luna', '', '', '', 'L', 553, 'GI'),
(689, 'Nala', '', '', '', 'L', 553, 'GI'),
(690, 'Sacha', '', '', '', 'L', 554, 'P.A.'),
(691, 'Guru', '', '', '', 'L', 555, 'GI'),
(692, 'Beltxi', '', '', '', 'S', 556, 'GI'),
(693, 'Maly', '', '', '', 'M', 557, 'GI'),
(694, 'Nube', '', '', '', 'L', 558, 'GI'),
(695, 'Sombra', '', '', '', 'L', 559, 'GI'),
(697, 'Siux', '', '', '', 'L', 326, 'GI'),
(698, 'Xenna', '', '', '', 'L', 560, 'GI'),
(699, 'Belatz', '', '', '', 'L', 561, 'GI'),
(700, 'Stella Maris', '', '', '', 'L', 562, 'GI'),
(701, 'Shura', '', '', '', 'L', 563, 'GI'),
(702, 'Marea', '', '', '', 'L', 564, 'GI'),
(703, 'Onyx', '', '', '', 'M', 565, 'GI'),
(704, 'Sweet', '', '', '', 'M', 566, 'GI'),
(705, 'Kenua', '', '', '', 'M', 567, 'GI'),
(706, 'Kalesy', '', '', '', 'S', 568, 'GI'),
(707, 'Cheroky', '', '', '', 'L', 569, 'GII'),
(708, 'Alteza', '', '', '', 'L', 35, 'GII'),
(709, 'Tato', '', '', 'A893', 'L', 232, 'GII'),
(710, 'Malú', '', '', 'A882', 'L', 570, 'GII'),
(711, 'Lía', '', '', 'A813', 'L', 189, 'GII'),
(712, 'Shoyer', '', '', 'A476', 'L', 123, 'GII'),
(713, 'Fox', '', '', '', 'L', 360, 'GII'),
(714, 'Gala', '', '', 'A530', 'L', 571, 'GII'),
(715, 'Cuco', '', '', '1555', 'L', 91, 'GII'),
(716, 'Patxi', '', '', 'A668', 'L', 290, 'GII'),
(717, 'Luna', '', '', '1643', 'L', 572, 'GII'),
(718, 'Izar', '', '', 'A943', 'L', 573, 'GII'),
(719, 'Mitsuko Kika', '', '', '', 'L', 143, 'GII'),
(720, 'Duncan', '', '', 'A684', 'L', 574, 'GII'),
(721, 'Tom', '', '', '1648', 'L', 575, 'GII'),
(722, 'Hiru', '', '', 'A601', 'L', 576, 'GII'),
(723, 'Gamberro', '', '', '', 'L', 380, 'GII'),
(724, 'Coba', '', '', '', 'L', 380, 'GII'),
(725, 'Argón', '', '', 'A488', 'L', 577, 'GII'),
(726, 'Sira', '', '', 'A968', 'L', 578, 'GII'),
(727, 'Xari', '', '', '', 'L', 63, 'GII'),
(728, 'Peke', '', '', 'A883', 'M', 19, 'GII'),
(729, 'Banda', '', '', 'A834', 'M', 10, 'GII'),
(730, 'Audrey', '', '', 'A849', 'M', 579, 'GII'),
(731, 'Lug', '', '', 'A841', 'M', 138, 'GII'),
(732, 'Rumba', '', '', '', 'M', 580, 'GII'),
(733, 'Shira', '', '', 'A960', 'M', 581, 'GII'),
(734, 'Nana', '', '', 'A912', 'S', 157, 'GII'),
(735, 'Ava', '', '', 'A667', 'S', 583, 'GII'),
(736, 'Alegra', '', '', 'A258', 'S', 360, 'GII'),
(737, 'Simona', '', '', 'A971', 'S', 584, 'GII'),
(738, 'Ray', '', '', '1622', 'S', 561, 'GII'),
(739, 'Bruno', '', '', 'A951', 'S', 585, 'GII'),
(740, 'Lua', '', '', 'A729', 'L', 586, 'GIII'),
(741, 'Maqui', '', '', '1575', 'L', 251, 'GIII'),
(742, 'Shiro', '', '', 'A799', 'M', 587, 'GIII'),
(743, 'Kenia', '', '', 'A686', 'S', 267, 'GIII');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincias`
--
-- Creación: 06-02-2015 a las 13:03:58
--

DROP TABLE IF EXISTS `provincias`;
CREATE TABLE IF NOT EXISTS `provincias` (
  `Provincia` varchar(32) NOT NULL DEFAULT '',
  `Comunidad` varchar(32) DEFAULT NULL,
  `Codigo` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `provincias`:
--

--
-- Volcado de datos para la tabla `provincias`
--

INSERT INTO `provincias` (`Provincia`, `Comunidad`, `Codigo`) VALUES
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
-- Estructura de tabla para la tabla `pruebas`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `pruebas`;
CREATE TABLE IF NOT EXISTS `pruebas` (
  `ID` int(4) NOT NULL,
  `Operador` int(4) DEFAULT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Club` int(4) NOT NULL DEFAULT '1',
  `Ubicacion` varchar(255) DEFAULT NULL,
  `Triptico` longblob,
  `Cartel` longblob,
  `Observaciones` varchar(255) DEFAULT NULL,
  `RSCE` tinyint(1) NOT NULL DEFAULT '0',
  `Selectiva` tinyint(1) NOT NULL DEFAULT '0',
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `pruebas`:
--   `Club`
--       `clubes` -> `ID`
--

--
-- Volcado de datos para la tabla `pruebas`
--

INSERT INTO `pruebas` (`ID`, `Operador`, `Nombre`, `Club`, `Ubicacion`, `Triptico`, `Cartel`, `Observaciones`, `RSCE`, `Selectiva`, `Cerrada`) VALUES
(1, NULL, '-- Sin asignar --', 1, NULL, NULL, NULL, 'NO BORRAR: Prueba por defecto para jornadas huerfanas', 0, 0, 1),
(6, NULL, 'Prueba de enero', 35, 'Serranillos del Valle', '', '', '31 enero - 1 febrero 2015', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultados`
--
-- Creación: 11-02-2015 a las 10:37:27
--

DROP TABLE IF EXISTS `resultados`;
CREATE TABLE IF NOT EXISTS `resultados` (
  `Prueba` int(4) NOT NULL,
  `Jornada` int(4) NOT NULL,
  `Manga` int(4) NOT NULL,
  `Dorsal` int(4) NOT NULL,
  `Perro` int(4) NOT NULL,
  `Equipo` int(4) NOT NULL DEFAULT '0',
  `Nombre` varchar(255) NOT NULL,
  `Licencia` varchar(255) NOT NULL DEFAULT '--------',
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Grado` varchar(16) NOT NULL DEFAULT '-',
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
  `Pendiente` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `resultados`:
--   `Perro`
--       `perros` -> `ID`
--   `Manga`
--       `mangas` -> `ID`
--   `Prueba`
--       `pruebas` -> `ID`
--   `Jornada`
--       `jornadas` -> `ID`
--

--
-- Volcado de datos para la tabla `resultados`
--

INSERT INTO `resultados` (`Prueba`, `Jornada`, `Manga`, `Dorsal`, `Perro`, `Equipo`, `Nombre`, `Licencia`, `Categoria`, `Grado`, `NombreGuia`, `NombreClub`, `Entrada`, `Comienzo`, `Faltas`, `Rehuses`, `Tocados`, `Eliminado`, `NoPresentado`, `Tiempo`, `Observaciones`, `Pendiente`) VALUES
(6, 33, 59, 55, 79, 5, 'Jade', '', 'L', 'GI', 'Rosa Maria Cañadillas', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 59, 30, 356, 5, 'Toska', '', 'L', 'GI', 'Jesús Gómez', 'Agilcan', '2015-01-31 14:48:00', '2015-01-31 14:48:00', 7, 1, 0, 0, 0, 37.83, '', 0),
(6, 33, 59, 28, 371, 5, 'Mitzy', '', 'L', 'GI', 'Angel González', 'Agilcan', '2015-01-31 14:49:14', '2015-01-31 14:49:14', 0, 1, 0, 0, 0, 56.77, '', 0),
(6, 33, 59, 86, 381, 5, 'Mambo', '', 'L', 'GI', 'Ana Palet', 'Vallgorguina', '2015-01-31 14:58:23', '2015-01-31 14:58:23', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 59, 45, 382, 5, 'Swing', '', 'M', 'GI', 'Olga Palomares', 'Deporcan', '2015-01-31 15:03:41', '2015-01-31 15:03:41', 0, 0, 0, 0, 0, 35.56, '', 0),
(6, 33, 59, 46, 383, 5, 'Trufa', '', 'M', 'GI', 'Rosa Rubio', 'Deporcan', '2015-01-31 15:12:17', '2015-01-31 15:12:17', 2, 1, 0, 0, 0, 35.38, '', 0),
(6, 33, 59, 31, 510, 5, 'Quenn', '', 'M', 'GI', 'Angel González', 'Agilcan', '2015-01-31 15:05:07', '2015-01-31 15:05:07', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 59, 44, 571, 5, 'Onza', '', 'M', 'GI', 'Irene Blanco', 'Deporcan', '2015-01-31 15:01:43', '2015-01-31 15:01:43', 0, 2, 0, 0, 0, 45.82, '', 0),
(6, 33, 59, 48, 573, 5, 'Manzanillo', '', 'S', 'GI', 'Virginia Pastor', 'Deporcan', '2015-01-31 15:10:13', '2015-01-31 15:10:13', 1, 2, 0, 1, 0, 0, '', 0),
(6, 33, 59, 115, 576, 5, 'Chuli', '', 'L', 'GI', 'Yaiza Caballero Fernández', 'Xanastur', '2015-01-31 14:45:03', '2015-01-31 14:45:03', 1, 1, 0, 0, 0, 43.17, '', 0),
(6, 33, 59, 116, 577, 5, 'Skye', '', 'L', 'GI', 'Ana Belén Ondategui Casas', 'Xanastur', '2015-01-31 14:45:57', '2015-01-31 14:45:57', 1, 2, 0, 0, 0, 44.08, '', 0),
(6, 33, 59, 42, 589, 5, 'Iris', '', 'L', 'GI', 'Sara Montila', 'Deporcan', '2015-01-31 14:46:22', '2015-01-31 14:46:22', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 59, 75, 593, 5, 'Ada', '1647', 'L', 'GII', 'Sonia García', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 59, 87, 595, 5, 'Lola', '', 'M', 'GI', 'Pablo Parra', 'Mi Perro 10', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 59, 96, 601, 5, 'Lee-Ann', '', 'L', 'GII', 'Iván Pardo García', 'Vallgorguina', '2015-01-31 14:53:51', '2015-01-31 14:53:51', 1, 2, 0, 1, 0, 0, '', 0),
(6, 33, 59, 55, 604, 5, 'Mizar', '', 'L', 'GI', 'Rómulo Parrilla', 'Eslón', '2015-01-31 14:59:21', '2015-01-31 14:59:21', 2, 0, 0, 1, 0, 0, '', 0),
(6, 33, 59, 54, 605, 5, 'Hera', '', 'L', 'GI', 'Carlos Escribano', 'Eslón', '2015-01-31 14:55:14', '2015-01-31 14:55:14', 0, 3, 0, 1, 0, 0, '', 0),
(6, 33, 59, 49, 616, 5, 'Mika', '', 'S', 'GI', 'Eli Brandsaeter', 'Deporcan', '2015-01-31 15:08:42', '2015-01-31 15:08:42', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 59, 47, 617, 5, 'Wendy', '', 'M', 'GI', 'Nicolas Alcaide', 'Deporcan', '2015-01-31 15:02:46', '2015-01-31 15:02:46', 0, 0, 0, 0, 0, 41.75, '', 0),
(6, 33, 59, 97, 622, 5, 'Truco', '', 'L', 'GI', 'Marta Acero', 'Vallgorguina', '2015-01-31 14:52:40', '2015-01-31 14:52:40', 1, 3, 0, 1, 0, 0, '', 0),
(6, 33, 59, 52, 624, 5, 'Bora', '', 'L', 'GI', 'Yulia Morugova', 'El Nogueral', '2015-01-31 14:58:07', '2015-01-31 14:58:07', 2, 2, 0, 0, 0, 39.47, '', 0),
(6, 33, 59, 1, 633, 5, 'Asics', '', 'L', 'GI', 'Luis Miguel Rodrigo', 'A-0', '2015-01-31 14:55:39', '2015-01-31 14:55:39', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 59, 94, 637, 5, 'Lis', '', 'L', 'GI', 'Miriam Villar', 'Tandem', '2015-01-31 14:57:00', '2015-01-31 14:57:00', 1, 0, 0, 0, 0, 32.04, '', 0),
(6, 33, 59, 40, 639, 5, 'Boss', '', 'L', 'GI', 'David Escribano', 'Cubas', '2015-01-31 14:46:45', '2015-01-31 14:46:45', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 59, 25, 640, 5, 'Nueve', '', 'S', 'GI', 'Miguel Angel Manzaneda', 'AA Y CIA', '2015-01-31 15:11:19', '2015-01-31 15:11:19', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 59, 29, 641, 5, 'Neo', '', 'L', 'GI', 'Mercedes Prieto', 'Agilcan', '2015-01-31 14:50:30', '2015-01-31 14:50:30', 2, 3, 0, 1, 0, 0, '', 0),
(6, 33, 59, 98, 642, 5, 'Charly', '', 'S', 'GI', 'Cristina González', 'Vallgorguina', '2015-01-31 15:10:39', '2015-01-31 15:10:39', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 60, 55, 79, 5, 'Jade', '', 'L', 'GI', 'Rosa Maria Cañadillas', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 60, 30, 356, 5, 'Toska', '', 'L', 'GI', 'Jesús Gómez', 'Agilcan', '2015-01-31 15:54:33', '2015-01-31 15:54:33', 1, 0, 0, 1, 0, 0, '', 0),
(6, 33, 60, 28, 371, 5, 'Mitzy', '', 'L', 'GI', 'Angel González', 'Agilcan', '2015-01-31 15:59:39', '2015-01-31 15:59:39', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 60, 86, 381, 5, 'Mambo', '', 'L', 'GI', 'Ana Palet', 'Vallgorguina', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 60, 45, 382, 5, 'Swing', '', 'M', 'GI', 'Olga Palomares', 'Deporcan', '2015-01-31 15:43:41', '2015-01-31 15:43:41', 0, 1, 0, 0, 0, 41.48, '', 0),
(6, 33, 60, 46, 383, 5, 'Trufa', '', 'M', 'GI', 'Rosa Rubio', 'Deporcan', '2015-01-31 15:39:31', '2015-01-31 15:39:31', 0, 0, 0, 0, 0, 34.3, '', 0),
(6, 33, 60, 31, 510, 5, 'Quenn', '', 'M', 'GI', 'Angel González', 'Agilcan', '2015-01-31 15:38:16', '2015-01-31 15:38:16', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 60, 44, 571, 5, 'Onza', '', 'M', 'GI', 'Irene Blanco', 'Deporcan', '2015-01-31 15:41:08', '2015-01-31 15:41:08', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 60, 48, 573, 5, 'Manzanillo', '', 'S', 'GI', 'Virginia Pastor', 'Deporcan', '2015-01-31 15:36:38', '2015-01-31 15:36:38', 0, 2, 0, 0, 0, 50.85, '', 0),
(6, 33, 60, 115, 576, 5, 'Chuli', '', 'L', 'GI', 'Yaiza Caballero Fernández', 'Xanastur', '2015-01-31 15:58:05', '2015-01-31 15:58:05', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 60, 116, 577, 5, 'Skye', '', 'L', 'GI', 'Ana Belén Ondategui Casas', 'Xanastur', '2015-01-31 15:57:43', '2015-01-31 15:57:43', 2, 2, 0, 0, 0, 52.27, '', 0),
(6, 33, 60, 42, 589, 5, 'Iris', '', 'L', 'GI', 'Sara Montila', 'Deporcan', '2015-01-31 15:48:54', '2015-01-31 15:48:54', 0, 1, 0, 0, 0, 33.84, '', 0),
(6, 33, 60, 75, 593, 5, 'Ada', '1647', 'L', 'GII', 'Sonia García', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 60, 87, 595, 5, 'Lola', '', 'M', 'GI', 'Pablo Parra', 'Mi Perro 10', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 60, 96, 601, 5, 'Lee-Ann', '', 'L', 'GII', 'Iván Pardo García', 'Vallgorguina', '2015-01-31 15:47:36', '2015-01-31 15:47:36', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 60, 55, 604, 5, 'Mizar', '', 'L', 'GI', 'Rómulo Parrilla', 'Eslón', '2015-01-31 15:45:44', '2015-01-31 15:45:44', 1, 0, 0, 1, 0, 0, '', 0),
(6, 33, 60, 54, 605, 5, 'Hera', '', 'L', 'GI', 'Carlos Escribano', 'Eslón', '2015-01-31 15:49:57', '2015-01-31 15:49:57', 0, 1, 0, 0, 0, 38.77, '', 0),
(6, 33, 60, 49, 616, 5, 'Mika', '', 'S', 'GI', 'Eli Brandsaeter', 'Deporcan', '2015-01-31 15:34:15', '2015-01-31 15:34:15', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 60, 47, 617, 5, 'Wendy', '', 'M', 'GI', 'Nicolas Alcaide', 'Deporcan', '2015-01-31 15:42:45', '2015-01-31 15:42:45', 1, 0, 0, 0, 0, 42.46, '', 0),
(6, 33, 60, 97, 622, 5, 'Truco', '', 'L', 'GI', 'Marta Acero', 'Vallgorguina', '2015-01-31 15:53:50', '2015-01-31 15:53:50', 1, 2, 0, 0, 0, 85.09, '', 0),
(6, 33, 60, 52, 624, 5, 'Bora', '', 'L', 'GI', 'Yulia Morugova', 'El Nogueral', '2015-01-31 15:56:23', '2015-01-31 15:56:23', 1, 1, 0, 0, 0, 33.83, '', 0),
(6, 33, 60, 1, 633, 5, 'Asics', '', 'L', 'GI', 'Luis Miguel Rodrigo', 'A-0', '2015-01-31 15:46:33', '2015-01-31 15:46:33', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 60, 94, 637, 5, 'Lis', '', 'L', 'GI', 'Miriam Villar', 'Tandem', '2015-01-31 16:00:39', '2015-01-31 16:00:39', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 60, 40, 639, 5, 'Boss', '', 'L', 'GI', 'David Escribano', 'Cubas', '2015-01-31 15:44:51', '2015-01-31 15:44:51', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 60, 25, 640, 5, 'Nueve', '', 'S', 'GI', 'Miguel Angel Manzaneda', 'AA Y CIA', '2015-01-31 15:31:51', '2015-01-31 15:31:51', 1, 2, 0, 1, 0, 0, '', 0),
(6, 33, 60, 29, 641, 5, 'Neo', '', 'L', 'GI', 'Mercedes Prieto', 'Agilcan', '2015-01-31 15:51:17', '2015-01-31 15:51:17', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 60, 98, 642, 5, 'Charly', '', 'S', 'GI', 'Cristina González', 'Vallgorguina', '2015-01-31 15:32:42', '2015-01-31 15:32:42', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 18, 8, 5, 'Thelma', '824', 'L', 'GII', 'Fabian Santolaya', 'A. D. A. Pozuelo', '2015-01-31 16:37:47', '2015-01-31 16:37:47', 1, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 62, 52, 5, 'Mister', 'A250', 'L', 'GII', 'Rubén García', 'Eslón', '2015-01-31 16:36:50', '2015-01-31 16:36:50', 0, 1, 0, 0, 0, 37.17, '', 0),
(6, 33, 61, 26, 75, 5, 'Nut', 'A430', 'L', 'GII', 'Mónica Rodríguez', 'AA Y CIA', '2015-01-31 16:35:41', '2015-01-31 16:35:41', 0, 2, 0, 1, 0, 0, '', 0),
(6, 33, 61, 103, 95, 5, 'Raissa', 'A563', 'L', 'GII', 'Javier Iniesta', 'Vallgorguina', '2015-01-31 17:06:00', '2015-01-31 17:06:00', 1, 0, 0, 0, 0, 45.58, '', 0),
(6, 33, 61, 63, 103, 5, 'Red Magic', 'A124', 'L', 'GII', 'Elena Miguel', 'Eslón', '2015-01-31 17:00:31', '2015-01-31 17:00:31', 0, 0, 0, 0, 0, 38.72, '', 0),
(6, 33, 61, 86, 113, 5, 'Yun', 'A484', 'L', 'GIII', 'Concepción Fernández', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 61, 60, 156, 5, 'Lennon', 'A336', 'L', 'GII', 'Miguel Angel García', 'Eslón', '2015-01-31 16:48:43', '2015-01-31 16:48:43', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 73, 189, 5, 'Rotten', '1518', 'L', 'GII', 'José Luis Prieto', 'Illa Blanca', '2015-01-31 16:55:49', '2015-01-31 16:55:49', 1, 0, 0, 0, 0, 38.1, '', 0),
(6, 33, 61, 101, 202, 5, 'Mara', 'A396', 'L', 'GII', 'Ana Isabel Escobar', 'Vallgorguina', '2015-01-31 16:52:15', '2015-01-31 16:52:15', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 61, 112, 229, 5, 'Shasta', 'A272', 'L', 'GIII', 'Mario Rodríguez', 'W.E.L.P.E.', '2015-01-31 17:05:02', '2015-01-31 17:05:02', 0, 0, 0, 0, 0, 36, '', 0),
(6, 33, 61, 9, 272, 5, 'Luna', 'A240', 'M', 'GII', 'Paula Rello', 'A-0', '2015-01-31 17:14:49', '2015-01-31 17:14:49', 2, 0, 0, 0, 0, 40.59, '', 0),
(6, 33, 61, 21, 330, 5, 'Pepa', 'A393', 'S', 'GII', 'Francisco de la Cruz', 'A. D. C. Pozuelo', '2015-01-31 17:17:33', '2015-01-31 17:17:33', 2, 0, 0, 0, 0, 39.08, '', 0),
(6, 33, 61, 108, 342, 5, 'Quillo', 'A604', 'S', 'GII', 'Cristina González', 'Vallgorguina', '2015-01-31 17:27:27', '2015-01-31 17:27:27', 0, 1, 0, 0, 0, 45.58, '', 0),
(6, 33, 61, 99, 348, 5, 'Aker', 'A397', 'L', 'GII', 'Francisco Javier Jaen', 'Vallgorguina', '2015-01-31 16:44:40', '2015-01-31 16:44:40', 0, 1, 0, 0, 0, 49.44, '', 0),
(6, 33, 61, 1, 354, 5, 'paco', '', 'T', 'GII', '-- Sin asignar --', '-- Sin asignar --', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 61, 32, 355, 5, 'Akela', 'A746', 'L', 'GII', 'Fernando Bibián', 'Agilcan', '2015-01-31 16:34:31', '2015-01-31 16:34:31', 0, 1, 0, 0, 0, 40.34, '', 0),
(6, 33, 61, 33, 357, 5, 'Sira', 'A584', 'L', 'GII', 'Joaquín Andrés', 'Agilcan', '2015-01-31 16:48:20', '2015-01-31 16:48:20', 0, 0, 0, 0, 0, 42.81, '', 0),
(6, 33, 61, 36, 361, 5, 'Kara', 'A-541', 'L', 'GII', 'Ramón García Maroto', 'Cinco Huesos', '2015-01-31 16:31:12', '2015-01-31 16:31:12', 1, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 100, 380, 5, 'Keko', '', 'L', 'GII', 'Alberto González', 'Vallgorguina', '2015-01-31 16:56:44', '2015-01-31 16:56:44', 0, 0, 0, 0, 0, 43.05, '', 0),
(6, 33, 61, 50, 384, 5, 'Soma', 'A696', 'L', 'GII', 'Menchu Melcom', 'Deporcan', '2015-01-31 16:54:56', '2015-01-31 16:54:56', 2, 0, 0, 0, 0, 56.35, '', 0),
(6, 33, 61, 106, 386, 5, 'Izzie', 'A275', 'S', 'GII', 'Estíbaliz Pereda Navarro', 'Vallgorguina', '2015-01-31 17:18:30', '2015-01-31 17:18:30', 0, 0, 0, 0, 0, 43.5, '', 0),
(6, 33, 61, 88, 390, 5, 'Dolce', '', 'L', 'GII', 'Natividad Ruiz García', 'A-0', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 61, 5, 391, 5, 'Gilda', 'A723', 'L', 'GII', 'Natividad Ruiz García', 'A-0', '2015-01-31 16:58:49', '2015-01-31 16:58:49', 0, 0, 0, 0, 0, 33.08, '', 0),
(6, 33, 61, 7, 392, 5, 'Noa', 'A540', 'L', 'GII', 'Pedro Delgado Fernandez', 'A-0', '2015-01-31 16:43:41', '2015-01-31 16:43:41', 0, 0, 0, 0, 0, 32.93, '', 0),
(6, 33, 61, 65, 393, 5, 'Thor', 'A835', 'L', 'GII', 'Juan Carlos Ruiz', 'Eslón', '2015-01-31 16:53:37', '2015-01-31 16:53:37', 0, 0, 0, 0, 0, 34.9, '', 0),
(6, 33, 61, 56, 394, 5, 'Akira Haru', 'A311', 'L', 'GII', 'Gema López', 'Eslón', '2015-01-31 16:40:53', '2015-01-31 16:40:53', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 17, 397, 5, 'Milady', 'A791', 'L', 'GII', 'Ricardo Santolaya', 'A. D. C. Pozuelo', '2015-01-31 16:59:17', '2015-01-31 16:59:17', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 61, 79, 399, 5, 'Dollar', '911', 'L', 'GII', 'Luis de Frías', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 61, 77, 400, 5, 'Héctor', '1597', 'L', 'GII', 'David Gómez-Calcerrada', 'La Princesa', '2015-01-31 16:30:37', '2015-01-31 16:30:37', 1, 0, 0, 0, 0, 36.92, '', 0),
(6, 33, 61, 82, 401, 5, 'Ron', 'A617', 'L', 'GII', 'Oscar Sacristan', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 61, 105, 407, 5, 'Luca', 'A749', 'M', 'GII', 'Cristina González', 'Vallgorguina', '2015-01-31 17:13:38', '2015-01-31 17:13:38', 0, 0, 0, 0, 0, 43.68, '', 0),
(6, 33, 61, 107, 408, 5, 'Kika', 'A722', 'S', 'GII', 'Noelia Mouchet', 'Vallgorguina', '2015-01-31 17:20:41', '2015-01-31 17:20:41', 1, 2, 0, 0, 0, 49.82, '', 0),
(6, 33, 61, 27, 410, 5, 'Sombra', 'A679', 'L', 'GII', 'Jose Luis J. Mori', 'AA Y CIA', '2015-01-31 16:37:04', '2015-01-31 16:37:04', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 61, 20, 413, 5, 'Lola', 'A633', 'S', 'GII', 'Francisco de la Cruz', 'A. D. C. Pozuelo', '2015-01-31 17:24:56', '2015-01-31 17:24:56', 2, 0, 0, 0, 0, 39.94, '', 0),
(6, 33, 61, 19, 414, 5, 'Yeny', 'A747', 'L', 'GII', 'José Luis Romero', 'A. D. C. Pozuelo', '2015-01-31 17:08:14', '2015-01-31 17:08:14', 1, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 92, 416, 5, 'Wind', 'A561', 'L', 'GII', 'Sergio Casalins', 'Educan', '2015-01-31 16:33:27', '2015-01-31 16:33:27', 2, 0, 0, 0, 0, 35.51, '', 0),
(6, 33, 61, 53, 418, 5, 'Nitra', 'A743', 'S', 'GII', 'Yulia Morugova', 'El Nogueral', '2015-01-31 17:22:59', '2015-01-31 17:22:59', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 66, 424, 5, 'Vlad', 'A752', 'L', 'GII', 'Angel Corroto', 'Eslón', '2015-01-31 17:07:21', '2015-01-31 17:07:21', 2, 1, 0, 0, 0, 46.08, '', 0),
(6, 33, 61, 67, 427, 5, 'Kala', '1574', 'S', 'GII', 'Cristina Cortijo', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 61, 57, 428, 5, 'Andy', 'A671', 'L', 'GII', 'Maite Guerrero', 'Eslón', '2015-01-31 16:34:41', '2015-01-31 16:34:41', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 61, 11, 440, 5, 'Horatio', 'A847', 'S', 'GII', 'Beatriz Juan', 'A-0', '2015-01-31 17:19:38', '2015-01-31 17:19:38', 0, 1, 0, 0, 0, 53.33, '', 0),
(6, 33, 61, 102, 517, 5, 'Nut', 'A-886', 'L', 'GII', 'Francisco Javier Jaen', 'Vallgorguina', '2015-01-31 17:01:06', '2015-01-31 17:01:06', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 79, 522, 5, 'Merche', 'A989', 'L', 'GII', 'Lourdes Rivera', 'La Princesa', '2015-01-31 16:32:44', '2015-01-31 16:32:44', 1, 1, 0, 0, 0, 46.61, '', 0),
(6, 33, 61, 10, 560, 5, 'Arturo', '', 'S', 'GII', 'Dolores Rosas', 'A-0', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 61, 80, 575, 5, 'Nashira', 'A903', 'L', 'GII', 'Ruben López', 'La Princesa', '2015-01-31 17:03:25', '2015-01-31 17:03:25', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 12, 584, 5, 'Ursula', 'A840', 'S', 'GII', 'Pablo Martínez', 'A-0', '2015-01-31 17:16:27', '2015-01-31 17:16:27', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 61, 3, 585, 5, 'Ari', 'A894', 'L', 'GII', 'Veronica Roda', 'A-0', '2015-01-31 17:10:13', '2015-01-31 17:10:13', 2, 1, 0, 0, 0, 44.28, '', 0),
(6, 33, 61, 75, 593, 5, 'Ada', '1647', 'L', 'GII', 'Sonia García', 'La Princesa', '2015-01-31 16:45:48', '2015-01-31 16:45:48', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 88, 594, 5, 'Mashel', 'A931', 'S', 'GII', 'Ainhoa de Frias', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 61, 110, 602, 5, 'Sucre', 'A920', 'L', 'GII', 'Adrian Bajo', 'Vila-Real', '2015-01-31 16:49:55', '2015-01-31 16:49:55', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 61, 59, 607, 5, 'Hela', 'A836', 'L', 'GII', 'Cesar Losada Mera', 'Eslón', '2015-01-31 16:51:15', '2015-01-31 16:51:15', 0, 0, 0, 0, 0, 34.1, '', 0),
(6, 33, 61, 64, 608, 5, 'Sendy', 'A926', 'L', 'GII', 'Carlos Martínez S.', 'Eslón', '2015-01-31 16:47:14', '2015-01-31 16:47:14', 2, 1, 0, 0, 0, 38.88, '', 0),
(6, 33, 61, 61, 609, 5, 'Lumbre', 'A932', 'L', 'GII', 'Arancha Ruipérez Moslares', 'Eslón', '2015-01-31 16:38:52', '2015-01-31 16:38:52', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 61, 58, 610, 5, 'Baddy', '', 'L', 'GII', 'David Calviño', 'Eslón', '2015-01-31 17:02:25', '2015-01-31 17:02:25', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 61, 34, 618, 5, 'Thais', '1640', 'L', 'GII', 'Elena Cardenal', 'Agilcan', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 61, 78, 634, 5, 'Menta', 'A450', 'L', 'GII', 'Jesus Perea', 'La Princesa', '2015-01-31 17:04:08', '2015-01-31 17:04:08', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 61, 113, 636, 5, 'Witzig', '', 'M', 'GII', 'Mario Rodríguez', 'W.E.L.P.E.', '2015-01-31 17:12:48', '2015-01-31 17:12:48', 2, 1, 0, 0, 0, 65.41, '', 0),
(6, 33, 61, 104, 643, 5, 'Wiki', 'A941', 'L', 'GII', 'Ana Palet', 'Vallgorguina', '2015-01-31 16:57:54', '2015-01-31 16:57:54', 0, 1, 0, 0, 0, 45.72, '', 0),
(6, 33, 62, 18, 8, 5, 'Thelma', '824', 'L', 'GII', 'Fabian Santolaya', 'A. D. A. Pozuelo', '2015-01-31 18:49:04', '2015-01-31 18:49:04', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 62, 62, 52, 5, 'Mister', 'A250', 'L', 'GII', 'Rubén García', 'Eslón', '2015-01-31 19:21:09', '2015-01-31 19:21:09', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 62, 26, 75, 5, 'Nut', 'A430', 'L', 'GII', 'Mónica Rodríguez', 'AA Y CIA', '2015-01-31 18:49:22', '2015-01-31 18:49:22', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 62, 103, 95, 5, 'Raissa', 'A563', 'L', 'GII', 'Javier Iniesta', 'Vallgorguina', '2015-01-31 19:02:57', '2015-01-31 19:02:57', 0, 0, 0, 0, 0, 41.58, '', 0),
(6, 33, 62, 63, 103, 5, 'Red Magic', 'A124', 'L', 'GII', 'Elena Miguel', 'Eslón', '2015-01-31 19:09:48', '2015-01-31 19:09:48', 0, 0, 0, 0, 0, 35.7, '', 0),
(6, 33, 62, 86, 113, 5, 'Yun', 'A484', 'L', 'GIII', 'Concepción Fernández', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 62, 60, 156, 5, 'Lennon', 'A336', 'L', 'GII', 'Miguel Angel García', 'Eslón', '2015-01-31 18:48:17', '2015-01-31 18:48:17', 1, 0, 0, 0, 0, 38.16, '', 0),
(6, 33, 62, 73, 189, 5, 'Rotten', '1518', 'L', 'GII', 'José Luis Prieto', 'Illa Blanca', '2015-01-31 19:21:06', '2015-01-31 19:21:06', 0, 2, 0, 0, 0, 44.85, '', 0),
(6, 33, 62, 101, 202, 5, 'Mara', 'A396', 'L', 'GII', 'Ana Isabel Escobar', 'Vallgorguina', '2015-01-31 18:52:54', '2015-01-31 18:52:54', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 62, 112, 229, 5, 'Shasta', 'A272', 'L', 'GIII', 'Mario Rodríguez', 'W.E.L.P.E.', '2015-01-31 19:10:20', '2015-01-31 19:10:20', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 62, 9, 272, 5, 'Luna', 'A240', 'M', 'GII', 'Paula Rello', 'A-0', '2015-01-31 19:19:26', '2015-01-31 19:19:26', 0, 0, 0, 0, 0, 40.7, '', 0),
(6, 33, 62, 21, 330, 5, 'Pepa', 'A393', 'S', 'GII', 'Francisco de la Cruz', 'A. D. C. Pozuelo', '2015-01-31 19:22:15', '2015-01-31 19:22:15', 2, 0, 0, 0, 0, 43.02, '', 0),
(6, 33, 62, 108, 342, 5, 'Quillo', 'A604', 'S', 'GII', 'Cristina González', 'Vallgorguina', '2015-01-31 19:27:24', '2015-01-31 19:27:24', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 62, 99, 348, 5, 'Aker', 'A397', 'L', 'GII', 'Francisco Javier Jaen', 'Vallgorguina', '2015-01-31 19:00:19', '2015-01-31 19:00:19', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 62, 1, 354, 5, 'paco', '', 'T', 'GII', '-- Sin asignar --', '-- Sin asignar --', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 62, 32, 355, 5, 'Akela', 'A746', 'L', 'GII', 'Fernando Bibián', 'Agilcan', '2015-01-31 19:03:54', '2015-01-31 19:03:54', 0, 0, 0, 0, 0, 31.91, '', 0),
(6, 33, 62, 33, 357, 5, 'Sira', 'A584', 'L', 'GII', 'Joaquín Andrés', 'Agilcan', '2015-01-31 19:09:01', '2015-01-31 19:09:01', 1, 0, 0, 0, 0, 41.01, '', 0),
(6, 33, 62, 36, 361, 5, 'Kara', 'A-541', 'L', 'GII', 'Ramón García Maroto', 'Cinco Huesos', '2015-01-31 18:50:31', '2015-01-31 18:50:31', 1, 0, 0, 0, 0, 37.36, '', 0),
(6, 33, 62, 100, 380, 5, 'Keko', '', 'L', 'GII', 'Alberto González', 'Vallgorguina', '2015-01-31 19:07:47', '2015-01-31 19:07:47', 0, 0, 0, 0, 0, 35.82, '', 0),
(6, 33, 62, 50, 384, 5, 'Soma', 'A696', 'L', 'GII', 'Menchu Melcom', 'Deporcan', '2015-01-31 18:55:18', '2015-01-31 18:55:18', 0, 2, 0, 1, 0, 0, '', 0),
(6, 33, 62, 106, 386, 5, 'Izzie', 'A275', 'S', 'GII', 'Estíbaliz Pereda Navarro', 'Vallgorguina', '2015-01-31 19:29:06', '2015-01-31 19:29:06', 0, 0, 0, 0, 0, 42.33, '', 0),
(6, 33, 62, 88, 390, 5, 'Dolce', '', 'L', 'GII', 'Natividad Ruiz García', 'A-0', '2015-01-31 18:40:22', '2015-01-31 18:40:22', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 62, 5, 391, 5, 'Gilda', 'A723', 'L', 'GII', 'Natividad Ruiz García', 'A-0', '2015-01-31 19:13:14', '2015-01-31 19:13:14', 0, 0, 0, 0, 0, 29.54, '', 0),
(6, 33, 62, 7, 392, 5, 'Noa', 'A540', 'L', 'GII', 'Pedro Delgado Fernandez', 'A-0', '2015-01-31 19:14:03', '2015-01-31 19:14:03', 0, 0, 0, 0, 0, 31.62, '', 0),
(6, 33, 62, 65, 393, 5, 'Thor', 'A835', 'L', 'GII', 'Juan Carlos Ruiz', 'Eslón', '2015-01-31 19:11:28', '2015-01-31 19:11:28', 1, 0, 0, 0, 0, 32.01, '', 0),
(6, 33, 62, 56, 394, 5, 'Akira Haru', 'A311', 'L', 'GII', 'Gema López', 'Eslón', '2015-01-31 18:42:53', '2015-01-31 18:42:53', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 62, 17, 397, 5, 'Milady', 'A791', 'L', 'GII', 'Ricardo Santolaya', 'A. D. C. Pozuelo', '2015-01-31 18:54:23', '2015-01-31 18:54:23', 0, 0, 0, 0, 0, 29.77, '', 0),
(6, 33, 62, 79, 399, 5, 'Dollar', '911', 'L', 'GII', 'Luis de Frías', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 62, 77, 400, 5, 'Héctor', '1597', 'L', 'GII', 'David Gómez-Calcerrada', 'La Princesa', '2015-01-31 19:06:56', '2015-01-31 19:06:56', 0, 0, 0, 0, 0, 35.87, '', 0),
(6, 33, 62, 82, 401, 5, 'Ron', 'A617', 'L', 'GII', 'Oscar Sacristan', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 62, 105, 407, 5, 'Luca', 'A749', 'M', 'GII', 'Cristina González', 'Vallgorguina', '2015-01-31 19:19:40', '2015-01-31 19:19:40', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 62, 107, 408, 5, 'Kika', 'A722', 'S', 'GII', 'Noelia Mouchet', 'Vallgorguina', '2015-01-31 19:31:06', '2015-01-31 19:31:06', 0, 1, 0, 0, 0, 47.63, '', 0),
(6, 33, 62, 27, 410, 5, 'Sombra', 'A679', 'L', 'GII', 'Jose Luis J. Mori', 'AA Y CIA', '2015-01-31 18:40:32', '2015-01-31 18:40:32', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 62, 20, 413, 5, 'Lola', 'A633', 'S', 'GII', 'Francisco de la Cruz', 'A. D. C. Pozuelo', '2015-01-31 19:31:08', '2015-01-31 19:31:08', 1, 0, 0, 0, 0, 41.34, '', 0),
(6, 33, 62, 19, 414, 5, 'Yeny', 'A747', 'L', 'GII', 'José Luis Romero', 'A. D. C. Pozuelo', '2015-01-31 19:23:13', '2015-01-31 19:23:13', 2, 0, 0, 0, 0, 31.69, '', 0),
(6, 33, 62, 92, 416, 5, 'Wind', 'A561', 'L', 'GII', 'Sergio Casalins', 'Educan', '2015-01-31 19:15:48', '2015-01-31 19:15:48', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 62, 53, 418, 5, 'Nitra', 'A743', 'S', 'GII', 'Yulia Morugova', 'El Nogueral', '2015-01-31 19:23:04', '2015-01-31 19:23:04', 0, 0, 0, 0, 0, 34.26, '', 0),
(6, 33, 62, 66, 424, 5, 'Vlad', 'A752', 'L', 'GII', 'Angel Corroto', 'Eslón', '2015-01-31 18:56:44', '2015-01-31 18:56:44', 0, 1, 0, 0, 0, 39.34, '', 0),
(6, 33, 62, 67, 427, 5, 'Kala', '1574', 'S', 'GII', 'Cristina Cortijo', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 62, 57, 428, 5, 'Andy', 'A671', 'L', 'GII', 'Maite Guerrero', 'Eslón', '2015-01-31 18:40:51', '2015-01-31 18:40:51', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 62, 11, 440, 5, 'Horatio', 'A847', 'S', 'GII', 'Beatriz Juan', 'A-0', '2015-01-31 19:26:56', '2015-01-31 19:26:56', 0, 0, 0, 0, 0, 51.99, '', 0),
(6, 33, 62, 102, 517, 5, 'Nut', 'A-886', 'L', 'GII', 'Francisco Javier Jaen', 'Vallgorguina', '2015-01-31 18:47:13', '2015-01-31 18:47:13', 0, 0, 0, 0, 0, 36.39, '', 0),
(6, 33, 62, 79, 522, 5, 'Merche', 'A989', 'L', 'GII', 'Lourdes Rivera', 'La Princesa', '2015-01-31 18:58:29', '2015-01-31 18:58:29', 0, 1, 0, 0, 0, 40.99, '', 0),
(6, 33, 62, 10, 560, 5, 'Arturo', '', 'S', 'GII', 'Dolores Rosas', 'A-0', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 62, 80, 575, 5, 'Nashira', 'A903', 'L', 'GII', 'Ruben López', 'La Princesa', '2015-01-31 18:45:49', '2015-01-31 18:45:49', 1, 1, 0, 0, 0, 37.45, '', 0),
(6, 33, 62, 12, 584, 5, 'Ursula', 'A840', 'S', 'GII', 'Pablo Martínez', 'A-0', '2015-01-31 19:23:55', '2015-01-31 19:23:55', 0, 0, 0, 0, 0, 36.42, '', 0),
(6, 33, 62, 3, 585, 5, 'Ari', 'A894', 'L', 'GII', 'Veronica Roda', 'A-0', '2015-01-31 19:15:38', '2015-01-31 19:15:38', 1, 0, 0, 1, 0, 0, '', 0),
(6, 33, 62, 75, 593, 5, 'Ada', '1647', 'L', 'GII', 'Sonia García', 'La Princesa', '2015-01-31 18:46:07', '2015-01-31 18:46:07', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 62, 88, 594, 5, 'Mashel', 'A931', 'S', 'GII', 'Ainhoa de Frias', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 62, 110, 602, 5, 'Sucre', 'A920', 'L', 'GII', 'Adrian Bajo', 'Vila-Real', '2015-01-31 18:52:20', '2015-01-31 18:52:20', 0, 0, 0, 0, 0, 30.82, '', 0),
(6, 33, 62, 59, 607, 5, 'Hela', 'A836', 'L', 'GII', 'Cesar Losada Mera', 'Eslón', '2015-01-31 19:12:21', '2015-01-31 19:12:21', 0, 1, 0, 0, 0, 32.5, '', 0),
(6, 33, 62, 64, 608, 5, 'Sendy', 'A926', 'L', 'GII', 'Carlos Martínez S.', 'Eslón', '2015-01-31 18:57:29', '2015-01-31 18:57:29', 1, 0, 0, 0, 0, 35.15, '', 0),
(6, 33, 62, 61, 609, 5, 'Lumbre', 'A932', 'L', 'GII', 'Arancha Ruipérez Moslares', 'Eslón', '2015-01-31 18:44:49', '2015-01-31 18:44:49', 3, 2, 0, 0, 0, 37.6, '', 0),
(6, 33, 62, 58, 610, 5, 'Baddy', '', 'L', 'GII', 'David Calviño', 'Eslón', '2015-01-31 18:51:27', '2015-01-31 18:51:27', 1, 0, 0, 0, 0, 33.73, '', 0),
(6, 33, 62, 34, 618, 5, 'Thais', '1640', 'L', 'GII', 'Elena Cardenal', 'Agilcan', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 62, 78, 634, 5, 'Menta', 'A450', 'L', 'GII', 'Jesus Perea', 'La Princesa', '2015-01-31 18:40:42', '2015-01-31 18:40:42', 0, 0, 0, 0, 1, 0, '', 0),
(6, 33, 62, 113, 636, 5, 'Witzig', '', 'M', 'GII', 'Mario Rodríguez', 'W.E.L.P.E.', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 62, 104, 643, 5, 'Wiki', 'A941', 'L', 'GII', 'Ana Palet', 'Vallgorguina', '2015-01-31 19:01:45', '2015-01-31 19:01:45', 1, 0, 0, 1, 0, 0, '', 0),
(6, 33, 63, 23, 3, 5, 'Hannibal Lecter XIII', 'A090', 'L', 'GIII', 'Tomás Pérez Ayuso', 'A. D. A. Pozuelo', '2015-01-31 18:42:22', '2015-01-31 18:42:22', 1, 0, 0, 0, 0, 36.56, '', 0),
(6, 33, 63, 114, 10, 5, 'Lee', 'A084', 'L', 'GIII', 'Antonio Molina', 'W.E.L.P.E.', '2015-01-31 18:07:36', '2015-01-31 18:07:36', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 63, 117, 18, 5, 'Woman', 'A206', 'L', 'GIII', 'Javier Mora Canales', 'A-0', '2015-01-31 18:15:41', '2015-01-31 18:15:41', 1, 0, 0, 0, 0, 33.93, '', 0),
(6, 33, 63, 38, 25, 5, 'Moss', 'A391', 'L', 'GIII', 'Roberto Iñigo', 'Cinco Huesos', '2015-01-31 18:03:25', '2015-01-31 18:03:25', 0, 0, 0, 0, 0, 35.74, '', 0),
(6, 33, 63, 68, 27, 5, 'Deby', 'A147', 'L', 'GIII', 'Cesar Losada Mera', 'Eslón', '2015-01-31 18:05:33', '2015-01-31 18:05:33', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 63, 70, 31, 5, 'Mc Coy', 'A322', 'L', 'GIII', 'Carmen Sotomayor', 'Eslón', '2015-01-31 18:13:17', '2015-01-31 18:13:17', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 63, 22, 45, 5, 'Asia', 'A364', 'L', 'GIII', 'José Luis Romero', 'A. D. C. Pozuelo', '2015-01-31 18:05:08', '2015-01-31 18:05:08', 0, 0, 0, 0, 0, 38.46, '', 0),
(6, 33, 63, 71, 46, 5, 'Xodro', 'A371', 'L', 'GIII', 'Carlos Martínez R.', 'Eslón', '2015-01-31 18:10:10', '2015-01-31 18:10:10', 0, 0, 0, 0, 0, 38.62, '', 0),
(6, 33, 63, 69, 51, 5, 'Akane', 'A249', 'L', 'GIII', 'Gerardo González', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 63, 85, 57, 5, 'Aby', 'A204', 'L', 'GIII', 'Roberto Reina Vega', 'La Princesa', '2015-01-31 18:04:16', '2015-01-31 18:04:16', 2, 0, 0, 0, 0, 37.43, '', 0),
(6, 33, 63, 14, 68, 5, 'Fito', 'A529', 'L', 'GIII', 'Luis Miguel Rodrigo', 'A-0', '2015-01-31 18:14:57', '2015-01-31 18:14:57', 0, 0, 0, 0, 0, 42.4, '', 0),
(6, 33, 63, 24, 78, 5, 'Winner', 'A497', 'L', 'GIII', 'Ricardo Santolaya', 'A. D. C. Pozuelo', '2015-01-31 18:27:46', '2015-01-31 18:27:46', 0, 0, 0, 0, 0, 34.65, '', 0),
(6, 33, 63, 69, 85, 5, 'Dylan', 'A461', 'L', 'GIII', 'Iván Sánchez García', 'Eslón', '2015-01-31 18:11:03', '2015-01-31 18:11:03', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 63, 95, 101, 5, 'Magic-Black', 'A318', 'L', 'GIII', 'Raquel Garrido', 'Tandem', '2015-01-31 18:12:00', '2015-01-31 18:12:00', 1, 1, 0, 1, 0, 0, '', 0),
(6, 33, 63, 86, 113, 5, 'Yun', 'A484', 'L', 'GIII', 'Concepción Fernández', 'La Princesa', '2015-01-31 18:06:27', '2015-01-31 18:06:27', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 63, 93, 148, 5, 'Kira', 'A116', 'L', 'GIII', 'Enrique Alonso Queija', 'Pura Vida', '2015-01-31 18:08:40', '2015-01-31 18:08:40', 2, 0, 0, 1, 0, 0, '', 0),
(6, 33, 63, 15, 220, 5, 'Neo', 'A077', 'L', 'GIII', 'Antonio López', 'A-0', '2015-01-31 17:59:32', '2015-01-31 17:59:32', 1, 2, 0, 1, 0, 0, '', 0),
(6, 33, 63, 112, 229, 5, 'Shasta', 'A272', 'L', 'GIII', 'Mario Rodríguez', 'W.E.L.P.E.', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 63, 41, 253, 5, 'Lass', 'A580', 'M', 'GIII', 'Eugenio Villares', 'Cubas', '2015-01-31 17:55:51', '2015-01-31 17:55:51', 0, 0, 0, 0, 0, 40.02, '', 0),
(6, 33, 63, 109, 312, 5, 'Nuca', 'A181', 'S', 'GIII', 'Iván Pardo García', 'Vallgorguina', '2015-01-31 17:51:49', '2015-01-31 17:51:49', 0, 0, 0, 0, 0, 46.48, '', 0),
(6, 33, 63, 51, 314, 5, 'Nana', 'A277', 'S', 'GIII', 'Sara Montila', 'Deporcan', '2015-01-31 17:50:19', '2015-01-31 17:50:19', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 63, 72, 350, 5, 'Dama', 'A641', 'M', 'GIII', 'Juan Antonio Martinez', 'Eslón', '2015-01-31 17:57:16', '2015-01-31 17:57:16', 0, 1, 0, 0, 0, 54.89, '', 0),
(6, 33, 63, 13, 417, 5, 'Crak', 'A719', 'L', 'GIII', 'Oscar Muñiz', 'A-0', '2015-01-31 18:02:29', '2015-01-31 18:02:29', 2, 0, 0, 0, 0, 33.35, '', 0),
(6, 33, 63, 39, 498, 5, 'Kobu', 'A806', 'M', 'GIII', 'Irena Montalvo', 'Correcan', '2015-01-31 17:54:57', '2015-01-31 17:54:57', 1, 0, 0, 0, 0, 42.04, '', 0),
(6, 33, 63, 91, 635, 5, 'Turco', 'A643', 'M', 'GIII', 'Gerardo Alvarez', 'Pataplán', '2015-01-31 17:53:46', '2015-01-31 17:53:46', 0, 0, 0, 0, 0, 44.66, '', 0),
(6, 33, 64, 23, 3, 5, 'Hannibal Lecter XIII', 'A090', 'L', 'GIII', 'Tomás Pérez Ayuso', 'A. D. A. Pozuelo', '2015-01-31 20:10:08', '2015-01-31 20:10:08', 0, 0, 0, 0, 0, 29.26, '', 0),
(6, 33, 64, 114, 10, 5, 'Lee', 'A084', 'L', 'GIII', 'Antonio Molina', 'W.E.L.P.E.', '2015-01-31 20:01:11', '2015-01-31 20:01:11', 0, 0, 0, 0, 0, 28.97, '', 0),
(6, 33, 64, 117, 18, 5, 'Woman', 'A206', 'L', 'GIII', 'Javier Mora Canales', 'A-0', '2015-01-31 20:10:46', '2015-01-31 20:10:46', 0, 0, 0, 0, 0, 28.58, '', 0),
(6, 33, 64, 38, 25, 5, 'Moss', 'A391', 'L', 'GIII', 'Roberto Iñigo', 'Cinco Huesos', '2015-01-31 20:13:57', '2015-01-31 20:13:57', 1, 1, 0, 0, 0, 33, '', 0),
(6, 33, 64, 68, 27, 5, 'Deby', 'A147', 'L', 'GIII', 'Cesar Losada Mera', 'Eslón', '2015-01-31 20:02:34', '2015-01-31 20:02:34', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 64, 70, 31, 5, 'Mc Coy', 'A322', 'L', 'GIII', 'Carmen Sotomayor', 'Eslón', '2015-01-31 20:04:04', '2015-01-31 20:04:04', 0, 0, 0, 0, 0, 30, '', 0),
(6, 33, 64, 22, 45, 5, 'Asia', 'A364', 'L', 'GIII', 'José Luis Romero', 'A. D. C. Pozuelo', '2015-01-31 20:13:08', '2015-01-31 20:13:08', 0, 0, 0, 0, 0, 29.75, '', 0),
(6, 33, 64, 71, 46, 5, 'Xodro', 'A371', 'L', 'GIII', 'Carlos Martínez R.', 'Eslón', '2015-01-31 20:11:58', '2015-01-31 20:11:58', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 64, 69, 51, 5, 'Akane', 'A249', 'L', 'GIII', 'Gerardo González', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 64, 85, 57, 5, 'Aby', 'A204', 'L', 'GIII', 'Roberto Reina Vega', 'La Princesa', '2015-01-31 20:08:05', '2015-01-31 20:08:05', 1, 0, 0, 0, 0, 31.24, '', 0),
(6, 33, 64, 14, 68, 5, 'Fito', 'A529', 'L', 'GIII', 'Luis Miguel Rodrigo', 'A-0', '2015-01-31 20:11:18', '2015-01-31 20:11:18', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 64, 24, 78, 5, 'Winner', 'A497', 'L', 'GIII', 'Ricardo Santolaya', 'A. D. C. Pozuelo', '2015-01-31 20:14:42', '2015-01-31 20:14:42', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 64, 69, 85, 5, 'Dylan', 'A461', 'L', 'GIII', 'Iván Sánchez García', 'Eslón', '2015-01-31 20:04:51', '2015-01-31 20:04:51', 0, 0, 0, 0, 0, 28.47, '', 0),
(6, 33, 64, 95, 101, 5, 'Magic-Black', 'A318', 'L', 'GIII', 'Raquel Garrido', 'Tandem', '2015-01-31 20:07:10', '2015-01-31 20:07:10', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 64, 86, 113, 5, 'Yun', 'A484', 'L', 'GIII', 'Concepción Fernández', 'La Princesa', '2015-01-31 20:01:46', '2015-01-31 20:01:46', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 64, 93, 148, 5, 'Kira', 'A116', 'L', 'GIII', 'Enrique Alonso Queija', 'Pura Vida', '2015-01-31 20:16:44', '2015-01-31 20:16:44', 0, 2, 0, 1, 0, 0, '', 0),
(6, 33, 64, 15, 220, 5, 'Neo', 'A077', 'L', 'GIII', 'Antonio López', 'A-0', '2015-01-31 20:05:37', '2015-01-31 20:05:37', 1, 0, 0, 0, 0, 29.98, '', 0),
(6, 33, 64, 112, 229, 5, 'Shasta', 'A272', 'L', 'GIII', 'Mario Rodríguez', 'W.E.L.P.E.', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 33, 64, 41, 253, 5, 'Lass', 'A580', 'M', 'GIII', 'Eugenio Villares', 'Cubas', '2015-01-31 19:59:30', '2015-01-31 19:59:30', 0, 1, 0, 0, 0, 35.79, '', 0),
(6, 33, 64, 109, 312, 5, 'Nuca', 'A181', 'S', 'GIII', 'Iván Pardo García', 'Vallgorguina', '2015-01-31 19:54:09', '2015-01-31 19:54:09', 0, 1, 0, 1, 0, 0, '', 0),
(6, 33, 64, 51, 314, 5, 'Nana', 'A277', 'S', 'GIII', 'Sara Montila', 'Deporcan', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 1, 0, 0, '', 0),
(6, 33, 64, 72, 350, 5, 'Dama', 'A641', 'M', 'GIII', 'Juan Antonio Martinez', 'Eslón', '2015-01-31 19:56:34', '2015-01-31 19:56:34', 0, 2, 0, 0, 0, 44.81, '', 0),
(6, 33, 64, 13, 417, 5, 'Crak', 'A719', 'L', 'GIII', 'Oscar Muñiz', 'A-0', '2015-01-31 20:08:44', '2015-01-31 20:08:44', 1, 0, 0, 1, 0, 0, '', 0),
(6, 33, 64, 39, 498, 5, 'Kobu', 'A806', 'M', 'GIII', 'Irena Montalvo', 'Correcan', '2015-01-31 19:57:26', '2015-01-31 19:57:26', 0, 1, 0, 0, 0, 36.01, '', 0),
(6, 33, 64, 91, 635, 5, 'Turco', 'A643', 'M', 'GIII', 'Gerardo Alvarez', 'Pataplán', '2015-01-31 19:58:32', '2015-01-31 19:58:32', 0, 0, 0, 0, 0, 35.71, '', 0),
(6, 34, 65, 55, 79, 5, 'Jade', '', 'L', 'GI', 'Rosa Maria Cañadillas', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 65, 86, 381, 5, 'Mambo', '', 'L', 'GI', 'Ana Palet', 'Vallgorguina', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 65, 74, 398, 5, 'Yashi', '', 'M', 'GI', 'Verónica Rodríguez', 'La Princesa', '2015-02-01 08:24:04', '2015-02-01 08:24:04', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 65, 38, 514, 5, 'Isis', '', 'L', 'GI', 'Antonio Fernandez Ortiz', 'Correcan', '2015-02-01 08:18:07', '2015-02-01 08:18:07', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 65, 119, 588, 5, 'Greta', '', 'M', 'GI', 'Susana Martín', 'Deporcan', '2015-02-01 08:23:43', '2015-02-01 08:23:43', 0, 0, 0, 0, 0, 50.16, '', 0),
(6, 34, 65, 42, 589, 5, 'Iris', '', 'L', 'GI', 'Sara Montila', 'Deporcan', '2015-02-01 08:19:29', '2015-02-01 08:19:29', 2, 0, 0, 1, 0, 0, '', 0),
(6, 34, 65, 75, 593, 5, 'Ada', '1647', 'L', 'GII', 'Sonia García', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 65, 87, 595, 5, 'Lola', '', 'M', 'GI', 'Pablo Parra', 'Mi Perro 10', '2015-02-01 08:25:37', '2015-02-01 08:25:37', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 65, 96, 601, 5, 'Lee-Ann', '', 'L', 'GII', 'Iván Pardo García', 'Vallgorguina', '2015-02-01 08:12:32', '2015-02-01 08:12:32', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 65, 55, 604, 5, 'Mizar', '', 'L', 'GI', 'Rómulo Parrilla', 'Eslón', '2015-02-01 08:14:18', '2015-02-01 08:14:18', 1, 1, 0, 1, 0, 0, '', 0),
(6, 34, 65, 97, 622, 5, 'Truco', '', 'L', 'GI', 'Marta Acero', 'Vallgorguina', '2015-02-01 08:15:46', '2015-02-01 08:15:46', 1, 2, 0, 1, 0, 0, '', 0),
(6, 34, 65, 52, 624, 5, 'Bora', '', 'L', 'GI', 'Yulia Morugova', 'El Nogueral', '2015-02-01 08:13:07', '2015-02-01 08:13:07', 1, 2, 0, 0, 0, 42.58, '', 0),
(6, 34, 65, 1, 633, 5, 'Asics', '', 'L', 'GI', 'Luis Miguel Rodrigo', 'A-0', '2015-02-01 08:21:10', '2015-02-01 08:21:10', 0, 1, 0, 1, 0, 0, '', 0),
(6, 34, 65, 94, 637, 5, 'Lis', '', 'L', 'GI', 'Miriam Villar', 'Tandem', '2015-02-01 08:20:17', '2015-02-01 08:20:17', 0, 2, 0, 1, 0, 0, '', 0),
(6, 34, 65, 40, 639, 5, 'Boss', '', 'L', 'GI', 'David Escribano', 'Cubas', '2015-02-01 08:14:40', '2015-02-01 08:14:40', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 65, 25, 640, 5, 'Nueve', '', 'S', 'GI', 'Miguel Angel Manzaneda', 'AA Y CIA', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 1, 0, 1, 0, 0, '', 0),
(6, 34, 65, 98, 642, 5, 'Charly', '', 'S', 'GI', 'Cristina González', 'Vallgorguina', '2015-02-01 08:30:24', '2015-02-01 08:30:24', 0, 0, 0, 0, 0, 46.96, '', 0),
(6, 34, 65, 2, 644, 5, 'Boss', '797', 'L', 'GI', 'Álvaro Muñiz', 'A-0', '2015-02-01 08:17:34', '2015-02-01 08:17:34', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 66, 55, 79, 5, 'Jade', '', 'L', 'GI', 'Rosa Maria Cañadillas', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 66, 86, 381, 5, 'Mambo', '', 'L', 'GI', 'Ana Palet', 'Vallgorguina', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 66, 74, 398, 5, 'Yashi', '', 'M', 'GI', 'Verónica Rodríguez', 'La Princesa', '2015-02-01 08:49:24', '2015-02-01 08:49:24', 0, 2, 0, 0, 0, 51.03, '', 0),
(6, 34, 66, 38, 514, 5, 'Isis', '', 'L', 'GI', 'Antonio Fernandez Ortiz', 'Correcan', '2015-02-01 09:02:04', '2015-02-01 09:02:04', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 66, 119, 588, 5, 'Greta', '', 'M', 'GI', 'Susana Martín', 'Deporcan', '2015-02-01 08:52:11', '2015-02-01 08:52:11', 1, 1, 0, 0, 0, 52.48, '', 0),
(6, 34, 66, 42, 589, 5, 'Iris', '', 'L', 'GI', 'Sara Montila', 'Deporcan', '2015-02-01 08:53:30', '2015-02-01 08:53:30', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 66, 75, 593, 5, 'Ada', '1647', 'L', 'GII', 'Sonia García', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 66, 87, 595, 5, 'Lola', '', 'M', 'GI', 'Pablo Parra', 'Mi Perro 10', '2015-02-01 08:50:15', '2015-02-01 08:50:15', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 66, 96, 601, 5, 'Lee-Ann', '', 'L', 'GII', 'Iván Pardo García', 'Vallgorguina', '2015-02-01 08:56:22', '2015-02-01 08:56:22', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 66, 55, 604, 5, 'Mizar', '', 'L', 'GI', 'Rómulo Parrilla', 'Eslón', '2015-02-01 08:57:42', '2015-02-01 08:57:42', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 66, 97, 622, 5, 'Truco', '', 'L', 'GI', 'Marta Acero', 'Vallgorguina', '2015-02-01 08:54:35', '2015-02-01 08:54:35', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 66, 52, 624, 5, 'Bora', '', 'L', 'GI', 'Yulia Morugova', 'El Nogueral', '2015-02-01 09:01:52', '2015-02-01 09:01:52', 0, 0, 0, 0, 0, 35.07, '', 0),
(6, 34, 66, 1, 633, 5, 'Asics', '', 'L', 'GI', 'Luis Miguel Rodrigo', 'A-0', '2015-02-01 08:58:40', '2015-02-01 08:58:40', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 66, 94, 637, 5, 'Lis', '', 'L', 'GI', 'Miriam Villar', 'Tandem', '2015-02-01 09:00:06', '2015-02-01 09:00:06', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 66, 40, 639, 5, 'Boss', '', 'L', 'GI', 'David Escribano', 'Cubas', '2015-02-01 08:53:57', '2015-02-01 08:53:57', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 66, 25, 640, 5, 'Nueve', '', 'S', 'GI', 'Miguel Angel Manzaneda', 'AA Y CIA', '2015-02-01 08:46:18', '2015-02-01 08:46:18', 0, 1, 0, 1, 0, 0, '', 0),
(6, 34, 66, 98, 642, 5, 'Charly', '', 'S', 'GI', 'Cristina González', 'Vallgorguina', '2015-02-01 08:47:40', '2015-02-01 08:47:40', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 66, 2, 644, 5, 'Boss', '797', 'L', 'GI', 'Álvaro Muñiz', 'A-0', '2015-02-01 09:03:37', '2015-02-01 09:03:37', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 18, 8, 5, 'Thelma', '824', 'L', 'GII', 'Fabian Santolaya', 'A. D. A. Pozuelo', '2015-02-01 10:01:46', '2015-02-01 10:01:46', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 62, 52, 5, 'Mister', 'A250', 'L', 'GII', 'Rubén García', 'Eslón', '2015-02-01 10:04:46', '2015-02-01 10:04:46', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 8, 94, 5, 'Panda', 'A474', 'L', 'GII', 'Lorena Díez', 'A-0', '2015-02-01 10:20:28', '2015-02-01 10:20:28', 0, 0, 0, 0, 0, 38.03, '', 0),
(6, 34, 67, 103, 95, 5, 'Raissa', 'A563', 'L', 'GII', 'Javier Iniesta', 'Vallgorguina', '2015-02-01 10:17:32', '2015-02-01 10:17:32', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 63, 103, 5, 'Red Magic', 'A124', 'L', 'GII', 'Elena Miguel', 'Eslón', '2015-02-01 09:44:13', '2015-02-01 09:44:13', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 86, 113, 5, 'Yun', 'A484', 'L', 'GIII', 'Concepción Fernández', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 67, 60, 156, 5, 'Lennon', 'A336', 'L', 'GII', 'Miguel Angel García', 'Eslón', '2015-02-01 09:45:03', '2015-02-01 09:45:03', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 73, 189, 5, 'Rotten', '1518', 'L', 'GII', 'José Luis Prieto', 'Illa Blanca', '2015-02-01 09:57:59', '2015-02-01 09:57:59', 1, 0, 0, 0, 0, 35.37, '', 0),
(6, 34, 67, 101, 202, 5, 'Mara', 'A396', 'L', 'GII', 'Ana Isabel Escobar', 'Vallgorguina', '2015-02-01 09:55:37', '2015-02-01 09:55:37', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 112, 229, 5, 'Shasta', 'A272', 'L', 'GIII', 'Mario Rodríguez', 'W.E.L.P.E.', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 67, 83, 267, 5, 'Jolie', '808', 'M', 'GII', 'Celeste Zarzosa', 'La Princesa', '2015-02-01 10:28:31', '2015-02-01 10:28:31', 0, 0, 0, 0, 0, 44.52, '', 0),
(6, 34, 67, 9, 272, 5, 'Luna', 'A240', 'M', 'GII', 'Paula Rello', 'A-0', '2015-02-01 10:30:52', '2015-02-01 10:30:52', 2, 0, 0, 0, 0, 39.62, '', 0),
(6, 34, 67, 84, 274, 5, 'Lucas', 'A248', 'M', 'GII', 'Alberto Pereda', 'La Princesa', '2015-02-01 10:28:19', '2015-02-01 10:28:19', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 67, 21, 330, 5, 'Pepa', 'A393', 'S', 'GII', 'Francisco de la Cruz', 'A. D. C. Pozuelo', '2015-02-01 10:36:09', '2015-02-01 10:36:09', 3, 0, 0, 0, 0, 36.06, '', 0),
(6, 34, 67, 108, 342, 5, 'Quillo', 'A604', 'S', 'GII', 'Cristina González', 'Vallgorguina', '2015-02-01 10:34:38', '2015-02-01 10:34:38', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 99, 348, 5, 'Aker', 'A397', 'L', 'GII', 'Francisco Javier Jaen', 'Vallgorguina', '2015-02-01 10:19:49', '2015-02-01 10:19:49', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 111, 352, 5, 'Donna', 'A795', 'L', 'GII', 'Ricardo Benito', 'W.E.L.P.E.', '2015-02-01 10:49:46', '2015-02-01 10:49:46', 3, 0, 0, 0, 0, 33.89, '', 0),
(6, 34, 67, 1, 354, 5, 'paco', '', 'T', 'GII', '-- Sin asignar --', '-- Sin asignar --', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 67, 36, 361, 5, 'Kara', 'A-541', 'L', 'GII', 'Ramón García Maroto', 'Cinco Huesos', '2015-02-01 09:52:11', '2015-02-01 09:52:11', 0, 0, 0, 0, 0, 33.36, '', 0),
(6, 34, 67, 37, 379, 5, 'Skay', 'A605', 'L', 'GII', 'Javier Santisteban', 'Cinco Huesos', '2015-02-01 10:21:07', '2015-02-01 10:21:07', 2, 0, 0, 0, 0, 32.78, '', 0),
(6, 34, 67, 100, 380, 5, 'Keko', '', 'L', 'GII', 'Alberto González', 'Vallgorguina', '2015-02-01 09:58:27', '2015-02-01 09:58:27', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 106, 386, 5, 'Izzie', 'A275', 'S', 'GII', 'Estíbaliz Pereda Navarro', 'Vallgorguina', '2015-02-01 10:35:26', '2015-02-01 10:35:26', 0, 0, 0, 0, 0, 41.68, '', 0),
(6, 34, 67, 6, 387, 5, 'Kyra', '1607', 'L', 'GII', 'Andres Morilla Sánchez', 'A-0', '2015-02-01 09:38:38', '2015-02-01 09:38:38', 2, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 4, 389, 5, 'Dolce', 'A681', 'L', 'GII', 'Andres Morilla Sánchez', 'A-0', '2015-02-01 10:05:27', '2015-02-01 10:05:27', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 5, 391, 5, 'Gilda', 'A723', 'L', 'GII', 'Natividad Ruiz García', 'A-0', '2015-02-01 09:59:43', '2015-02-01 09:59:43', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 7, 392, 5, 'Noa', 'A540', 'L', 'GII', 'Pedro Delgado Fernandez', 'A-0', '2015-02-01 10:06:52', '2015-02-01 10:06:52', 1, 1, 0, 0, 0, 31.81, '', 0),
(6, 34, 67, 65, 393, 5, 'Thor', 'A835', 'L', 'GII', 'Juan Carlos Ruiz', 'Eslón', '2015-02-01 09:53:54', '2015-02-01 09:53:54', 1, 2, 0, 0, 0, 38.15, '', 0),
(6, 34, 67, 56, 394, 5, 'Akira Haru', 'A311', 'L', 'GII', 'Gema López', 'Eslón', '2015-02-01 09:49:15', '2015-02-01 09:49:15', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 17, 397, 5, 'Milady', 'A791', 'L', 'GII', 'Ricardo Santolaya', 'A. D. C. Pozuelo', '2015-02-01 09:52:50', '2015-02-01 09:52:50', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 77, 400, 5, 'Héctor', '1597', 'L', 'GII', 'David Gómez-Calcerrada', 'La Princesa', '2015-02-01 10:02:45', '2015-02-01 10:02:45', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 82, 401, 5, 'Ron', 'A617', 'L', 'GII', 'Oscar Sacristan', 'La Princesa', '2015-02-01 09:37:59', '2015-02-01 09:37:59', 1, 0, 0, 0, 0, 35.7, '', 0),
(6, 34, 67, 88, 404, 5, 'Bimba', 'A288', 'L', 'GII', 'Luisa Fernanda Millan', 'Pataplán', '2015-02-01 10:01:04', '2015-02-01 10:01:04', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 89, 405, 5, 'Dudy', 'A753', 'L', 'GII', 'Juan José González', 'Pataplán', '2015-02-01 10:04:09', '2015-02-01 10:04:09', 0, 0, 0, 0, 0, 37.17, '', 0),
(6, 34, 67, 105, 407, 5, 'Luca', 'A749', 'M', 'GII', 'Cristina González', 'Vallgorguina', '2015-02-01 10:31:35', '2015-02-01 10:31:35', 0, 0, 0, 0, 0, 40.26, '', 0),
(6, 34, 67, 107, 408, 5, 'Kika', 'A722', 'S', 'GII', 'Noelia Mouchet', 'Vallgorguina', '2015-02-01 10:34:55', '2015-02-01 10:34:55', 0, 2, 0, 0, 0, 52.88, '', 0),
(6, 34, 67, 20, 413, 5, 'Lola', 'A633', 'S', 'GII', 'Francisco de la Cruz', 'A. D. C. Pozuelo', '2015-02-01 10:34:21', '2015-02-01 10:34:21', 2, 0, 0, 0, 0, 38.13, '', 0),
(6, 34, 67, 19, 414, 5, 'Yeny', 'A747', 'L', 'GII', 'José Luis Romero', 'A. D. C. Pozuelo', '2015-02-01 10:22:13', '2015-02-01 10:22:13', 0, 0, 0, 0, 0, 36.29, '', 0),
(6, 34, 67, 53, 418, 5, 'Nitra', 'A743', 'S', 'GII', 'Yulia Morugova', 'El Nogueral', '2015-02-01 10:35:42', '2015-02-01 10:35:42', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 81, 423, 5, 'Noa', 'A143', 'L', 'GII', 'Jenifer Tolín', 'La Princesa', '2015-02-01 09:48:52', '2015-02-01 09:48:52', 1, 0, 0, 0, 0, 36.08, '', 0),
(6, 34, 67, 66, 424, 5, 'Vlad', 'A752', 'L', 'GII', 'Angel Corroto', 'Eslón', '2015-02-01 10:16:07', '2015-02-01 10:16:07', 0, 2, 0, 1, 0, 0, '', 0),
(6, 34, 67, 67, 427, 5, 'Kala', '1574', 'S', 'GII', 'Cristina Cortijo', 'Eslón', '2015-02-01 10:35:06', '2015-02-01 10:35:06', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 67, 11, 440, 5, 'Horatio', 'A847', 'S', 'GII', 'Beatriz Juan', 'A-0', '2015-02-01 10:35:37', '2015-02-01 10:35:37', 0, 0, 0, 0, 0, 52.57, '', 0),
(6, 34, 67, 102, 517, 5, 'Nut', 'A-886', 'L', 'GII', 'Francisco Javier Jaen', 'Vallgorguina', '2015-02-01 09:52:48', '2015-02-01 09:52:48', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 35, 519, 5, 'Dunah', 'A635', 'M', 'GII', 'Alfredo Ortíz', 'Junior', '2015-02-01 10:30:29', '2015-02-01 10:30:29', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 76, 521, 5, 'Buck', 'A877', 'L', 'GII', 'Carlos Pérez', 'La Princesa', '2015-02-01 09:42:33', '2015-02-01 09:42:33', 1, 1, 0, 1, 0, 0, '', 0),
(6, 34, 67, 10, 560, 5, 'Arturo', '', 'S', 'GII', 'Dolores Rosas', 'A-0', '2015-02-01 10:34:32', '2015-02-01 10:34:32', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 12, 584, 5, 'Ursula', 'A840', 'S', 'GII', 'Pablo Martínez', 'A-0', '2015-02-01 10:35:16', '2015-02-01 10:35:16', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 3, 585, 5, 'Ari', 'A894', 'L', 'GII', 'Veronica Roda', 'A-0', '2015-02-01 10:20:11', '2015-02-01 10:20:11', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 75, 593, 5, 'Ada', '1647', 'L', 'GII', 'Sonia García', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 67, 110, 602, 5, 'Sucre', 'A920', 'L', 'GII', 'Adrian Bajo', 'Vila-Real', '2015-02-01 09:52:41', '2015-02-01 09:52:41', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 67, 59, 607, 5, 'Hela', 'A836', 'L', 'GII', 'Cesar Losada Mera', 'Eslón', '2015-02-01 10:08:46', '2015-02-01 10:08:46', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 64, 608, 5, 'Sendy', 'A926', 'L', 'GII', 'Carlos Martínez S.', 'Eslón', '2015-02-01 09:40:01', '2015-02-01 09:40:01', 0, 1, 0, 0, 0, 38.05, '', 0),
(6, 34, 67, 61, 609, 5, 'Lumbre', 'A932', 'L', 'GII', 'Arancha Ruipérez Moslares', 'Eslón', '2015-02-01 09:43:27', '2015-02-01 09:43:27', 0, 1, 0, 1, 0, 0, '', 0),
(6, 34, 67, 58, 610, 5, 'Baddy', '', 'L', 'GII', 'David Calviño', 'Eslón', '2015-02-01 09:40:38', '2015-02-01 09:40:38', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 90, 623, 5, 'Nubia', 'A-309', 'L', 'GII', 'Juan José González', 'Pataplán', '2015-02-01 09:36:34', '2015-02-01 09:36:34', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 67, 78, 634, 5, 'Menta', 'A450', 'L', 'GII', 'Jesus Perea', 'La Princesa', '2015-02-01 10:07:45', '2015-02-01 10:07:45', 1, 0, 0, 0, 0, 32.82, '', 0),
(6, 34, 67, 118, 646, 5, 'Nora', 'A624', 'L', 'GII', 'Juan Campin', 'La Princesa', '2015-02-01 09:54:43', '2015-02-01 09:54:43', 1, 1, 0, 0, 0, 38.99, '', 0),
(6, 34, 68, 18, 8, 5, 'Thelma', '824', 'L', 'GII', 'Fabian Santolaya', 'A. D. A. Pozuelo', '2015-02-01 11:55:04', '2015-02-01 11:55:04', 0, 2, 0, 0, 0, 43.53, '', 0),
(6, 34, 68, 62, 52, 5, 'Mister', 'A250', 'L', 'GII', 'Rubén García', 'Eslón', '2015-02-01 12:02:07', '2015-02-01 12:02:07', 1, 2, 0, 1, 0, 0, '', 0),
(6, 34, 68, 8, 94, 5, 'Panda', 'A474', 'L', 'GII', 'Lorena Díez', 'A-0', '2015-02-01 12:15:55', '2015-02-01 12:15:55', 0, 2, 0, 0, 0, 39.17, '', 0),
(6, 34, 68, 103, 95, 5, 'Raissa', 'A563', 'L', 'GII', 'Javier Iniesta', 'Vallgorguina', '2015-02-01 11:50:10', '2015-02-01 11:50:10', 0, 1, 0, 1, 0, 0, '', 0),
(6, 34, 68, 63, 103, 5, 'Red Magic', 'A124', 'L', 'GII', 'Elena Miguel', 'Eslón', '2015-02-01 11:48:05', '2015-02-01 11:48:05', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 86, 113, 5, 'Yun', 'A484', 'L', 'GIII', 'Concepción Fernández', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1);
INSERT INTO `resultados` (`Prueba`, `Jornada`, `Manga`, `Dorsal`, `Perro`, `Equipo`, `Nombre`, `Licencia`, `Categoria`, `Grado`, `NombreGuia`, `NombreClub`, `Entrada`, `Comienzo`, `Faltas`, `Rehuses`, `Tocados`, `Eliminado`, `NoPresentado`, `Tiempo`, `Observaciones`, `Pendiente`) VALUES
(6, 34, 68, 60, 156, 5, 'Lennon', 'A336', 'L', 'GII', 'Miguel Angel García', 'Eslón', '2015-02-01 11:58:51', '2015-02-01 11:58:51', 1, 0, 0, 0, 0, 36.59, '', 0),
(6, 34, 68, 73, 189, 5, 'Rotten', '1518', 'L', 'GII', 'José Luis Prieto', 'Illa Blanca', '2015-02-01 12:10:21', '2015-02-01 12:10:21', 0, 2, 0, 1, 0, 0, '', 0),
(6, 34, 68, 101, 202, 5, 'Mara', 'A396', 'L', 'GII', 'Ana Isabel Escobar', 'Vallgorguina', '2015-02-01 11:59:50', '2015-02-01 11:59:50', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 112, 229, 5, 'Shasta', 'A272', 'L', 'GIII', 'Mario Rodríguez', 'W.E.L.P.E.', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 68, 83, 267, 5, 'Jolie', '808', 'M', 'GII', 'Celeste Zarzosa', 'La Princesa', '2015-02-01 12:21:46', '2015-02-01 12:21:46', 0, 0, 0, 0, 0, 41.1, '', 0),
(6, 34, 68, 9, 272, 5, 'Luna', 'A240', 'M', 'GII', 'Paula Rello', 'A-0', '2015-02-01 12:20:58', '2015-02-01 12:20:58', 1, 1, 0, 0, 0, 40.49, '', 0),
(6, 34, 68, 84, 274, 5, 'Lucas', 'A248', 'M', 'GII', 'Alberto Pereda', 'La Princesa', '2015-02-01 12:18:49', '2015-02-01 12:18:49', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 68, 21, 330, 5, 'Pepa', 'A393', 'S', 'GII', 'Francisco de la Cruz', 'A. D. C. Pozuelo', '2015-02-01 12:24:24', '2015-02-01 12:24:24', 2, 2, 0, 0, 0, 45.81, '', 0),
(6, 34, 68, 108, 342, 5, 'Quillo', 'A604', 'S', 'GII', 'Cristina González', 'Vallgorguina', '2015-02-01 12:33:45', '2015-02-01 12:33:45', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 99, 348, 5, 'Aker', 'A397', 'L', 'GII', 'Francisco Javier Jaen', 'Vallgorguina', '2015-02-01 11:49:09', '2015-02-01 11:49:09', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 111, 352, 5, 'Donna', 'A795', 'L', 'GII', 'Ricardo Benito', 'W.E.L.P.E.', '2015-02-01 12:13:11', '2015-02-01 12:13:11', 1, 0, 0, 0, 0, 33.64, '', 0),
(6, 34, 68, 1, 354, 5, 'paco', '', 'T', 'GII', '-- Sin asignar --', '-- Sin asignar --', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 68, 36, 361, 5, 'Kara', 'A-541', 'L', 'GII', 'Ramón García Maroto', 'Cinco Huesos', '2015-02-01 12:14:03', '2015-02-01 12:14:03', 1, 0, 0, 0, 0, 33.26, '', 0),
(6, 34, 68, 37, 379, 5, 'Skay', 'A605', 'L', 'GII', 'Javier Santisteban', 'Cinco Huesos', '2015-02-01 12:05:48', '2015-02-01 12:05:48', 3, 0, 0, 0, 0, 31.22, '', 0),
(6, 34, 68, 100, 380, 5, 'Keko', '', 'L', 'GII', 'Alberto González', 'Vallgorguina', '2015-02-01 11:44:42', '2015-02-01 11:44:42', 1, 0, 0, 0, 0, 36.32, '', 0),
(6, 34, 68, 106, 386, 5, 'Izzie', 'A275', 'S', 'GII', 'Estíbaliz Pereda Navarro', 'Vallgorguina', '2015-02-01 12:32:29', '2015-02-01 12:32:29', 0, 0, 0, 0, 0, 41.3, '', 0),
(6, 34, 68, 6, 387, 5, 'Kyra', '1607', 'L', 'GII', 'Andres Morilla Sánchez', 'A-0', '2015-02-01 12:02:50', '2015-02-01 12:02:50', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 4, 389, 5, 'Dolce', 'A681', 'L', 'GII', 'Andres Morilla Sánchez', 'A-0', '2015-02-01 11:52:52', '2015-02-01 11:52:52', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 5, 391, 5, 'Gilda', 'A723', 'L', 'GII', 'Natividad Ruiz García', 'A-0', '2015-02-01 11:46:07', '2015-02-01 11:46:07', 0, 1, 0, 1, 0, 0, '', 0),
(6, 34, 68, 7, 392, 5, 'Noa', 'A540', 'L', 'GII', 'Pedro Delgado Fernandez', 'A-0', '2015-02-01 12:06:53', '2015-02-01 12:06:53', 1, 0, 0, 0, 0, 31.6, '', 0),
(6, 34, 68, 65, 393, 5, 'Thor', 'A835', 'L', 'GII', 'Juan Carlos Ruiz', 'Eslón', '2015-02-01 12:04:03', '2015-02-01 12:04:03', 0, 0, 0, 0, 0, 30.87, '', 0),
(6, 34, 68, 56, 394, 5, 'Akira Haru', 'A311', 'L', 'GII', 'Gema López', 'Eslón', '2015-02-01 11:47:37', '2015-02-01 11:47:37', 0, 0, 0, 0, 0, 35.77, '', 0),
(6, 34, 68, 17, 397, 5, 'Milady', 'A791', 'L', 'GII', 'Ricardo Santolaya', 'A. D. C. Pozuelo', '2015-02-01 11:43:09', '2015-02-01 11:43:09', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 68, 77, 400, 5, 'Héctor', '1597', 'L', 'GII', 'David Gómez-Calcerrada', 'La Princesa', '2015-02-01 11:53:56', '2015-02-01 11:53:56', 0, 0, 0, 0, 0, 33.05, '', 0),
(6, 34, 68, 82, 401, 5, 'Ron', 'A617', 'L', 'GII', 'Oscar Sacristan', 'La Princesa', '2015-02-01 12:09:28', '2015-02-01 12:09:28', 0, 1, 0, 0, 0, 36.78, '', 0),
(6, 34, 68, 88, 404, 5, 'Bimba', 'A288', 'L', 'GII', 'Luisa Fernanda Millan', 'Pataplán', '2015-02-01 11:45:32', '2015-02-01 11:45:32', 1, 0, 0, 0, 0, 34.45, '', 0),
(6, 34, 68, 89, 405, 5, 'Dudy', 'A753', 'L', 'GII', 'Juan José González', 'Pataplán', '2015-02-01 12:11:41', '2015-02-01 12:11:41', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 105, 407, 5, 'Luca', 'A749', 'M', 'GII', 'Cristina González', 'Vallgorguina', '2015-02-01 12:22:34', '2015-02-01 12:22:34', 0, 1, 0, 1, 0, 0, '', 0),
(6, 34, 68, 107, 408, 5, 'Kika', 'A722', 'S', 'GII', 'Noelia Mouchet', 'Vallgorguina', '2015-02-01 12:28:46', '2015-02-01 12:28:46', 0, 0, 0, 0, 0, 41.05, '', 0),
(6, 34, 68, 20, 413, 5, 'Lola', 'A633', 'S', 'GII', 'Francisco de la Cruz', 'A. D. C. Pozuelo', '2015-02-01 12:33:41', '2015-02-01 12:33:41', 1, 0, 0, 0, 0, 37.28, '', 0),
(6, 34, 68, 19, 414, 5, 'Yeny', 'A747', 'L', 'GII', 'José Luis Romero', 'A. D. C. Pozuelo', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 1, 0, 0, 0, 0, 30.54, '', 0),
(6, 34, 68, 53, 418, 5, 'Nitra', 'A743', 'S', 'GII', 'Yulia Morugova', 'El Nogueral', '2015-02-01 12:27:10', '2015-02-01 12:27:10', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 81, 423, 5, 'Noa', 'A143', 'L', 'GII', 'Jenifer Tolín', 'La Princesa', '2015-02-01 12:08:31', '2015-02-01 12:08:31', 0, 1, 0, 0, 0, 38.25, '', 0),
(6, 34, 68, 66, 424, 5, 'Vlad', 'A752', 'L', 'GII', 'Angel Corroto', 'Eslón', '2015-02-01 11:57:34', '2015-02-01 11:57:34', 0, 0, 0, 0, 0, 33.53, '', 0),
(6, 34, 68, 67, 427, 5, 'Kala', '1574', 'S', 'GII', 'Cristina Cortijo', 'Eslón', '2015-02-01 12:24:37', '2015-02-01 12:24:37', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 68, 11, 440, 5, 'Horatio', 'A847', 'S', 'GII', 'Beatriz Juan', 'A-0', '2015-02-01 12:29:41', '2015-02-01 12:29:41', 0, 0, 0, 0, 0, 46.93, '', 0),
(6, 34, 68, 102, 517, 5, 'Nut', 'A-886', 'L', 'GII', 'Francisco Javier Jaen', 'Vallgorguina', '2015-02-01 12:01:23', '2015-02-01 12:01:23', 0, 1, 0, 0, 0, 35.98, '', 0),
(6, 34, 68, 35, 519, 5, 'Dunah', 'A635', 'M', 'GII', 'Alfredo Ortíz', 'Junior', '2015-02-01 12:19:53', '2015-02-01 12:19:53', 0, 0, 0, 0, 0, 40.85, '', 0),
(6, 34, 68, 76, 521, 5, 'Buck', 'A877', 'L', 'GII', 'Carlos Pérez', 'La Princesa', '2015-02-01 11:56:02', '2015-02-01 11:56:02', 0, 0, 0, 0, 0, 36.87, '', 0),
(6, 34, 68, 10, 560, 5, 'Arturo', '', 'S', 'GII', 'Dolores Rosas', 'A-0', '2015-02-01 12:25:04', '2015-02-01 12:25:04', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 12, 584, 5, 'Ursula', 'A840', 'S', 'GII', 'Pablo Martínez', 'A-0', '2015-02-01 12:26:14', '2015-02-01 12:26:14', 0, 1, 0, 1, 0, 0, '', 0),
(6, 34, 68, 3, 585, 5, 'Ari', 'A894', 'L', 'GII', 'Veronica Roda', 'A-0', '2015-02-01 12:14:41', '2015-02-01 12:14:41', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 75, 593, 5, 'Ada', '1647', 'L', 'GII', 'Sonia García', 'La Princesa', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 68, 110, 602, 5, 'Sucre', 'A920', 'L', 'GII', 'Adrian Bajo', 'Vila-Real', '2015-02-01 11:43:46', '2015-02-01 11:43:46', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 59, 607, 5, 'Hela', 'A836', 'L', 'GII', 'Cesar Losada Mera', 'Eslón', '2015-02-01 11:51:31', '2015-02-01 11:51:31', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 64, 608, 5, 'Sendy', 'A926', 'L', 'GII', 'Carlos Martínez S.', 'Eslón', '2015-02-01 12:07:36', '2015-02-01 12:07:36', 0, 0, 0, 0, 0, 32.26, '', 0),
(6, 34, 68, 61, 609, 5, 'Lumbre', 'A932', 'L', 'GII', 'Arancha Ruipérez Moslares', 'Eslón', '2015-02-01 11:42:59', '2015-02-01 11:42:59', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 58, 610, 5, 'Baddy', '', 'L', 'GII', 'David Calviño', 'Eslón', '2015-02-01 11:59:24', '2015-02-01 11:59:24', 1, 0, 0, 0, 0, 31.25, '', 0),
(6, 34, 68, 90, 623, 5, 'Nubia', 'A-309', 'L', 'GII', 'Juan José González', 'Pataplán', '2015-02-01 11:47:52', '2015-02-01 11:47:52', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 68, 78, 634, 5, 'Menta', 'A450', 'L', 'GII', 'Jesus Perea', 'La Princesa', '2015-02-01 12:11:20', '2015-02-01 12:11:20', 1, 1, 0, 0, 0, 34.16, '', 0),
(6, 34, 68, 118, 646, 5, 'Nora', 'A624', 'L', 'GII', 'Juan Campin', 'La Princesa', '2015-02-01 12:04:59', '2015-02-01 12:04:59', 0, 0, 0, 0, 0, 34.78, '', 0),
(6, 34, 69, 23, 3, 5, 'Hannibal Lecter XIII', 'A090', 'L', 'GIII', 'Tomás Pérez Ayuso', 'A. D. A. Pozuelo', '2015-02-01 11:10:56', '2015-02-01 11:10:56', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 69, 38, 25, 5, 'Moss', 'A391', 'L', 'GIII', 'Roberto Iñigo', 'Cinco Huesos', '2015-02-01 11:06:03', '2015-02-01 11:06:03', 1, 0, 0, 1, 0, 0, '', 0),
(6, 34, 69, 68, 27, 5, 'Deby', 'A147', 'L', 'GIII', 'Cesar Losada Mera', 'Eslón', '2015-02-01 11:17:15', '2015-02-01 11:17:15', 1, 0, 0, 0, 0, 31.5, '', 0),
(6, 34, 69, 70, 31, 5, 'Mc Coy', 'A322', 'L', 'GIII', 'Carmen Sotomayor', 'Eslón', '2015-02-01 11:12:01', '2015-02-01 11:12:01', 1, 0, 0, 0, 0, 29.95, '', 0),
(6, 34, 69, 16, 34, 5, 'Zoe', 'A289', 'L', 'GIII', 'Marta Sánchez', 'A-0', '2015-02-01 11:03:13', '2015-02-01 11:03:13', 1, 0, 0, 0, 0, 33.35, '', 0),
(6, 34, 69, 22, 45, 5, 'Asia', 'A364', 'L', 'GIII', 'José Luis Romero', 'A. D. C. Pozuelo', '2015-02-01 11:12:49', '2015-02-01 11:12:49', 2, 0, 0, 0, 0, 29.34, '', 0),
(6, 34, 69, 71, 46, 5, 'Xodro', 'A371', 'L', 'GIII', 'Carlos Martínez R.', 'Eslón', '2015-02-01 11:17:04', '2015-02-01 11:17:04', 0, 0, 0, 0, 0, 33.35, '', 0),
(6, 34, 69, 69, 51, 5, 'Akane', 'A249', 'L', 'GIII', 'Gerardo González', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 69, 85, 57, 5, 'Aby', 'A204', 'L', 'GIII', 'Roberto Reina Vega', 'La Princesa', '2015-02-01 11:16:46', '2015-02-01 11:16:46', 0, 0, 0, 0, 0, 32.02, '', 0),
(6, 34, 69, 14, 68, 5, 'Fito', 'A529', 'L', 'GIII', 'Luis Miguel Rodrigo', 'A-0', '2015-02-01 11:14:23', '2015-02-01 11:14:23', 2, 0, 0, 0, 0, 32.8, '', 0),
(6, 34, 69, 24, 78, 5, 'Winner', 'A497', 'L', 'GIII', 'Ricardo Santolaya', 'A. D. C. Pozuelo', '2015-02-01 11:15:52', '2015-02-01 11:15:52', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 69, 69, 85, 5, 'Dylan', 'A461', 'L', 'GIII', 'Iván Sánchez García', 'Eslón', '2015-02-01 11:03:58', '2015-02-01 11:03:58', 0, 0, 0, 0, 0, 30.91, '', 0),
(6, 34, 69, 95, 101, 5, 'Magic-Black', 'A318', 'L', 'GIII', 'Raquel Garrido', 'Tandem', '2015-02-01 11:13:03', '2015-02-01 11:13:03', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 69, 86, 113, 5, 'Yun', 'A484', 'L', 'GIII', 'Concepción Fernández', 'La Princesa', '2015-02-01 11:06:51', '2015-02-01 11:06:51', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 69, 15, 220, 5, 'Neo', 'A077', 'L', 'GIII', 'Antonio López', 'A-0', '2015-02-01 11:14:51', '2015-02-01 11:14:51', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 69, 112, 229, 5, 'Shasta', 'A272', 'L', 'GIII', 'Mario Rodríguez', 'W.E.L.P.E.', '2015-02-01 11:04:35', '2015-02-01 11:04:35', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 69, 41, 253, 5, 'Lass', 'A580', 'M', 'GIII', 'Eugenio Villares', 'Cubas', '2015-02-01 11:01:24', '2015-02-01 11:01:24', 2, 0, 0, 1, 0, 0, '', 0),
(6, 34, 69, 51, 314, 5, 'Nana', 'A277', 'S', 'GIII', 'Sara Montila', 'Deporcan', '2015-02-01 10:57:11', '2015-02-01 10:57:11', 0, 0, 0, 0, 0, 35.3, '', 0),
(6, 34, 69, 72, 350, 5, 'Dama', 'A641', 'M', 'GIII', 'Juan Antonio Martinez', 'Eslón', '2015-02-01 10:59:52', '2015-02-01 10:59:52', 1, 0, 0, 0, 0, 41.14, '', 0),
(6, 34, 69, 13, 417, 5, 'Crak', 'A719', 'L', 'GIII', 'Oscar Muñiz', 'A-0', '2015-02-01 11:05:31', '2015-02-01 11:05:31', 0, 0, 0, 0, 0, 27.63, '', 0),
(6, 34, 69, 39, 498, 5, 'Kobu', 'A806', 'M', 'GIII', 'Irena Montalvo', 'Correcan', '2015-02-01 10:58:42', '2015-02-01 10:58:42', 0, 0, 0, 0, 0, 32.57, '', 0),
(6, 34, 69, 91, 635, 5, 'Turco', 'A643', 'M', 'GIII', 'Gerardo Alvarez', 'Pataplán', '2015-02-01 11:00:57', '2015-02-01 11:00:57', 1, 0, 0, 0, 0, 32.69, '', 0),
(6, 34, 70, 23, 3, 5, 'Hannibal Lecter XIII', 'A090', 'L', 'GIII', 'Tomás Pérez Ayuso', 'A. D. A. Pozuelo', '2015-02-01 13:02:54', '2015-02-01 13:02:54', 0, 0, 0, 0, 0, 29.06, '', 0),
(6, 34, 70, 38, 25, 5, 'Moss', 'A391', 'L', 'GIII', 'Roberto Iñigo', 'Cinco Huesos', '2015-02-01 13:02:00', '2015-02-01 13:02:00', 0, 1, 0, 0, 0, 33.36, '', 0),
(6, 34, 70, 68, 27, 5, 'Deby', 'A147', 'L', 'GIII', 'Cesar Losada Mera', 'Eslón', '2015-02-01 13:05:47', '2015-02-01 13:05:47', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 70, 70, 31, 5, 'Mc Coy', 'A322', 'L', 'GIII', 'Carmen Sotomayor', 'Eslón', '2015-02-01 13:07:35', '2015-02-01 13:07:35', 0, 0, 0, 0, 0, 30.81, '', 0),
(6, 34, 70, 16, 34, 5, 'Zoe', 'A289', 'L', 'GIII', 'Marta Sánchez', 'A-0', '2015-02-01 13:05:23', '2015-02-01 13:05:23', 0, 0, 0, 0, 0, 30.61, '', 0),
(6, 34, 70, 22, 45, 5, 'Asia', 'A364', 'L', 'GIII', 'José Luis Romero', 'A. D. C. Pozuelo', '2015-02-01 13:04:13', '2015-02-01 13:04:13', 0, 2, 0, 1, 0, 0, '', 0),
(6, 34, 70, 71, 46, 5, 'Xodro', 'A371', 'L', 'GIII', 'Carlos Martínez R.', 'Eslón', '2015-02-01 13:08:08', '2015-02-01 13:08:08', 0, 3, 0, 1, 0, 0, '', 0),
(6, 34, 70, 69, 51, 5, 'Akane', 'A249', 'L', 'GIII', 'Gerardo González', 'Eslón', '2014-01-01 00:00:00', '2014-01-01 00:00:00', 0, 0, 0, 0, 0, 0, '', 1),
(6, 34, 70, 85, 57, 5, 'Aby', 'A204', 'L', 'GIII', 'Roberto Reina Vega', 'La Princesa', '2015-02-01 13:08:48', '2015-02-01 13:08:48', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 70, 14, 68, 5, 'Fito', 'A529', 'L', 'GIII', 'Luis Miguel Rodrigo', 'A-0', '2015-02-01 13:03:45', '2015-02-01 13:03:45', 0, 0, 0, 0, 0, 34.21, '', 0),
(6, 34, 70, 24, 78, 5, 'Winner', 'A497', 'L', 'GIII', 'Ricardo Santolaya', 'A. D. C. Pozuelo', '2015-02-01 12:59:04', '2015-02-01 12:59:04', 0, 0, 0, 0, 1, 0, '', 0),
(6, 34, 70, 69, 85, 5, 'Dylan', 'A461', 'L', 'GIII', 'Iván Sánchez García', 'Eslón', '2015-02-01 13:10:05', '2015-02-01 13:10:05', 1, 0, 0, 0, 0, 29.82, '', 0),
(6, 34, 70, 95, 101, 5, 'Magic-Black', 'A318', 'L', 'GIII', 'Raquel Garrido', 'Tandem', '2015-02-01 12:58:50', '2015-02-01 12:58:50', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 70, 86, 113, 5, 'Yun', 'A484', 'L', 'GIII', 'Concepción Fernández', 'La Princesa', '2015-02-01 13:00:00', '2015-02-01 13:00:00', 0, 0, 0, 1, 0, 0, '', 0),
(6, 34, 70, 15, 220, 5, 'Neo', 'A077', 'L', 'GIII', 'Antonio López', 'A-0', '2015-02-01 12:58:11', '2015-02-01 12:58:11', 2, 0, 0, 1, 0, 0, '', 0),
(6, 34, 70, 112, 229, 5, 'Shasta', 'A272', 'L', 'GIII', 'Mario Rodríguez', 'W.E.L.P.E.', '2015-02-01 13:01:17', '2015-02-01 13:01:17', 0, 0, 0, 0, 0, 28.6, '', 0),
(6, 34, 70, 41, 253, 5, 'Lass', 'A580', 'M', 'GIII', 'Eugenio Villares', 'Cubas', '2015-02-01 12:53:58', '2015-02-01 12:53:58', 0, 0, 0, 0, 0, 32.27, '', 0),
(6, 34, 70, 51, 314, 5, 'Nana', 'A277', 'S', 'GIII', 'Sara Montila', 'Deporcan', '2015-02-01 12:53:05', '2015-02-01 12:53:05', 0, 0, 0, 0, 0, 35.78, '', 0),
(6, 34, 70, 72, 350, 5, 'Dama', 'A641', 'M', 'GIII', 'Juan Antonio Martinez', 'Eslón', '2015-02-01 12:55:03', '2015-02-01 12:55:03', 0, 0, 0, 0, 0, 37.62, '', 0),
(6, 34, 70, 13, 417, 5, 'Crak', 'A719', 'L', 'GIII', 'Oscar Muñiz', 'A-0', '2015-02-01 13:10:43', '2015-02-01 13:10:43', 0, 0, 0, 0, 0, 28.39, '', 0),
(6, 34, 70, 39, 498, 5, 'Kobu', 'A806', 'M', 'GIII', 'Irena Montalvo', 'Correcan', '2015-02-01 12:56:47', '2015-02-01 12:56:47', 0, 1, 0, 0, 0, 34.11, '', 0),
(6, 34, 70, 91, 635, 5, 'Turco', 'A643', 'M', 'GIII', 'Gerardo Alvarez', 'Pataplán', '2015-02-01 12:55:56', '2015-02-01 12:55:56', 0, 0, 0, 0, 0, 34.44, '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--
-- Creación: 06-02-2015 a las 13:04:01
--

DROP TABLE IF EXISTS `sesiones`;
CREATE TABLE IF NOT EXISTS `sesiones` (
  `ID` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL DEFAULT '-- Sin asignar --',
  `Comentario` varchar(255) DEFAULT NULL,
  `Operador` int(4) NOT NULL DEFAULT '1',
  `SessionKey` varchar(255) DEFAULT NULL,
  `Prueba` int(4) NOT NULL DEFAULT '0',
  `Jornada` int(4) NOT NULL DEFAULT '0',
  `Manga` int(4) NOT NULL DEFAULT '0',
  `Tanda` int(4) NOT NULL DEFAULT '0',
  `LiveStream` varchar(255) DEFAULT NULL,
  `LiveStream2` varchar(255) DEFAULT NULL,
  `LiveStream3` varchar(255) DEFAULT NULL,
  `LastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `sesiones`:
--   `Operador`
--       `usuarios` -> `ID`
--

--
-- Volcado de datos para la tabla `sesiones`
--

INSERT INTO `sesiones` (`ID`, `Nombre`, `Comentario`, `Operador`, `SessionKey`, `Prueba`, `Jornada`, `Manga`, `Tanda`, `LiveStream`, `LiveStream2`, `LiveStream3`, `LastModified`) VALUES
(1, '-- Sin asignar --', '', 5, 'bSr8Rne5XANm7l0O', 3, 9, 0, 0, '/agility/videos/sample_video.mp4', NULL, NULL, '2015-02-10 08:20:33'),
(2, 'Ring 1', 'Mangas a realizar en el Ring de honor', 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, '2014-12-05 19:14:34'),
(3, 'Ring 2', 'Mangas a realizar en el segundo ring', 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, '2014-12-05 19:14:34'),
(4, 'Ring 3', 'Mangas a realizar en el tercer ring', 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, '2014-12-05 19:14:34'),
(5, 'Ring 4', 'Mangas a realizar en el cuarto ring', 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, '2014-12-05 19:14:34'),
(50, 'Console', 'admin - Administrador de la aplicacion', 1, 'RjvKoqO4pIGEl6aM', 0, 0, 0, 0, NULL, NULL, NULL, '2015-01-06 21:59:28'),
(87, 'Console', 'admin - Administrador de la aplicacion', 3, 'jIV04OWt9vsX6hKo', 0, 0, 0, 0, NULL, NULL, NULL, '2015-02-11 10:37:58'),
(88, 'Console', 'operator - Operador de consola', 4, '8TeOI2NGJj3oVfiP', 0, 0, 0, 0, NULL, NULL, NULL, '2015-02-11 10:38:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tandas`
--
-- Creación: 06-02-2015 a las 13:04:01
--

DROP TABLE IF EXISTS `tandas`;
CREATE TABLE IF NOT EXISTS `tandas` (
  `ID` int(4) NOT NULL,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Jornada` int(4) NOT NULL,
  `Sesion` int(4) NOT NULL DEFAULT '1',
  `Orden` int(4) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Categoria` varchar(16) NOT NULL,
  `Grado` varchar(16) NOT NULL,
  `Horario` varchar(128) DEFAULT NULL,
  `Tipo` int(4) NOT NULL,
  `Comentario` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=383 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `tandas`:
--   `Prueba`
--       `pruebas` -> `ID`
--   `Jornada`
--       `jornadas` -> `ID`
--   `Sesion`
--       `sesiones` -> `ID`
--

--
-- Volcado de datos para la tabla `tandas`
--

INSERT INTO `tandas` (`ID`, `Prueba`, `Jornada`, `Sesion`, `Orden`, `Nombre`, `Categoria`, `Grado`, `Horario`, `Tipo`, `Comentario`) VALUES
(347, 6, 33, 1, 1, 'Agility-1 GI Large', 'L', 'GI', NULL, 3, NULL),
(348, 6, 33, 1, 2, 'Agility-1 GI Medium', 'M', 'GI', NULL, 4, NULL),
(349, 6, 33, 1, 3, 'Agility-1 GI Small', 'S', 'GI', NULL, 5, NULL),
(350, 6, 33, 1, 4, 'Agility-2 GI Large', 'L', 'GI', NULL, 6, NULL),
(351, 6, 33, 1, 5, 'Agility-2 GI Medium', 'M', 'GI', NULL, 7, NULL),
(352, 6, 33, 1, 6, 'Agility-2 GI Small', 'S', 'GI', NULL, 8, NULL),
(353, 6, 33, 1, 7, 'Agility GII Large', 'L', 'GII', NULL, 9, NULL),
(354, 6, 33, 1, 8, 'Agility GII Medium', 'M', 'GII', NULL, 10, NULL),
(355, 6, 33, 1, 9, 'Agility GII Small', 'S', 'GII', NULL, 11, NULL),
(356, 6, 33, 1, 10, 'Jumping GII Large', 'L', 'GII', NULL, 23, NULL),
(357, 6, 33, 1, 11, 'Jumping GII Medium', 'M', 'GII', NULL, 24, NULL),
(358, 6, 33, 1, 12, 'Jumping GII Small', 'S', 'GII', NULL, 25, NULL),
(359, 6, 33, 1, 13, 'Agility GIII Large', 'L', 'GIII', NULL, 12, NULL),
(360, 6, 33, 1, 14, 'Agility GIII Medium', 'M', 'GIII', NULL, 13, NULL),
(361, 6, 33, 1, 15, 'Agility GIII Small', 'S', 'GIII', NULL, 14, NULL),
(362, 6, 33, 1, 16, 'Jumping GIII Large', 'L', 'GIII', NULL, 26, NULL),
(363, 6, 33, 1, 17, 'Jumping GIII Medium', 'M', 'GIII', NULL, 27, NULL),
(364, 6, 33, 1, 18, 'Jumping GIII Small', 'S', 'GIII', NULL, 28, NULL),
(365, 6, 34, 1, 1, 'Agility-1 GI Large', 'L', 'GI', NULL, 3, NULL),
(366, 6, 34, 1, 2, 'Agility-1 GI Medium', 'M', 'GI', NULL, 4, NULL),
(367, 6, 34, 1, 3, 'Agility-1 GI Small', 'S', 'GI', NULL, 5, NULL),
(368, 6, 34, 1, 4, 'Agility-2 GI Large', 'L', 'GI', NULL, 6, NULL),
(369, 6, 34, 1, 5, 'Agility-2 GI Medium', 'M', 'GI', NULL, 7, NULL),
(370, 6, 34, 1, 6, 'Agility-2 GI Small', 'S', 'GI', NULL, 8, NULL),
(371, 6, 34, 1, 7, 'Agility GII Large', 'L', 'GII', NULL, 9, NULL),
(372, 6, 34, 1, 8, 'Agility GII Medium', 'M', 'GII', NULL, 10, NULL),
(373, 6, 34, 1, 9, 'Agility GII Small', 'S', 'GII', NULL, 11, NULL),
(374, 6, 34, 1, 10, 'Jumping GII Large', 'L', 'GII', NULL, 23, NULL),
(375, 6, 34, 1, 11, 'Jumping GII Medium', 'M', 'GII', NULL, 24, NULL),
(376, 6, 34, 1, 12, 'Jumping GII Small', 'S', 'GII', NULL, 25, NULL),
(377, 6, 34, 1, 13, 'Agility GIII Large', 'L', 'GIII', NULL, 12, NULL),
(378, 6, 34, 1, 14, 'Agility GIII Medium', 'M', 'GIII', NULL, 13, NULL),
(379, 6, 34, 1, 15, 'Agility GIII Small', 'S', 'GIII', NULL, 14, NULL),
(380, 6, 34, 1, 16, 'Jumping GIII Large', 'L', 'GIII', NULL, 26, NULL),
(381, 6, 34, 1, 17, 'Jumping GIII Medium', 'M', 'GIII', NULL, 27, NULL),
(382, 6, 34, 1, 18, 'Jumping GIII Small', 'S', 'GIII', NULL, 28, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_manga`
--
-- Creación: 06-02-2015 a las 13:04:01
--

DROP TABLE IF EXISTS `tipo_manga`;
CREATE TABLE IF NOT EXISTS `tipo_manga` (
  `ID` int(4) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  `Grado` varchar(16) NOT NULL DEFAULT '-'
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `tipo_manga`:
--   `Grado`
--       `grados_perro` -> `Grado`
--

--
-- Volcado de datos para la tabla `tipo_manga`
--

INSERT INTO `tipo_manga` (`ID`, `Descripcion`, `Grado`) VALUES
(1, 'Pre-Agility Manga 1', 'P.A.'),
(2, 'Pre-Agility Manga 2', 'P.A.'),
(3, 'Agility Grado I Manga 1', 'GI'),
(4, 'Agility Grado I Manga 2', 'GI'),
(5, 'Agility Grado II', 'GII'),
(6, 'Agility Grado III', 'GIII'),
(7, 'Agility Abierta (Open)', '-'),
(8, 'Agility Equipos (3 mejores)', '-'),
(9, 'Agility Equipos (Conjunta)', '-'),
(10, 'Jumping Grado II', 'GII'),
(11, 'Jumping Grado III', 'GIII'),
(12, 'Jumping Abierta (Open)', '-'),
(13, 'Jumping por Equipos (3 mejores)', '-'),
(14, 'Jumping por Equipos (Conjunta)', '-'),
(15, 'Ronda K.O.', '-'),
(16, 'Manga Especial', '-');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--
-- Creación: 06-02-2015 a las 13:04:00
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID` int(4) NOT NULL,
  `Login` varchar(255) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Gecos` varchar(255) NOT NULL DEFAULT '',
  `Phone` varchar(255) NOT NULL DEFAULT '',
  `Email` varchar(255) NOT NULL DEFAULT '',
  `Perms` int(4) NOT NULL DEFAULT '5'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `usuarios`:
--

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID`, `Login`, `Password`, `Gecos`, `Phone`, `Email`, `Perms`) VALUES
(1, '-- Sin asignar --', '--LOCKED--', 'NO BORRAR: Usuario por defecto para sesiones anonimas', '', '', 5),
(2, 'root', 'JDJ5JDEwJHc2Lm50WFhsQUYuWDl2Zm9JbnNVb09TVEVwcllGaHBCQjFQYk12Yk81VzlJWDd0cTNPRnd5', 'Usuario Root', '', '', 0),
(3, 'admin', 'JDJ5JDEwJFcwa3B4YUxDVkJ0OVd0NFZVNUhzcXVBTE1yN0x2WWhBTFo4RHQ5TWZZQzgzZGRnMDA1VlVD', 'Administrador de la aplicacion', '', '', 1),
(4, 'operator', 'JDJ5JDEwJHMyclNoQUtsMlJ0UU5pRG9yUXF3QXUwbEVRdWpUT0daSXJGZmJLR3B4MEVHRzRiOFNYSjdt', 'Operador de consola', '', '', 2),
(5, 'assistant', 'JDJ5JDEwJHRLL09tT2xJZ1lRRlovNVhsLksxRC52aXo4L1UxNTMub1EwRDRoZ3pCZDcxRHRnSmo0LmE2', 'Asistente del juez (tablet)', '', '', 3),
(6, 'guest', '--NULL--', 'Usuario invitado (anonimo)', '', '', 4);

-- --------------------------------------------------------

--
-- Estructura para la vista `perroguiaclub`
--
DROP TABLE IF EXISTS `perroguiaclub`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `perroguiaclub` AS select `perros`.`ID` AS `ID`,`perros`.`Nombre` AS `Nombre`,`perros`.`Raza` AS `Raza`,`perros`.`Licencia` AS `Licencia`,`perros`.`LOE_RRC` AS `LOE_RRC`,`perros`.`Categoria` AS `Categoria`,`categorias_perro`.`Observaciones` AS `NombreCategoria`,`perros`.`Grado` AS `Grado`,`grados_perro`.`Comentarios` AS `NombreGrado`,`perros`.`Guia` AS `Guia`,`guias`.`Nombre` AS `NombreGuia`,`guias`.`Club` AS `Club`,`clubes`.`Nombre` AS `NombreClub`,`clubes`.`Logo` AS `LogoClub` from ((((`perros` join `guias`) join `clubes`) join `grados_perro`) join `categorias_perro`) where ((`perros`.`Guia` = `guias`.`ID`) and (`guias`.`Club` = `clubes`.`ID`) and (`perros`.`Categoria` = `categorias_perro`.`Categoria`) and (`perros`.`Grado` = `grados_perro`.`Grado`)) order by `clubes`.`Nombre`,`perros`.`Categoria`,`perros`.`Nombre`;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias_perro`
--
ALTER TABLE `categorias_perro`
  ADD PRIMARY KEY (`Categoria`);

--
-- Indices de la tabla `clubes`
--
ALTER TABLE `clubes`
  ADD PRIMARY KEY (`ID`), ADD KEY `Clubes_Nombre` (`Nombre`), ADD KEY `Clubes_Provincia` (`Provincia`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `Equipos_PruebaNombre` (`Prueba`,`Nombre`), ADD KEY `Equipos_Prueba` (`Prueba`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`ID`), ADD KEY `Eventos_Session` (`Session`);

--
-- Indices de la tabla `grados_perro`
--
ALTER TABLE `grados_perro`
  ADD PRIMARY KEY (`Grado`);

--
-- Indices de la tabla `guias`
--
ALTER TABLE `guias`
  ADD PRIMARY KEY (`ID`), ADD KEY `Guias_Nombre` (`Nombre`), ADD KEY `Guias_Club` (`Club`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `Inscripciones_PruebaPerro` (`Prueba`,`Perro`), ADD KEY `Inscripciones_Perro` (`Perro`), ADD KEY `Inscripciones_Prueba` (`Prueba`), ADD KEY `Inscripciones_Equipo` (`Equipo`), ADD KEY `Inscripciones_Dorsal` (`Dorsal`);

--
-- Indices de la tabla `jornadas`
--
ALTER TABLE `jornadas`
  ADD PRIMARY KEY (`ID`), ADD KEY `Jornadas_Prueba` (`Prueba`);

--
-- Indices de la tabla `jueces`
--
ALTER TABLE `jueces`
  ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `Jueces_Nombre` (`Nombre`);

--
-- Indices de la tabla `mangas`
--
ALTER TABLE `mangas`
  ADD PRIMARY KEY (`ID`), ADD KEY `Mangas_Tipo` (`Tipo`), ADD KEY `Mangas_Grado` (`Grado`), ADD KEY `Mangas_Juez1` (`Juez1`), ADD KEY `Mangas_Juez2` (`Juez2`), ADD KEY `Mangas_Jornada` (`Jornada`);

--
-- Indices de la tabla `perros`
--
ALTER TABLE `perros`
  ADD PRIMARY KEY (`ID`), ADD KEY `Perros_GuiaNombre` (`Guia`), ADD KEY `Perros_Categoria` (`Categoria`), ADD KEY `Perros_Grado` (`Grado`);

--
-- Indices de la tabla `provincias`
--
ALTER TABLE `provincias`
  ADD PRIMARY KEY (`Provincia`), ADD UNIQUE KEY `Provincias_Codigo` (`Codigo`);

--
-- Indices de la tabla `pruebas`
--
ALTER TABLE `pruebas`
  ADD PRIMARY KEY (`ID`), ADD KEY `Pruebas_Club` (`Club`);

--
-- Indices de la tabla `resultados`
--
ALTER TABLE `resultados`
  ADD PRIMARY KEY (`Manga`,`Perro`), ADD KEY `Resultados_Perro` (`Perro`), ADD KEY `Resultados_Manga` (`Manga`), ADD KEY `Resultados_Dorsal` (`Dorsal`), ADD KEY `Resultados_Jornada` (`Jornada`), ADD KEY `Resultados_Prueba` (`Prueba`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`ID`), ADD KEY `Sesiones_Operador` (`Operador`);

--
-- Indices de la tabla `tandas`
--
ALTER TABLE `tandas`
  ADD PRIMARY KEY (`ID`), ADD KEY `Tandas_Prueba` (`Prueba`), ADD KEY `Tandas_Jornada` (`Jornada`), ADD KEY `Tandas_Sesion` (`Sesion`);

--
-- Indices de la tabla `tipo_manga`
--
ALTER TABLE `tipo_manga`
  ADD PRIMARY KEY (`ID`), ADD KEY `Descripcion` (`Descripcion`), ADD KEY `Grado` (`Grado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `Login` (`Login`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clubes`
--
ALTER TABLE `clubes`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=96;
--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `guias`
--
ALTER TABLE `guias`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=588;
--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=426;
--
-- AUTO_INCREMENT de la tabla `jornadas`
--
ALTER TABLE `jornadas`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT de la tabla `jueces`
--
ALTER TABLE `jueces`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT de la tabla `mangas`
--
ALTER TABLE `mangas`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=71;
--
-- AUTO_INCREMENT de la tabla `perros`
--
ALTER TABLE `perros`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=744;
--
-- AUTO_INCREMENT de la tabla `pruebas`
--
ALTER TABLE `pruebas`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=89;
--
-- AUTO_INCREMENT de la tabla `tandas`
--
ALTER TABLE `tandas`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=383;
--
-- AUTO_INCREMENT de la tabla `tipo_manga`
--
ALTER TABLE `tipo_manga`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clubes`
--
ALTER TABLE `clubes`
ADD CONSTRAINT `Clubes_ibfk_1` FOREIGN KEY (`Provincia`) REFERENCES `provincias` (`Provincia`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
ADD CONSTRAINT `Equipos_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
ADD CONSTRAINT `Eventos_ibfk_1` FOREIGN KEY (`Session`) REFERENCES `sesiones` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `guias`
--
ALTER TABLE `guias`
ADD CONSTRAINT `Guias_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `clubes` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
ADD CONSTRAINT `Inscripciones_ibfk_1` FOREIGN KEY (`Perro`) REFERENCES `perros` (`ID`),
ADD CONSTRAINT `Inscripciones_ibfk_2` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON UPDATE CASCADE,
ADD CONSTRAINT `Inscripciones_ibfk_3` FOREIGN KEY (`Equipo`) REFERENCES `equipos` (`ID`);

--
-- Filtros para la tabla `jornadas`
--
ALTER TABLE `jornadas`
ADD CONSTRAINT `Jornadas_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mangas`
--
ALTER TABLE `mangas`
ADD CONSTRAINT `Mangas_ibfk_1` FOREIGN KEY (`Tipo`) REFERENCES `tipo_manga` (`ID`) ON UPDATE CASCADE,
ADD CONSTRAINT `Mangas_ibfk_2` FOREIGN KEY (`Grado`) REFERENCES `grados_perro` (`Grado`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `Mangas_ibfk_3` FOREIGN KEY (`Juez1`) REFERENCES `jueces` (`ID`) ON UPDATE CASCADE,
ADD CONSTRAINT `Mangas_ibfk_4` FOREIGN KEY (`Juez2`) REFERENCES `jueces` (`ID`) ON UPDATE CASCADE,
ADD CONSTRAINT `Mangas_ibfk_5` FOREIGN KEY (`Jornada`) REFERENCES `jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `perros`
--
ALTER TABLE `perros`
ADD CONSTRAINT `Perros_ibfk_1` FOREIGN KEY (`Categoria`) REFERENCES `categorias_perro` (`Categoria`) ON UPDATE CASCADE,
ADD CONSTRAINT `Perros_ibfk_2` FOREIGN KEY (`Grado`) REFERENCES `grados_perro` (`Grado`) ON UPDATE CASCADE,
ADD CONSTRAINT `Perros_ibfk_3` FOREIGN KEY (`Guia`) REFERENCES `guias` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pruebas`
--
ALTER TABLE `pruebas`
ADD CONSTRAINT `Pruebas_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `clubes` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `resultados`
--
ALTER TABLE `resultados`
ADD CONSTRAINT `Resultados_ibfk_1` FOREIGN KEY (`Perro`) REFERENCES `perros` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `Resultados_ibfk_2` FOREIGN KEY (`Manga`) REFERENCES `mangas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `Resultados_ibfk_3` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `Resultados_ibfk_4` FOREIGN KEY (`Jornada`) REFERENCES `jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
ADD CONSTRAINT `Sesiones_ibfk_1` FOREIGN KEY (`Operador`) REFERENCES `usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tandas`
--
ALTER TABLE `tandas`
ADD CONSTRAINT `Tandas_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `Tandas_ibfk_2` FOREIGN KEY (`Jornada`) REFERENCES `jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `Tandas_ibfk_3` FOREIGN KEY (`Sesion`) REFERENCES `sesiones` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `tipo_manga`
--
ALTER TABLE `tipo_manga`
ADD CONSTRAINT `Tipo_Manga_ibfk_1` FOREIGN KEY (`Grado`) REFERENCES `grados_perro` (`Grado`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
