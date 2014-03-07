-- phpMyAdmin SQL Dump
-- version 4.1.8deb0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 07-03-2014 a las 11:59:03
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
CREATE DATABASE IF NOT EXISTS `agility` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `agility`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Categorias_Perro`
--
-- Creación: 24-02-2014 a las 07:55:20
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
-- Creación: 06-03-2014 a las 10:37:49
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
  `Logo` varchar(255) DEFAULT 'rsce.png',
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
('-- Sin asignar --', '', '', 'Madrid', '', '', '', '', '', '', '', '', '', 'rsce.png', 'NO BORRAR ESTA ENTRADA. SE USARA PARA AQUELLOS GUIAS QUE NO TENGAN CLUB ASIGNADO', 0),
('AA Y CIA', '28609 Sevilla La Nueva (Madrid)', '', 'Madrid', '+ 34 619 29 03 98', '', '', '', '', 'arribas.anabel@gmail.com', '', '', '', 'aaycia.png', '', 0),
('ACADE', 'Salvadas, 41, 2º C', '15705 Santiago de Compostela', 'Coruña, A', '+ 34 620 29 58 31', '+ 34 881 93 95 5', '', '', 'http://www.asociacionacade.com/', 'asociacioncansdeportistas@gmail.com', '', '', '', 'acade.png', '', 0),
('Agilcan', 'Paseo de los Olivos 10', '28330 San Martin de la Vega', 'Madrid', '634 417 893', '918 946 096', '659 146 878', NULL, 'http://www.agilcan.es/', 'info@agilcan.es', NULL, NULL, NULL, 'agilcan.png', NULL, 0),
('Alhambra', 'Urano, 24', '18200 Maracena (Granada)', 'Granada', ' + 34 958 42 12 85 ', '', '', '', '', 'agilityalhambra@hotmail.com', '', '', '', 'alhambra.png', '', 0),
('Askizu', 'Caserio Benta - Barrio Askizu', '20808 Getaria (Guipúzcoa)', 'Gipuzkoa/Guipúzcoa', '+ 34 656 76 60 65', '', '', '', 'http://www.agilityaskizu.com/', 'antonio@agilityaskizu.com', '', '', '', 'askizu.png', '', 0),
('Badalona', 'Camí del Xiprers, s/n', '08916 Badalona (Barcelona)', 'Barcelona', ' + 34 93 597 23 53 ', ' + 34 676 48 99 40 ', '', '', 'http://www.agilitybadalona.con/', 'info@agilitybadalona.com', '', '', '', 'badalona.png', '', 0),
('Baix Llobregat', 'Enric Borras, 10', '08820 El Prat de Llobregat (Barcelona)', 'Barcelona', '+34 695 79 42 74', '', '', '', 'http://www.agilitybaixllobregat.com/', 'agilitybaixllobregat@hotmail.com', '', '', '', 'baix_llobregat.png', '', 0),
('Campo de Gibraltar', 'Arbol de la Paz, 4', '11205 Algeciras (Cádiz)', 'Cádiz', ' + 34 647 80 32 64 ', '', '', '', '', 'villa-zahara@hotmail.com', '', '', '', 'campo_de_gibraltar.png', '', 0),
('Camu', 'Párraco Pedro Lozano, 26', '30007 Zarandona (Murcia)', 'Murcia', '+ 34 636 25 19 39', '', '', '', '', 'clubagilitymurcia@hotmail.com', '', '', '', 'camu.png', '', 0),
('Can Natura', 'Peñella Baja, 1', '33310 Cabranes (Asturias)', 'Asturias', '+ 34 696 86 08 63', '', '', '', 'http://www.cannatura.net/', 'cannatura@hotmail.com', '', '', '', 'cannatura.png', '', 0),
('Canic', 'Sant Pere, 10', '08392 Llavaneres (Barcelona)', 'Barcelona', '+ 34 93 792 76 55', '', '', '', 'http://www.agilitycanic.cat/', 'info@agilitycanic.cat', '', '', '', 'canic.png', '', 0),
('Canino Algecireño', 'Los Naranjos, 8', '11380 Tarifa (Cádiz)', 'Cádiz', ' + 34 663 55 86 42 ', '', '', '', '', 'arquikm@gmail.com', '', '', '', 'canino_algecireno.png', '', 0),
('Castellón', 'Mar Cantábrico, 2, 1º C', '12100 Grao de Castellón (Castellón)', 'Castellón/Castelló', '+ 34 964 28 61 52', '+ 34 625 82 25 35', '', '', 'http://www.agilitycastellon.es/', 'agilitycastellon@gmx.es', '', '', '', 'castellon.png', '', 0),
('Cinco Huesos', 'Paseo de los Pozos, Km. 1,2', '28813 Torres de la Alameda', 'Madrid', '+ 34 91 832 83 00 ', '+ 34 691 77 75 24 ', '', '', 'http://www.cincohuesos.com/', 'cincohuesos@gmail.com', '', '', '', 'cinco_huesos.png', 'Antiguo club "Apata"', 0),
('Ciudad de Antequera', '', '', 'Málaga', '', '', '', '', '', '', '', '', '', 'ciudad_de_antequera.png', '', 1),
('Ciutat Comtal', 'Escultor Llimona, 38-40, Entr. 2ª', '08031 Barcelona', 'Barcelona', ' + 34 645 85 10 06 ', '', '', '', 'http://www.agilitybarcelona.com/', 'info@agilitybarcelona.com', '', '', '', 'ciutat_comtal.png', '', 0),
('Ciutat de Valencia', 'Sequia de Calvera, 33, Bajo', '46910 Sedaví (Valencia)', 'Valencia/Valéncia', '', '', '', '', 'http://www.bichopeludo.com/ciutat_de_valencia.html', 'ciutatdevalencia@bichopeludo.com', '', '', '', 'ciutat_de_valencia.png', '', 0),
('Clotet', 'Apdo. de correos 517', '12500 Vinaroz (Castellón)', 'Castellón/Castelló', '+ 34 687 52 38 11', '', '', '', 'http://www.degarrof.com/', 'declotet@hotmail.com', '', '', '', 'clotet.png', '', 0),
('Cornella', 'Mn. Andreu, 13-19', '08940 Cornellà de Llobregat (Barcelona)', 'Barcelona', '+ 34 638 98 75 91', '', '', '', 'http://www.agilitycornella.com/', 'info@agilitycornella.com', '', '', '', 'cornella.png', '', 0),
('Correcan', ' Lóndrés, 58, 1º D', '28850 Torrejón de Ardoz (Madrid)', 'Madrid', '+ 34 668 86 76 81', '', '', '', 'http://www.correcan.es/', 'info@correcan.es', '', '', '', 'correcan.png', '', 0),
('Costa Azahar', 'Camino Caminas, 223 - Apdo. de correos 717', '12080 Castellón', 'Castellón/Castelló', '+ 34 964 76 60 83', '', '', '', 'http://www.mediterraniacentrocanino.com/', 'info@mediterraniacentrocanino.com', '', '', '', 'costa_azahar.png', '', 0),
('Costa Blanca', 'C/ Baltasar Gracián Nº3, Urb. Montecid', '03670 Monforte del Cid ', 'Alicante/Alacant', NULL, NULL, NULL, NULL, 'http://www.agilitycostablanca.com/', 'agility@agilitycostablanca.com', NULL, NULL, NULL, 'costa_blanca.png', NULL, 0),
('Cousadecans', 'Lugar de Foxo, s/n - San Vicente de Vigo', '15175 Carral (La Coruña)', 'Coruña, A', '+ 34 652 83 28 34', '+ 34 620 67 61 40', '', '', '', 'cousadecans@gmail.com', '', '', '', 'cousadecans.png', '', 0),
('Cuatro Patas', 'Club social Urb/ El Socorro', 'Carmona', 'Sevilla', '630 52 72 42 (Isaac) ', '615 45 58 78 (Rafa)', NULL, 'N 37.43865 - W 5.79858', NULL, 'agiltiy4patas@hotmail.com', NULL, NULL, NULL, 'cuatro_patas.png', NULL, 0),
('Cubas', 'Paseo de los Cipreses s/n', 'Cubas de la Sagra', 'Madrid', '918143395', '619 56 43 49', NULL, NULL, 'http://clubagilitycubas.es/', 'clubagilitycubas@terra.com', NULL, NULL, NULL, 'cubas.png', NULL, 0),
('Deporcan', 'Jazmín, 16, Entreplanta', '28033 Madrid', 'Madrid', '629 843 681', ' + 34 91 302 44 35', '', '40.32132, -3.41895', 'http://www.clubagilityboadilla.org/', 'agility.deporcan@gmail.com', '', '', '', 'deporcan.png', 'Antiguo "Club Boadilla"', 0),
('Depordog', 'Avd del Mueble s/n', '11130 Chiclana', 'Cádiz', '652 73 45 17', NULL, NULL, NULL, 'http://www.clubagilitydepordog.es/', 'ildegolo@hotmail.com', NULL, NULL, NULL, 'depordog.png', NULL, 0),
('Educan', 'Mester de Juglaría, 20', '28978 Cubas de la Sagra (Madrid)', 'Madrid', '617 469 312', '+ 34 676 67 76 38', '', '', 'http://www.madrid.educan.es/', 'agility.madrid@educan.es', '', '', '', 'educan.png', '', 0),
('El Hechizo del Border C.', 'Ctra. Monserrat, Km. 7''5, nº 26', '46900 Torrent ', 'Valencia/Valéncia', '+ 34 96 156 56 75', NULL, NULL, NULL, 'http://www.elhechizo.com/', 'elhechizobc@gmail.com', NULL, NULL, NULL, 'el_hechizo_del_border_collie.png', NULL, 0),
('El Nogueral', 'Cami del Camp, 23', '03460 Beneixama', 'Alicante/Alacant', '+ 34 695 45 23 69', NULL, NULL, NULL, 'http://www.clubagility.es/', 'info@clubagility.es', NULL, NULL, NULL, 'el_nogueral.png', NULL, 0),
('El Tramusser Benifaio', 'Polígono 16 - Cami Prefasic', '46450 Benifaió (Valencia)', 'Valencia/Valéncia', '+ 34 678 57 47 86', '', '', '', 'http://www.escuelacaninavalencia.com/', 'madamagility@hotmail.com', '', '', '', 'el_tramusser_benifaio.png', '', 0),
('Els Dimonis de Bascara', 'Apartado de correos 241', '17600 Figueres (Gerona)', 'Girona/Gerona', ' + 34 657 20 44 81', '', '', '', 'http://www.dimonisdebascara.cat/', 'dimonisdebascara@hotmail.es', '', '', '', 'els_dimonis_de_bascara.png', '', 0),
('Eslón', 'Carretera de Carranque s/n', 'Serranillos del Valle', 'Madrid', '657 209 274', '', '', '', 'http://www.agilityeslon.com', 'info@agilityeslon.com', '', '', '', 'eslon.png', '', 0),
('Euskadi', 'CLUB DE AGILITY EUSKADI Beike Bidea, 2, 2º Dcha', '48950 Asua - Erandio (Vizcaya)', 'Bizkaia/Vizcaya', '619 423 720 - Pedro Martinez', '', '', '', ' www.agilityeuskadi.com', 'info@agilityeuskadi.com', '', '', '', 'euskadi.png', '', 0),
('Hoop Agility', 'Alberto Conti, 8, 7º C', '28935 Móstoles (Madrid)', 'Madrid', '+ 34 635 65 78 42', '', '', '', 'http://www.agilityclub.es/', 'info@agilityclub.es', '', '', '', 'hoop.png', '', 0),
('Illa Blanca', 'Washington, 18, 2º', '07820 San Antonio de Portmany (Ibiza)', 'Balears, Illes', '+ 34 672 32 39 22', NULL, NULL, NULL, 'http://www.agilityillablanca.com/', 'info@agilityillablanca.com', NULL, NULL, NULL, 'illa_blanca.png', NULL, 0),
('Indog Maria de Huerva', 'Calle Orfeón 13  Nave A', '50410  Cuarte de Huerva', 'Zaragoza', '', '', '', '', 'http://www.agilityindog.com/', 'info@agilityindog.com', '', '', '', 'indog.png', '', 0),
('Junior', 'Calle de la Fuente, nº 8', '16162-Villar del Horno', 'Cuenca', '626389032', NULL, NULL, NULL, 'http://www.agilityjunior.es/', 'agilityjunior@gmail.com, info@agilityjunior.es', NULL, NULL, NULL, 'junior.png', NULL, 0),
('Kai Argi', 'Oiartzun, 6, Entlo. B', '20110 Pasaia San Pedro (Guipúzcoa)', 'Gipuzkoa/Guipúzcoa', '+ 34 656 71 51 31', '', '', '', 'http://www.kaiargi.com', 'kaiargi@kaiargi.com', '', '', '', 'kai_argi.png', '', 0),
('L''Almozara', 'Camino de Pinseque, 147-A', '50190 Garrapinillos (Zaragoza)', 'Zaragoza', ' + 34 637 54 15 86', '', '', '', '', '', '', '', '', 'almozara.png', '', 0),
('L''Horta Nord', 'Vora Vía, 2', '46132 Almassera (Valencia)', 'Valencia/Valéncia', '+ 34 651 30 41 47', '', '', '', 'http://clubagilitylhortanord.blogspot.com.es/', '', 'https://www.facebook.com/agility.lhortanordvalencia', '', '', 'horta_nord.png', '', 0),
('La Daga', '25230 Mollerussa (Lérida)', '', 'Lleida/Lérida', ' + 34 660 72 04 90 ', '', '', '', 'http://www.clubagilityladaga.blogspot.com/', 'niisia84@hotmail.com', '', '', '', 'la_daga.png', '', 0),
('La Dama', 'Partida de las Casicas, 5', '03330 Crevillente (Alicante) – España', 'Alicante/Alacant', ' +34 622 109 409', '', '', ' 38° 10′ 44″ N – 0° 48′ 30″ W”', 'http://www.agilityladama.com/ladama/', 'agilityladama@gmail.com', '', '', '', 'la_dama.png', '', 0),
('La Manada', 'Partida Calvet, 37, Bajo', '46120 Alboraia (Valencia)', 'Valencia/Valéncia', '+ 34 659 89 78 40 (Vicent)', '', '', '39° 29'' 47.55'''' N - 0° 20'' 37.1'''' W', 'http://www.la-manada.org/agility/', 'info@la-manada.org', '', '', '', 'la_manada.png', '', 0),
('La Palma', 'Paraje Los Pérez de Arriba', '30593 La Palma - Cartagena (Murcia)', 'Murcia', ' + 34 669 23 31 83', '', '', '', 'http://www.agilitycartagena.com/', 'agilitycartagena@gmail.com', '', '', '', 'la_palma.png', '', 0),
('La Princesa', 'Ocaña, 104, Bajo', '28047 Madrid', 'Madrid', '+ 34 91 465 50 05 ', '', '', '+40° 19'' 35,41", -3° 50'' 50,61" ', 'http://www.agilitylaprincesa.es/', 'agilitylaprincesa@gmail.com', '', '', '', 'la_princesa.png', '', 0),
('La Ribera', 'Plaza España, 11', '50638 Cabañas de Ebro (Zaragoza)', 'Zaragoza', ' + 34 976 75 86 33', ' + 34 649 58 65 98 ', '', '', 'http://www.agilitylaribera.es/', 'agilitylaribera@hotmail.com', '', '', '', 'la_ribera.png', '', 0),
('La Selva', 'Carretera Vella de Riudarenes, s/n', '17430 Santa Coloma de Farners (Gerona)', 'Girona/Gerona', '+ 34 606 77 64 65', ' + 34 629 36 37 39', '', '', 'http://www.asscaninalaselva.com/', 'agility@asscaninalaselva.com', '', '', '', 'la_selva.png', '', 0),
('Lealcan', 'José Luis Sampedro, 14, 2º D', '28529 Rivas Vaciamadrid (Madrid)', 'Madrid', ': + 34 646 44 45 55', '', '', '', 'http://www.lealcan.com/', 'info@lealcan.com', '', '', '', 'lealcan.png', '', 0),
('Maresme', 'Santiago Rusiñol, 90', '08340 Vilassar de Mar (Barcelona)', 'Barcelona', ' + 34 93 759 70 54 ', '', '', '', '', 'agilitymaresme@telefonica.net', '', '', '', 'maresme.png', '', 0),
('Marvi', '40 Pins, 36 - Urb. Roca II', '08430 La Roca del Valles (Barcelona)', 'Barcelona', '+ 34 93 842 21 05 ', '', '', '', '', 'Marvistel@hotmail.com', '', '', '', 'marvi.png', '', 0),
('Mediterráneo', 'Senda Estrecha, 14', '30011 Murcia', 'Murcia', '+ 34 968 25 77 83', '+ 34 677 40 98 57', '', '', '', '', '', '', '', 'mediterraneo.png', '', 0),
('Mi Perro 10', 'Andalucía, 25', '28750 San Agustín de Guadalix (Madrid)', 'Madrid', '+ 34 651 91 41 46', '', '', '', 'http://www.miperro10.com/', 'info@miperro10.com', '', '', '', 'mi_perro_10.png', '', 0),
('Miramar', 'Carrer del Llorer, 3', '08789 La Torre de Claramunt (Barcelona)', 'Barcelona', '+ 34 679 27 27 91 ', '', '', '', '', 'jmtorres323@gmail.com', '', '', '', 'miramar.png', '', 0),
('Negreira', 'Avda. Recinto Ferial, 2, 4º A', '36540 Silleda (Pontevedra)', 'Pontevedra', '+ 34 629 50 71 76', '', '', '', '', '', '', '', '', 'negreira.png', '', 0),
('Neo Reus', 'Mª Aurelia Campany, 4, 6º, 2º', '43204 Reus (Tarragona)', 'Tarragona', '+ 34 616 44 62 41', '', '', '', '', 'agilityneo@hotmail.com', '', '', '', 'neo_reus.png', '', 0),
('Palaciego', 'Roble, 7', '41720 Los Palacios y Villafranca (Sevilla)', 'Sevilla', '+ 34 619 12 73 25 ', '', '', '', 'http://www.actiweb.es/palaciego/', '', 'https://www.facebook.com/pages/Club-Deportivo-Agility-Palaciego/192778427478835', '', '', 'palaciego.png', '', 0),
('Parbayon Cantabria', 'Bº Sorribero Bajo, nº 4', '39470 Renedo de Piélagos (Cantabria)', 'Cantabria', '+ 34 626 79 14 54 ', '', '', '', 'http://www.agilitycantabria.com/', 'agilitycantabria@gmail.com', '', '', '', 'parbayon.png', '', 0),
('Parque del Alamillo', 'Antonio Machín, 9, 1º Izda.', '41009 Sevilla', 'Sevilla', ' + 34 655 76 46 03', '+ 34 95 443 45 77 ', '', '', 'http://www.clubagilityalamillo.com/', 'clubagilityalamillo@hotmail.com', '', '', '', 'parque_del_alamillo.png', '', 0),
('Pataplán', ' N-320a carretera de Valencia Km.134.', '', 'Cuenca', ' Juan José González (Presidente del Club) - tfn: 639776502', '	 Javier Martínez (Tesorero y Webmaster) - tfn: 605914763', '', 'N40.033819,W2.11415', 'http://www.agilitypataplan.es/', 'info@agilitypataplan.es', '', '', '', 'pataplan.png', '', 0),
('Patas', 'Ameixoada, 28C', '36954 Moaña (Pontevedra)', 'Pontevedra', '+ 34 986 31 10 13', '+ 34 659 01 36 68 ', '', '', 'http://patas.blogaliza.org/', 'agilitypatas@hotmail.com', '', '', '', 'patas.png', '', 0),
('Paterna', 'Cid Campeador, 12-13', '46980 Paterna (Valencia)', 'Valencia/Valéncia', '+ 34 677 72 27 30', '', '', '', 'http://www.agilitypaterna.com/', 'info@agilitypaterna.com', '', '', '', 'paterna.png', '', 0),
('Pican', 'Marqués de Dos Aguas, 39, p. 8', '46220 Picassent (Valencia)', 'Valencia/Valéncia', '+ 34 687 70 69 90', '', '', '', '', 'clubagilitypican@gmail.com', '', '', '', 'pican.png', '', 0),
('Pura Vida', 'Almendro, 3, Bl. 2, 1º C', '28710 El Molar (Madrid)', 'Madrid', ' + 34 680 19 08 42', '', '', '', 'http://www.mascotaspuravida.es/', 'agility@mascotaspuravida.es', '', '', '', 'pura_vida.png', '', 0),
('Santa Quiteria', '08410 Vilanova del Valles (Barcelona)', '', 'Barcelona', '+ 34 651 89 21 07', '', '', '', 'http://www.agilitysantaquiteria.es/', 'info@agilitysantaquiteria.es', '', '', '', 'santa_quiteria.png', '', 0),
('Star Can', 'Avda. del Vincle, 28', '03560 Campello (Alicante)', 'Alicante/Alacant', '+ 34 96 563 01 60', '(Ana Alonso) anastarcan@hotmail.com', '', 'W 0º 33´ 03" N 38º 22´31"', 'http://www.starcan.es/', 'nsoler@starcan.es', '', '', '', 'star_can.png', '', 0),
('Talavera', 'Cervera - Local 39', '45600 Talavera de la Reina (Toledo)', 'Toledo', '+ 34 610 01 79 75 ', '', '', '', '', 'reinaboxtalavera@hotmail.com', '', '', '', 'talavera.png', '', 0),
('Tandem', 'Carretera M413 km 8,5 de Arroyomolinos a Moraleja de Enmedio', 'Madrid.', 'Madrid', '687 964891', '627 964845', '', '', '', 'informacion@agilitytandem.es', '', '', '', 'tandem.png', '', 0),
('Tercans', 'Reibon, 192-A  -  Meira', '36955 Moaña (Pontevedra)', 'Pontevedra', ' + 34 617 34 07 63 ', '', '', '', '', 'tercans@gmail.com', '', '', '', 'tercans.png', '', 0),
('Torrevieja', 'Moriones, 43, 3º E', '03182 Torrevieja (Alicante)', 'Alicante/Alacant', '+ 34 635 41 30 31', '', '', '', '', 'info.agilitytorrevieja@gmail.com', '', '', '', 'torrevieja.png', '', 0),
('Toskahua', 'Cmno Santa Pau, 233 Garrapinillos-ZARAGOZA', '', 'Zaragoza', 'Tel.- 976-780583 movil.-666-436111', '', '', '', 'http://perso.wanadoo.es/vjuan/agility.htm', 'grupotoskahua@eresmas.com', '', '', '', 'toskaua.png', '', 0),
('Valles Club Cani', '08140 Caldes de Montbui (Barcelona)', '', 'Barcelona', ' telèfon:    619 28 68 82 ', '', '', ' N 41º 37'' 07''''    E 2º 10'' 31.7''''', '', 'info@vallesgrupcani.org', '', '', '', 'valles_club_cani.png', '', 0),
('Vallgorguina', 'Vila Carina Ctra. C-61, Km. 15,5', '08471 Vallgorguina (Barcelona)', 'Barcelona', '+ 34 93 867 93 18 ', '+ 34 600 00 54 99 ', '', '', '', 'agilityvallgorguina@centrecani.cat', '', '', '', 'vallgorguina.png', '', 0),
('Vila-Real', 'Padre Lluis María Llop, 54, 1º B', '12540 Vila-real (Castellón)', 'Castellón/Castelló', '+ 34 964 52 40 09', '', '', '', 'http://www.agilityvila-real.es/', 'agilityvilareal@gmail.com', '', '', '', 'vila_real.png', '', 0),
('Vilcan', 'Nou, 20', '46270 Villanueva de Castellón (Valencia)', 'Valencia/Valéncia', '+ 34 96 245 31 81', '', '', '', '', '', '', '', '', 'vilcan.png', '', 0),
('Villena', 'Plaza El Rollo, 5', '03400 Villena (Alicante)', 'Alicante/Alacant', '+ 34 636 42 67 13', '', '', '', 'http://clubagilityvillena.blogspot.com.es/', '', '', '', '', 'villena.png', '', 0),
('W.E.L.P.E.', 'Polideportivo Municipal La Canaleja', 'Alcorcón', 'Madrid', '+ 34 91 619 52 79', NULL, NULL, NULL, 'http://www.grupowelpe.com', 'gwelpe@teleline.es', 'https://www.facebook.com/groups/484854411592829/', NULL, '@gwelpe', 'welpe.png', NULL, 0),
('Xanastur', ' Baleares, 39, 3º D', '33208 Gijón (Asturias)', 'Asturias', '+ 34 607 11 90 56', '', '', '', 'http://www.xanastur.org/', 'xanasturcentrocanino@gmail.com', '', '', '', 'xanastur.png', '', 0),
('Zampican', 'Río Navía, 2', '12006 Castellón', 'Castellón/Castelló', '+ 34 629 07 06 75', '', '', '', 'http://www.agilityzampican.es/', '', '', '', '', 'zampican.png', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Equipos`
--
-- Creación: 24-02-2014 a las 07:55:22
--

DROP TABLE IF EXISTS `Equipos`;
CREATE TABLE IF NOT EXISTS `Equipos` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL,
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
-- Creación: 24-02-2014 a las 07:55:21
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
-- Creación: 24-02-2014 a las 07:55:23
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
('-- Sin asignar --', '', '', '-- Sin asignar --', 'NO BORRAR. Valor por defecto cuando un perro se define por primera vez'),
('Aaron Laro', NULL, NULL, 'Cubas', NULL),
('Ada Serrano', '', '', 'Santa Quiteria', ''),
('Adoración Morales', NULL, NULL, 'La Dama', NULL),
('Adrian Bajo', '', '', 'Vila-Real', ''),
('Adrian Díaz', NULL, NULL, 'El Hechizo del Border C.', NULL),
('Adrian Martínez', '', '', 'Pataplán', ''),
('Adrián Soria', '', '', 'Pataplán', ''),
('Africa Cabañas', '', '', 'Correcan', ''),
('Agustin Centelles', NULL, NULL, 'L''Almozara', NULL),
('Agustín González', '', '', 'L''Horta Nord', ''),
('Aida Al-Nehlawi', NULL, NULL, 'Canic', NULL),
('Alaitz Idarraga', '', '', 'Euskadi', ''),
('Albert Ulldemolins', NULL, NULL, 'Badalona', NULL),
('Alberto Alonso', NULL, NULL, 'Costa Blanca', NULL),
('Alberto Costas', NULL, NULL, 'Vallgorguina', NULL),
('Alberto González', '', '', 'Deporcan', ''),
('Alberto Marugan', '', '', 'Agilcan', ''),
('Alberto Mudarra', NULL, NULL, 'L''Almozara', NULL),
('Alberto Pereda', NULL, NULL, 'La Princesa', NULL),
('Alejandro Piñeiro', NULL, NULL, 'Tercans', NULL),
('Alejandro Rodríguez Villalta', '', '', 'Palaciego', ''),
('Alex del Río', NULL, NULL, 'Ciutat Comtal', NULL),
('Alex Olivera', NULL, NULL, 'Santa Quiteria', NULL),
('Alex Sabini', NULL, NULL, 'Ciutat Comtal', NULL),
('Alfredo Ortíz', NULL, NULL, 'Junior', NULL),
('Alicia Mejias', NULL, NULL, 'Parque del Alamillo', NULL),
('Alicia Sanjurjo', NULL, NULL, 'Valles Club Cani', NULL),
('Almudena Novo', '', '', 'Cinco Huesos', ''),
('Amparo Roig', NULL, NULL, 'L''Horta Nord', NULL),
('Ana Alonso', NULL, NULL, 'Star Can', NULL),
('Ana Baeza', NULL, NULL, 'Junior', NULL),
('Ana Beltran', NULL, NULL, 'El Nogueral', NULL),
('Ana Isabel Escobar', NULL, NULL, 'Deporcan', NULL),
('Ana Mateu', NULL, NULL, 'Canic', NULL),
('Ana Ontañon', NULL, NULL, 'Maresme', NULL),
('Ana Palet', '', '', 'Deporcan', ''),
('Ana Valencia', '', '', 'Agilcan', ''),
('Andrea García', NULL, NULL, 'Vila-Real', NULL),
('Andrés Gimeno', NULL, NULL, 'La Dama', NULL),
('Andrés López', NULL, NULL, 'Tercans', NULL),
('Andres Morillas Sanjuan', '', '', 'Educan', ''),
('Angel Corroto', '', '', 'Eslón', ''),
('Angel Fernández', '', '', 'Cubas', ''),
('Angel González', '', '', 'Agilcan', ''),
('Angel Insa', NULL, NULL, 'L''Almozara', NULL),
('Angel Puertolas', NULL, NULL, 'Kai Argi', NULL),
('Angel Rubio', '', '', 'Indog Maria de Huerva', ''),
('Angeles Abad', NULL, NULL, 'L''Almozara', NULL),
('Angelica Castaño', NULL, NULL, 'Eslón', NULL),
('Ankie Kleijberg', NULL, NULL, 'Star Can', NULL),
('Anna Aguilella', NULL, NULL, 'Costa Blanca', NULL),
('Antje Lippold', NULL, NULL, 'Cornella', NULL),
('Antonio Carmona', NULL, NULL, 'Cornella', NULL),
('Antonio Fernández', NULL, NULL, 'Correcan', NULL),
('Antonio López', '', '', 'Educan', ''),
('Antonio Molina', NULL, NULL, 'Cinco Huesos', NULL),
('Antonio Ojeda', NULL, NULL, 'Castellón', NULL),
('Antonio Santos', '', '', 'Santa Quiteria', ''),
('Antonio Tovar', '', '', 'L''Almozara', ''),
('Arabia Vidal', NULL, NULL, 'Vilcan', NULL),
('Araceli Montero', NULL, NULL, 'Badalona', NULL),
('Aracelis Rodríguez', NULL, NULL, 'Ciutat Comtal', NULL),
('Arcadio Nohales', NULL, NULL, 'Zampican', NULL),
('Ariadna Soriano', NULL, NULL, 'L''Almozara', NULL),
('Arianna Bucci', NULL, NULL, 'La Selva', NULL),
('Arturo Conejera', NULL, NULL, 'Correcan', NULL),
('Astrid Hoffmeister', NULL, NULL, 'Lealcan', NULL),
('Barbara Flemming', NULL, NULL, 'Star Can', NULL),
('Beatriz Juan', '', '', 'L''Horta Nord', ''),
('Belén de Carvalho', '', '', 'Educan', ''),
('Berit Kittel', '', '', 'Indog Maria de Huerva', ''),
('Carles Fortuny', NULL, NULL, 'Ciutat Comtal', NULL),
('Carlos Alvarez', NULL, NULL, 'Tercans', NULL),
('Carlos Casado', NULL, NULL, 'Star Can', NULL),
('Carlos Iglesias', NULL, NULL, 'Vallgorguina', NULL),
('Carlos Martínez', NULL, NULL, 'Eslón', NULL),
('Carlos Pérez', '', '', 'La Princesa', ''),
('Carlos Pulpón', '', '', 'Eslón', ''),
('Carlos Serra', NULL, NULL, 'Illa Blanca', NULL),
('Carmen Alos', NULL, NULL, 'Canic', NULL),
('Carmen Antequera', NULL, NULL, 'L''Horta Nord', NULL),
('Carmen Briceño', NULL, NULL, 'L''Almozara', NULL),
('Carmen Sotomayor', NULL, NULL, 'Eslón', NULL),
('Carmen Vázquez', NULL, NULL, 'La Selva', NULL),
('Carolina Verdú', NULL, NULL, 'Villena', NULL),
('Celeste Zarzosa', NULL, NULL, 'La Princesa', NULL),
('Celso Valle', NULL, NULL, 'Maresme', NULL),
('Cesar Losada', NULL, NULL, 'Eslón', NULL),
('Charly Castañer', '', '', 'Indog Maria de Huerva', ''),
('Concepción Fernández', NULL, NULL, 'La Princesa', NULL),
('Concepción López', NULL, NULL, 'La Ribera', NULL),
('Cristian Verde', NULL, NULL, 'Tercans', NULL),
('Cristina Blanco', '', '', 'Cubas', ''),
('Cristina Cortijo', '', '', 'Eslón', ''),
('Cristina García', NULL, NULL, 'La Daga', NULL),
('Cristina González', '', '', 'Vallgorguina', ''),
('Cristina Pedraz', '', '', 'AA Y CIA', ''),
('Cristina Ruill', '', '', 'La Daga', ''),
('Cristofol Albert', NULL, NULL, 'L''Horta Nord', NULL),
('Damian Alarcon', NULL, NULL, 'Educan', NULL),
('Daniel Amigo', NULL, NULL, 'Badalona', NULL),
('Daniel Luna', NULL, NULL, 'Junior', NULL),
('Daniel Menéndez', NULL, NULL, 'El Hechizo del Border C.', NULL),
('David Alique', NULL, NULL, 'Deporcan', NULL),
('David Asenjo', '', '', 'L''Almozara', ''),
('David Escribano', '', '', 'Cubas', ''),
('David Ferrer', NULL, NULL, 'Badalona', NULL),
('David Flix', NULL, NULL, 'Santa Quiteria', NULL),
('David Gómez-Calcerrada', '', '', 'La Princesa', ''),
('David Gonzalbo', NULL, NULL, 'El Hechizo del Border C.', NULL),
('David Molina', '', '', 'L''Almozara', ''),
('David Parejo', NULL, NULL, 'Ciudad de Antequera', NULL),
('David Sepulveda', NULL, NULL, 'Badalona', NULL),
('Debra Howard', NULL, NULL, 'La Princesa', NULL),
('Diana Cozar', NULL, NULL, 'Canic', NULL),
('Diana García', NULL, NULL, 'Parque del Alamillo', NULL),
('Diego Rouco', NULL, NULL, 'Patas', NULL),
('Dolores López', NULL, NULL, 'Villena', NULL),
('Dolores Sampedro', NULL, NULL, 'Zampican', NULL),
('Eduard Bonet', NULL, NULL, 'Badalona', NULL),
('Eduard Giralt Canadell', '', '', 'Santa Quiteria', ''),
('Eduardo Adán', '', '', 'Indog Maria de Huerva', ''),
('Efren Lucas', NULL, NULL, 'Star Can', NULL),
('Elena Alberich', NULL, NULL, 'Canic', NULL),
('Elena Chinchilla', NULL, NULL, 'L''Horta Nord', NULL),
('Elena Miguel', '', '', 'Eslón', ''),
('Elena Sin', NULL, NULL, 'L''Almozara', NULL),
('Elisenda Huidobro', NULL, NULL, 'Canic', NULL),
('Emilio Calvo', NULL, NULL, 'El Hechizo del Border C.', NULL),
('Emilio José Pedrazuela', NULL, NULL, 'Educan', NULL),
('Enric García', NULL, NULL, 'Zampican', NULL),
('Enric Lleixa', NULL, NULL, 'Badalona', NULL),
('Enrique Alonso Queija', '', '', 'Educan', ''),
('Enrique Camarero', '', '', 'Eslón', ''),
('Enrique Grau', NULL, NULL, 'Costa Blanca', NULL),
('Enrique Herbera', NULL, NULL, 'Badalona', NULL),
('Enrique Lleixa', NULL, NULL, 'Badalona', NULL),
('Enrique Sendra', NULL, NULL, 'Tercans', NULL),
('Ernesto Sorribes', NULL, NULL, 'Zampican', NULL),
('Estefanía Pérez', NULL, NULL, 'Educan', NULL),
('Estíbaliz Pereda Navarro', '', '', 'Deporcan', ''),
('Estíbaliz Pujana', '', '', 'L''Horta Nord', ''),
('Eugenio Villares', NULL, NULL, 'Cubas', NULL),
('Eva Grau', NULL, NULL, 'Paterna', NULL),
('Eva Vázquez Morales', '', '', 'L''Almozara', ''),
('Fermin Gil', NULL, NULL, 'L''Horta Nord', NULL),
('Fernando Benet', NULL, NULL, 'Zampican', NULL),
('Fernando Bibián', '', '', 'Agilcan', ''),
('Fernando De La Fuente', '', '', 'L''Almozara', ''),
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
('Gabriel Gómez', '', '', 'L''Almozara', ''),
('Gabriel Martín', NULL, NULL, 'Parbayon Cantabria', NULL),
('Gema López', '', '', 'Eslón', ''),
('Gerard Barberá', NULL, NULL, 'Neo Reus', NULL),
('Gerardo Alvarez', NULL, NULL, 'Junior', NULL),
('Gerardo González', NULL, NULL, 'Eslón', NULL),
('Gisela Solis', NULL, NULL, 'La Daga', NULL),
('Gorka Pozuelo', '', '', 'L''Horta Nord', ''),
('Gregorio Conde', NULL, NULL, 'Badalona', NULL),
('Iago Sánchez', NULL, NULL, 'Tercans', NULL),
('Iban Cubedo', NULL, NULL, 'Costa Blanca', NULL),
('Imanol López', NULL, NULL, 'L''Almozara', NULL),
('Iñaki García', '', '', 'Euskadi', ''),
('Inmaculada Rubio', NULL, NULL, 'Eslón', NULL),
('Irati Diego', '', '', 'L''Horta Nord', ''),
('Irena', '', '', 'L''Almozara', ''),
('Irene Artacho', NULL, NULL, 'Cinco Huesos', NULL),
('Isabel Gómez', NULL, NULL, 'Depordog', NULL),
('Isabel Rodríguez', NULL, NULL, 'Valles Club Cani', NULL),
('Isidoro Vázquez', NULL, NULL, 'Cubas', NULL),
('Ismael Pérez', '', '', 'Canic', ''),
('Israel Díaz', '', '', 'Correcan', ''),
('Israel Fernández', NULL, NULL, 'Vallgorguina', NULL),
('Iván Pardo García', '', '', 'Vallgorguina', ''),
('Iván San Antonio', NULL, NULL, 'Cinco Huesos', NULL),
('Iván Sánchez García', '', '', 'La Princesa', ''),
('Jacqueline Holemans', NULL, NULL, 'Santa Quiteria', NULL),
('Jaime Gamir', NULL, NULL, 'Baix Llobregat', NULL),
('Jara Pérez', '', '', 'Agilcan', ''),
('Jaume Fernández', NULL, NULL, 'Ciutat Comtal', NULL),
('Javier Gómez', NULL, NULL, 'Pican', NULL),
('Javier Iniesta', '', '', 'Vallgorguina', ''),
('Javier López', NULL, NULL, 'Santa Quiteria', NULL),
('Javier Martín', '', '', 'Agilcan', ''),
('Javier Martínez', '', '', 'Pataplán', ''),
('Javier Mora Canales', '', '', 'Correcan', ''),
('Javier Ovejero', NULL, NULL, 'Eslón', NULL),
('Javier Sanchis', NULL, NULL, 'Maresme', NULL),
('Javier Santisteban', '', '', 'Cinco Huesos', ''),
('Jenifer Tolín', '', '', 'La Princesa', ''),
('Jennifer Tolín', NULL, NULL, 'La Princesa', NULL),
('Jenny Funcke', NULL, NULL, 'Badalona', NULL),
('Jerónimo Martínez', NULL, NULL, 'Star Can', NULL),
('Jessica Graciano', '', '', 'La Princesa', ''),
('Jesús Crespo', NULL, NULL, 'Mi Perro 10', NULL),
('Jesús Cuellar', NULL, NULL, 'Eslón', NULL),
('Jesús Gómez', '', '', 'Agilcan', ''),
('Jesús Manuel Romero', NULL, NULL, 'Palaciego', NULL),
('Joan Castillo', '', '', 'Maresme', ''),
('Joan Wenceslao Pastor', NULL, NULL, 'Valles Club Cani', NULL),
('Joaquín Andrés', '', '', 'Agilcan', ''),
('Jonathan Guillen', NULL, NULL, 'Vilcan', NULL),
('Jordi Boix', NULL, NULL, 'Canic', NULL),
('Jordi Gómez', NULL, NULL, 'Canic', NULL),
('Jorge Arcas Perales', '', '', 'L''Almozara', ''),
('Jorge Bala', '', '', 'L''Almozara', ''),
('Jorge Muñoz Leal', '', '', 'Cubas', ''),
('Jorge Valero', NULL, NULL, 'L''Almozara', NULL),
('José Angel Beired', '', '', 'Toskahua', ''),
('José Angel Torres', NULL, NULL, 'Tercans', NULL),
('José Antonio Encinas', NULL, NULL, 'Correcan', NULL),
('José Antonio Pascual', NULL, NULL, 'L''Almozara', NULL),
('José Antonio Vega', NULL, NULL, 'Agilcan', NULL),
('José Carlos Iglesias', NULL, NULL, 'Vallgorguina', NULL),
('José Castaño', NULL, NULL, 'Valles Club Cani', NULL),
('José Francisco Martorell', NULL, NULL, 'Pican', NULL),
('José Guix', NULL, NULL, 'Pican', NULL),
('José Luis García', NULL, NULL, 'Zampican', NULL),
('Jose Luis J. Mori', '', '', 'AA Y CIA', ''),
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
('Jose Ramón López', '', '', 'Eslón', ''),
('José Santos Luna', NULL, NULL, 'Junior', NULL),
('José Soliño', NULL, NULL, 'Tercans', NULL),
('Josep Barbera', NULL, NULL, 'Neo Reus', NULL),
('Josep Mª Pineda', NULL, NULL, 'Valles Club Cani', NULL),
('Juan Antonio Martinez', NULL, 'juansgaviota@gmail.com', 'W.E.L.P.E.', NULL),
('Juan Campin', NULL, NULL, 'La Princesa', NULL),
('Juan Carlos Blas', NULL, NULL, 'Eslón', NULL),
('Juan Carlos Companys', NULL, NULL, 'Costa Blanca', NULL),
('Juan Carlos Hinojal', NULL, NULL, 'Parbayon Cantabria', NULL),
('Juan Carlos Redondo', '', '', 'Tandem', ''),
('Juan Carlos Ruiz', '', '', 'Eslón', ''),
('Juan del Amo', NULL, NULL, 'Junior', NULL),
('Juan Escos', '', '', 'La Ribera', ''),
('Juan Francisco Pelegrin', NULL, NULL, 'La Ribera', NULL),
('Juan Francisco Torres', NULL, NULL, 'Badalona', NULL),
('Juan José Espadas', NULL, NULL, 'Canic', NULL),
('Juan José González', '', '', 'Pataplán', ''),
('Juan Luis Colmano', NULL, NULL, 'L''Almozara', NULL),
('Juan Manuel Caballo', NULL, NULL, 'L''Horta Nord', NULL),
('Juan Martín de las Blancas', '', '', 'Cubas', ''),
('Juan Miguel Cifuentes', NULL, NULL, 'L''Horta Nord', NULL),
('Juan Pablo Díaz', NULL, NULL, 'Cubas', NULL),
('Juan Pedro Martínez', NULL, NULL, 'Castellón', NULL),
('Juan Rodríguez', '', '', 'La Princesa', ''),
('Juan Solanes', NULL, NULL, 'Vilcan', NULL),
('Judith Cortes', NULL, NULL, 'L''Almozara', NULL),
('Judith Franco', '', '', 'Cinco Huesos', ''),
('Judith Herms', NULL, NULL, 'Ciutat Comtal', NULL),
('Julia Faci Green', '', '', 'L''Almozara', ''),
('Julian Sánchez', NULL, NULL, 'Valles Club Cani', NULL),
('Julio Freire', '', '', 'Parbayon Cantabria', ''),
('Katia Moeller', NULL, NULL, 'Canino Algecireño', NULL),
('Katy Navarro', NULL, NULL, 'Parbayon Cantabria', NULL),
('Laura Carrasco', NULL, NULL, 'Ciutat Comtal', NULL),
('Laura Chiva', NULL, NULL, 'Badalona', NULL),
('Laura Monrabal', '', '', 'L''Almozara', ''),
('Lorena Díez', NULL, NULL, 'La Princesa', NULL),
('Lorena García', NULL, NULL, 'Parbayon Cantabria', NULL),
('Lorena Gargoles', NULL, NULL, 'Canic', NULL),
('Lourdes Giménez', NULL, NULL, 'El Hechizo del Border C.', NULL),
('Lourdes Peñarrocha', NULL, NULL, 'Star Can', NULL),
('Lourdes Rivera', '', '', 'La Princesa', ''),
('Lucía Montalbán', '', '', 'L''Almozara', ''),
('Lucía Romero', '', '', 'Palaciego', ''),
('Luciano Fernández', '', '', 'La Princesa', ''),
('Luis Alberto Pereira', NULL, NULL, 'Parbayon Cantabria', NULL),
('Luis Carlos Sanchez', NULL, NULL, 'Agilcan', NULL),
('Luis de Frías', '', '', 'La Princesa', ''),
('Luis Ignacio Carazo', NULL, NULL, 'Valles Club Cani', NULL),
('Luis Luque', NULL, NULL, 'Vallgorguina', NULL),
('Luis Miguel Rodrigo', '', '', 'L''Horta Nord', ''),
('Luis Miguel Rodriguez', '', '', 'La Princesa', ''),
('Luisa Fernanda Millan', '', '', 'Pataplán', ''),
('Luna Ramírez', NULL, NULL, 'Maresme', NULL),
('Maitane Luengo', '', '', 'Euskadi', ''),
('Maite Guerrero', '', '', 'Eslón', ''),
('Manel Martínez', NULL, NULL, 'Valles Club Cani', NULL),
('Manuel Basco', NULL, NULL, 'Palaciego', NULL),
('Manuel Jesús García', NULL, NULL, 'Campo de Gibraltar', NULL),
('Manuel Lara', NULL, NULL, 'Ciutat Comtal', NULL),
('Manuel Santomé', NULL, NULL, 'Tercans', NULL),
('Mar Bermúdez', NULL, NULL, 'Camu', NULL),
('Marc Rabada', NULL, NULL, 'La Daga', NULL),
('Marco Maldonado', '', '', 'Indog Maria de Huerva', ''),
('Marcos Martínez', NULL, NULL, 'Pican', NULL),
('María López', NULL, NULL, 'Valles Club Cani', NULL),
('Marina López', NULL, NULL, 'Ciutat Comtal', NULL),
('Mario Rodríguez', NULL, NULL, 'W.E.L.P.E.', NULL),
('Marisa Jarabo', NULL, NULL, 'W.E.L.P.E.', NULL),
('Marta de la Rosa', NULL, NULL, 'Zampican', NULL),
('Marta Gregorio', NULL, NULL, 'Tercans', NULL),
('Marta Jiménez', '', '', 'Agilcan', ''),
('Marta Ponce', '', '', 'L''Almozara', ''),
('Marta Sánchez', NULL, NULL, 'Cubas', NULL),
('Massimiliano Miggiano', NULL, NULL, 'Vallgorguina', NULL),
('Matias Monleón', NULL, NULL, 'L''Horta Nord', NULL),
('Matias Rodríguez', NULL, NULL, 'Vallgorguina', NULL),
('Menchu Melcom', '', '', 'Deporcan', ''),
('Mercedes Fernández', NULL, NULL, 'Star Can', NULL),
('Michael Volkert', NULL, NULL, 'La Selva', NULL),
('Miguel Angel Fernández', NULL, NULL, 'Xanastur', NULL),
('Miguel Angel García', NULL, NULL, 'Eslón', NULL),
('Miguel Angel Morales', NULL, NULL, 'Agilcan', NULL),
('Miguel Angel Soriano', NULL, NULL, 'Star Can', NULL),
('Miguel García Rodríguez', '', '', 'Tercans', ''),
('Mireia Carrascoso', NULL, NULL, 'La Daga', NULL),
('Miriam García', NULL, NULL, 'Neo Reus', NULL),
('Mónica Muñiz', NULL, NULL, 'Educan', NULL),
('Mónica Rodríguez', '', '', 'AA Y CIA', ''),
('Mónica Saavedra', NULL, NULL, 'Mi Perro 10', NULL),
('Montserrat Calvet', NULL, NULL, 'Badalona', NULL),
('Mª José Manzano', NULL, NULL, 'L''Almozara', NULL),
('Narciso Leita', NULL, NULL, 'La Ribera', NULL),
('Natalia Cuadrado', NULL, NULL, 'Cornella', NULL),
('Natividad Ruiz García', '', '', 'Educan', ''),
('Natividad Soler', NULL, NULL, 'Star Can', NULL),
('Neus Baró', NULL, NULL, 'Canic', NULL),
('Noelia Gimeno', NULL, NULL, 'Paterna', NULL),
('Noelia Mouchet', '', '', 'Vallgorguina', ''),
('Nuria Alonso', NULL, NULL, 'Ciutat Comtal', NULL),
('Nuria Costa', NULL, NULL, 'Valles Club Cani', NULL),
('Nuria Fortuny', NULL, NULL, 'Ciutat Comtal', NULL),
('Nuria Morell Nadal', '', '', 'Valles Club Cani', ''),
('Olga Palomares', '', '', 'Deporcan', ''),
('Oria Micó', NULL, NULL, 'Villena', NULL),
('Oscar Bravo', NULL, NULL, 'Cornella', NULL),
('Oscar Muñiz', NULL, NULL, 'Educan', NULL),
('Oscar Reboredo', NULL, NULL, 'Ciutat Comtal', NULL),
('Oscar Sacristan', NULL, NULL, 'La Princesa', NULL),
('Pablo Ballesta', NULL, NULL, 'Camu', NULL),
('Pablo Miró', NULL, NULL, 'Zampican', NULL),
('Paloma Faci Green', '', '', 'L''Almozara', ''),
('Pau Serrano Ciratusa', '', '', 'L''Almozara', ''),
('Paula de Lucas', NULL, NULL, 'Cubas', NULL),
('Paula Rello', NULL, NULL, 'Cubas', NULL),
('Paulino Iranzo', NULL, NULL, 'Pican', NULL),
('Pedro Delgado Fernandez', '', '', 'Educan', ''),
('Pedro Jesús Tazón', NULL, NULL, 'Parbayon Cantabria', NULL),
('Pedro Martínez', '', '', 'Euskadi', ''),
('pepe', '', 'pepe@pepe.com', 'ACADE', ''),
('pepepepe', '', '', 'ACADE', ''),
('Pilar Collado', '', '', 'Indog Maria de Huerva', ''),
('Pilar Matesanz', NULL, NULL, 'W.E.L.P.E.', NULL),
('Pilar Rodríguez', NULL, NULL, 'Parbayon Cantabria', NULL),
('Rachel Stevens', NULL, NULL, 'Neo Reus', NULL),
('Rafael Altava', NULL, NULL, 'Costa Blanca', NULL),
('Rafael Camacho', NULL, NULL, 'Vallgorguina', NULL),
('Rafael Fernández', NULL, NULL, 'Pican', NULL),
('Rafael García', NULL, NULL, 'Vila-Real', NULL),
('Rafael Torregrosa', NULL, NULL, 'Costa Blanca', NULL),
('Ramón Arribas', '', '', 'AA Y CIA', ''),
('Ramón García', '', '', 'Cinco Huesos', ''),
('Raquel Frago', NULL, NULL, 'La Ribera', NULL),
('Raquel Garrido', NULL, NULL, 'Cubas', NULL),
('Raúl Sánchez', NULL, NULL, 'Cubas', NULL),
('Remedios Torres', NULL, NULL, 'Star Can', NULL),
('Reyes García', NULL, NULL, 'El Hechizo del Border C.', NULL),
('Ricardo Benito', NULL, NULL, 'W.E.L.P.E.', NULL),
('Ricardo Martínez', NULL, NULL, 'Depordog', NULL),
('Ricardo Santolaya', NULL, NULL, 'L''Almozara', NULL),
('Roberto Castro', NULL, NULL, 'Eslón', NULL),
('Roberto Iñigo', NULL, NULL, 'Cinco Huesos', NULL),
('Roberto Reina Vega', '', '', 'La Princesa', ''),
('Rocio Hermelo', NULL, NULL, 'Tercans', NULL),
('Rocio Santos', NULL, NULL, 'Parque del Alamillo', NULL),
('Rodrigo González', '', '', 'La Princesa', ''),
('Roque Alonso', '', '', 'Junior', ''),
('Ross Rubio', '', '', 'Deporcan', ''),
('Ruben Jurado', NULL, NULL, 'La Daga', NULL),
('Ruben Lopera', NULL, NULL, 'Marvi', NULL),
('Ruben Montero', NULL, NULL, 'La Princesa', NULL),
('Sabina González', NULL, NULL, 'La Selva', NULL),
('Salvador Martí', NULL, NULL, 'Santa Quiteria', NULL),
('Sandra Gracia', '', '', 'L''Almozara', ''),
('Sara Bellido', NULL, NULL, 'Canino Algecireño', NULL),
('Sara Lara', '', '', 'L''Horta Nord', ''),
('Sara Montila', NULL, NULL, 'Deporcan', NULL),
('Sara Montoya', NULL, NULL, 'Eslón', NULL),
('Sebastian González', NULL, NULL, 'Badalona', NULL),
('Sergio Casalins', NULL, NULL, 'Educan', NULL),
('Sergio Colomé', NULL, NULL, 'L''Almozara', NULL),
('Sergio García', NULL, NULL, 'Vila-Real', NULL),
('Sergio Martín', NULL, NULL, 'L''Almozara', NULL),
('Sergio Romeo', '', '', 'L''Almozara', ''),
('Sergio Ruiz', NULL, NULL, 'Correcan', NULL),
('Sergio Tella', '', '', 'La Ribera', ''),
('Sheila Giménez', '', '', 'L''Almozara', ''),
('Silvia León', NULL, NULL, 'Zampican', NULL),
('Silvia Perea', '', '', 'W.E.L.P.E.', ''),
('Silvia Rodríguez', NULL, NULL, 'Valles Club Cani', NULL),
('Sofía Díaz', '', '', 'Santa Quiteria', ''),
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
('Verónica Díez Gómez', '', '', 'Parbayon Cantabria', ''),
('Verónica Ibañez', NULL, NULL, 'Ciudad de Antequera', NULL),
('Verónica Rodríguez', NULL, NULL, 'La Princesa', NULL),
('Vicente Cambra', NULL, NULL, 'La Manada', NULL),
('Vicente Martín', '', '', 'Agilcan', ''),
('Vicente Micó', NULL, NULL, 'Villena', NULL),
('Vicente Villalba', NULL, NULL, 'L''Horta Nord', NULL),
('Victor García', NULL, NULL, 'Marvi', NULL),
('Virginia García', '', '', 'Educan', ''),
('Wladimiro', '', '', 'L''Almozara', ''),
('Xavier López', NULL, NULL, 'Santa Quiteria', NULL),
('Yolanda Larena', NULL, NULL, 'Neo Reus', NULL),
('Yolanda Moreno', NULL, NULL, 'L''Horta Nord', NULL),
('Yolanda Torres', NULL, NULL, 'Maresme', NULL),
('Yulia Morugova', '', '', 'El Nogueral', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inscripciones`
--
-- Creación: 06-03-2014 a las 07:38:31
--

DROP TABLE IF EXISTS `Inscripciones`;
CREATE TABLE IF NOT EXISTS `Inscripciones` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL,
  `IDPerro` int(4) NOT NULL,
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
  UNIQUE KEY `Inscripciones_dj` (`Prueba`,`IDPerro`),
  KEY `IDPerro` (`IDPerro`),
  KEY `Jornada` (`Prueba`),
  KEY `Equipo` (`Equipo`),
  KEY `Dorsal_index` (`Dorsal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Inscripciones`:
--   `IDPerro`
--       `Perros` -> `IDPerro`
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
-- Estructura Stand-in para la vista `InscritosJornada`
--
DROP VIEW IF EXISTS `InscritosJornada`;
CREATE TABLE IF NOT EXISTS `InscritosJornada` (
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Jornadas`
--
-- Creación: 24-02-2014 a las 07:55:23
--

DROP TABLE IF EXISTS `Jornadas`;
CREATE TABLE IF NOT EXISTS `Jornadas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Prueba` int(4) NOT NULL,
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
-- Creación: 24-02-2014 a las 07:55:21
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
('-- Sin asignar --', NULL, NULL, '--- -- -- --', 0, 0, 'nobody@nomail.com', 'NO BORRAR: Asignacion de juez por defecto'),
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
-- Creación: 24-02-2014 a las 07:55:24
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
  `Juez1` varchar(255) DEFAULT '-- Sin asignar --',
  `Juez2` varchar(255) DEFAULT '-- Sin asignar --',
  `Observaciones` varchar(255) DEFAULT NULL,
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  `Orden_Salida` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Tipo` (`Tipo`),
  KEY `Grado` (`Grado`),
  KEY `Juez Titular` (`Juez1`),
  KEY `Juez Practicas` (`Juez2`),
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
-- Estructura Stand-in para la vista `PerroGuiaClub`
--
DROP VIEW IF EXISTS `PerroGuiaClub`;
CREATE TABLE IF NOT EXISTS `PerroGuiaClub` (
`IDPerro` int(4)
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
-- Creación: 24-02-2014 a las 07:55:24
--

DROP TABLE IF EXISTS `Perros`;
CREATE TABLE IF NOT EXISTS `Perros` (
  `IDPerro` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Raza` varchar(255) DEFAULT NULL,
  `LOE_RRC` varchar(255) DEFAULT NULL,
  `Licencia` varchar(255) DEFAULT '--------',
  `Categoria` varchar(1) NOT NULL DEFAULT '-',
  `Guia` varchar(255) DEFAULT '-- Sin asignar --',
  `Grado` varchar(16) DEFAULT '-',
  PRIMARY KEY (`IDPerro`),
  KEY `Perros_ibfk_3` (`Guia`),
  KEY `Perros_ibfk_1` (`Categoria`),
  KEY `Perros_ibfk_2` (`Grado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=755 ;

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

INSERT INTO `Perros` (`IDPerro`, `Nombre`, `Raza`, `LOE_RRC`, `Licencia`, `Categoria`, `Guia`, `Grado`) VALUES
(10, 'Yuma', 'P.B.Malinoise', '1936256', 'A330', 'L', 'Juan Miguel Cifuentes', 'GIII'),
(11, 'Hannibal', '', '1764520', 'A090', 'L', 'Tomás Pérez', 'GIII'),
(12, 'Ardi', '', '79097', '729', 'L', 'Paloma Faci Green', '-'),
(13, 'William', NULL, '1667920', '920', 'L', 'Jenny Funcke', '-'),
(14, 'Xonny', NULL, '1317156', '622', 'L', 'Cesar Losada', '-'),
(15, 'Indiana Jones', '', '1720531', '987', 'L', 'Juan Francisco Pelegrin', 'GIII'),
(16, 'Thelma', '', '1515702', '824', 'L', 'Ricardo Santolaya', 'GIII'),
(17, 'Boss', '', '1528991', '797', 'L', 'Oscar Muñiz', 'GIII'),
(18, 'Lee', 'Border Collie', '95245', 'A084', 'L', 'Antonio Molina', 'GIII'),
(19, 'Chinouk', NULL, '1390419', '724', 'L', 'Michael Volkert', '-'),
(20, 'Angie', NULL, '1370168', '691', 'L', 'Antonio Molina', '-'),
(21, 'Burundi', '', '1874262', 'A310', 'L', 'Elena Chinchilla', 'GIII'),
(22, 'Piter', '', '110594', 'A360', 'L', 'Cristofol Albert', 'GIII'),
(23, 'Napa', '', '1975832', 'A401', 'L', 'Paloma Faci Green', 'GIII'),
(24, 'Gon', '', '1725855', 'A024', 'L', 'Manuel Santomé', 'GIII'),
(25, 'Valerie', '', '1467667', '786', 'L', 'Manel Martínez', 'GIII'),
(26, 'Woman', '', '1866186', 'A206', 'L', 'Javier Mora Canales', 'GIII'),
(27, 'Baloo', NULL, '86974', '991', 'L', 'Pilar Collado', '-'),
(28, 'Piter Winers', NULL, '100338', 'A188', 'L', 'José Guix', '-'),
(29, 'Lula', NULL, '1891977', 'A344', 'L', 'Angel Puertolas', '-'),
(30, 'Karen', NULL, '1970258', 'A427', 'L', 'Amparo Roig', '-'),
(31, 'Runa', '', '112361', 'A347', 'L', 'Alex Olivera', 'GIII'),
(33, 'Chiruca', '', '1635759', '986', 'L', 'Antonio Fernández', 'GII'),
(35, 'Moss', '', '113891', 'A391', 'L', 'Roberto Iñigo', 'GIII'),
(36, 'Nena', NULL, '1521753', '930', 'L', 'Julia Faci Green', '-'),
(38, 'Deby', 'BorderCollie', '101610', 'A147', 'L', 'Cesar Losada', 'GIII'),
(39, 'Noah', '', '1887262', 'A268', 'L', 'Vicente Villalba', 'GIII'),
(40, 'Sil', NULL, '1831356', 'A150', 'L', 'Manuel Santomé', '-'),
(41, 'Furia', '', '1554907', '892', 'L', 'Nuria Fortuny', 'GIII'),
(42, 'Mc Coy', 'Border Collie', '1905162', 'A322', 'L', 'Carmen Sotomayor', 'GIII'),
(43, 'Argi', '', '110120', 'A241', 'L', 'Manuel Basco', 'GIII'),
(44, 'Lua', NULL, '118441', 'A327', 'L', 'José Manuel Linares', '-'),
(45, 'Zoe', '', '109748', 'A289', 'L', 'Marta Sánchez', 'GIII'),
(47, 'Juice', NULL, '117997', 'A387', 'L', 'Berit Kittel', '-'),
(48, 'Vega', NULL, '1552296', '855', 'L', 'Amparo Roig', '-'),
(49, 'Idris', NULL, '83909', '880', 'L', 'Narciso Leita', '-'),
(51, 'Izar', NULL, '1596718', '851', 'L', 'Francisco Martín', '-'),
(52, 'Pica', '', '104103', 'A091', 'L', 'Alberto Costas', 'GIII'),
(53, 'Nana', '', '1780849', 'A073', 'L', 'José Luis García', 'GIII'),
(54, 'Tara', NULL, 'No tiene', '1466', 'L', 'Oscar Reboredo', '-'),
(56, 'Finn', '', '2074557', 'A596', 'L', 'Rachel Stevens', 'GIII'),
(57, 'Rocky', '', '1796496', 'A355', 'L', 'Arturo Conejera', 'GIII'),
(58, 'Neil', 'Flat Coated Retriever', '122117', 'A417', 'L', 'Isidoro Vázquez', '-'),
(59, 'Asia', 'Border Collie', '1958017', 'A364', 'L', 'José Luis Romero', 'GIII'),
(60, 'Xodro', 'BorderCollie', '1959124', 'A371', 'L', 'Carlos Martínez', 'GIII'),
(61, 'Bec', NULL, '87712', '979', 'L', 'José Martí', '-'),
(62, 'Bely', NULL, '1370171', '692', 'L', 'Francisco Pérez', '-'),
(63, 'Laia', '', '1594320', '921', 'L', 'Israel Fernández', 'GIII'),
(64, 'Bamba', '', '1887258', 'A242', 'L', 'Andrea García', 'GII'),
(65, 'Akane', 'Euskal A Txakurra', '101429', 'A249', 'L', 'Gerardo González', 'GIII'),
(66, 'Mister', 'Border Collie', '99971', 'A250', 'L', 'Angelica Castaño', 'GII'),
(67, 'Spyro', NULL, '89457', 'A155', 'L', 'Angel Puertolas', '-'),
(68, 'Becho', NULL, '1831350', 'A203', 'L', 'Alejandro Piñeiro', '-'),
(69, 'Luna', NULL, '1798021', 'A163', 'L', 'Vicente Micó', '-'),
(70, 'Buh', '', '1929147', 'A297', 'L', 'Pilar Collado', 'GIII'),
(71, 'Aby', 'Border Collie', '105806', 'A204', 'L', 'Roberto Reina Vega', 'GIII'),
(72, 'Yuma', '', '113067', 'A385', 'L', 'Luis Alberto Pereira', 'GIII'),
(73, 'Zak', NULL, '1831579', 'A160', 'L', 'Pablo Miró', '-'),
(75, 'Tanga', 'Border Collie', '119678', 'A366', 'L', 'Sara Montoya', 'GIII'),
(76, 'Viconte', 'Mudi', '1561797', '813', 'L', 'Luis Miguel Rodriguez', 'GII'),
(77, 'Dela', '', '2028765', 'A377', 'L', 'Luis Luque', 'GII'),
(78, 'Brus', NULL, '1763613', 'A140', 'L', 'David Parejo', '-'),
(80, 'King', '', '1520008', '856', 'L', 'Mónica Muñiz', 'GIII'),
(81, 'Brujostel', 'Border Collie', '1717447', 'A231', 'L', 'Yulia Morugova', 'GIII'),
(83, 'Kora', 'P.B.Malinoise', '111627', 'A332', 'L', 'Pedro Delgado Fernandez', 'GII'),
(84, 'Rubia', NULL, '127474', 'A481', 'L', 'Luis Miguel Rodrigo', '-'),
(85, 'Fito', '', '127473', 'A529', 'L', 'Luis Miguel Rodrigo', 'GIII'),
(86, 'Visente', NULL, '116863', 'A359', 'L', 'Paulino Iranzo', '-'),
(87, 'Maia', NULL, '1780846', 'A132', 'L', 'Arcadio Nohales', '-'),
(88, 'Mecha', 'P.B.Malinoise', '129549', 'A558', 'L', 'José Antonio Encinas', 'GIII'),
(89, 'Ari', NULL, '1893230', 'A301', 'L', 'Anna Aguilella', '-'),
(90, 'Aslan', '', '1970223', 'A457', 'L', 'Iban Cubedo', 'GIII'),
(91, 'Mambo', '', '1392048', '753', 'L', 'Eduard Giralt Canadell', 'GIII'),
(93, 'Nut', '', '120681', 'A430', 'L', 'Mónica Rodríguez', 'GII'),
(94, 'Xena', NULL, '1570666', 'A118', 'L', 'José Miguel Agustín', '-'),
(95, 'Fiona', NULL, '2010068', 'A491', 'L', 'Rafael Altava', '-'),
(96, 'Winner', 'Border Collie', '127815', 'A497', 'L', 'Ricardo Santolaya', 'GII'),
(97, 'Jade', 'skal A Txakurra', '120128', 'A522', 'L', 'Gerardo González', 'GIII'),
(98, 'Dasher', '', '2000291', 'A411', 'L', 'Juan Francisco Pelegrin', 'GIII'),
(99, 'Gaston', NULL, '1717449', 'A340', 'L', 'Francisco Pérez', '-'),
(100, 'Pipo', NULL, '118271', 'A458', 'L', 'Rafael Fernández', '-'),
(101, 'Grey', NULL, '1717450', 'A066', 'L', 'Araceli Montero', '-'),
(102, 'Maty', 'Border Collie', '1964043', 'A333', 'L', 'Cristina Cortijo', 'GII'),
(104, 'Dylan', 'Border Collie', '127444', 'A461', 'L', 'Iván Sánchez García', 'GIII'),
(106, 'Magia', NULL, '1753228', 'A104', 'L', 'Carolina Verdú', '-'),
(107, 'Lia', NULL, '1842317', 'A389', 'L', 'Silvia León', '-'),
(108, 'Coma', NULL, '1957039', 'A538', 'L', 'Diana Cozar', '-'),
(109, 'Isis', '', '1443353', '838', 'L', 'Ana Alonso', 'GIII'),
(110, 'Otto', NULL, '91641', 'A070', 'L', 'Rafael García', '-'),
(111, 'Nana', '', '1781905', 'A225', 'L', 'Enrique Herbera', 'GII'),
(113, 'Assucar', 'Border Collie', '1594318', '992', 'L', 'Juan Carlos Redondo', 'GII'),
(114, 'Fantastico', NULL, '109814', 'A218', 'L', 'Pau Serrano Ciratusa', '-'),
(115, 'Panda', 'Border Collie', '1936233', 'A474', 'L', 'Lorena Díez', 'GII'),
(116, 'Raissa', 'American Stanford', '131570', 'A563', 'L', 'Javier Iniesta', 'GII'),
(119, 'Nuka', NULL, '127265', 'A486', 'L', 'Alex Sabini', '-'),
(120, 'Halcon', '', '126805', 'A482', 'L', 'Carlos Serra', 'GIII'),
(121, 'California', '', '1967653', 'A414', 'L', 'Jorge Arcas Perales', 'GII'),
(122, 'Astra', NULL, '1551831', '783', 'L', 'Alejandro Piñeiro', '-'),
(123, 'Fito', '', '1980971', 'A490', 'L', 'Silvia León', 'GII'),
(124, 'Magic-Black', NULL, '101184', 'A318', 'L', 'Raquel Garrido', '-'),
(125, 'Blacky', NULL, 'No tiene', '1493', 'L', 'Marina López', '-'),
(126, 'Red Magic', 'Border Collie', '1834052', 'A124', 'L', 'Carlos Martínez', 'GII'),
(127, 'Beauty', '', '120728', 'A452', 'L', 'Pau Serrano Ciratusa', 'GIII'),
(128, 'Savannah', NULL, '1627377', '833', 'L', 'Ana Ontañon', '-'),
(129, 'Ra', '', '2009048', 'A494', 'L', 'Manuel Santomé', 'GII'),
(130, 'Flay', NULL, '123186', 'A418', 'L', 'Gregorio Conde', '-'),
(131, 'Bu', NULL, '111416', 'A519', 'L', 'Rafael Torregrosa', '-'),
(132, 'Heidy', NULL, '1727593', 'A035', 'L', 'Sergio Martín', '-'),
(133, 'Kiko', NULL, '1780843', 'A145', 'L', 'Ernesto Sorribes', '-'),
(134, 'Koba', NULL, '1533440', '843', 'L', 'Imanol López', '-'),
(135, 'Kiwi', NULL, '1908098', 'A267', 'L', 'Imanol López', '-'),
(136, 'Yun', 'Border Collie', '2001179', 'A484', 'L', 'Concepción Fernández', 'GII'),
(137, 'Nora', NULL, '80601', '735', 'L', 'Silvia Rodríguez', '-'),
(139, 'Liss', NULL, '1779665', 'A212', 'L', 'David Parejo', '-'),
(141, 'Rayko', 'Border Collie', '2027590', 'A321', 'L', 'Paula de Lucas', 'GIII'),
(143, 'Nani', NULL, '1838888', 'A238', 'L', 'Judith Cortes', '-'),
(146, 'Rasca', '', '2047380', 'A564', 'L', 'Alberto Costas', 'GIII'),
(147, 'Abby', '', '2104382', 'A533', 'L', 'Jenny Funcke', '-'),
(150, 'Merlin', NULL, '1996593', 'A523', 'L', 'Diana García', '-'),
(151, 'Rusti', '', '1831356', 'A227', 'L', 'Francisco José Sousa', 'GII'),
(153, 'Cora', '', 'No tiene', '1525', 'L', 'Angel Insa', 'GII'),
(154, 'Dux', '', '1727100', 'A134', 'L', 'Francisco Sobral', 'GIII'),
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
(168, 'Nora', 'Braco', '113894', 'A356', 'L', 'Raúl Sánchez', 'GII'),
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
(193, 'Completa', NULL, '95707', 'A121', 'L', 'Pau Serrano Ciratusa', '-'),
(194, 'Jayna', '', '1779666', 'A173', 'L', 'José Antonio Pascual', 'GII'),
(197, 'Kira', '', '1710175', 'A116', 'L', 'Enrique Alonso Queija', 'GIII'),
(198, 'Elfo', NULL, '1496681', '963', 'L', 'José Soliño', '-'),
(199, 'Choco', NULL, '96941', 'A058', 'L', 'Carlos Alvarez', '-'),
(200, 'Gini', NULL, '1891331', 'A193', 'L', 'Katia Moeller', '-'),
(201, 'Alinka', NULL, '1677160', '982', 'L', 'Verónica Ibañez', '-'),
(202, 'Zazpi', NULL, '1779668', 'A278', 'L', 'David Parejo', '-'),
(203, 'Frasqui', NULL, '1962362', 'A438', 'L', 'Francisco Aguilera', '-'),
(204, 'Yhara', NULL, '1827258', 'A357', 'L', 'Judith Herms', '-'),
(209, 'Lennon', 'A. Foxhound', '111486', 'A336', 'L', 'Miguel Angel García', 'GII'),
(210, 'Irma', NULL, '1779789', 'A096', 'L', 'Elena Sin', '-'),
(212, 'Yai', NULL, '1879217', 'A475', 'L', 'Andrés Gimeno', '-'),
(215, 'Clara', 'Border Collie', '1936237', 'A462', 'L', 'Ruben Montero', 'GII'),
(216, 'Laika', NULL, '93988', 'A224', 'L', 'Raquel Frago', '-'),
(218, 'Argon', '', '1926695', '987', 'L', 'Concepción López', 'GII'),
(219, 'Nube', NULL, '124447', 'A465', 'L', 'José Manuel Basco', '-'),
(221, 'Blue', NULL, '1863534', 'A552', 'L', 'Katy Navarro', '-'),
(222, 'Hana', NULL, 'No tiene', '1526', 'L', 'Rocio Santos', '-'),
(223, 'Liss', NULL, '86748', '961', 'L', 'José Carlos Iglesias', '-'),
(224, 'Tara', NULL, '123013', 'A442', 'L', 'Jerónimo Martínez', '-'),
(227, 'Onis', '', '1478689', 'A498', 'L', 'José Antonio Vega', 'GII'),
(228, 'Qumba', NULL, '2007589', 'A546', 'L', 'Juan José Espadas', '-'),
(229, 'Jotave', NULL, '1962366', 'A407', 'L', 'Juan Manuel Caballo', '-'),
(230, 'Kora', NULL, '124215', 'A585', 'L', 'Juan Manuel Caballo', '-'),
(233, 'Zana', NULL, '1634512', 'A014', 'L', 'Sergio Martín', '-'),
(234, 'Hugo', NULL, '97357', 'A201', 'L', 'José Luis Sogorb', '-'),
(235, 'Gus', NULL, '111335', 'A548', 'L', 'Vicente Cambra', '-'),
(236, 'Paco', NULL, '85033', 'A550', 'L', 'Vicente Cambra', '-'),
(237, 'Thor', NULL, '1839333', 'A348', 'L', 'José Peris', '-'),
(238, 'Fol', '', '86747', '940', 'L', 'Miguel García Rodríguez', 'GIII'),
(239, 'Fito', NULL, '1914405', 'A362', 'L', 'Francisco José Sousa', '-'),
(240, 'Troy', '', '98617', 'A044', 'L', 'Nuria Morell Nadal', 'GII'),
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
(260, 'Pixie Moon', '', '1879218', 'A260', 'L', 'José Luis Sogorb', '-'),
(262, 'Timba', '', '119645', 'A403', 'L', 'Carmen Vázquez', 'GIII'),
(263, 'Nuwa', '', '132677', 'A597', 'L', 'Tamara Vidal', 'GII'),
(264, 'Kora', NULL, '99230', 'A466', 'L', 'Jesús Manuel Romero', '-'),
(265, 'Kona', NULL, '1986188', 'A524', 'L', 'Ankie Kleijberg', '-'),
(266, 'Arwen', NULL, '1753230', 'A151', 'L', 'Cristian Verde', '-'),
(267, 'Blas', NULL, '1761207', 'A086', 'L', 'José Miguel Paredes', '-'),
(268, 'Rudy', NULL, '102435', 'A113', 'L', 'Luis Ignacio Carazo', '-'),
(272, 'Ira', 'P.B.Malinoise', '101193', 'A141', 'L', 'Alberto Marugan', 'GII'),
(273, 'Terra', NULL, '114522', 'A328', 'L', 'Juan Francisco Torres', '-'),
(274, 'Kira', NULL, '120733', 'A405', 'L', 'Aida Al-Nehlawi', '-'),
(278, 'Nube', NULL, '97289', 'A515', 'L', 'José Luis Romero', '-'),
(279, 'Mara', NULL, '80787', 'A396', 'L', 'Ana Isabel Escobar', '-'),
(281, 'Trasto', NULL, 'No tiene', '1391', 'L', 'Adrian Díaz', '-'),
(286, 'Kiria', '', 'No tiene', 'A155', 'L', 'Jorge Valero', 'GII'),
(287, 'Wind', 'Border Collie', '1881696', 'A421', 'L', 'Ruben Jurado', 'GII'),
(291, 'Rocky', NULL, '1756734', 'A156', 'L', 'José Francisco Martorell', '-'),
(292, 'Pepo', NULL, '1756735', 'A200', 'L', 'José Francisco Martorell', '-'),
(293, 'Mina', NULL, '124583', 'A501', 'L', 'Javier Gómez', '-'),
(294, 'Gipsy', NULL, 'No tiene', '1542', 'L', 'Javier López', '-'),
(296, 'Atril', NULL, '1985153', 'A454', 'L', 'Marta Gregorio', '-'),
(297, 'Max', NULL, '1945286', 'A487', 'L', 'Andrés López', '-'),
(299, 'N''Hug', '', '110043', 'A283', 'L', 'José Castaño', 'GII'),
(301, 'Che', NULL, '1472786', 'A313', 'L', 'Juan Solanes', '-'),
(305, 'Chincheta', NULL, '97802', 'A165', 'L', 'Sonia Asensio', '-'),
(314, 'Nemo', NULL, '1950998', 'A478', 'L', 'Enrique Grau', '-'),
(316, 'Tao', NULL, '1807243', 'A554', 'L', 'Sara Montila', '-'),
(317, 'Luna', '', 'No tiene', '1539', 'L', 'Ricardo Martínez', 'GII'),
(322, 'Molsa', NULL, '1710467', 'A103', 'L', 'Sabina González', '-'),
(323, 'Dina', 'Border Collie', '1798019', 'A136', 'L', 'Javier Martínez', 'GII'),
(325, 'Neo', 'Border Collie', '99972', 'A077', 'L', 'Antonio López', 'GIII'),
(329, 'Zidane', NULL, '2081050', 'A591', 'L', 'Alberto Mudarra', '-'),
(332, 'Poly', NULL, '2060633', 'A587', 'L', 'José Luis Sogorb', '-'),
(333, 'Kinder', '', '101433', 'A223', 'L', 'Raquel Frago', 'GII'),
(334, 'Juno', NULL, 'No tiene', '1552', 'L', 'Arianna Bucci', '-'),
(335, 'Flash', NULL, '109519', 'A420', 'L', 'Gerard Barberá', '-'),
(337, 'Troya', NULL, '127255', 'A527', 'L', 'Xavier López', '-'),
(341, 'Pluto', NULL, '80602', '785', 'L', 'María López', '-'),
(342, 'Poli', NULL, '98654', 'A290', 'L', 'Silvia Rodríguez', '-'),
(345, 'Shasta', 'Border Collie', '109487', 'A272', 'L', 'Mario Rodríguez', 'GII'),
(349, 'Kira', NULL, '83038', '789', 'L', 'Manuel Jesús García', '-'),
(353, 'Maya', NULL, '1683945', 'A110', 'L', 'Juan Pedro Martínez', '-'),
(356, 'Grace', NULL, '127031', 'A575', 'L', 'Oscar Reboredo', '-'),
(358, 'Artemisa', NULL, '1911726', 'A406', 'L', 'Vanessa Hermoso', '-'),
(361, 'Ron', NULL, '94045', 'A265', 'L', 'Elena Sin', '-'),
(362, 'Maña', NULL, '1872301', 'A423', 'L', 'Carmen Briceño', '-'),
(368, 'Blue', NULL, '86743', 'A180', 'L', 'Diego Rouco', '-'),
(369, 'Golfa', NULL, '1719195', 'A557', 'L', 'Jacqueline Holemans', '-'),
(370, 'Runa', NULL, '2006235', 'A537', 'L', 'Lourdes Peñarrocha', '-'),
(371, 'Fanta', '', '1940535', 'A544', 'L', 'Natividad Soler', 'GII'),
(373, 'Xula', NULL, '83896', '844', 'M', 'Julia Faci Green', '-'),
(374, 'Danko', '', '113906', 'A325', 'M', 'Jorge Muñoz Leal', 'GIII'),
(375, 'Hanna', NULL, '1843070', 'A159', 'M', 'Nuria Fortuny', '-'),
(376, 'Guti', 'Cocker Spaniel', '111666', 'A215', 'M', 'Javier Ovejero', 'GIII'),
(377, 'Cala', NULL, '1691264', 'A095', 'M', 'Alberto Alonso', '-'),
(379, 'Milo', NULL, '1399802', '718', 'M', 'Juan Carlos Redondo', '-'),
(380, 'Gotika', NULL, '1682493', '971', 'M', 'Ariadna Soriano', '-'),
(381, 'Drac', '', '106327', 'A196', 'M', 'Elisenda Huidobro', 'GIII'),
(382, 'Neo', NULL, '109522', 'A445', 'M', 'Gerard Barberá', '-'),
(383, 'Sra. Maruja', NULL, '1695088', '997', 'M', 'Enrique Lleixa', '-'),
(384, 'Duna', '', '93084', '953', 'M', 'Francisco Esteban', 'GII'),
(385, 'Kiwi', '', '1982934', 'A525', 'M', 'Verónica Díez Gómez', 'GIII'),
(386, 'Sucre', NULL, '1558711', '938', 'M', 'Nuria Alonso', '-'),
(387, 'Lass', 'Perro de Aguas Español', '131981', 'A580', 'M', 'Eugenio Villares', 'GIII'),
(389, 'Tuna', NULL, '1731780', 'A057', 'M', 'Manuel Basco', '-'),
(390, 'Norai', NULL, '97039', 'A015', 'M', 'Fernando Benet', '-'),
(391, 'Menta', '', '1459964', '767', 'M', 'Albert Ulldemolins', 'GIII'),
(393, 'Wirbel', 'Schnnauzer', '1252941', '588', 'M', 'Mario Rodríguez', 'Ret.'),
(394, 'Pepsi', '', '1849505', 'A270', 'M', 'Carles Fortuny', 'GII'),
(395, 'Kiss', NULL, '1258632', '762', 'M', 'Daniel Amigo', '-'),
(396, 'Ina', NULL, 'No tiene', '1549', 'M', 'Ubaldo Delgado', '-'),
(397, 'Kenia', NULL, '86609', '954', 'M', 'Remedios Torres', '-'),
(398, 'Gamma', '', '1988761', 'A307', 'M', 'Jordi Boix', 'GIII'),
(399, 'Goku', 'Fox Terrier Wire ', 'No tiene', '1513', 'M', 'Juan Carlos Blas', 'GIII'),
(400, 'Coockie', NULL, '120745', 'A434', 'M', 'Toni Rios', '-'),
(401, 'Habana', NULL, '131393', 'A634', 'M', 'Estefanía Pérez', '-'),
(402, 'Dau', NULL, '113713', 'A446', 'M', 'Nuria Alonso', '-'),
(404, 'Jolie', 'Mudi', '1561798', '808', 'M', 'Celeste Zarzosa', 'GII'),
(406, 'Boira', '', '1809307', 'A281', 'M', 'Cristina García', 'GIII'),
(407, 'Queen', NULL, '93971', '989', 'M', 'Juan Luis Colmano', '-'),
(410, 'Legend', NULL, '1606337', '927', 'M', 'Sonia Conejero', '-'),
(412, 'Koku', NULL, '1394913', '714', 'M', 'Oria Micó', '-'),
(414, 'Luna', '', '103250', 'A240', 'M', 'Paula Rello', 'GII'),
(415, 'Nell', NULL, '1329279', 'A293', 'M', 'Eva Vázquez Morales', '-'),
(416, 'Lucas', 'Podenco Andaluz', '112275', 'A248', 'M', 'Alberto Pereda', 'GII'),
(417, 'Benavis', NULL, '1803327', 'A195', 'M', 'Alicia Mejias', '-'),
(419, 'Paquita', NULL, '1852290', 'A229', 'M', 'Pablo Ballesta', '-'),
(420, 'Fey', NULL, '2063400', 'A440', 'M', 'Stina Sandquist', '-'),
(422, 'Robbi', NULL, '104953', 'A146', 'M', 'Vanessa Calpe', '-'),
(424, 'Mate', 'Schnauzer', '1930526', 'A469', 'M', 'David Alique', 'GII'),
(425, 'Veron', '', '1423605', 'A412', 'M', 'Eva Vázquez Morales', 'GII'),
(426, 'Lume', NULL, '1985134', 'A416', 'M', 'José Angel Torres', '-'),
(429, 'Foska', NULL, '108199', 'A182', 'M', 'Juan Solanes', '-'),
(431, 'Bimba', NULL, '1968121', 'A451', 'M', 'Mercedes Fernández', '-'),
(432, 'Chocolate', NULL, '93486', 'A045', 'M', 'Sonia Asensio', '-'),
(433, 'Tuna', 'Bodeguero Andaliuz', '122113', 'A408', 'M', 'Jorge Muñoz Leal', 'GII'),
(434, 'Naru', NULL, '132678', 'A598', 'M', 'Tamara Vidal', '-'),
(437, 'Mizar', NULL, '1876817', 'A274', 'M', 'Enrique Sendra', '-'),
(439, 'Tina', NULL, 'No tiene', '1532', 'M', 'Jaime Gamir', '-'),
(440, 'Kenya', NULL, '112354', 'A233', 'M', 'Laura Carrasco', '-'),
(444, 'Duque', NULL, 'No tiene', '1551', 'M', 'Alicia Mejias', '-'),
(446, 'Harley', NULL, '2025374', 'A428', 'M', 'Efren Lucas', '-'),
(447, 'Johnny Cash', '', '2057033', 'A594', 'M', 'Vicente Micó', 'GIII'),
(452, 'Striptease', '', '2023589', 'A577', 'M', 'Carmen Alos', 'GII'),
(454, 'Tuco', NULL, '2026054', 'A419', 'M', 'Inmaculada Rubio', '-'),
(456, 'Mia', NULL, '121232', 'A547', 'M', 'Matias Monleón', '-'),
(457, 'Gunilla', NULL, 'No tiene', '1471', 'M', 'Luna Ramírez', '-'),
(463, 'Alma', NULL, '90313', 'A566', 'M', 'Lourdes Giménez', '-'),
(464, 'Noa', NULL, '1926077', 'A526', 'M', 'Pilar Rodríguez', '-'),
(465, 'Magia', '', '2032179', 'A528', 'S', 'Carmen Briceño', 'GIII'),
(466, 'Saroa', '', '1789456', 'A149', 'S', 'Yolanda Torres', 'GIII'),
(467, 'Melendi', '', '1842276', 'A164', 'S', 'Luis Luque', 'GIII'),
(468, 'Mims', NULL, '102903', 'A279', 'S', 'Agustin Centelles', '-'),
(469, 'Hancock', NULL, '131702', 'A609', 'S', 'Miguel Angel Soriano', '-'),
(473, 'Tris', NULL, '119441', 'A398', 'S', 'Rocio Hermelo', '-'),
(474, 'Lula', NULL, '123192', 'A607', 'S', 'David Sepulveda', '-'),
(475, 'Rufo', NULL, '123178', 'A435', 'S', 'José Peris', '-'),
(477, 'Sira', '', '106345', 'A168', 'S', 'David Ferrer', 'GIII'),
(478, 'Nit', '', '112007', 'A208', 'S', 'Montserrat Calvet', 'GIII'),
(479, 'Xira', '', '124731', 'A424', 'S', 'Sergio Ruiz', 'GIII'),
(482, 'Che Guevara', 'Caniche', '112448', 'A230', 'S', 'Yulia Morugova', 'GIII'),
(484, 'Enzo', NULL, '117909', 'A444', 'S', 'Miriam García', '-'),
(485, 'Nuca', 'Tibetan Spaniel', '109471', 'A181', 'S', 'Iván Pardo García', 'GIII'),
(487, 'Gismo', NULL, '123726', 'A449', 'S', 'Stefan Eggenschwiler', '-'),
(488, 'Nana', NULL, '103211', 'A277', 'S', 'Sara Montila', '-'),
(489, 'Nikita', '', '123006', 'A443', 'S', 'Ana Alonso', 'GIII'),
(490, 'Xena', '', '125524', 'A509', 'S', 'Enric García', 'GIII'),
(491, 'Chula', '', '106335', 'A135', 'S', 'Ismael Pérez', 'GIII'),
(492, 'Dagga', NULL, '127338', 'A507', 'S', 'Iago Sánchez', '-'),
(495, 'Greta', NULL, '1849716', 'A337', 'S', 'Barbara Flemming', '-'),
(496, 'Bengel', 'Schnnauzer', '1433208', '760', 'S', 'Mario Rodríguez', 'GII'),
(497, 'Nei', NULL, '1400011', '770', 'S', 'Isabel Rodríguez', '-'),
(498, 'Tess', NULL, '102439', 'A245', 'S', 'Jaume Fernández', '-'),
(500, 'Lia', 'Mestizo', '132000', 'A588', 'S', 'Irene Artacho', 'GII'),
(504, 'Taca', '', '128455', 'A589', 'S', 'Ana Mateu', 'GII'),
(505, 'Miche', '', '1706141', 'A097', 'S', 'Manuel Lara', 'GII'),
(506, 'Manin', NULL, '104120', 'A100', 'S', 'Rocio Hermelo', '-'),
(507, 'Doña Matilde', '', '2005359', 'A514', 'S', 'Enric Lleixa', 'GIII'),
(508, 'Aqua', NULL, '2056249', 'A513', 'S', 'Laura Chiva', '-'),
(512, 'Nuca', 'Schnauzer', '1678476', 'A088', 'S', 'Aaron Laro', 'GII'),
(514, 'Pepa', 'Jack Rusell', '1957259', 'A393', 'S', 'Francisco de la Cruz', 'GII'),
(515, 'Spyro', '', '131527', 'A569', 'S', 'Oscar Bravo', 'GIII'),
(516, 'Della', NULL, '2061744', 'A467', 'S', 'Daniel Amigo', '-'),
(518, 'Lua', NULL, '116884', 'A287', 'S', 'Juan Carlos Companys', '-'),
(519, 'Mayo', NULL, 'No tiene', '1419', 'S', 'Noelia Gimeno', '-'),
(520, 'Nala', NULL, '108635', 'A530', 'S', 'David Flix', '-'),
(522, 'Lola', NULL, 'No tiene', '1464', 'S', 'Javier Sanchis', '-'),
(525, 'Noah', NULL, '131506', 'A590', 'S', 'Jordi Gómez', '-'),
(526, 'Sully', NULL, '116639', 'A520', 'S', 'Rafael Torregrosa', '-'),
(531, 'Gus', NULL, '119626', 'A346', 'S', 'Antonio Carmona', '-'),
(534, 'Lola', NULL, 'No tiene', '1541', 'S', 'Lourdes Giménez', '-'),
(540, 'Thor', '', '1939205', 'A535', 'S', 'Aaron Laro', 'GII'),
(541, 'Quillo', 'Rusky Toy', '127443', 'A604', 'S', 'Cristina González', 'GII'),
(542, 'Lennon', NULL, '103239', 'A144', 'S', 'Mónica Saavedra', '-'),
(543, 'Boira', NULL, 'No tiene', '1554', 'S', 'Salvador Martí', '-'),
(544, 'Kyra', NULL, '131481', 'A600', 'S', 'Alicia Sanjurjo', '-'),
(556, 'Acha', NULL, '123731', 'A483', 'M', 'Mar Bermúdez', '-'),
(557, 'Ada', 'Mestizo', '', '1459', 'S', 'Ramón Arribas', 'GII'),
(558, 'Aker', 'P.B. Malinoise', '1553051', 'A397', 'L', 'Francisco Javier Jaen', 'GII'),
(559, 'Akira', NULL, '125877', 'A455', 'L', 'José Moreno', 'GII'),
(563, 'Dama', 'Fox Terrier Wire', '0131204', 'A641', 'M', 'Juan Antonio Martinez', 'GIII'),
(564, 'Flai', 'Fox Terrier Wire', '0129738', 'en tramite', 'M', 'Juan Antonio Martinez', 'GII'),
(567, 'Donna', 'Border Collie', '', '', 'L', 'Ricardo Benito', 'GII'),
(569, 'fito', 'mestizo', '', '', 'L', 'pepe', 'GIII'),
(571, 'paco', '', '', '', '-', '-- Sin asignar --', '-'),
(572, 'Akela', 'Border Collie', '', 'A746', 'L', 'Fernando Bibián', 'GII'),
(573, 'Toska', 'Border Collie', '', '', 'L', 'Jesús Gómez', 'GI'),
(574, 'Sira', 'P.B.Malinoise', '', 'A584', 'L', 'Joaquín Andrés', 'GII'),
(575, 'Duna', 'P. Aleman', '', 'A586', 'L', 'Vicente Martín', 'GII'),
(576, 'Olivia', 'Schnauzer', '', '', 'S', 'Judith Franco', 'GII'),
(577, 'Kyle', 'Schnauzer', '', 'A-539', 'M', 'Iván San Antonio', 'GII'),
(578, 'Kara', 'Border Collie', '', 'A-541', 'L', 'Ramón García', 'GII'),
(579, 'Tibet', 'Border Collie', '', '', 'L', 'Antonio Molina', 'GII'),
(580, 'Beltxa', 'Schnauzer', '', 'A-622', 'S', 'Sergio Ruiz', 'GII'),
(581, 'Yeni', 'Border Collie', '', '', 'L', 'José Luis Romero', 'GI'),
(582, 'Danah', 'P. Australiano', '', '', 'L', 'Juan Martín de las Blancas', 'GI'),
(583, 'Net', 'P. Australiano', '', '', 'L', 'Isidoro Vázquez', 'GI'),
(584, 'Sira', 'Boxer', '', '', 'L', 'David Escribano', 'GI'),
(585, 'Maggie', 'Mestizo', '', '1570', 'S', 'Raúl Sánchez', 'GII'),
(586, 'Kiss', '', '', '', 'L', 'Jara Pérez', 'GI'),
(587, 'Milka', '', '', '', 'L', 'Javier Martín', 'GI'),
(588, 'Mitzy', '', '', '', 'L', 'Angel González', 'GI'),
(589, 'Sura', '', '', '', 'L', 'Marta Jiménez', 'GI'),
(590, 'Queen', '', '', '', 'M', 'Angel González', 'GI'),
(591, 'Neo', '', '', '', 'L', 'Jesús Gómez', 'GII'),
(592, 'Sitan', '', '', '795', 'L', 'Joaquín Andrés', 'GII'),
(593, 'Kaiser', '', '', 'A383', 'S', 'Jara Pérez', 'GII'),
(594, 'Geha', '', '', 'A162', 'L', 'Luis Carlos Sanchez', 'GIII'),
(595, 'Momo', 'Borde Collie', '', '1593', 'L', 'Roberto Iñigo', 'GII'),
(597, 'Skay', '', '', '', 'L', 'Javier Santisteban', 'GII'),
(598, 'Keko', '', '', '', 'L', 'Alberto González', 'GI'),
(599, 'Mambo', 'Border Collie', '', '', 'L', 'Ana Palet', 'GI'),
(601, 'Swing', '', '', '', 'L', 'Olga Palomares', 'GI'),
(603, 'Trufa', '', '', '', 'L', 'Ross Rubio', 'GI'),
(605, 'Soma', 'Border Collie', '', 'A696', 'L', 'Menchu Melcom', 'GII'),
(606, 'Phoebe', 'Border Collie', '', 'A555', 'M', 'Estíbaliz Pereda Navarro', 'GII'),
(607, 'Izzie', 'West Higland White Terrier', '', 'A275', 'S', 'Estíbaliz Pereda Navarro', 'GII'),
(608, 'Kyra', 'Border Collie', '', '1607', 'L', 'Andres Morillas Sanjuan', 'GII'),
(610, 'Nika', 'Border Collie', '', '', 'L', 'Belén de Carvalho', 'GI'),
(611, 'Dolce', 'Border Collie', '', 'A681', 'L', 'Andres Morillas Sanjuan', 'GII'),
(612, 'Dolce', '', '', 'A723', 'L', 'Natividad Ruiz García', 'GII'),
(613, 'Gilda', 'Border Collie', '', 'A723', 'L', 'Natividad Ruiz García', 'GII'),
(614, 'Noa', 'Border Collie', '', 'A540', 'L', 'Pedro Delgado Fernandez', 'GII'),
(616, 'Thor', 'Border Collie', '', '', 'L', 'Juan Carlos Ruiz', 'GI'),
(618, 'Akira Haru', 'BorderCollie', '', 'A311', 'L', 'Gema López', 'GII'),
(619, 'Nala', 'BorderCollie', '', 'A142', 'L', 'Enrique Camarero', 'GIII'),
(620, 'Peka', '', '', 'A615', 'L', 'José Santos Luna', 'GII'),
(621, 'Milady', '', '', '', 'L', 'Ricardo Santolaya', 'GII'),
(622, 'Yashi', 'Pastor de los Pirineos', '', '', 'M', 'Verónica Rodríguez', 'GI'),
(623, 'Dollar', '', '', '911', 'L', 'Luis de Frías', 'GII'),
(625, ' Héctor', 'Pastor Vasco', '', '1597', 'L', 'David Gómez-Calcerrada', 'GII'),
(626, 'Ron', 'Border Collie', '', 'A617', 'L', 'Oscar Sacristan', 'GII'),
(627, 'Viconte', '', '', 'A367', 'L', 'Juan Rodríguez', 'GII'),
(628, 'Agran', 'Border Collie', '', 'A752', 'L', 'Adrián Soria', 'GII'),
(629, 'Bimba', 'Border Collie', '', 'A288', 'L', 'Luisa Fernanda Millan', 'GII'),
(630, 'Dudy', 'Border Collie', '', 'A753', 'L', 'Juan José González', 'GII'),
(631, 'Kember', 'Bóxer', '', '1558', 'L', 'Adrian Martínez', 'GII'),
(632, 'Luca', 'Ratonero Bodeguero Andaluz', '', 'A749', 'M', 'Cristina González', 'GII'),
(633, 'Kika', 'West Higland White Terrier', '', 'A722', 'S', 'Noelia Mouchet', 'GII'),
(634, 'Olivia', 'Caniche', '', 'A701', 'S', 'Cristina Pedraz', 'GII'),
(635, 'Sombra', 'Border Collie', '', 'A679', 'L', 'Jose Luis J. Mori', 'GII'),
(636, 'Noah', 'Schnauzer', '', '', 'S', 'Iván San Antonio', 'GI'),
(638, 'Brea', 'Mestizo', '', '', 'L', 'Almudena Novo', 'GI'),
(639, 'Lola', 'Jack Rusell', '', 'A633', 'S', 'Francisco de la Cruz', 'GII'),
(640, 'Yeny', 'Border Collie', '', 'A747', 'L', 'José Luis Romero', 'GII'),
(641, 'Sonic', 'Perro de Aguas Español', '', 'A495', 'L', 'Cristina Blanco', 'GII'),
(642, 'Wind', 'Border Collie', '', 'A561', 'L', 'Sergio Casalins', 'GII'),
(643, 'Crack', 'Border Collie', '', 'A719', 'L', 'Oscar Muñiz', 'GII'),
(644, 'Nitra', 'Caniche', '', 'A743', 'S', 'Yulia Morugova', 'GII'),
(645, 'Nova', 'Border Collie', '', 'A642', 'L', 'Roque Alonso', 'GII'),
(646, 'Momo', 'Border Collie', '', 'A621', 'L', 'Ana Baeza', 'GII'),
(647, 'Byron', 'Border Collie', '', '', 'L', 'Rodrigo González', 'GI'),
(648, 'Amis', 'Border Collie', '', 'A620', 'L', 'José Mahillo', 'GII'),
(649, 'Noa', 'BorderCollie', '', 'A143', 'L', 'Jenifer Tolín', 'GII'),
(650, 'Vlad', 'Perro de Aguas Español', '', 'A752', 'L', 'Angel Corroto', 'GII'),
(651, 'Mou', 'Kelpie Australiano', '', '', 'L', 'Carlos Pulpón', 'GI'),
(652, 'Black', 'Border Collie', '', '', 'L', 'Jose Ramón López', 'GI'),
(653, 'Kala', 'Yorkshire Terrier', '', '1574', 'S', 'Cristina Cortijo', 'GII'),
(654, 'Andy', 'Border Collie', '', 'A671', 'L', 'Maite Guerrero', 'GII'),
(655, 'beep', '', '', '', 'L', 'Rachel Stevens', 'GI'),
(656, 'Ella', '', '', '', 'L', 'Maitane Luengo', 'GI'),
(657, 'Nya', '', '', '', 'L', 'Ada Serrano', 'GI'),
(658, 'Tibet', '', '', '', 'L', 'Cristina Ruill', 'GI'),
(659, 'Kenzo', '', '', '', 'L', 'Wladimiro', 'GI'),
(660, 'Dafne', '', '', '', 'L', 'Juan Escos', 'GI'),
(661, 'Nut', '', '', '', 'L', 'Sandra Gracia', 'GI'),
(662, 'Morgan', '', '', '', 'L', 'Elisenda Huidobro', 'GI'),
(663, 'Juke', '', '', '', 'L', 'Marta Ponce', 'GI'),
(664, 'Argi', '', '', '', 'L', 'Estíbaliz Pujana', 'GI'),
(665, 'Pi', '', '', '', 'L', 'Francisco Sobral', 'GI'),
(666, 'Horatio', '', '', '', 'S', 'Beatriz Juan', 'GI'),
(667, 'Goldie', '', '', '', 'S', 'Paloma Faci Green', 'GI'),
(668, 'Cachirulo', '', '', 'A578', 'L', 'David Molina', 'GII'),
(669, 'Lucky', '', '', '1561', 'L', 'Alejandro Rodríguez Villalta', 'GII'),
(671, 'Chika', '', '', 'A518', 'L', 'Julio Freire', 'GII'),
(672, 'Inka', '', '', 'A699', 'L', 'Cristina García', 'GII'),
(673, 'Nupsi', '', '', '', 'L', 'Berit Kittel', 'GII'),
(674, 'Inka', '', '', 'A381', 'L', 'Pedro Martínez', 'GII'),
(675, 'Tiri', '', '', 'A782', 'L', 'José Luis García', 'GII'),
(677, 'Itoitz', '', '', 'A713', 'L', 'Alaitz Idarraga', 'GII'),
(678, 'Xira', '', '', '1499', 'L', 'Adrian Bajo', 'GII'),
(679, 'Gala', '', '', 'A500', 'L', 'Alex Olivera', 'GII'),
(680, 'Luna', '', '', '', 'L', 'Angel Rubio', 'GII'),
(681, 'Lorenzo', '', '', 'A666', 'L', 'Pilar Collado', 'GII'),
(682, 'Bombon', '', '', 'A773', 'L', 'Angeles Abad', 'GII'),
(683, 'Lluna', '', '', '1537', 'L', 'Angeles Abad', 'GII'),
(684, 'Ardi de Rioja', '', '', '0729', 'L', 'Jorge Arcas Perales', 'GII'),
(686, 'Danko', '', '', '1509', 'L', 'David Asenjo', 'GII'),
(687, 'Tessa', '', '', 'A317', 'L', 'Sergio Tella', 'GII'),
(688, 'Rex', '', '', '1578', 'L', 'Sofía Díaz', 'GII'),
(689, 'Chicaa', '', '', 'A703', 'L', 'Gabriel Gómez', 'GII'),
(690, 'Sue', '', '', 'A788', 'L', 'Silvia León', 'GII'),
(691, 'Kira', '', '', '', 'L', 'Charly Castañer', 'GII'),
(692, 'Heidy', '', '', 'A496', 'L', 'Juan Pablo Díaz', 'GII'),
(693, 'Bella', '', '', '1586', 'L', 'Lucía Romero', 'GII'),
(694, 'Hanna', '', '', '1584', 'L', 'Agustín González', 'GII'),
(695, 'Eo', '', '', 'A768', 'L', 'Manuel Santomé', 'GII'),
(696, 'Panda', '', '', '', 'L', 'Laura Monrabal', 'GII'),
(697, 'Venus', '', '', 'A743', 'L', 'Luis Luque', 'GII'),
(698, 'Charly', '', '', '1592', 'L', 'José Angel Beired', 'GII'),
(699, 'Argi', '', '', '1600', 'L', 'Irati Diego', 'GII'),
(700, 'Broto', '', '', 'A764', 'L', 'Sergio Romeo', 'GII'),
(701, 'Anouk', '', '', 'A480', 'L', 'Sheila Giménez', 'GII'),
(702, 'Flecha', '', '', 'A748', 'L', 'Sara Lara', 'GII'),
(703, 'Heidi', '', '', 'A035', 'L', 'Sergio Martín', 'GII'),
(704, 'Moa', '', '', 'A508', 'L', 'Francisco José Sousa', 'GII'),
(705, 'Greta', '', '', '', 'L', 'Jorge Bala', 'GII'),
(706, 'Lur', '', '', 'A741', 'L', 'Gorka Pozuelo', 'GII'),
(707, 'Charli', '', '', 'A655', 'L', 'Arabia Vidal', 'GII'),
(709, 'Lia', '', '', '1563', 'L', 'Lucía Montalbán', 'GII'),
(710, 'Nika', '', '', '1590', 'S', 'Fernando De La Fuente', 'GII'),
(711, 'Erinka', '', '', 'A761', 'S', 'Nuria Morell Nadal', 'GII'),
(712, 'Ursus', '', '', '', 'S', 'Iñaki García', 'GII'),
(713, 'Ela', '', '', 'A618', 'S', 'Neus Baró', 'GII'),
(714, 'Lua', '', '', 'A763', 'S', 'Antonio Santos', 'GII'),
(715, 'Wembley', '', '', 'A769', 'S', 'Carles Fortuny', 'GII'),
(716, 'Rikke', '', '', '', 'S', 'Antonio Tovar', 'GII'),
(717, 'Imo', '', '', 'A631', 'M', 'Jorge Bala', 'GII'),
(718, 'Alpargata', '', '', 'A606', 'M', 'Eva Vázquez Morales', 'GII'),
(719, 'Lucky Luque', '', '', 'A766', 'S', 'Israel Fernández', 'GII'),
(720, 'Salma', '', '', 'A521', 'S', 'Joan Castillo', 'GII'),
(721, 'Dracma', '', '', 'A626', 'S', 'Marta de la Rosa', 'GII'),
(722, 'Jade', '', '', 'A629', 'L', 'Jonathan Guillen', 'GIII'),
(723, 'Rinoa', '', '', 'A447', 'L', 'Marc Rabada', 'GIII'),
(724, 'Chus', '', '', '934', 'L', 'Jonathan Guillen', 'GIII'),
(725, 'Gala', '', '', 'A674', 'L', 'Eduardo Adán', 'GIII'),
(726, 'Chamán', '', '', 'A262', 'L', 'Pedro Martínez', 'GIII'),
(727, 'Koira', '', '', 'A252', 'L', 'Marco Maldonado', 'GIII'),
(728, 'Kobu', '', '', '1564', 'M', 'Irena', 'GIII'),
(729, 'Zeus', '', '', 'A139', 'M', 'Marc Rabada', 'GIII'),
(730, 'Gea', '', '', 'A175', 'M', 'Juan Carlos Hinojal', 'GIII'),
(731, 'Sacha', '', '', 'A632', 'M', 'Francisco Medina', 'GIII'),
(732, 'Fran', '', '', 'A028', 'M', 'David Molina', 'GIII'),
(733, 'Peka', '', '', 'A291', 'M', 'Julio Freire', 'GIII'),
(734, 'Time', '', '', 'A571', 'M', 'Ruben Jurado', 'GIII'),
(735, 'Alma', '', '', '874', 'M', 'Emilio José Pedrazuela', 'GIII'),
(736, 'Kira', '', '', 'A600', 'S', 'Alicia Sanjurjo', 'GIII'),
(737, 'Cleo', '', '', 'A152', 'S', 'Carmen Antequera', 'GIII'),
(738, 'Luna', '', '', '0956', 'S', 'Carmen Antequera', 'GIII'),
(739, 'Nika', '', '', 'A082', 'S', 'Marc Rabada', 'GII'),
(740, 'Quenn', 'Palleiro', '', '', 'M', 'Angel González', 'GI'),
(741, 'Gala', 'Pastor Alemán', '', '', 'L', 'Ana Valencia', 'GI'),
(742, 'Moli', 'Jack Rusell', '', '', 'S', 'Israel Díaz', 'GI'),
(743, 'Vali', 'Border Collie', '', '', 'L', 'Africa Cabañas', 'GI'),
(744, 'Isis', 'Border Collie', '', '', 'L', 'Antonio Fernández', 'GI'),
(745, 'Darco', 'Beagle', '', '', 'M', 'Angel Fernández', 'GI'),
(746, 'Bruce', 'Ratonero Bodeguero Andaluz', '', '', 'M', 'Jorge Muñoz Leal', 'GI'),
(747, 'Nut', 'Pastor Belga Malinoise', '', '', 'L', 'Francisco Javier Jaen', 'GI'),
(749, 'Lluna', 'Border Collie', '', '', 'L', 'Virginia García', 'GI'),
(750, 'Dunah', 'Cocker Spaniel', '', 'A635', 'M', 'Alfredo Ortíz', 'GII'),
(751, 'Aska', 'Border Collie', '', '', 'L', 'Luciano Fernández', 'GI'),
(752, 'Buck', 'Border Collie', '', '', 'L', 'Carlos Pérez', 'GI'),
(753, 'Merche', 'Schnauzer Gigante', '', '', 'L', 'Lourdes Rivera', 'GI'),
(754, 'Bambú', 'Border Collie', '', '', 'L', 'Jessica Graciano', 'GI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Provincias`
--
-- Creación: 24-02-2014 a las 07:55:22
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
-- Creación: 27-02-2014 a las 14:58:47
--

DROP TABLE IF EXISTS `Pruebas`;
CREATE TABLE IF NOT EXISTS `Pruebas` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Club` varchar(255) NOT NULL,
  `Ubicacion` varchar(255) DEFAULT NULL,
  `Triptico` longblob,
  `Cartel` longblob,
  `Observaciones` varchar(255) DEFAULT NULL,
  `Cerrada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Club` (`Club`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELACIONES PARA LA TABLA `Pruebas`:
--   `Club`
--       `Clubes` -> `Nombre`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Resultados`
--
-- Creación: 24-02-2014 a las 07:55:25
--

DROP TABLE IF EXISTS `Resultados`;
CREATE TABLE IF NOT EXISTS `Resultados` (
  `Manga` int(4) NOT NULL,
  `IDPerro` int(4) NOT NULL,
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
  PRIMARY KEY (`Manga`,`IDPerro`),
  KEY `Resultados_ibfk_1` (`IDPerro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELACIONES PARA LA TABLA `Resultados`:
--   `IDPerro`
--       `Perros` -> `IDPerro`
--   `Manga`
--       `Mangas` -> `ID`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tipo_Manga`
--
-- Creación: 24-02-2014 a las 07:55:25
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
-- Estructura para la vista `InscritosJornada`
--
DROP TABLE IF EXISTS `InscritosJornada`;
-- en uso(#1356 - View 'agility.InscritosJornada' references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them)

-- --------------------------------------------------------

--
-- Estructura para la vista `PerroGuiaClub`
--
DROP TABLE IF EXISTS `PerroGuiaClub`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `PerroGuiaClub` AS select `Perros`.`IDPerro` AS `IDPerro`,`Perros`.`Nombre` AS `Nombre`,`Perros`.`Raza` AS `Raza`,`Perros`.`Licencia` AS `Licencia`,`Perros`.`LOE_RRC` AS `LOE_RRC`,`Perros`.`Categoria` AS `Categoria`,`Perros`.`Grado` AS `Grado`,`Perros`.`Guia` AS `Guia`,`Guias`.`Club` AS `Club` from (`Perros` join `Guias`) where (`Perros`.`Guia` = `Guias`.`Nombre`) order by `Guias`.`Club`,`Perros`.`Categoria`,`Perros`.`Nombre`;

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
  ADD CONSTRAINT `Guias_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `Clubes` (`Nombre`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `Inscripciones`
--
ALTER TABLE `Inscripciones`
  ADD CONSTRAINT `Inscripciones_ibfk_1` FOREIGN KEY (`IDPerro`) REFERENCES `Perros` (`IDPerro`),
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
  ADD CONSTRAINT `Mangas_ibfk_3` FOREIGN KEY (`Juez1`) REFERENCES `Jueces` (`Nombre`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Mangas_ibfk_4` FOREIGN KEY (`Juez2`) REFERENCES `Jueces` (`Nombre`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Mangas_ibfk_5` FOREIGN KEY (`Jornada`) REFERENCES `Jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Perros`
--
ALTER TABLE `Perros`
  ADD CONSTRAINT `Perros_ibfk_1` FOREIGN KEY (`Categoria`) REFERENCES `Categorias_Perro` (`Categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Perros_ibfk_2` FOREIGN KEY (`Grado`) REFERENCES `Grados_Perro` (`Grado`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Perros_ibfk_3` FOREIGN KEY (`Guia`) REFERENCES `Guias` (`Nombre`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Pruebas`
--
ALTER TABLE `Pruebas`
  ADD CONSTRAINT `Pruebas_ibfk_1` FOREIGN KEY (`Club`) REFERENCES `Clubes` (`Nombre`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `Resultados`
--
ALTER TABLE `Resultados`
  ADD CONSTRAINT `Resultados_ibfk_1` FOREIGN KEY (`IDPerro`) REFERENCES `Perros` (`IDPerro`) ON DELETE CASCADE ON UPDATE CASCADE,
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
