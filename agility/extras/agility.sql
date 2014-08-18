-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 12-07-2014 a las 09:36:57
-- Versión del servidor: 5.5.37-MariaDB
-- Versión de PHP: 5.5.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `agility`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Categorias_Perro`
--
-- Creación: 12-07-2014 a las 09:16:36
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
-- Creación: 12-07-2014 a las 09:16:36
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=86 ;

--
-- RELACIONES PARA LA TABLA `Clubes`:
--   `Provincia`
--       `Provincias` -> `Provincia`
--

--
-- Volcado de datos para la tabla `Clubes`
--

INSERT INTO `Clubes` (`ID`, `Nombre`, `Direccion1`, `Direccion2`, `Provincia`, `Contacto1`, `Contacto2`, `Contacto3`, `GPS`, `Web`, `Email`, `Facebook`, `Google`, `Twitter`, `Logo`, `Observaciones`, `Baja`) VALUES
(1, '-- Sin asignar --', '', '', '-- Sin asignar --', '', '', '', '', '', '', '', '', '', 'rsce.png', 'NO BORRAR ESTA ENTRADA. SE USARA PARA AQUELLOS GUIAS QUE NO TENGAN CLUB ASIGNADO', 0),
(2, 'AA Y CIA', '28609 Sevilla La Nueva (Madrid)', '', 'Madrid', '+ 34 619 29 03 98', '', '', '', '', 'arribas.anabel@gmail.com', '', '', '', 'aaycia.png', '', 0),
(3, 'ACADE', 'Salvadas, 41, 2º C', '15705 Santiago de Compostela', 'Coruña, A', '+ 34 620 29 58 31', '+ 34 881 93 95 5', '', '', 'http://www.asociacionacade.com/', 'asociacioncansdeportistas@gmail.com', '', '', '', 'acade.png', '', 0),
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
(85, 'Teocan', 'Rarís, s/n', '15883 Teo (La Coruña)', 'Coruña, A', '+ 34 627 93 72 81', '', '', '', '', 'adteocan@gmail.com', '', '', '', 'teocan.png', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Equipos`
--
-- Creación: 12-07-2014 a las 09:16:36
--

DROP TABLE IF EXISTS `Equipos`;
CREATE TABLE IF NOT EXISTS `Equipos` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL DEFAULT '1',
  `Nombre` varchar(255) NOT NULL,
  `Observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Equipos_PruebaNombre` (`Prueba`,`Nombre`),
  KEY `Equipos_Prueba` (`Prueba`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- RELACIONES PARA LA TABLA `Equipos`:
--   `Prueba`
--       `Pruebas` -> `ID`
--

--
-- Volcado de datos para la tabla `Equipos`
--

INSERT INTO `Equipos` (`ID`, `Prueba`, `Nombre`, `Observaciones`) VALUES
(1, 2, '-- Sin asignar --', 'NO BORRAR: PRUEBA 2 - Equipo por defecto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Grados_Perro`
--
-- Creación: 12-07-2014 a las 09:16:36
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
-- Creación: 12-07-2014 a las 09:16:36
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=468 ;

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
(2, 'Aaron Laro', '', '', 27, ''),
(3, 'Ada Serrano', '', '', 67, ''),
(4, 'Adoración Morales', NULL, NULL, 45, NULL),
(5, 'Adrian Bajo', '', '', 76, ''),
(6, 'Adrian Díaz', NULL, NULL, 31, NULL),
(7, 'Adrian Martínez', '', '', 62, ''),
(8, 'Adrián Soria', '', '', 62, ''),
(9, 'Africa Cabañas', '', '', 22, ''),
(10, 'Agustin Centelles', NULL, NULL, 42, NULL),
(11, 'Agustín González', '', '', 43, ''),
(12, 'Aida Al-Nehlawi', NULL, NULL, 13, NULL),
(13, 'Alaitz Idarraga', '', '', 36, ''),
(14, 'Albert Ulldemolins', NULL, NULL, 7, NULL),
(15, 'Alberto Alonso', NULL, NULL, 24, NULL),
(16, 'Alberto Costas', NULL, NULL, 75, NULL),
(17, 'Alberto González', '', '', 28, ''),
(18, 'Alberto Marugan', '', '', 4, ''),
(19, 'Alberto Mudarra', NULL, NULL, 42, NULL),
(20, 'Alberto Pereda', NULL, NULL, 48, NULL),
(21, 'Alejandra Alvarez', '', '', 31, ''),
(22, 'Alejandro Piñeiro', NULL, NULL, 71, NULL),
(23, 'Alejandro Rodríguez Villalta', '', '', 59, ''),
(24, 'Alejandro Salas', '', '', 80, ''),
(25, 'Alex del Río', NULL, NULL, 18, NULL),
(26, 'Alex Olivera', NULL, NULL, 67, NULL),
(27, 'Alex Sabini', NULL, NULL, 18, NULL),
(28, 'Alfredo Ortíz', NULL, NULL, 40, NULL),
(29, 'Alicia Mejias', NULL, NULL, 61, NULL),
(30, 'Alicia Sanjurjo', NULL, NULL, 74, NULL),
(31, 'Almudena Novo', '', '', 16, ''),
(32, 'Amparo Roig', NULL, NULL, 43, NULL),
(33, 'Ana Alonso', NULL, NULL, 68, NULL),
(34, 'Ana Baeza', NULL, NULL, 40, NULL),
(35, 'Ana Beltran', NULL, NULL, 32, NULL),
(36, 'Ana Isabel Escobar', NULL, NULL, 28, NULL),
(37, 'Ana Mateu', NULL, NULL, 13, NULL),
(38, 'Ana Ontañon', NULL, NULL, 52, NULL),
(39, 'Ana Palet', '', '', 28, ''),
(40, 'Ana Valencia', '', '', 4, ''),
(41, 'Andrea García', NULL, NULL, 76, NULL),
(42, 'Andrés Gimeno', NULL, NULL, 45, NULL),
(43, 'Andrés López', NULL, NULL, 71, NULL),
(44, 'Andres Morillas Sanjuan', '', '', 30, ''),
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
(58, 'Antonio López', '', '', 30, ''),
(59, 'Antonio Molina', NULL, NULL, 16, NULL),
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
(73, 'Beatriz Juan', '', '', 43, ''),
(74, 'Belén de Carvalho', '', '', 30, ''),
(75, 'Berit Kittel', '', '', 39, ''),
(76, 'Carles Fortuny', NULL, NULL, 18, NULL),
(77, 'Carlos Alvarez', NULL, NULL, 71, NULL),
(78, 'Carlos Casado', NULL, NULL, 68, NULL),
(79, 'Carlos Iglesias', NULL, NULL, 75, NULL),
(80, 'Carlos Martínez', NULL, NULL, 35, NULL),
(81, 'Carlos Pérez', '', '', 48, ''),
(82, 'Carlos Pulpón', '', '', 35, ''),
(83, 'Carlos Serra', NULL, NULL, 38, NULL),
(84, 'Carmen Alos', NULL, NULL, 13, NULL),
(85, 'Carmen Antequera', NULL, NULL, 43, NULL),
(86, 'Carmen Briceño', NULL, NULL, 42, NULL),
(87, 'Carmen Sotomayor', NULL, NULL, 35, NULL),
(88, 'Carmen Vázquez', NULL, NULL, 50, NULL),
(89, 'Carolina Verdú', NULL, NULL, 78, NULL),
(90, 'Celeste Zarzosa', NULL, NULL, 48, NULL),
(91, 'Celso Valle', NULL, NULL, 52, NULL),
(92, 'Cesar Losada', NULL, NULL, 35, NULL),
(93, 'Charly Castañer', '', '', 39, ''),
(94, 'Clara Ruisánchez', '', '', 80, ''),
(95, 'Concepción Fernández', NULL, NULL, 48, NULL),
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
(113, 'David Flix', NULL, NULL, 67, NULL),
(114, 'David Gómez-Calcerrada', '', '', 48, ''),
(115, 'David Gonzalbo', NULL, NULL, 31, NULL),
(116, 'David Molina', '', '', 42, ''),
(117, 'David Parejo', NULL, NULL, 17, NULL),
(118, 'David Sepulveda', NULL, NULL, 7, NULL),
(119, 'Debra Howard', NULL, NULL, 48, NULL),
(120, 'Diana Cozar', NULL, NULL, 13, NULL),
(121, 'Diana García', NULL, NULL, 61, NULL),
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
(136, 'Emilio José Pedrazuela', NULL, NULL, 30, NULL),
(137, 'Enma Gutiérrez', '', '', 60, ''),
(138, 'Enric García', NULL, NULL, 81, NULL),
(139, 'Enric Lleixa', NULL, NULL, 7, NULL),
(140, 'Enrique Alonso Queija', '', '', 30, ''),
(141, 'Enrique Camarero', '', '', 35, ''),
(142, 'Enrique Grau', NULL, NULL, 24, NULL),
(143, 'Enrique Herbera', NULL, NULL, 7, NULL),
(144, 'Enrique Lleixa', NULL, NULL, 7, NULL),
(145, 'Enrique Sendra', NULL, NULL, 71, NULL),
(146, 'Ernesto Sorribes', NULL, NULL, 81, NULL),
(147, 'Estefanía Pérez', NULL, NULL, 30, NULL),
(148, 'Estíbaliz Pereda Navarro', '', '', 28, ''),
(149, 'Estíbaliz Pujana', '', '', 43, ''),
(150, 'Eugenio Villares', NULL, NULL, 27, NULL),
(151, 'Eva Grau', NULL, NULL, 64, NULL),
(152, 'Eva Vázquez Morales', '', '', 42, ''),
(153, 'Fermin Gil', NULL, NULL, 43, NULL),
(154, 'Fernando Benet', NULL, NULL, 81, NULL),
(155, 'Fernando Bibián', '', '', 4, ''),
(156, 'Fernando Cardeña', '', '', 80, ''),
(157, 'Fernando De La Fuente', '', '', 42, ''),
(158, 'Fito Rodríguez', '', '', 80, ''),
(159, 'Francisco Aguilera', NULL, NULL, 17, NULL),
(160, 'Francisco de la Cruz', NULL, NULL, 16, NULL),
(161, 'Francisco Esteban', NULL, NULL, 24, NULL),
(162, 'Francisco Javier Jaen', NULL, NULL, 28, NULL),
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
(175, 'Gerardo Alvarez', NULL, NULL, 40, NULL),
(176, 'Gerardo González', NULL, NULL, 35, NULL),
(177, 'Gisela Solis', NULL, NULL, 44, NULL),
(178, 'Gorka Pozuelo', '', '', 43, ''),
(179, 'Gregorio Conde', NULL, NULL, 7, NULL),
(180, 'Gustavo Deus', '', '', 31, ''),
(181, 'Iago Sánchez', NULL, NULL, 71, NULL),
(182, 'Iban Cubedo', NULL, NULL, 24, NULL),
(183, 'Imanol López', NULL, NULL, 42, NULL),
(184, 'Iñaki García', '', '', 36, ''),
(185, 'Inmaculada Rubio', NULL, NULL, 35, NULL),
(186, 'Irati Diego', '', '', 43, ''),
(187, 'Irena', '', '', 42, ''),
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
(199, 'Iván Sánchez García', '', '', 48, ''),
(200, 'Jacqueline Holemans', NULL, NULL, 67, NULL),
(201, 'Jaime Gamir', NULL, NULL, 8, NULL),
(202, 'Jara Pérez', '', '', 4, ''),
(203, 'Jaume Fernández', NULL, NULL, 18, NULL),
(204, 'Javier Gómez', NULL, NULL, 65, NULL),
(205, 'Javier Iniesta', '', '', 75, ''),
(206, 'Javier López', NULL, NULL, 67, NULL),
(207, 'Javier Martín', '', '', 4, ''),
(208, 'Javier Martínez', '', '', 62, ''),
(209, 'Javier Mora Canales', '', '', 22, ''),
(210, 'Javier Ovejero', NULL, NULL, 35, NULL),
(211, 'Javier Sanchis', NULL, NULL, 52, NULL),
(212, 'Javier Santisteban', '', '', 16, ''),
(213, 'Jenifer Tolín', '', '', 48, ''),
(214, 'Jennifer Tolín', NULL, NULL, 48, NULL),
(215, 'Jenny Funcke', NULL, NULL, 7, NULL),
(216, 'Jerónimo Martínez', NULL, NULL, 68, NULL),
(217, 'Jessica Graciano', '', '', 48, ''),
(218, 'Jesús Crespo', NULL, NULL, 55, NULL),
(219, 'Jesús Cuellar', NULL, NULL, 35, NULL),
(220, 'Jesús Gómez', '', '', 4, ''),
(221, 'Jesús Manuel Romero', NULL, NULL, 59, NULL),
(222, 'Joan Castillo', '', '', 52, ''),
(223, 'Joan Wenceslao Pastor', NULL, NULL, 74, NULL),
(224, 'Joaquín Andrés', '', '', 4, ''),
(225, 'Jonathan Guillen', NULL, NULL, 77, NULL),
(226, 'Jordi Boix', NULL, NULL, 13, NULL),
(227, 'Jordi Gómez', NULL, NULL, 13, NULL),
(228, 'Jorge Arcas Perales', '', '', 42, ''),
(229, 'Jorge Bala', '', '', 42, ''),
(230, 'Jorge Muñoz Leal', '', '', 27, ''),
(231, 'Jorge Valero', NULL, NULL, 42, NULL),
(232, 'José Angel Beired', '', '', 73, ''),
(233, 'José Angel Torres', NULL, NULL, 71, NULL),
(234, 'José Antonio Encinas', NULL, NULL, 22, NULL),
(235, 'José Antonio Pascual', NULL, NULL, 42, NULL),
(236, 'José Antonio Vega', NULL, NULL, 4, NULL),
(237, 'José Carlos Iglesias', NULL, NULL, 75, NULL),
(238, 'José Castaño', NULL, NULL, 74, NULL),
(239, 'José Francisco Martorell', NULL, NULL, 65, NULL),
(240, 'José Guix', NULL, NULL, 65, NULL),
(241, 'José Luis García', NULL, NULL, 81, NULL),
(242, 'Jose Luis J. Mori', '', '', 2, ''),
(243, 'José Luis Prieto', NULL, NULL, 38, NULL),
(244, 'José Luis Quiroga', NULL, NULL, 21, NULL),
(245, 'José Luis Romero', NULL, NULL, 27, NULL),
(246, 'José Luis Sogorb', NULL, NULL, 45, NULL),
(247, 'José Mahillo', NULL, NULL, 48, NULL),
(248, 'José Manuel Basco', NULL, NULL, 59, NULL),
(249, 'José Manuel Linares', NULL, NULL, 72, NULL),
(250, 'José Martí', NULL, NULL, 43, NULL),
(251, 'José Mateo Moreno', NULL, NULL, 21, NULL),
(252, 'José Miguel Agustín', NULL, NULL, 4, NULL),
(253, 'José Miguel Morant', NULL, NULL, 48, NULL),
(254, 'José Miguel Paredes', NULL, NULL, 72, NULL),
(255, 'José Moreno', NULL, NULL, 26, NULL),
(256, 'José Pavon', NULL, NULL, 29, NULL),
(257, 'José Peris', NULL, NULL, 64, NULL),
(258, 'Jose Ramón López', '', '', 35, ''),
(259, 'José Santos Luna', NULL, NULL, 40, NULL),
(260, 'José Soliño', NULL, NULL, 71, NULL),
(261, 'Josep Barbera', NULL, NULL, 58, NULL),
(262, 'Josep Mª Pineda', NULL, NULL, 74, NULL),
(263, 'Juan', '', '', 3, ''),
(264, 'Juan Antonio Martinez', NULL, 'juansgaviota@gmail.com', 79, NULL),
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
(277, 'Juan José Paz', '', '', 12, ''),
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
(291, 'Julio Freire', '', '', 60, ''),
(292, 'Katia Moeller', NULL, NULL, 14, NULL),
(293, 'Katy Navarro', NULL, NULL, 60, NULL),
(294, 'Laura Carrasco', NULL, NULL, 18, NULL),
(295, 'Laura Chiva', NULL, NULL, 7, NULL),
(296, 'Laura Monrabal', '', '', 42, ''),
(297, 'Leire Herrera', '', '', 36, ''),
(298, 'Lorena Díez', NULL, NULL, 48, NULL),
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
(312, 'Luis Miguel Rodrigo', '', '', 43, ''),
(313, 'Luis Miguel Rodriguez', '', '', 48, ''),
(314, 'Luisa Fernanda Millan', '', '', 62, ''),
(315, 'Luna Ramírez', NULL, NULL, 52, NULL),
(316, 'Maitane Luengo', '', '', 36, ''),
(317, 'Maite Guerrero', '', '', 35, ''),
(318, 'Manel Martínez', NULL, NULL, 74, NULL),
(319, 'Manuel Basco', NULL, NULL, 59, NULL),
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
(337, 'Marta Sánchez', NULL, NULL, 27, NULL),
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
(360, 'Natalia Cuadrado', NULL, NULL, 21, NULL),
(361, 'Natividad Ruiz García', '', '', 30, ''),
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
(374, 'Oscar Muñiz', NULL, NULL, 30, NULL),
(375, 'Oscar Reboredo', NULL, NULL, 18, NULL),
(376, 'Oscar Sacristan', NULL, NULL, 48, NULL),
(377, 'Pablo Ballesta', NULL, NULL, 10, NULL),
(378, 'Pablo Miró', NULL, NULL, 81, NULL),
(379, 'Paloma Faci Green', '', '', 42, ''),
(380, 'Pau Serrano Ciratusa', '', '', 42, ''),
(381, 'Paula de Lucas', NULL, NULL, 27, NULL),
(382, 'Paula Rello', NULL, NULL, 27, NULL),
(383, 'Paulino Iranzo', NULL, NULL, 65, NULL),
(384, 'Pedro Delgado Fernandez', '', '', 30, ''),
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
(401, 'Raquel Garrido', NULL, NULL, 27, NULL),
(402, 'Raúl Sánchez', NULL, NULL, 27, NULL),
(403, 'Remedios Torres', NULL, NULL, 68, NULL),
(404, 'Reyes García', NULL, NULL, 31, NULL),
(405, 'Ricardo Benito', NULL, NULL, 79, NULL),
(406, 'Ricardo Martínez', NULL, NULL, 29, NULL),
(407, 'Ricardo Santolaya', NULL, NULL, 42, NULL),
(408, 'Roberto Castro', NULL, NULL, 35, NULL),
(409, 'Roberto Iñigo', NULL, NULL, 16, NULL),
(410, 'Roberto Reina Vega', '', '', 48, ''),
(411, 'Rocio Hermelo', NULL, NULL, 71, NULL),
(412, 'Rocio Santos', NULL, NULL, 61, NULL),
(413, 'Rodrigo González', '', '', 48, ''),
(414, 'Roque Alonso', '', '', 40, ''),
(415, 'Ross Rubio', '', '', 28, ''),
(416, 'Ruben Jurado', NULL, NULL, 44, NULL),
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
(427, 'Sergio Casalins', NULL, NULL, 30, NULL),
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
(441, 'Stefan Eggenschwiler', NULL, NULL, 72, NULL),
(442, 'Stina Sandquist', NULL, NULL, 17, NULL),
(443, 'Tamara Vidal', NULL, NULL, 44, NULL),
(444, 'Tomás Pérez', NULL, NULL, 42, NULL),
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
(456, 'Vicente Micó', NULL, NULL, 78, NULL),
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
(467, 'Carmen Gutiérrez', '', '', 2, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inscripciones`
--
-- Creación: 12-07-2014 a las 09:16:36
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
  `Jornadas` int(4) NOT NULL DEFAULT '0',
  `Pagado` int(4) NOT NULL DEFAULT '24',
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
-- Creación: 12-07-2014 a las 09:33:32
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
  `Open` tinyint(1) NOT NULL DEFAULT '0',
  `Equipos3` tinyint(1) NOT NULL DEFAULT '0',
  `Equipos4` tinyint(1) NOT NULL DEFAULT '0',
  `PreAgility` tinyint(1) NOT NULL DEFAULT '1',
  `KO` tinyint(1) NOT NULL DEFAULT '0',
  `Exhibicion` tinyint(1) NOT NULL DEFAULT '0',
  `Otras` tinyint(1) NOT NULL DEFAULT '0',
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Prueba` (`Prueba`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- RELACIONES PARA LA TABLA `Jornadas`:
--   `Prueba`
--       `Pruebas` -> `ID`
--

--
-- Volcado de datos para la tabla `Jornadas`
--

INSERT INTO `Jornadas` (`ID`, `Prueba`, `Numero`, `Nombre`, `Fecha`, `Hora`, `Grado1`, `Grado2`, `Grado3`, `Open`, `Equipos3`, `Equipos4`, `PreAgility`, `KO`, `Exhibicion`, `Otras`, `Cerrada`) VALUES
(1, 2, 1, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0),
(2, 2, 2, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(3, 2, 3, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0),
(4, 2, 4, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0),
(5, 2, 5, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0),
(6, 2, 6, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0),
(7, 2, 7, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0),
(8, 2, 8, '-- Sin asignar --', '2013-01-01', '00:00:00', 1, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Jueces`
--
-- Creación: 12-07-2014 a las 09:16:36
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Volcado de datos para la tabla `Jueces`
--

INSERT INTO `Jueces` (`ID`, `Nombre`, `Direccion1`, `Direccion2`, `Telefono`, `Internacional`, `Practicas`, `Email`, `Observaciones`) VALUES
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
-- Estructura de tabla para la tabla `Mangas`
--
-- Creación: 12-07-2014 a las 09:16:36
--

DROP TABLE IF EXISTS `Mangas`;
CREATE TABLE IF NOT EXISTS `Mangas` (
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
  `Orden_Salida` text NOT NULL DEFAULT "",
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
--       `Tipo_Manga` -> `ID`
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
`ID` int(4)
,`Nombre` varchar(255)
,`Raza` varchar(255)
,`Licencia` varchar(255)
,`LOE_RRC` varchar(255)
,`Categoria` varchar(1)
,`Grado` varchar(16)
,`Guia` int(4)
,`NombreGuia` varchar(255)
,`Club` int(4)
,`NombreClub` varchar(255)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Perros`
--
-- Creación: 12-07-2014 a las 09:16:36
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=559 ;

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
(2, 'Yuma', 'P.B.Malinoise', '1936256', 'A330', 'L', 281, 'GIII'),
(3, 'Hannibal', '', '1764520', 'A090', 'L', 444, 'GIII'),
(4, 'Ardi', '', '79097', '729', 'L', 379, '-'),
(5, 'William', NULL, '1667920', '920', 'L', 215, '-'),
(6, 'Xonny', NULL, '1317156', '622', 'L', 92, '-'),
(7, 'Indiana Jones', '', '1720531', '987', 'L', 273, 'GIII'),
(8, 'Thelma', '', '1515702', '824', 'L', 407, 'GIII'),
(9, 'Boss', '', '1528991', '797', 'L', 374, 'GIII'),
(10, 'Lee', 'Border Collie', '95245', 'A084', 'L', 59, 'GIII'),
(11, 'Chinouk', NULL, '1390419', '724', 'L', 345, '-'),
(12, 'Angie', NULL, '1370168', '691', 'L', 59, '-'),
(13, 'Burundi', '', '1874262', 'A310', 'L', 130, 'GIII'),
(14, 'Piter', '', '110594', 'A360', 'L', 104, 'GIII'),
(15, 'Napa', '', '1975832', 'A401', 'L', 379, 'GIII'),
(16, 'Gon', '', '1725855', 'A024', 'L', 323, 'GIII'),
(17, 'Valerie', '', '1467667', '786', 'L', 318, 'GIII'),
(18, 'Woman', '', '1866186', 'A206', 'L', 209, 'GIII'),
(19, 'Baloo', NULL, '86974', '991', 'L', 389, '-'),
(20, 'Piter Winers', NULL, '100338', 'A188', 'L', 240, '-'),
(21, 'Lula', NULL, '1891977', 'A344', 'L', 49, '-'),
(22, 'Karen', NULL, '1970258', 'A427', 'L', 32, '-'),
(23, 'Runa', '', '112361', 'A347', 'L', 26, 'GIII'),
(24, 'Chiruca', '', '1635759', '986', 'L', 57, 'GII'),
(25, 'Moss', '', '113891', 'A391', 'L', 409, 'GIII'),
(26, 'Nena', NULL, '1521753', '930', 'L', 289, '-'),
(27, 'Deby', 'BorderCollie', '101610', 'A147', 'L', 92, 'GIII'),
(28, 'Noah', '', '1887262', 'A268', 'L', 457, 'GIII'),
(29, 'Sil', NULL, '1831356', 'A150', 'L', 323, '-'),
(30, 'Furia', '', '1554907', '892', 'L', 369, 'GIII'),
(31, 'Mc Coy', 'Border Collie', '1905162', 'A322', 'L', 87, 'GIII'),
(32, 'Argi', '', '110120', 'A241', 'L', 319, 'GIII'),
(33, 'Lua', NULL, '118441', 'A327', 'L', 249, '-'),
(34, 'Zoe', '', '109748', 'A289', 'L', 337, 'GIII'),
(35, 'Juice', NULL, '117997', 'A387', 'L', 75, '-'),
(36, 'Vega', NULL, '1552296', '855', 'L', 32, '-'),
(37, 'Idris', NULL, '83909', '880', 'L', 359, '-'),
(38, 'Izar', NULL, '1596718', '851', 'L', 166, '-'),
(39, 'Pica', '', '104103', 'A091', 'L', 16, 'GIII'),
(40, 'Nana', '', '1780849', 'A073', 'L', 241, 'GIII'),
(41, 'Tara', NULL, 'No tiene', '1466', 'L', 375, '-'),
(42, 'Finn', '', '2074557', 'A596', 'L', 392, 'GIII'),
(43, 'Rocky', '', '1796496', 'A355', 'L', 69, 'GIII'),
(44, 'Neil', 'Flat Coated Retriever', '122117', 'A417', 'L', 192, '-'),
(45, 'Asia', 'Border Collie', '1958017', 'A364', 'L', 245, 'GIII'),
(46, 'Xodro', 'BorderCollie', '1959124', 'A371', 'L', 80, 'GIII'),
(47, 'Bec', NULL, '87712', '979', 'L', 250, '-'),
(48, 'Bely', NULL, '1370171', '692', 'L', 168, '-'),
(49, 'Laia', '', '1594320', '921', 'L', 195, 'GIII'),
(50, 'Bamba', '', '1887258', 'A242', 'L', 41, 'GII'),
(51, 'Akane', 'Euskal A Txakurra', '101429', 'A249', 'L', 176, 'GIII'),
(52, 'Mister', 'Border Collie', '99971', 'A250', 'L', 52, 'GII'),
(53, 'Spyro', NULL, '89457', 'A155', 'L', 49, '-'),
(54, 'Becho', NULL, '1831350', 'A203', 'L', 22, '-'),
(55, 'Luna', NULL, '1798021', 'A163', 'L', 456, '-'),
(56, 'Buh', '', '1929147', 'A297', 'L', 389, 'GIII'),
(57, 'Aby', 'Border Collie', '105806', 'A204', 'L', 410, 'GIII'),
(58, 'Yuma', 'Border Collie', '113067', 'A385', 'L', 307, 'GIII'),
(59, 'Zak', NULL, '1831579', 'A160', 'L', 378, '-'),
(60, 'Tanga', 'Border Collie', '119678', 'A366', 'L', 425, 'GIII'),
(61, 'Viconte', 'Mudi', '1561797', '813', 'L', 313, 'GII'),
(62, 'Dela', '', '2028765', 'A377', 'L', 311, 'GII'),
(63, 'Brus', NULL, '1763613', 'A140', 'L', 117, '-'),
(64, 'King', '', '1520008', '856', 'L', 353, 'GIII'),
(65, 'Brujostel', 'Border Collie', '1717447', 'A231', 'L', 465, 'GIII'),
(66, 'Kora', 'P.B.Malinoise', '111627', 'A332', 'L', 384, 'GII'),
(67, 'Rubia', NULL, '127474', 'A481', 'L', 312, '-'),
(68, 'Fito', '', '127473', 'A529', 'L', 312, 'GIII'),
(69, 'Visente', NULL, '116863', 'A359', 'L', 383, '-'),
(70, 'Maia', NULL, '1780846', 'A132', 'L', 66, '-'),
(71, 'Mecha', 'P.B.Malinoise', '129549', 'A558', 'L', 234, 'GIII'),
(72, 'Ari', NULL, '1893230', 'A301', 'L', 54, '-'),
(73, 'Aslan', '', '1970223', 'A457', 'L', 182, 'GIII'),
(74, 'Mambo', '', '1392048', '753', 'L', 126, 'GIII'),
(75, 'Nut', '', '120681', 'A430', 'L', 354, 'GII'),
(76, 'Xena', NULL, '1570666', 'A118', 'L', 252, '-'),
(77, 'Fiona', NULL, '2010068', 'A491', 'L', 393, '-'),
(78, 'Winner', 'Border Collie', '127815', 'A497', 'L', 407, 'GII'),
(79, 'Jade', 'skal A Txakurra', '120128', 'A522', 'L', 176, 'GIII'),
(80, 'Dasher', '', '2000291', 'A411', 'L', 273, 'GIII'),
(81, 'Gaston', NULL, '1717449', 'A340', 'L', 168, '-'),
(82, 'Pipo', NULL, '118271', 'A458', 'L', 395, '-'),
(83, 'Grey', NULL, '1717450', 'A066', 'L', 64, '-'),
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
(100, 'Fito', '', '1980971', 'A490', 'L', 435, 'GII'),
(101, 'Magic-Black', NULL, '101184', 'A318', 'L', 401, '-'),
(102, 'Blacky', NULL, 'No tiene', '1493', 'L', 330, '-'),
(103, 'Red Magic', 'Border Collie', '1834052', 'A124', 'L', 80, 'GII'),
(104, 'Beauty', '', '120728', 'A452', 'L', 380, 'GIII'),
(105, 'Savannah', NULL, '1627377', '833', 'L', 38, '-'),
(106, 'Ra', '', '2009048', 'A494', 'L', 323, 'GII'),
(107, 'Flay', NULL, '123186', 'A418', 'L', 179, '-'),
(108, 'Bu', NULL, '111416', 'A519', 'L', 397, '-'),
(109, 'Heidy', NULL, '1727593', 'A035', 'L', 430, '-'),
(110, 'Kiko', NULL, '1780843', 'A145', 'L', 146, '-'),
(111, 'Koba', NULL, '1533440', '843', 'L', 183, '-'),
(112, 'Kiwi', NULL, '1908098', 'A267', 'L', 183, '-'),
(113, 'Yun', 'Border Collie', '2001179', 'A484', 'L', 95, 'GII'),
(114, 'Nora', NULL, '80601', '735', 'L', 437, '-'),
(115, 'Liss', NULL, '1779665', 'A212', 'L', 117, '-'),
(116, 'Rayko', 'Border Collie', '2027590', 'A321', 'L', 381, 'GIII'),
(117, 'Nani', NULL, '1838888', 'A238', 'L', 286, '-'),
(118, 'Rasca', '', '2047380', 'A564', 'L', 16, 'GIII'),
(119, 'Abby', 'P. B. Groenendael', '2104382', 'A533', 'L', 215, 'GII'),
(120, 'Merlin', NULL, '1996593', 'A523', 'L', 121, '-'),
(121, 'Rusti', '', '1831356', 'A227', 'L', 164, 'GII'),
(122, 'Cora', '', 'No tiene', '1525', 'L', 48, 'GII'),
(123, 'Dux', '', '1727100', 'A134', 'L', 169, 'GIII'),
(124, 'Urko', NULL, '127390', 'A565', 'L', 79, '-'),
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
(147, 'Jayna', '', '1779666', 'A173', 'L', 235, 'GII'),
(148, 'Kira', '', '1710175', 'A116', 'L', 140, 'GIII'),
(149, 'Elfo', NULL, '1496681', '963', 'L', 260, '-'),
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
(162, 'Nube', NULL, '124447', 'A465', 'L', 248, '-'),
(163, 'Blue', '', '1863534', 'A552', 'L', 293, 'GII'),
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
(178, 'Troy', '', '98617', 'A044', 'L', 370, 'GII'),
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
(189, 'Rotten', NULL, 'No tiene', '1518', 'L', 243, '-'),
(190, 'Pixie Moon', '', '1879218', 'A260', 'L', 246, '-'),
(191, 'Timba', '', '119645', 'A403', 'L', 88, 'GIII'),
(192, 'Nuwa', '', '132677', 'A597', 'L', 443, 'GII'),
(193, 'Kora', NULL, '99230', 'A466', 'L', 221, '-'),
(194, 'Kona', NULL, '1986188', 'A524', 'L', 53, '-'),
(195, 'Arwen', NULL, '1753230', 'A151', 'L', 97, '-'),
(196, 'Blas', NULL, '1761207', 'A086', 'L', 254, '-'),
(197, 'Rudy', NULL, '102435', 'A113', 'L', 310, '-'),
(198, 'Ira', 'P.B.Malinoise', '101193', 'A141', 'L', 18, 'GII'),
(199, 'Terra', NULL, '114522', 'A328', 'L', 274, '-'),
(200, 'Kira', NULL, '120733', 'A405', 'L', 12, '-'),
(201, 'Nube', NULL, '97289', 'A515', 'L', 245, '-'),
(202, 'Mara', NULL, '80787', 'A396', 'L', 36, '-'),
(203, 'Trasto', NULL, 'No tiene', '1391', 'L', 6, '-'),
(204, 'Kiria', '', 'No tiene', 'A155', 'L', 231, 'GII'),
(205, 'Wind', 'Border Collie', '1881696', 'A421', 'L', 416, 'GII'),
(206, 'Rocky', NULL, '1756734', 'A156', 'L', 239, '-'),
(207, 'Pepo', NULL, '1756735', 'A200', 'L', 239, '-'),
(208, 'Mina', NULL, '124583', 'A501', 'L', 204, '-'),
(209, 'Gipsy', NULL, 'No tiene', '1542', 'L', 206, '-'),
(210, 'Atril', NULL, '1985153', 'A454', 'L', 334, '-'),
(211, 'Max', NULL, '1945286', 'A487', 'L', 43, '-'),
(212, 'N''Hug', '', '110043', 'A283', 'L', 238, 'GII'),
(213, 'Che', NULL, '1472786', 'A313', 'L', 285, '-'),
(214, 'Chincheta', NULL, '97802', 'A165', 'L', 439, '-'),
(215, 'Nemo', NULL, '1950998', 'A478', 'L', 142, '-'),
(216, 'Tao', NULL, '1807243', 'A554', 'L', 424, '-'),
(217, 'Luna', '', 'No tiene', '1539', 'L', 406, 'GII'),
(218, 'Molsa', NULL, '1710467', 'A103', 'L', 419, '-'),
(219, 'Dina', 'Border Collie', '1798019', 'A136', 'L', 208, 'GII'),
(220, 'Neo', 'Border Collie', '99972', 'A077', 'L', 58, 'GIII'),
(221, 'Zidane', NULL, '2081050', 'A591', 'L', 19, '-'),
(222, 'Poly', NULL, '2060633', 'A587', 'L', 246, '-'),
(223, 'Kinder', '', '101433', 'A223', 'L', 400, 'GII'),
(224, 'Juno', NULL, 'No tiene', '1552', 'L', 68, '-'),
(225, 'Flash', NULL, '109519', 'A420', 'L', 174, '-'),
(226, 'Troya', NULL, '127255', 'A527', 'L', 461, '-'),
(227, 'Pluto', NULL, '80602', '785', 'L', 328, '-'),
(228, 'Poli', NULL, '98654', 'A290', 'L', 437, '-'),
(229, 'Shasta', 'Border Collie', '109487', 'A272', 'L', 331, 'GII'),
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
(241, 'Danko', '', '113906', 'A325', 'M', 230, 'GIII'),
(242, 'Hanna', NULL, '1843070', 'A159', 'M', 369, '-'),
(243, 'Guti', 'Cocker Spaniel', '111666', 'A215', 'M', 210, 'GIII'),
(244, 'Cala', NULL, '1691264', 'A095', 'M', 15, '-'),
(245, 'Milo', NULL, '1399802', '718', 'M', 269, '-'),
(246, 'Gotika', NULL, '1682493', '971', 'M', 67, '-'),
(247, 'Drac', '', '106327', 'A196', 'M', 133, 'GIII'),
(248, 'Neo', NULL, '109522', 'A445', 'M', 174, '-'),
(249, 'Sra. Maruja', NULL, '1695088', '997', 'M', 144, '-'),
(250, 'Duna', '', '93084', '953', 'M', 161, 'GII'),
(251, 'Kiwi', '', '1982934', 'A525', 'M', 450, 'GIII'),
(252, 'Sucre', NULL, '1558711', '938', 'M', 366, '-'),
(253, 'Lass', 'Perro de Aguas Español', '131981', 'A580', 'M', 150, 'GIII'),
(254, 'Tuna', NULL, '1731780', 'A057', 'M', 319, '-'),
(255, 'Norai', NULL, '97039', 'A015', 'M', 154, '-'),
(256, 'Menta', '', '1459964', '767', 'M', 14, 'GIII'),
(257, 'Wirbel', 'Schnnauzer', '1252941', '588', 'M', 331, 'Ret.'),
(258, 'Pepsi', '', '1849505', 'A270', 'M', 76, 'GII'),
(259, 'Kiss', NULL, '1258632', '762', 'M', 106, '-'),
(260, 'Ina', NULL, 'No tiene', '1549', 'M', 446, '-'),
(261, 'Kenia', NULL, '86609', '954', 'M', 403, '-'),
(262, 'Gamma', '', '1988761', 'A307', 'M', 226, 'GIII'),
(263, 'Goku', 'Fox Terrier Wire ', 'No tiene', '1513', 'M', 266, 'GIII'),
(264, 'Coockie', NULL, '120745', 'A434', 'M', 445, '-'),
(265, 'Habana', NULL, '131393', 'A634', 'M', 147, '-'),
(266, 'Dau', NULL, '113713', 'A446', 'M', 366, '-'),
(267, 'Jolie', 'Mudi', '1561798', '808', 'M', 90, 'GII'),
(268, 'Boira', '', '1809307', 'A281', 'M', 100, 'GIII'),
(269, 'Queen', NULL, '93971', '989', 'M', 278, '-'),
(270, 'Legend', NULL, '1606337', '927', 'M', 440, '-'),
(271, 'Koku', NULL, '1394913', '714', 'M', 372, '-'),
(272, 'Luna', '', '103250', 'A240', 'M', 382, 'GII'),
(273, 'Nell', NULL, '1329279', 'A293', 'M', 152, '-'),
(274, 'Lucas', 'Podenco Andaluz', '112275', 'A248', 'M', 20, 'GII'),
(275, 'Benavis', NULL, '1803327', 'A195', 'M', 29, '-'),
(276, 'Paquita', NULL, '1852290', 'A229', 'M', 377, '-'),
(277, 'Fey', NULL, '2063400', 'A440', 'M', 442, '-'),
(278, 'Robbi', NULL, '104953', 'A146', 'M', 448, '-'),
(279, 'Mate', 'Schnauzer', '1930526', 'A469', 'M', 109, 'GII'),
(280, 'Veron', '', '1423605', 'A412', 'M', 152, 'GII'),
(281, 'Lume', NULL, '1985134', 'A416', 'M', 233, '-'),
(282, 'Foska', NULL, '108199', 'A182', 'M', 285, '-'),
(283, 'Bimba', NULL, '1968121', 'A451', 'M', 344, '-'),
(284, 'Chocolate', NULL, '93486', 'A045', 'M', 439, '-'),
(285, 'Tuna', 'Bodeguero Andaliuz', '122113', 'A408', 'M', 230, 'GII'),
(286, 'Naru', NULL, '132678', 'A598', 'M', 443, '-'),
(287, 'Mizar', NULL, '1876817', 'A274', 'M', 145, '-'),
(288, 'Tina', NULL, 'No tiene', '1532', 'M', 201, '-'),
(289, 'Kenya', NULL, '112354', 'A233', 'M', 294, '-'),
(290, 'Duque', NULL, 'No tiene', '1551', 'M', 29, '-'),
(291, 'Harley', NULL, '2025374', 'A428', 'M', 128, '-'),
(292, 'Johnny Cash', '', '2057033', 'A594', 'M', 456, 'GIII'),
(293, 'Striptease', '', '2023589', 'A577', 'M', 84, 'GII'),
(294, 'Tuco', NULL, '2026054', 'A419', 'M', 185, '-'),
(295, 'Mia', NULL, '121232', 'A547', 'M', 340, '-'),
(296, 'Gunilla', NULL, 'No tiene', '1471', 'M', 315, '-'),
(297, 'Alma', NULL, '90313', 'A566', 'M', 301, '-'),
(298, 'Noa', '', '1926077', 'A526', 'M', 391, 'GII'),
(299, 'Magia', '', '2032179', 'A528', 'S', 86, 'GIII'),
(300, 'Saroa', '', '1789456', 'A149', 'S', 464, 'GIII'),
(301, 'Melendi', '', '1842276', 'A164', 'S', 311, 'GIII'),
(302, 'Mims', NULL, '102903', 'A279', 'S', 10, '-'),
(303, 'Hancock', NULL, '131702', 'A609', 'S', 349, '-'),
(304, 'Tris', NULL, '119441', 'A398', 'S', 411, '-'),
(305, 'Lula', NULL, '123192', 'A607', 'S', 118, '-'),
(306, 'Rufo', NULL, '123178', 'A435', 'S', 257, '-'),
(307, 'Sira', '', '106345', 'A168', 'S', 112, 'GIII'),
(308, 'Nit', '', '112007', 'A208', 'S', 357, 'GIII'),
(309, 'Xira', '', '124731', 'A424', 'S', 432, 'GIII'),
(310, 'Che Guevara', 'Caniche', '112448', 'A230', 'S', 465, 'GIII'),
(311, 'Enzo', NULL, '117909', 'A444', 'S', 352, '-'),
(312, 'Nuca', 'Tibetan Spaniel', '109471', 'A181', 'S', 197, 'GIII'),
(313, 'Gismo', NULL, '123726', 'A449', 'S', 441, '-'),
(314, 'Nana', NULL, '103211', 'A277', 'S', 424, '-'),
(315, 'Nikita', '', '123006', 'A443', 'S', 33, 'GIII'),
(316, 'Xena', '', '125524', 'A509', 'S', 138, 'GIII'),
(317, 'Chula', '', '106335', 'A135', 'S', 193, 'GIII'),
(318, 'Dagga', NULL, '127338', 'A507', 'S', 181, '-'),
(319, 'Greta', NULL, '1849716', 'A337', 'S', 72, '-'),
(320, 'Bengel', 'Schnnauzer', '1433208', '760', 'S', 331, 'GII'),
(321, 'Nei', NULL, '1400011', '770', 'S', 191, '-'),
(322, 'Tess', NULL, '102439', 'A245', 'S', 203, '-'),
(323, 'Lia', 'Mestizo', '132000', 'A588', 'S', 188, 'GII'),
(324, 'Taca', '', '128455', 'A589', 'S', 37, 'GII'),
(325, 'Miche', '', '1706141', 'A097', 'S', 321, 'GII'),
(326, 'Manin', NULL, '104120', 'A100', 'S', 411, '-'),
(327, 'Doña Matilde', '', '2005359', 'A514', 'S', 139, 'GIII'),
(328, 'Aqua', NULL, '2056249', 'A513', 'S', 295, '-'),
(329, 'Nuca', 'Schnauzer', '1678476', 'A088', 'S', 2, 'GII'),
(330, 'Pepa', 'Jack Rusell', '1957259', 'A393', 'S', 160, 'GII'),
(331, 'Spyro', '', '131527', 'A569', 'S', 373, 'GIII'),
(332, 'Della', NULL, '2061744', 'A467', 'S', 106, '-'),
(333, 'Lua', NULL, '116884', 'A287', 'S', 267, '-'),
(334, 'Mayo', NULL, 'No tiene', '1419', 'S', 364, '-'),
(335, 'Nala', NULL, '108635', 'A530', 'S', 113, '-'),
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
(351, 'Flai', 'Fox Terrier Wire', '0129738', 'en tramite', 'M', 264, 'GII'),
(352, 'Donna', 'Border Collie', '', '', 'L', 405, 'GII'),
(353, 'fito', 'mestizo', '', '', 'L', 387, 'GIII'),
(354, 'paco', '', '', '', '-', 1, '-'),
(355, 'Akela', 'Border Collie', '', 'A746', 'L', 155, 'GII'),
(356, 'Toska', 'Border Collie', '', '', 'L', 220, 'GI'),
(357, 'Sira', 'P.B.Malinoise', '', 'A584', 'L', 224, 'GII'),
(358, 'Duna', 'P. Aleman', '', 'A586', 'L', 455, 'GII'),
(359, 'Olivia', 'Schnauzer', '', '', 'S', 287, 'GII'),
(360, 'Kyle', 'Schnauzer', '', 'A-539', 'M', 198, 'GII'),
(361, 'Kara', 'Border Collie', '', 'A-541', 'L', 399, 'GII'),
(362, 'Tibet', 'Border Collie', '', '', 'L', 59, 'GII'),
(363, 'Beltxa', 'Schnauzer', '', 'A622', 'S', 432, 'GII'),
(364, 'Yeni', 'Border Collie', '', '', 'L', 245, 'GI'),
(365, 'Danah', 'P. Australiano', '', '', 'L', 280, 'GI'),
(366, 'Net', 'P. Australiano', '', '', 'L', 192, 'GI'),
(367, 'Sira', 'Boxer', '', '', 'L', 111, 'GI'),
(368, 'Maggie', 'Mestizo', '', '1570', 'S', 402, 'GII'),
(369, 'Kiss', '', '', '', 'L', 202, 'GI'),
(370, 'Milka', '', '', '', 'L', 207, 'GI'),
(371, 'Mitzy', '', '', '', 'L', 47, 'GI'),
(372, 'Sura', '', '', '', 'L', 335, 'GI'),
(374, 'Neo', '', '', '', 'L', 220, 'GII'),
(375, 'Sitan', '', '', '795', 'L', 224, 'GII'),
(376, 'Kaiser', '', '', 'A383', 'S', 202, 'GII'),
(377, 'Geha', '', '', 'A162', 'L', 308, 'GIII'),
(378, 'Momo', 'Borde Collie', '', 'A391', 'L', 409, 'GII'),
(379, 'Skay', '', '', '', 'L', 212, 'GII'),
(380, 'Keko', '', '', '', 'L', 17, 'GI'),
(381, 'Mambo', 'Border Collie', '', '', 'L', 39, 'GI'),
(382, 'Swing', '', '', '', 'L', 371, 'GI'),
(383, 'Trufa', '', '', '', 'L', 415, 'GI'),
(384, 'Soma', 'Border Collie', '', 'A696', 'L', 343, 'GII'),
(385, 'Phoebe', 'Border Collie', '', 'A555', 'M', 148, 'GII'),
(386, 'Izzie', 'West Higland White Terrier', '', 'A275', 'S', 148, 'GII'),
(387, 'Kyra', 'Border Collie', '', '1607', 'L', 44, 'GII'),
(388, 'Nika', 'Border Collie', '', '', 'L', 74, 'GI'),
(389, 'Dolce', 'Border Collie', '', 'A681', 'L', 44, 'GII'),
(390, 'Dolce', '', '', 'A723', 'L', 361, 'GII'),
(391, 'Gilda', 'Border Collie', '', 'A723', 'L', 361, 'GII'),
(392, 'Noa', 'Border Collie', '', 'A540', 'L', 384, 'GII'),
(393, 'Thor', 'Border Collie', '', '', 'L', 270, 'GI'),
(394, 'Akira Haru', 'BorderCollie', '', 'A311', 'L', 172, 'GII'),
(395, 'Nala', 'BorderCollie', '', 'A142', 'L', 141, 'GIII'),
(396, 'Peka', '', '', 'A615', 'L', 259, 'GII'),
(397, 'Milady', '', '', '', 'L', 407, 'GII'),
(398, 'Yashi', 'Pastor de los Pirineos', '', '', 'M', 453, 'GI'),
(399, 'Dollar', '', '', '911', 'L', 309, 'GII'),
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
(412, 'Brea', 'Mestizo', '', '', 'L', 31, 'GI'),
(413, 'Lola', 'Jack Rusell', '', 'A633', 'S', 160, 'GII'),
(414, 'Yeny', 'Border Collie', '', 'A747', 'L', 245, 'GII'),
(415, 'Sonic', 'Perro de Aguas Español', '', 'A495', 'L', 98, 'GII'),
(416, 'Wind', 'Border Collie', '', 'A561', 'L', 427, 'GII'),
(417, 'Crack', 'Border Collie', '', 'A719', 'L', 374, 'GII'),
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
(434, 'Dafne', '', '', '', 'L', 272, 'GI'),
(435, 'Nut', '', '', '', 'L', 421, 'GI'),
(436, 'Morgan', '', '', '', 'L', 133, 'GI'),
(437, 'Juke', '', '', '', 'L', 336, 'GI'),
(438, 'Argi', '', '', '', 'L', 149, 'GI'),
(439, 'Pi', '', '', '', 'L', 169, 'GI'),
(440, 'Horatio', '', '', '', 'S', 73, 'GI'),
(441, 'Goldie', '', '', '', 'S', 379, 'GI'),
(442, 'Cachirulo', '', '', 'A578', 'L', 116, 'GII'),
(443, 'Lucky', '', '', '1561', 'L', 23, 'GII'),
(444, 'Chika', '', '', 'A518', 'L', 291, 'GII'),
(445, 'Inka', '', '', 'A699', 'L', 100, 'GII'),
(446, 'Nupsi', '', '', '', 'L', 75, 'GII'),
(447, 'Inka', '', '', 'A381', 'L', 386, 'GII'),
(448, 'Tiri', '', '', 'A782', 'L', 241, 'GII'),
(449, 'Itoitz', '', '', 'A713', 'L', 13, 'GII'),
(450, 'Xira', '', '', '1499', 'L', 5, 'GII'),
(451, 'Gala', '', '', 'A500', 'L', 26, 'GII'),
(452, 'Luna', '', '', '', 'L', 50, 'GII'),
(453, 'Lorenzo', '', '', 'A666', 'L', 389, 'GII'),
(454, 'Bombon', '', '', 'A773', 'L', 51, 'GII'),
(455, 'Lluna', '', '', '1537', 'L', 51, 'GII'),
(456, 'Ardi de Rioja', '', '', '0729', 'L', 228, 'GII'),
(457, 'Danko', '', '', '1509', 'L', 110, 'GII'),
(458, 'Tessa', '', '', 'A317', 'L', 433, 'GII'),
(459, 'Rex', '', '', '1578', 'L', 438, 'GII'),
(460, 'Chicaa', '', '', 'A703', 'L', 170, 'GII'),
(461, 'Sue', '', '', 'A788', 'L', 435, 'GII'),
(462, 'Kira', '', '', '', 'L', 93, 'GII'),
(463, 'Heidy', '', '', 'A496', 'L', 282, 'GII'),
(464, 'Bella', '', '', '1586', 'L', 305, 'GII'),
(465, 'Hanna', '', '', '1584', 'L', 11, 'GII'),
(466, 'Eo', '', '', 'A768', 'L', 323, 'GII'),
(467, 'Panda', '', '', '', 'L', 296, 'GII'),
(468, 'Venus', '', '', 'A743', 'L', 311, 'GII'),
(469, 'Charly', '', '', '1592', 'L', 232, 'GII'),
(470, 'Argi', '', '', '1600', 'L', 186, 'GII'),
(471, 'Broto', '', '', 'A764', 'L', 431, 'GII'),
(472, 'Anouk', '', '', 'A480', 'L', 434, 'GII'),
(473, 'Flecha', '', '', 'A748', 'L', 423, 'GII'),
(474, 'Heidi', '', '', 'A035', 'L', 430, 'GII'),
(475, 'Moa', '', '', 'A508', 'L', 164, 'GII'),
(476, 'Greta', '', '', '', 'L', 229, 'GII'),
(477, 'Lur', '', '', 'A741', 'L', 178, 'GII'),
(478, 'Charli', '', '', 'A655', 'L', 63, 'GII'),
(479, 'Lia', '', '', '1563', 'L', 304, 'GII'),
(480, 'Nika', '', '', '1590', 'S', 157, 'GII'),
(481, 'Erinka', '', '', 'A761', 'S', 370, 'GII'),
(482, 'Ursus', '', '', '', 'S', 184, 'GII'),
(483, 'Ela', '', '', 'A618', 'S', 363, 'GII'),
(484, 'Lua', '', '', 'A763', 'S', 61, 'GII'),
(485, 'Wembley', '', '', 'A769', 'S', 76, 'GII'),
(486, 'Rikke', '', '', '', 'S', 62, 'GII'),
(487, 'Imo', '', '', 'A631', 'M', 229, 'GII'),
(488, 'Alpargata', '', '', 'A606', 'M', 152, 'GII'),
(489, 'Lucky Luque', '', '', 'A766', 'S', 195, 'GII'),
(490, 'Salma', '', '', 'A521', 'S', 222, 'GII'),
(491, 'Dracma', '', '', 'A626', 'S', 333, 'GII'),
(492, 'Jade', '', '', 'A629', 'L', 225, 'GIII'),
(493, 'Rinoa', '', '', 'A447', 'L', 325, 'GIII'),
(494, 'Chus', '', '', '934', 'L', 225, 'GIII'),
(495, 'Gala', '', '', 'A674', 'L', 127, 'GIII'),
(496, 'Chamán', '', '', 'A262', 'L', 386, 'GIII'),
(497, 'Koira', '', '', 'A252', 'L', 326, 'GIII'),
(498, 'Kobu', '', '', '1564', 'M', 187, 'GIII'),
(499, 'Zeus', '', '', 'A139', 'M', 325, 'GIII'),
(500, 'Gea', '', '', 'A175', 'M', 268, 'GIII'),
(501, 'Sacha', '', '', 'A632', 'M', 167, 'GIII'),
(502, 'Fran', '', '', 'A028', 'M', 116, 'GIII'),
(503, 'Peka', '', '', 'A291', 'M', 291, 'GIII'),
(504, 'Time', '', '', 'A571', 'M', 416, 'GIII'),
(505, 'Alma', '', '', '874', 'M', 136, 'GIII'),
(506, 'Kira', '', '', 'A600', 'S', 30, 'GIII'),
(507, 'Cleo', '', '', 'A152', 'S', 85, 'GIII'),
(508, 'Luna', '', '', '0956', 'S', 85, 'GIII'),
(509, 'Nika', '', '', 'A082', 'S', 325, 'GII'),
(510, 'Quenn', 'Palleiro', '', '', 'M', 47, 'GI'),
(511, 'Gala', 'Pastor Alemán', '', '', 'L', 40, 'GI'),
(512, 'Moli', 'Jack Rusell', '', '', 'S', 194, 'GI'),
(513, 'Vali', 'Border Collie', '', '', 'L', 9, 'GI'),
(514, 'Isis', 'Border Collie', '', '', 'L', 57, 'GI'),
(515, 'Darco', 'Beagle', '', '', 'M', 46, 'GI'),
(516, 'Bruce', 'Ratonero Bodeguero Andaluz', '', '', 'M', 230, 'GI'),
(517, 'Nut', 'Pastor Belga Malinoise', '', '', 'L', 162, 'GI'),
(518, 'Lluna', 'Border Collie', '', '', 'L', 459, 'GI'),
(519, 'Dunah', 'Cocker Spaniel', '', 'A635', 'M', 28, 'GII'),
(520, 'Aska', 'Border Collie', '', '', 'L', 306, 'GI'),
(521, 'Buck', 'Border Collie', '', '', 'L', 81, 'GI'),
(522, 'Merche', 'Schnauzer Gigante', '', '', 'L', 303, 'GI'),
(523, 'Bambú', 'Border Collie', '', '', 'L', 217, 'GI'),
(524, 'King', '', '', 'A683', 'S', 263, 'GII'),
(525, 'Toxo', '', '', 'A677', 'S', 277, 'GII'),
(526, 'Newton', '', '', '', 'L', 137, 'GI'),
(527, 'Cala', '', '', 'A697', 'L', 293, 'GII'),
(528, 'Onna', '', '', '', 'L', 356, 'GII'),
(529, 'Trasgo', '', '', 'A650', 'L', 171, 'GII'),
(530, 'Samba', '', '', 'A702', 'M', 385, 'GII'),
(531, 'Charlie', '', '', 'A532', 'S', 385, 'GIII'),
(532, 'Alan', '', '', '', 'L', 190, 'GI'),
(533, 'Moly', '', '', '', 'L', 70, 'GI'),
(534, 'Pipa', '', '', 'A718', 'S', 297, 'GII'),
(535, 'Chola', '', '', 'A691', 'M', 329, 'GII'),
(536, 'Blue', '', '', '', 'S', 21, 'GI'),
(537, 'Fly', '', '', '', 'L', 180, 'GII'),
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
(554, 'Phoebe', '', '', 'A690', 'L', 196, 'GII'),
(555, 'Sella', '', '', '', 'L', 94, 'GII'),
(556, 'botinera', '', '', '', 'L', 466, 'GI'),
(558, 'Rumba', '', '', '', 'L', 467, 'GI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Provincias`
--
-- Creación: 12-07-2014 a las 09:16:36
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
-- Creación: 12-07-2014 a las 09:16:36
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- RELACIONES PARA LA TABLA `Pruebas`:
--   `Club`
--       `Clubes` -> `ID`
--

--
-- Volcado de datos para la tabla `Pruebas`
--

INSERT INTO `Pruebas` (`ID`, `Nombre`, `Club`, `Ubicacion`, `Triptico`, `Cartel`, `Observaciones`, `Cerrada`) VALUES
(1, '-- Sin asignar --', 1, NULL, NULL, NULL, 'NO BORRAR: Prueba por defecto para jornadas huerfanas', 1),
(2, 'Prueba de prueba', 79, 'Instalaciones del club', '', '', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Resultados`
--
-- Creación: 12-07-2014 a las 09:16:37
--

DROP TABLE IF EXISTS `Resultados`;
CREATE TABLE IF NOT EXISTS `Resultados` (
  `Manga` int(4) NOT NULL,
  `Dorsal` int(4) NOT NULL,
  `Perro` int(4) NOT NULL,
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
  PRIMARY KEY (`Manga`,`Perro`),
  KEY `Resultados_Perro` (`Perro`),
  KEY `Resultados_Dorsal` (`Dorsal`),
  KEY `Resultados_Manga` (`Manga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `Resultados`:
--   `Perro`
--       `Perros` -> `ID`
--   `Manga`
--       `Mangas` -> `ID`
--   `Dorsal`
--       `Inscripciones` -> `Dorsal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tipo_Manga`
--
-- Creación: 12-07-2014 a las 09:16:37
--

DROP TABLE IF EXISTS `Tipo_Manga`;
CREATE TABLE IF NOT EXISTS `Tipo_Manga` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) DEFAULT NULL,
  `Grado` varchar(16) NOT NULL DEFAULT '-',
  PRIMARY KEY (`ID`),
  KEY `Descripcion` (`Descripcion`),
  KEY `Grado` (`Grado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17;
--
-- RELACIONES PARA LA TABLA `Tipo_Manga`:
--   `Grado`
--       `Grados_Perro` -> `Grado`
--

--
-- Volcado de datos para la tabla `Tipo_Manga`
--

INSERT INTO `Tipo_Manga` (`ID`, `Descripcion`, `Grado`) VALUES
(1, 'Manga sin tipo definido', '-'),
(2, 'Ronda de Pre-Agility', 'P.A.'),
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
(16, 'Ronda de Exhibición', '-');

-- --------------------------------------------------------

--
-- Estructura para la vista `PerroGuiaClub`
--
DROP TABLE IF EXISTS `PerroGuiaClub`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `PerroGuiaClub` AS select `Perros`.`ID` AS `ID`,`Perros`.`Nombre` AS `Nombre`,`Perros`.`Raza` AS `Raza`,`Perros`.`Licencia` AS `Licencia`,`Perros`.`LOE_RRC` AS `LOE_RRC`,`Perros`.`Categoria` AS `Categoria`,`Perros`.`Grado` AS `Grado`,`Perros`.`Guia` AS `Guia`,`Guias`.`Nombre` AS `NombreGuia`,`Guias`.`Club` AS `Club`,`Clubes`.`Nombre` AS `NombreClub` from ((`Perros` join `Guias`) join `Clubes`) where ((`Perros`.`Guia` = `Guias`.`ID`) and (`Guias`.`Club` = `Clubes`.`ID`)) order by `Clubes`.`Nombre`,`Perros`.`Categoria`,`Perros`.`Nombre`;

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
  ADD CONSTRAINT `Mangas_ibfk_1` FOREIGN KEY (`Tipo`) REFERENCES `Tipo_Manga` (`ID`) ON UPDATE CASCADE,
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
  ADD CONSTRAINT `Resultados_ibfk_1` FOREIGN KEY (`Perro`) REFERENCES `Perros` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `Resultados`
  ADD CONSTRAINT `Resultados_ibfk_2` FOREIGN KEY (`Manga`) REFERENCES `Mangas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Tipo_Manga`
--
ALTER TABLE `Tipo_Manga`
  ADD CONSTRAINT `Tipo_Manga_ibfk_1` FOREIGN KEY (`Grado`) REFERENCES `Grados_Perro` (`Grado`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
