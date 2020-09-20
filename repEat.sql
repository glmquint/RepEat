-- Progettazione Web 
DROP DATABASE if exists `repeat`; 
CREATE DATABASE `repeat`; 
USE `repeat`; 
-- MySQL dump 10.13  Distrib 5.6.20, for Win32 (x86)
--
-- Host: localhost    Database: repeat
-- ------------------------------------------------------
-- Server version	5.6.20

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
-- Table structure for table `composizionemenu`
--

DROP TABLE IF EXISTS `composizionemenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `composizionemenu` (
  `menu` int(11) unsigned NOT NULL,
  `piatto` int(11) unsigned NOT NULL,
  PRIMARY KEY (`menu`,`piatto`),
  KEY `piatto` (`piatto`),
  CONSTRAINT `composizionemenu_ibfk_1` FOREIGN KEY (`menu`) REFERENCES `menu` (`id_menu`),
  CONSTRAINT `composizionemenu_ibfk_2` FOREIGN KEY (`piatto`) REFERENCES `piatto` (`id_piatto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `composizionemenu`
--

LOCK TABLES `composizionemenu` WRITE;
/*!40000 ALTER TABLE `composizionemenu` DISABLE KEYS */;
INSERT INTO `composizionemenu` VALUES (1,1),(1,2),(1,3),(1,4),(1,5);
/*!40000 ALTER TABLE `composizionemenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conto`
--

DROP TABLE IF EXISTS `conto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conto` (
  `id_conto` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `valutazione` tinyint(4) DEFAULT NULL,
  `recensione` varchar(1024) DEFAULT NULL,
  `totale` float unsigned DEFAULT NULL,
  `tipo_pagamento` enum('carta','bancomat','contanti') DEFAULT NULL,
  `ts_primo_ordine` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ts_pagamento` timestamp NULL DEFAULT NULL,
  `tavolo` int(11) unsigned NOT NULL,
  `stanza` int(11) unsigned NOT NULL,
  `ristorante` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_conto`),
  KEY `tavolo` (`tavolo`),
  KEY `stanza` (`stanza`),
  KEY `ristorante` (`ristorante`),
  CONSTRAINT `conto_ibfk_1` FOREIGN KEY (`tavolo`) REFERENCES `tavolo` (`id_tavolo`),
  CONSTRAINT `conto_ibfk_2` FOREIGN KEY (`stanza`) REFERENCES `tavolo` (`stanza`),
  CONSTRAINT `conto_ibfk_3` FOREIGN KEY (`ristorante`) REFERENCES `tavolo` (`ristorante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conto`
--

LOCK TABLES `conto` WRITE;
/*!40000 ALTER TABLE `conto` DISABLE KEYS */;
/*!40000 ALTER TABLE `conto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `only_one_unpaid_check_per_table` BEFORE INSERT ON `Conto`
FOR EACH ROW
BEGIN
DECLARE var_conto int(11) UNSIGNED;
DECLARE msg_txt_ varchar(128);
SELECT id_conto FROM Conto WHERE ts_pagamento IS NULL AND ristorante = NEW.ristorante AND stanza = NEW.stanza AND tavolo = NEW.tavolo INTO var_conto;
IF !ISNULL(var_conto)
THEN 
	SELECT CONCAT('There\'s already an unpaid check for this table. check_id: ', var_conto) INTO msg_txt_;
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg_txt_;
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `calculate_check_total` BEFORE UPDATE ON `Conto`
FOR EACH ROW
BEGIN
SET NEW.totale = (SELECT SUM(P.prezzo * O.quantita) FROM Ordine O INNER JOIN Piatto P ON O.piatto = P.id_piatto WHERE O.conto = NEW.id_conto);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `reset_table_status` AFTER UPDATE ON `Conto`
FOR EACH ROW
BEGIN
IF NEW.ts_pagamento IS NOT NULL
THEN
UPDATE Tavolo SET stato = 'libero' WHERE ristorante = (SELECT ristorante FROM Conto WHERE id_conto = NEW.id_conto) 
									AND stanza = (SELECT stanza FROM Conto WHERE id_conto = NEW.id_conto)
                                    AND id_tavolo = (SELECT tavolo FROM Conto WHERE id_conto = NEW.id_conto);
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `licenza`
--

DROP TABLE IF EXISTS `licenza`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licenza` (
  `chiave` int(11) unsigned NOT NULL,
  `data_acquisto` date NOT NULL,
  `data_attivazione` date DEFAULT NULL,
  `active` bit(1) DEFAULT b'0',
  `livello` int(11) unsigned NOT NULL,
  PRIMARY KEY (`chiave`),
  KEY `livello` (`livello`),
  CONSTRAINT `licenza_ibfk_1` FOREIGN KEY (`livello`) REFERENCES `livello` (`id_livello`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licenza`
--

LOCK TABLES `licenza` WRITE;
/*!40000 ALTER TABLE `licenza` DISABLE KEYS */;
INSERT INTO `licenza` VALUES (1,'2020-07-10','2020-09-20','',1),(2,'2020-09-20','2020-09-20','',2),(3,'2020-09-20',NULL,'\0',3);
/*!40000 ALTER TABLE `licenza` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `livello`
--

DROP TABLE IF EXISTS `livello`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `livello` (
  `id_livello` int(11) unsigned NOT NULL,
  `max_dipendenti` tinyint(3) unsigned NOT NULL,
  `max_tavoli` tinyint(3) unsigned NOT NULL,
  `max_menu` tinyint(3) unsigned NOT NULL,
  `max_stanze` tinyint(3) unsigned NOT NULL,
  `durata_validita` int(11) unsigned NOT NULL,
  `prezzo` float unsigned NOT NULL,
  PRIMARY KEY (`id_livello`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `livello`
--

LOCK TABLES `livello` WRITE;
/*!40000 ALTER TABLE `livello` DISABLE KEYS */;
INSERT INTO `livello` VALUES (1,5,10,1,1,30,0),(2,15,30,2,3,365,0),(3,0,0,0,0,0,0);
/*!40000 ALTER TABLE `livello` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logtable`
--

DROP TABLE IF EXISTS `logtable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logtable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `info` text,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logtable`
--

LOCK TABLES `logtable` WRITE;
/*!40000 ALTER TABLE `logtable` DISABLE KEYS */;
/*!40000 ALTER TABLE `logtable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id_menu` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orarioInizio` time NOT NULL DEFAULT '00:00:00',
  `orarioFine` time NOT NULL DEFAULT '23:59:59',
  `ristorante` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_menu`),
  KEY `ristorante` (`ristorante`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`ristorante`) REFERENCES `ristorante` (`id_ristorante`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'06:00:00','23:59:00',1);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messaggio`
--

DROP TABLE IF EXISTS `messaggio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messaggio` (
  `id_msg` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_req` bit(1) NOT NULL DEFAULT b'0',
  `is_read` bit(1) NOT NULL DEFAULT b'0',
  `msg` text NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `from_user` int(11) unsigned NOT NULL,
  `to_user` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_msg`),
  KEY `from_user` (`from_user`),
  KEY `to_user` (`to_user`),
  CONSTRAINT `messaggio_ibfk_1` FOREIGN KEY (`from_user`) REFERENCES `utente` (`id_utente`),
  CONSTRAINT `messaggio_ibfk_2` FOREIGN KEY (`to_user`) REFERENCES `utente` (`id_utente`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messaggio`
--

LOCK TABLES `messaggio` WRITE;
/*!40000 ALTER TABLE `messaggio` DISABLE KEYS */;
INSERT INTO `messaggio` VALUES (1,'','','Ciao! Sono topolino','2020-09-20 17:20:36',2,1),(2,'','\0','Hey! Accetti la mia richiesta? Sono Paperino','2020-09-20 17:17:57',3,1),(3,'','','Posso entrare a far parte dello staff? Sono Paperone','2020-09-20 17:21:34',4,1),(4,'\0','\0','Ciao topolino, benvenuto!','2020-09-20 17:20:55',1,2),(5,'\0','\0','Ma certo! Benvenuto!!','2020-09-20 17:21:51',1,4);
/*!40000 ALTER TABLE `messaggio` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `only_chat_to_cowerkers` BEFORE INSERT ON `Messaggio`
FOR EACH ROW
BEGIN

IF !NEW.is_req AND
	(SELECT IF(U1.ristorante=U2.ristorante, 1, NULL)
	FROM (SELECT V1.ristorante
			FROM Utente V1
			WHERE V1.id_utente = NEW.from_user) U1 INNER JOIN 
		(SELECT V2.ristorante
			FROM Utente V2
			WHERE V2.id_utente = NEW.to_user) U2 ON U1.ristorante = U1.ristorante) IS NULL
THEN 
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'You can only send messages to people within the same restaurant';
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `ordine`
--

DROP TABLE IF EXISTS `ordine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ordine` (
  `id_ordine` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `note` varchar(255) DEFAULT NULL,
  `quantita` float unsigned NOT NULL DEFAULT '1',
  `ts_ordine` timestamp NULL DEFAULT NULL,
  `ts_preparazione` timestamp NULL DEFAULT NULL,
  `ts_consegna` timestamp NULL DEFAULT NULL,
  `conto` int(11) unsigned NOT NULL,
  `piatto` int(11) unsigned NOT NULL,
  `utente_ordine` int(11) unsigned NOT NULL,
  `utente_preparazione` int(11) unsigned DEFAULT NULL,
  `utente_consegna` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_ordine`),
  KEY `conto` (`conto`),
  KEY `piatto` (`piatto`),
  KEY `utente_ordine` (`utente_ordine`),
  KEY `utente_preparazione` (`utente_preparazione`),
  KEY `utente_consegna` (`utente_consegna`),
  CONSTRAINT `ordine_ibfk_1` FOREIGN KEY (`conto`) REFERENCES `conto` (`id_conto`),
  CONSTRAINT `ordine_ibfk_2` FOREIGN KEY (`piatto`) REFERENCES `piatto` (`id_piatto`),
  CONSTRAINT `ordine_ibfk_3` FOREIGN KEY (`utente_ordine`) REFERENCES `utente` (`id_utente`),
  CONSTRAINT `ordine_ibfk_4` FOREIGN KEY (`utente_preparazione`) REFERENCES `utente` (`id_utente`),
  CONSTRAINT `ordine_ibfk_5` FOREIGN KEY (`utente_consegna`) REFERENCES `utente` (`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordine`
--

LOCK TABLES `ordine` WRITE;
/*!40000 ALTER TABLE `ordine` DISABLE KEYS */;
/*!40000 ALTER TABLE `ordine` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `upd_table_status` AFTER UPDATE ON `Ordine`
FOR EACH ROW
BEGIN

IF NOT EXISTS (SELECT * 
				FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto 
				WHERE C.id_conto = NEW.conto
					AND O.ts_preparazione IS NULL
                    AND O.ts_consegna IS NULL)
THEN
UPDATE Tavolo SET stato = 'pronto' WHERE ristorante = (SELECT ristorante FROM Conto WHERE id_conto = NEW.conto) 
									AND stanza = (SELECT stanza FROM Conto WHERE id_conto = NEW.conto)
                                    AND id_tavolo = (SELECT tavolo FROM Conto WHERE id_conto = NEW.conto);
	IF NOT EXISTS (SELECT * 
					FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto
					WHERE C.id_conto = NEW.conto
						AND O.ts_preparazione IS NOT NULL
						AND O.ts_consegna IS NULL)
	THEN
	UPDATE Tavolo SET stato = 'servito' WHERE ristorante = (SELECT ristorante FROM Conto WHERE id_conto = NEW.conto) 
										AND stanza = (SELECT stanza FROM Conto WHERE id_conto = NEW.conto)
										AND id_tavolo = (SELECT tavolo FROM Conto WHERE id_conto = NEW.conto);
	END IF;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `piatto`
--

DROP TABLE IF EXISTS `piatto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `piatto` (
  `id_piatto` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(32) NOT NULL DEFAULT 'senza-nome',
  `categoria` varchar(32) NOT NULL DEFAULT '',
  `prezzo` float unsigned NOT NULL DEFAULT '0',
  `ingredienti` varchar(255) DEFAULT NULL,
  `allergeni` set('pesce','molluschi','latticini','glutine','frutta a guscio','crostacei','arachidi','lupini','uova','solfiti','soia','sesamo','senape','sedano','piccante','surgelato') DEFAULT NULL,
  `ristorante` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_piatto`),
  KEY `ristorante` (`ristorante`),
  CONSTRAINT `piatto_ibfk_1` FOREIGN KEY (`ristorante`) REFERENCES `ristorante` (`id_ristorante`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `piatto`
--

LOCK TABLES `piatto` WRITE;
/*!40000 ALTER TABLE `piatto` DISABLE KEYS */;
INSERT INTO `piatto` VALUES (1,'Pasta al pomodoro','primi',4.75,'Pasta fresca con salsa di pomodoro e basilico','glutine',1),(2,'Bistecca','secondi',8,'tenera carne di bovini di primissima qualitÃ ','',1),(3,'Birra','bevande',1.5,'Niente acqua, fa ruggine','',1),(4,'Spaghetti allo scoglio','primi',5.25,'Spaghetti con gustosi frutti di mare misti molto gustosi','pesce,glutine,crostacei,surgelato',1),(5,'Insalata verde','secondi',3.5,'Un piatto fresco e appetitoso','frutta a guscio,uova,soia,sesamo,senape,sedano',1);
/*!40000 ALTER TABLE `piatto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ristorante`
--

DROP TABLE IF EXISTS `ristorante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ristorante` (
  `id_ristorante` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome_ristorante` varchar(255) NOT NULL,
  `indirizzo` varchar(255) NOT NULL,
  `limite_consegna_ordine` tinyint(3) unsigned DEFAULT '15',
  `license_key` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_ristorante`),
  UNIQUE KEY `nomeRistorante_UNIQUE` (`nome_ristorante`),
  UNIQUE KEY `indirizzo_UNIQUE` (`indirizzo`),
  KEY `license_key` (`license_key`),
  CONSTRAINT `ristorante_ibfk_1` FOREIGN KEY (`license_key`) REFERENCES `licenza` (`chiave`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ristorante`
--

LOCK TABLES `ristorante` WRITE;
/*!40000 ALTER TABLE `ristorante` DISABLE KEYS */;
INSERT INTO `ristorante` VALUES (1,'Il Gambero Rosso','piazza dei Miracoli, 7',15,2);
/*!40000 ALTER TABLE `ristorante` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `activate_key_ins` BEFORE INSERT ON `Ristorante`
FOR EACH ROW
BEGIN
IF EXISTS (SELECT chiave FROM Licenza WHERE chiave = NEW.license_key AND active = 0) THEN
UPDATE `Licenza` SET active = 1, data_attivazione = CURRENT_DATE  WHERE chiave = NEW.license_key;
ELSE
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The key is invalid or inactive';
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `activate_key_upd` BEFORE UPDATE ON `Ristorante`
FOR EACH ROW
BEGIN
IF (SELECT license_key FROM Ristorante WHERE id_ristorante = NEW.id_ristorante) = New.license_key
	OR EXISTS (SELECT chiave FROM Licenza WHERE chiave = NEW.license_key AND active = 0) THEN
UPDATE `Licenza` SET active = 1, data_attivazione = CURRENT_DATE  WHERE chiave = NEW.license_key;
ELSE
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The key is invalid or inactive';
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `stanza`
--

DROP TABLE IF EXISTS `stanza`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stanza` (
  `id_stanza` int(11) unsigned NOT NULL,
  `nome_stanza` varchar(32) NOT NULL DEFAULT 'senza-nome',
  `ristorante` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_stanza`,`ristorante`),
  KEY `ristorante` (`ristorante`),
  CONSTRAINT `stanza_ibfk_1` FOREIGN KEY (`ristorante`) REFERENCES `ristorante` (`id_ristorante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stanza`
--

LOCK TABLES `stanza` WRITE;
/*!40000 ALTER TABLE `stanza` DISABLE KEYS */;
INSERT INTO `stanza` VALUES (0,'Stanza Principale',1),(1,'Esterno',1),(2,'senza-nome',1);
/*!40000 ALTER TABLE `stanza` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `manual_autoincrement_room_and_check_limit_room` BEFORE INSERT ON `Stanza`
FOR EACH ROW
BEGIN
DECLARE msg_txt_ varchar(128);
SET NEW.id_stanza = (SELECT IFNULL((SELECT MAX(id_stanza)+1 FROM Stanza WHERE ristorante = NEW.ristorante), 0));
IF NOT (SELECT IF(Lv.max_stanze = 0, 1, COUNT(S.id_stanza) < Lv.max_stanze)
	FROM Ristorante R INNER JOIN Licenza L ON R.license_key = L.chiave INNER JOIN Livello Lv ON L.livello = Lv.id_livello INNER JOIN Stanza S ON R.id_ristorante = S.ristorante
	WHERE R.id_ristorante = NEW.ristorante)
THEN 
	SELECT CONCAT('Limite stanze raggiunto. Aggiornare la licenza in uso per aggiungere piu\' stanze') INTO msg_txt_;
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg_txt_;
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `tavolo`
--

DROP TABLE IF EXISTS `tavolo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tavolo` (
  `id_tavolo` int(11) unsigned NOT NULL,
  `percentX` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `percentY` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stato` enum('libero','ordinato','pronto','servito') NOT NULL DEFAULT 'libero',
  `stanza` int(11) unsigned NOT NULL,
  `ristorante` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_tavolo`,`stanza`,`ristorante`),
  KEY `stanza` (`stanza`),
  KEY `ristorante` (`ristorante`),
  CONSTRAINT `tavolo_ibfk_1` FOREIGN KEY (`stanza`) REFERENCES `stanza` (`id_stanza`),
  CONSTRAINT `tavolo_ibfk_2` FOREIGN KEY (`ristorante`) REFERENCES `stanza` (`ristorante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tavolo`
--

LOCK TABLES `tavolo` WRITE;
/*!40000 ALTER TABLE `tavolo` DISABLE KEYS */;
INSERT INTO `tavolo` VALUES (0,25,33,'libero',0,1),(0,25,25,'libero',1,1),(0,15,15,'libero',2,1),(1,50,33,'libero',0,1),(1,75,25,'libero',1,1),(1,15,35,'libero',2,1),(2,75,33,'libero',0,1),(2,30,55,'libero',1,1),(2,85,15,'libero',2,1),(3,25,66,'libero',0,1),(3,50,60,'libero',1,1),(3,85,35,'libero',2,1),(4,50,66,'libero',0,1),(4,70,55,'libero',1,1),(4,40,75,'libero',2,1),(5,75,66,'libero',0,1),(5,60,75,'libero',2,1);
/*!40000 ALTER TABLE `tavolo` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `manual_autoincrement_table_and_check_limit_table` BEFORE INSERT ON `Tavolo`
FOR EACH ROW
BEGIN
DECLARE msg_txt_ varchar(128);
SET NEW.id_tavolo = (SELECT IFNULL((SELECT MAX(id_tavolo)+1 FROM Tavolo WHERE ristorante = NEW.ristorante AND stanza = NEW.stanza), 0));
IF NOT (SELECT IF(Lv.max_tavoli = 0, 1, COUNT(T.id_tavolo) < Lv.max_tavoli)
	FROM Ristorante R INNER JOIN Licenza L ON R.license_key = L.chiave INNER JOIN Livello Lv ON L.livello = Lv.id_livello INNER JOIN Tavolo T ON R.id_ristorante = T.ristorante
	WHERE R.id_ristorante = NEW.ristorante)
THEN 
	SELECT CONCAT('Limite tavoli raggiunto. Aggiornare la licenza in uso per aggiungere piu\' tavoli') INTO msg_txt_;
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg_txt_;
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `utente`
--

DROP TABLE IF EXISTS `utente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utente` (
  `id_utente` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pref_theme` enum('light','dark') DEFAULT 'light',
  `privilegi` bit(4) DEFAULT b'0',
  `ristorante` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_utente`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`mail`),
  KEY `ristorante` (`ristorante`),
  CONSTRAINT `utente_ibfk_1` FOREIGN KEY (`ristorante`) REFERENCES `ristorante` (`id_ristorante`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utente`
--

LOCK TABLES `utente` WRITE;
/*!40000 ALTER TABLE `utente` DISABLE KEYS */;
INSERT INTO `utente` VALUES (1,'pippo','pippo@mail.com','$2y$10$ivVxNaue8pn.wevMnuDj9Of3FArJ.ATy5OejIGsP9/PvK0b.jT4Uu','dark','',1),             -- pwd: pippo
                            (2,'topolino','topolino@mail.com','$2y$10$0dVjnPO/qAFhQhPTcNlhr.K2YNhLTXTx1Rqqp3aE9qwF0PiHVU8Lm','light','',1),      -- pwd: Topolino1234
                            (3,'paperino','paperino@mail.com','$2y$10$tPz6W0io/h5X9t4gnWBI5uEBMO78./fQy1Q/qVoGYo2IfIHg8YSIi','light','\0',NULL),  -- pwd: Paperino1234
                            (4,'paperone','paperone@mail.com','$2y$10$7ImH6u88CacSpkIIN.EHXeFTPXLJaIyXf4cpLq042aMoDnGrZBBnO','light','',1);      -- pwd: Paperone1234
/*!40000 ALTER TABLE `utente` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-09-20 19:53:23
