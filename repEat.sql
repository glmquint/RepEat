DROP DATABASE `repEat`;
CREATE DATABASE  IF NOT EXISTS `repEat`;
USE `repEat`;
/*----For debugging purpose-----*/

DROP TABLE IF EXISTS  logtable ;
CREATE TABLE  logtable
(
	id	INT AUTO_INCREMENT,
	info	TEXT(1024),
    ts TIMESTAMP,
	primary key (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP PROCEDURE IF EXISTS LOG;
DELIMITER $$
CREATE PROCEDURE LOG(IN _msg TEXT(1024))
BEGIN
	INSERT INTO logtable (info, ts) VALUES (_msg, current_timestamp());
    
END $$
DELIMITER ;


--
-- Creazione database
--

DROP TABLE IF EXISTS `Livello`;
CREATE TABLE `Livello` (
  `id_livello` int(11) UNSIGNED NOT NULL,
  `max_dipendenti` tinyint UNSIGNED NOT NULL,
  `max_tavoli` tinyint UNSIGNED NOT NULL,
  `max_menu` tinyint UNSIGNED NOT NULL,
  `max_stanze` tinyint UNSIGNED NOT NULL,
  `durata_validita` int(11) UNSIGNED NOT NULL, -- giorni di validità dall'attivazione (0 = inf)
  `prezzo` float UNSIGNED NOT NULL,
  PRIMARY KEY (`id_livello`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Licenza`;
CREATE TABLE `Licenza` (
  `chiave` int(11) UNSIGNED NOT NULL,
  `data_acquisto` DATE NOT NULL,
  `data_attivazione` DATE DEFAULT NULL,
  `active` bit(1) DEFAULT 0,
  `livello` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY(`chiave`),
  foreign key(`livello`) references Livello(`id_livello`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP PROCEDURE IF EXISTS `generateKey`;
DELIMITER $$
CREATE PROCEDURE `generateKey`
	(IN _level int(11) UNSIGNED)
BEGIN
DECLARE rand_key int(11) UNSIGNED;
SELECT FLOOR(RAND()*4294967295) INTO rand_key;
INSERT INTO Licenza (chiave, data_acquisto, livello) VALUE (rand_key, CURRENT_DATE, _level); 
SELECT rand_key;
END $$
DELIMITER ;

DROP TABLE IF EXISTS `Ristorante`;
CREATE TABLE `Ristorante` (
  `id_ristorante` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_ristorante` varchar(255) NOT NULL,
  `indirizzo` varchar(255) NOT NULL,
  `limite_consegna_ordine` tinyint UNSIGNED DEFAULT 15, -- minuti prima che un ordine sia considerato definitivamente in ritardo
  `license_key` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_ristorante`),
  foreign key (`license_key`) references Licenza(`chiave`),
  UNIQUE KEY `nomeRistorante_UNIQUE` (`nome_ristorante`),
  UNIQUE KEY `indirizzo_UNIQUE` (`indirizzo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DELIMITER $$
CREATE TRIGGER `activate_key_ins` BEFORE INSERT ON `Ristorante`
FOR EACH ROW
BEGIN
IF EXISTS (SELECT chiave FROM Licenza WHERE chiave = NEW.license_key AND active = 0) THEN
UPDATE `Licenza` SET active = 1, data_attivazione = CURRENT_DATE  WHERE chiave = NEW.license_key;
ELSE
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The key is invalid or inactive';
END IF;
END $$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `activate_key_upd` BEFORE UPDATE ON `Ristorante`
FOR EACH ROW
BEGIN
IF EXISTS (SELECT chiave FROM Licenza WHERE chiave = NEW.license_key AND active = 0) THEN
UPDATE `Licenza` SET active = 1, data_attivazione = CURRENT_DATE  WHERE chiave = NEW.license_key;
ELSE
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The key is invalid or inactive';
END IF;
END $$
DELIMITER ;


DROP TABLE IF EXISTS `Utente`;
CREATE TABLE `Utente` (
  `id_utente` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,/*
  `name` varchar(32) NOT NULL,
  `surname` varchar(32) NOT NULL,*/
  `pref_theme` enum('light', 'dark') DEFAULT 'light',
  `privilegi` bit(3) DEFAULT NULL, -- ispirato ad UNIX (b000 è amministratore, b111 è visibilità completa), NULL è assenza di privilegi
								-- bit_cassa (b1xx) - bit_cucina (bx1x) - bit_cameriere (bxx1)
  `ristorante` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_utente`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`mail`),
  foreign key(`ristorante`) references Ristorante(`id_ristorante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP PROCEDURE IF EXISTS `registerRestaurant`;
DELIMITER $$
CREATE PROCEDURE `registerRestaurant`
	(/* il ristorante */
    IN _nome_ristorante varchar(255), 
    IN _indirizzo varchar(255),
    IN _license_key int(11) UNSIGNED,
    /* l'amministratore creatore */
    IN _user int(11) UNSIGNED)
BEGIN
DECLARE new_restaurant int(11) UNSIGNED;
INSERT INTO Ristorante (nome_ristorante, indirizzo, license_key) VALUE (_nome_ristorante, _indirizzo, _license_key);
SELECT last_insert_id() INTO new_restaurant;
UPDATE Utente SET ristorante = new_restaurant, privilegi = 0 WHERE id_utente = _user;
SELECT new_restaurant;
END $$
DELIMITER ;

DROP TABLE IF EXISTS `Messaggio`;
CREATE TABLE `Messaggio` (
  `id_msg` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_req` bit(1) NOT NULL DEFAULT 0,
  `is_read` bit(1) NOT NULL DEFAULT 0,
  `msg` text NOT NULL,
  `ts` timestamp NOT NULL,
  `from_user` int(11) UNSIGNED NOT NULL,
  `to_user` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_msg`),
  foreign key(`from_user`) references Utente(`id_utente`),
  foreign key(`to_user`) references Utente(`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DELIMITER $$
CREATE TRIGGER `only_chat_to_cowerkers` BEFORE INSERT ON `Messaggio`
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
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `readMessages`;
DELIMITER $$
CREATE PROCEDURE `readMessages`
	(/* la chat */
    IN _user int(11) UNSIGNED, 
    IN _other int(11) UNSIGNED)
BEGIN
UPDATE Messaggio SET is_read = 1 WHERE from_user = _other AND to_user = _user AND is_read = 0 AND is_req = 0;
SELECT *, (SELECT username FROM Utente WHERE id_utente = _other) AS other_name FROM Messaggio WHERE (from_user = _other AND to_user = _user) OR (from_user = _user AND to_user = _other);
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `sendRequest`;
DELIMITER $$
CREATE PROCEDURE `sendRequest`
	(IN _user int(11) UNSIGNED, 
    IN _ristorante int(11) UNSIGNED,
    IN _msg text)
BEGIN
	DECLARE finito INTEGER DEFAULT 0;
    DECLARE this_admin int(11) UNSIGNED;
	DECLARE cursore CURSOR FOR
		SELECT id_utente
        FROM Utente
        WHERE privilegi = 0
			AND ristorante = _ristorante;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET finito = 1;
	OPEN cursore;

	preleva: LOOP
		FETCH cursore INTO this_admin;
		IF finito = 1 THEN
			LEAVE preleva;
		END IF;
        INSERT INTO Messaggio (from_user, to_user, msg, is_req, ts) VALUE (_user, this_admin, _msg, 1, CURRENT_TIMESTAMP);
        SELECT last_insert_id();
	END LOOP preleva;
	CLOSE cursore;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `processRequest`;
DELIMITER $$
CREATE PROCEDURE `processRequest`
	(IN _req int(11) UNSIGNED,
    IN _accepted bit(1))
BEGIN
	IF _accepted THEN
	UPDATE Utente SET ristorante = (SELECT * FROM (SELECT ristorante FROM Utente WHERE id_utente = (SELECT to_user FROM Messaggio WHERE id_msg = _req)) tmp) 
		WHERE id_utente = (SELECT from_user FROM Messaggio WHERE id_msg = _req);
	END IF;
	UPDATE Messaggio SET is_read = 1 WHERE id_msg = _req AND is_read = 0 AND is_req = 1;
END $$
DELIMITER ;


DROP TABLE IF EXISTS `Stanza`;
CREATE TABLE `Stanza` (
  `id_stanza` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, -- TODO: make manual auto-increment per restaurant 
  `nome_stanza` varchar(32) NOT NULL,
  `ristorante` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_stanza`, `ristorante`),
  foreign key(`ristorante`) references Ristorante(`id_ristorante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Tavolo`;
CREATE TABLE `Tavolo` (
  `id_tavolo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, -- TODO: make manual auto-increment per room per restaurant 
  `percentX` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `percentY` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `stato` enum('libero', 'ordinato', 'pronto', 'servito') NOT NULL DEFAULT 'libero',
  `stanza` int(11) UNSIGNED NOT NULL,
  `ristorante` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_tavolo`, `stanza`,`ristorante`),
  foreign key(`stanza`) references Stanza(`id_stanza`),
  foreign key(`ristorante`) references Stanza(`ristorante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Menu`;
CREATE TABLE `Menu` (
  `id_menu` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `orarioInizio` TIME DEFAULT NULL,
  `orarioFine` TIME DEFAULT NULL,
  `ristorante` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_menu`),
  foreign key(`ristorante`) references Ristorante(`id_ristorante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Piatto`;
CREATE TABLE `Piatto` (
  `id_piatto` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(32) NOT NULL,
  `categoria` varchar(32) NOT NULL,
  `prezzo` float UNSIGNED NOT NULL, -- per porzione o al kg
  `ingredienti` varchar(255) DEFAULT NULL,
  `allergeni` set('pesce', 'molluschi', 'latticini', 'glutine', 
					'frutta a guscio', 'crostacei', 'arachidi', 
                    'lupini', 'uova', 'solfiti', 'soia', 'sesamo', 
                    'senape', 'sedano', 'piccante', 'surgelato') DEFAULT NULL, 
  `ristorante` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_piatto`),
  foreign key(`ristorante`) references Ristorante(`id_ristorante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `ComposizioneMenu`;
CREATE TABLE `composizioneMenu` (
  `menu` int(11) UNSIGNED NOT NULL,
  `piatto` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`menu`, `piatto`),
  foreign key(`menu`) references Menu(`id_menu`),
  foreign key(`piatto`) references Piatto(`id_piatto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Conto`;
CREATE TABLE `Conto` (
  `id_conto` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `valutazione` tinyint DEFAULT NULL,
  `recensione` varchar(1024) DEFAULT NULL,
  `totale` float UNSIGNED,
  `tipo_pagamento` enum('carta', 'bancomat', 'contanti'),
  `ts_primo_ordine` timestamp DEFAULT CURRENT_TIMESTAMP, -- not a star wars reference
  `ts_pagamento` timestamp NULL DEFAULT NULL,
  `tavolo` int(11) UNSIGNED NOT NULL,
  `stanza` int(11) UNSIGNED NOT NULL,
  `ristorante` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_conto`),
  foreign key(`tavolo`) references Tavolo(`id_tavolo`),
  foreign key(`stanza`) references Tavolo(`stanza`),
  foreign key(`ristorante`) references Tavolo(`ristorante`)

) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DELIMITER $$
CREATE TRIGGER `only_one_unpaid_check_per_table` BEFORE INSERT ON `Conto`
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
END $$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `calculate_check_total` BEFORE UPDATE ON `Conto`
FOR EACH ROW
BEGIN
SET NEW.totale = (SELECT SUM(P.prezzo * O.quantita) FROM Ordine O INNER JOIN Piatto P ON O.piatto = P.id_piatto WHERE O.conto = NEW.id_conto);
END $$
DELIMITER ;

DROP TABLE IF EXISTS `Ordine`;
CREATE TABLE `Ordine` (
  `id_ordine` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `note` varchar(255) DEFAULT NULL,
  `quantita` float UNSIGNED NOT NULL DEFAULT 1,
  `ts_ordine` timestamp NOT NULL,
  `ts_preparazione` timestamp NULL DEFAULT NULL,
  `ts_consegna` timestamp NULL DEFAULT NULL,
  `conto` int(11) UNSIGNED NOT NULL,
  `piatto` int(11) UNSIGNED NOT NULL,
  `utente_ordine` int(11) UNSIGNED NOT NULL,
  `utente_preparazione` int(11) UNSIGNED,
  `utente_consegna` int(11) UNSIGNED,
  PRIMARY KEY (`id_ordine`),
  foreign key(`conto`) references Conto(`id_conto`),
  foreign key(`piatto`) references Piatto(`id_piatto`),
  foreign key(`utente_ordine`) references Utente(`id_utente`),
  foreign key(`utente_preparazione`) references Utente(`id_utente`),
  foreign key(`utente_consegna`) references Utente(`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP PROCEDURE IF EXISTS `makeOrder`;
DELIMITER $$
CREATE PROCEDURE `makeOrder`
	(/* l'ordine */
    IN _utente_ordine int(11) UNSIGNED, 
    IN _piatto int(11) UNSIGNED, 
    IN _quantita TINYINT UNSIGNED, 
    IN _note varchar(255),
    /* il tavolo */
    IN _tavolo int(11) UNSIGNED,
    IN _stanza int(11) UNSIGNED,
    IN _ristorante int(11) UNSIGNED)
BEGIN
	DECLARE var_conto int(11) UNSIGNED;
    SELECT id_conto 
	FROM Conto
	WHERE tavolo = _tavolo
		AND stanza = _stanza
		AND ristorante = _ristorante
		AND ts_pagamento IS NULL INTO var_conto;
	CALL LOG(var_conto);
	IF ISNULL(var_conto)
	THEN
		CALL LOG('Branched!');
		INSERT INTO Conto (tavolo, stanza, ristorante) VALUE (_tavolo, _stanza, _ristorante);
        SET var_conto = (SELECT LAST_INSERT_ID());
    END IF;
    CALL LOG('Finally');
	INSERT INTO Ordine (utente_ordine, conto, piatto, quantita, note, ts_ordine) VALUE (_utente_ordine, var_conto, _piatto, _quantita, _note, CURRENT_TIMESTAMP);
	SELECT LAST_INSERT_ID();

END $$
DELIMITER ;

-- SELECT * FROM logtable;


--
-- Popolamento
--

INSERT INTO Livello (id_livello, max_dipendenti, max_tavoli, max_menu, max_stanze, durata_validita, prezzo) VALUES (1, 5, 10, 1, 1, 30, 0),
																													(2, 15, 30, 2, 3, 365, 0),
																												(3, 0, 0, 0, 0, 0, 0);
INSERT INTO Licenza (chiave, data_acquisto, livello) VALUE (1, '2020-07-10', 1); -- FLOOR(RAND()*4294967295)
INSERT INTO Licenza (chiave, data_acquisto, livello) VALUE (2, CURRENT_DATE, 2); -- FLOOR(RAND()*4294967295)
INSERT INTO Licenza (chiave, data_acquisto, livello) VALUE (3, CURRENT_DATE, 3); -- FLOOR(RAND()*4294967295)


-- INSERT INTO Ristorante (nome_ristorante, indirizzo, license_key) VALUE ('Sorcio Verde', 'via Verdi 76', 3); 
INSERT INTO Ristorante (nome_ristorante, indirizzo, license_key) VALUE ('Pesce Rosso', 'via Rossi 34', 1);
INSERT INTO Ristorante (nome_ristorante, indirizzo, license_key) VALUE ('Gatto Blu', 'via Blu 82', 2);

INSERT INTO Utente (username, mail, password, privilegi, ristorante) VALUE('admin1','admin1@test.com','$2y$10$sdL8QG/QCDArDgoWH2Gj8Oq5oiYF2N49m8rmcDmJGegYICbSKRrCS', 0, 1); -- password: test 
INSERT INTO Utente (username, mail, password, privilegi, ristorante) VALUE('admin2','admin2@test.com','$2y$10$sdL8QG/QCDArDgoWH2Gj8Oq5oiYF2N49m8rmcDmJGegYICbSKRrCS', 0, 2); -- password: test 


/*INSERT INTO Ristorante (nome_ristorante, indirizzo) VALUES ('Pesce Rosso', 'via Rossi 34'), -- id 1
																('Gatto blu', 'via Verdi 82'); -- id 2


INSERT INTO Utente VALUES (1,'pippo','pippo@gmail.com','$2y$10$vm/G2EAMu9nhZnkW7QiBO.ka9Z5XnFEERXij17zdFkzfdIa/Z7KJW', 'light', 0, 1), -- password: pippo 
                          (2,'pluto','pluto@ymail.com','$2y$10$LOXbujYOPx6npVbdVpGMDeQKmuyQLaoNcaaEjji86k1ldYu3gkqMG', 'dark', 1, 1), -- password: pluto 
                          (3,'john92','john92@hotmail.it','$2y$10$m1OiDSbJwGZEAcTu8w4f3uv.zHSvBnzWx/p4s3saGilm3KxBc84dC', 'light', 2, 1), -- password: john92 
                          (4,'mark_71','m.mark@gmail.com','$2y$10$xVn6SgFn86eVL5Kqcw.8CuxSG/GJWSWHkTl3aBw/D/zpDd9/40J/2', 'dark', 4, 1), -- password: mark_71 
                          (5,'Fefuzz','fefuzz97@gmail.com','$2y$10$kNR4d86xvDa7vhhTIg6yKujjpqxhNa1iDrr95L/uhymqmgZ4ro1NW', 'light', 0, 2), -- password: Fefuzz 
                          (6,'paperino','paperino@disney.com','$2y$10$ng3QWNM686MOUu1m5aQ8CembPL2t77AHZg1bdUUeI.ZAxD1aJs6TS', 'light', 5, 2), -- password: paperino 
                          (7,'minny_52','minny_52@disney.com','$2y$10$ghzaD1f08KVOmg3fG9huouFrxCAohbivNJArVZF5ohHgrK7wWZTe.', 'light', 3, 2), -- password: minny_52 
                          (8,'test','test@test.com','$2y$10$sdL8QG/QCDArDgoWH2Gj8Oq5oiYF2N49m8rmcDmJGegYICbSKRrCS', null, 6, 2), -- password: test 
                          (9,'pweb','pweb@pweb.com','$2y$10$sFoHeeKuvasxuF8wTJa2N.9uURE6REl89WdAuTrXLtymnq4a6nIyO', null, 7, 2); -- password: pweb 
                          
-- 
-- Funzioni
-- 



-- register_user(...) -- invalid info, existing user, 
INSERT INTO Utente (username, mail, password) VALUE ('username', 'mail', 'password');
-- update_user(id, ...), -- invalid id, invalid info
UPDATE Utente SET username = 'username', mail = 'mail', pasword = 'password', pref_theme = 'pref_theme' WHERE id_utente = 'id';
-- delete_user(id), -- DON'T IMPLEMENT IT!!
-- set_privilege(id, user_id, priv)
UPDATE Utente SET privilegi = 'priv' WHERE id_utente = 'id';

-- get_user(id) -- invalid id
SELECT * FROM Utente WHERE id_utente = 'id';
-- list_users(rest_id)
SELECT id_utente, username FROM Utente WHERE id_ristorante = 'id_ristorante';

-- get_staff_stats(id, rest_id) -- invalid restaurant id
-- TODO!!
-- register_restaurant(key, id, ...), -- invalid key, out of date key, invalid id
INSERT INTO Ristorante (nome_ristorante, indirizzo, license_key) VALUE ('nome', 'indirizzo', (SELECT chiave FROM Licenza WHERE chiave = 'license_key' AND active = 0)); 
-- update_restaurant(id, ...), -- invalid restaurant id
UPDATE Ristorante SET nome_ristorante = 'nome_ristorante', indirizzo = 'indirizzo', limite_consegna_ordine = 'limite_consegna_ordine', license_key = (SELECT chiave FROM Licenza WHERE chiave = 'license_key') WHERE id_ristorante = 'id';
-- get_restaurant(id), 
SELECT * FROM Ristorante WHERE id_ristorante = 'id';
-- get_restaurant_detailed(id), 
-- list_restaurants()
SELECT nome_ristorante FROM Ristorante;

-- available components: stanza, tavolo, menu, piatto
-- add_component(id, component, ...), 
-- update_component(id, component_id, ...), 
-- delete_component(id, component_id),
-- get_component(id)

INSERT INTO Stanza (nome_stanza, ristorante) VALUE ('nome_stanza', 'ristorante');
INSERT INTO Tavolo (stanza, ristorante) VALUE ('stanza', 'ristorante');
INSERT INTO Menu (ristorante) VALUE ('ristorante');
INSERT INTO Piatto (nome, prezzo, ingredienti, allergeni, ristorante) VALUE ('nome', 'prezzo', 'ingredienti', 'allergeni', 'ristorante');

UPDATE Stanza SET nome_stanza = 'nome_stanza' WHERE id_Stanza = 'id';
UPDATE Tavolo SET percentX = 'percentX', percentY = 'percentY' WHERE id_Tavolo = 'id';
UPDATE Menu SET orarioInizio = 'orarioInizio', orarioFine = 'orarioFine' WHERE id_Menu = 'id';
UPDATE Piatto SET nome = 'nome', prezzo = 'prezzo', ingredienti = 'ingredienti', allergeni = 'allergeni' WHERE id_Piatto = 'id';

DELETE FROM Stanza WHERE id_Stanza = 'id';
DELETE FROM Tavolo WHERE id_Tavolo = 'id';
DELETE FROM Menu WHERE id_Menu = 'id';
DELETE FROM Piatto WHERE id_Piatto = 'id';

SELECT * FROM Stanza WHERE id_Stanza = 'id';
SELECT * FROM Tavolo WHERE id_Tavolo = 'id';
SELECT * FROM Menu WHERE id_Menu = 'id';
SELECT * FROM Piatto WHERE id_Piatto = 'id';

-- add_dish_to_menu(id_menu, id_piatto)
INSERT INTO ComposizioneMenu (menu, piatto) VALUE ('menu', 'piatto');
-- remove_dish_from_menu(id_menu, id_piatto)
DELETE FROM ComposizioneMenu WHERE menu = 'menu' AND piatto = 'piatto';

-- make_order(id, ...), 
INSERT INTO Ordine (utente_ordine, conto, piatto, quantita, note, ts_ordine) VALUE ('utente', 'conto', 'piatto', 'quantita', 'note', CURRENT_TIMESTAMP);

-- set_prepared(id, order_id), 
UPDATE Ordine SET ts_preparazione=CURRENT_TIMESTAMP, utente_preparazione = 'user' WHERE id_ ordine = 'id';
-- set_delivered(id, order_id), 
UPDATE Ordine SET ts_consegna=CURRENT_TIMESTAMP, utente_consegna = 'user' WHERE id_ ordine = 'id';
-- get_orders(id)
SELECT * 
    FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto
    WHERE C.ristorante = (SELECT ristorante
                            FROM Utente
                            WHERE id_utente = 'id');
-- get_max_table_wait(id, table_id) TODO!!

-- get_check(id, table_id),  TODO!!
-- review(check_id, review)
UPDATE Conto SET valutwzione = 'valutazione' WHERE id_conto = 'conto';
 
-- write_message(id, ...), 
INSERT INTO Messaggio (from, to, msg, is_req, ts) VALUE ('id', 'dest', 'msg', 0, CURRENT_TIMESTAMP);

-- get_contacts(id)
SELECT DISTINCT id_utente FROM Utente U INNER JOIN Messaggio M1 ON U.id_utente = M1.from INNER JOIN Messaggio M2 ON U.id_utente = M2.to
  WHERE M1.to = 'id' OR M2.from = 'id';
-- get_unread_messages(id, dest)
SELECT COUNT(*) FROM Messaggio WHERE from = 'dest' AND to = 'id' AND is_read = 0
-- get_messages(id, dest)
UPDATE Messaggio SET is_read = 1 WHERE from = 'dest' AND to = 'id' AND is_read = 0 AND is_req = 0;
SELECT * FROM Messaggio WHERE (from = 'id' AND to = 'dest') OR (from = 'dest' AND to = 'id');
-- send_request(id, restaurant_id), 
for 'dest' in (SELECT id_utente FROM Utente WHERE id_ristorante = 'id_ristorante'):
INSERT INTO Messaggio (from, to, msg, is_req, ts) VALUE ('id', 'dest', NULL, 1, CURRENT_TIMESTAMP);
-- refuse_request(meg_id)
UPDATE Messaggio SET is_read = 1 WHERE id_msg = 'msg_id' AND is_read = 0 AND is_req = 1;
-- accept_request(msg_id) 
UPDATE Messaggio SET is_read = 1 WHERE id_msg = 'msg_id' AND is_read = 0 AND is_req = 1;
UPDATE Utente SET id_ristorante = (SELECT ristorante FROM Utente WHERE id_utente = (SELECT to FROM Messaggio WHERE id_msg = 'msg_id'))

-- generate_key(level), 
INSERT INTO Licenza (chiave, data_acquisto, livello) VALUE (FLOOR(RAND()*4294967295), CURRENT_TIMESTAMP, 'livello');
-- activate_key(key, id) TODO!! Probabilmente non ha molto senso, per registrare rest è necessaria chiave


