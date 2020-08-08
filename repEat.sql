DROP DATABASE `repEat`;
CREATE DATABASE  IF NOT EXISTS `repEat`;
USE `repEat`;

--
-- Creazione database
--

DROP TABLE IF EXISTS `Ristorante`;
CREATE TABLE `Ristorante` (
  `id_ristorante` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_ristorante` varchar(255) NOT NULL,
  `indirizzo` varchar(255) NOT NULL,
  `limite_consegna_ordine` tinyint UNSIGNED DEFAULT 15, -- minuti prima che un ordine sia considerato definitivamente in ritardo

  PRIMARY KEY (`id_ristorante`),
  UNIQUE KEY `nomeRistorante_UNIQUE` (`nome_ristorante`),
  UNIQUE KEY `indirizzo_UNIQUE` (`indirizzo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Utente`;
CREATE TABLE `Utente` (
  `id_utente` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,/*
  `name` varchar(32) NOT NULL,
  `surname` varchar(32) NOT NULL,*/
  `pref_theme` enum('light', 'dark') DEFAULT 'light',
  `privilegi` bit(3), -- ispirato ad UNIX (b000 è amministratore, b111 è visibilità completa), NULL è assenza di privilegi
  `id_ristorante` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_utente`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`mail`),
  foreign key(`id_ristorante`) references Ristorante(`id_ristorante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Messaggio`;
CREATE TABLE `Messaggio` (
  `id_msg` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_req` bit(1) NOT NULL,
  `msg` text DEFAULT NULL,
  `ts` timestamp NOT NULL,
  `from` int(11) UNSIGNED NOT NULL,
  `to` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_msg`),
  foreign key(`from`) references Utente(`id_utente`),
  foreign key(`to`) references Utente(`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Stanza`;
CREATE TABLE `Stanza` (
  `id_stanza` int(11) UNSIGNED NOT NULL, -- TODO: make manual auto-increment per restaurant 
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
  foreign key(`menu`) references Menu(`id_menu`),
  foreign key(`piatto`) references Piatto(`id_piatto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Conto`;
CREATE TABLE `Conto` (
  `id_conto` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `valutazione` tinyint,
  `totale` float UNSIGNED,
  `tipo_pagamento` enum('carta', 'bancomat', 'contanti'),
  `ts_pagamento` timestamp DEFAULT CURRENT_TIMESTAMP,
  `tavolo` int(11) UNSIGNED NOT NULL,
  `stanza` int(11) UNSIGNED NOT NULL,
  `ristorante` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_conto`),
  foreign key(`tavolo`) references Tavolo(`id_tavolo`),
  foreign key(`stanza`) references Tavolo(`stanza`),
  foreign key(`ristorante`) references Tavolo(`ristorante`)

) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `utente` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_ordine`),
  foreign key(`conto`) references Conto(`id_conto`),
  foreign key(`piatto`) references Piatto(`id_piatto`),
  foreign key(`utente`) references Utente(`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Limiti`;
CREATE TABLE `Limiti` (
  `livello` int(11) UNSIGNED NOT NULL,
  `max_dipendenti` tinyint UNSIGNED NOT NULL,
  `max_tavoli` tinyint UNSIGNED NOT NULL,
  `max_menu` tinyint UNSIGNED NOT NULL,
  `max_stanza` tinyint UNSIGNED NOT NULL,
  `durata_validita` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`livello`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Licenza`;
CREATE TABLE `Licenza` (
  `chiave` int(11) UNSIGNED NOT NULL,
  `data_acquisto` DATE NOT NULL,
  `livello` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY(`chiave`),
  foreign key(`livello`) references Limiti(`livello`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Popolamento
--

INSERT INTO `Ristorante` (nome_ristorante, indirizzo) VALUES ('Pesce Rosso', 'via Rossi 34'), -- id 1
																('Gatto blu', 'via Verdi 82'); -- id 2


INSERT INTO `Utente` VALUES (1,'pippo','pippo@gmail.com','$2y$10$vm/G2EAMu9nhZnkW7QiBO.ka9Z5XnFEERXij17zdFkzfdIa/Z7KJW', 'light', 0, 1), -- password: pippo 
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

/*

register_user(...) -- invalid info, existing user, 
update_user(id, ...), -- invalid id, invalid info
delete_user(id), -- DON'T IMPLEMENT IT!!
set_privilege(id, user_id, priv)
get_user(id) -- invalid id

get_staff_stats(id, rest_id) -- invalid restaurant id
register_restaurant(key, id, ...), -- invalid key, out of date key, invalid id
update_restaurant(id, ...), -- invalid restaurant id
get_restaurant(id), 
get_restaurant_detailed(id), 
list_restaurants()

-- available components: stanza, tavolo, menu, piatto
add_component(id, component, ...), 
update_component(id, component_id, ...), 
delete_component(id, component_id)

make_order(id, ...), 
set_prepared(id, order_id), 
set_delivered(id, order_id), 
get_orders(id)
get_max_table_wait(id, table_id)

get_check(id, table_id), 
review(check_id)

write_message(id, ...), 
get_messages(id)
send_request(id, restaurant_id), 
accept_request(msg_id)

generate_key(level), 
activate_key(key, id)

*/
