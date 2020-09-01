<?php
	require_once __DIR__ . "/../config.php";
    require_once DIR_UTIL . "repEatDbManager.php"; //includes Database Class
	require_once DIR_UTIL . "sessionUtil.php"; //includes session login


	/*--------------------------------------------------------*/

	function addLicenseLevel($param){  // CURRENTLY NOT AVAILABLE
		global $repEatDb;
		if (isset($param['id_livello'])){
			$id_livello = $repEatDb->sqlInjectionFilter($param['id_livello']);
		} else  return 'Missing argument: id_livello';

		if (isset($param['max_dipendenti'])){
			$max_dipendenti = $repEatDb->sqlInjectionFilter($param['max_dipendenti']);
		} else  return 'Missing argument: max_dipendenti';

		if (isset($param['max_tavoli'])){
			$max_tavoli = $repEatDb->sqlInjectionFilter($param['max_tavoli']);
		} else  return 'Missing argument: max_tavoli';

		if (isset($param['max_menu'])){
			$max_menu = $repEatDb->sqlInjectionFilter($param['max_menu']);
		} else  return 'Missing argument: max_menu';

		if (isset($param['max_stanze'])){
			$max_stanze = $repEatDb->sqlInjectionFilter($param['max_stanze']);
		} else  return 'Missing argument: max_stanze';

		if (isset($param['durata_validita'])){
			$durata_validita = $repEatDb->sqlInjectionFilter($param['durata_validita']);
		} else  return 'Missing argument: durata_validita';

		if (isset($param['prezzo'])){
			$prezzo = $repEatDb->sqlInjectionFilter($param['prezzo']);
		} else  return 'Missing argument: prezzo';

		$queryText = 'INSERT INTO Livello (id_livello, max_dipendenti, max_tavoli, max_menu, max_stanze, durata_validita, prezzo) VALUES ('.
											$id_livello . ', ' . $max_dipendenti . ', ' . $max_tavoli . ', ' . $max_menu . ', ' . $max_stanze . ', ' . $durata_validita . ', ' . $prezzo . ');';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;
	}

	function listLevels(){
		global $repEatDb;
		$queryText = 'SELECT * FROM Livello';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function generateKey($param){
		global $repEatDb;
		if (isset($param['level'])){
			$level = $repEatDb->sqlInjectionFilter($param['level']);
		} else  return 'Missing argument: level';

		$queryText = 'CALL generateKey(' . $level . ');';		
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;
	}

	function registerRestaurant($param){
		global $repEatDb;
		if (isset($param['nome_ristorante'])){
			$nome_ristorante = $repEatDb->sqlInjectionFilter($param['nome_ristorante']);
		} else  return 'Missing argument: nome_ristorante';

		if (isset($param['indirizzo'])){
			$indirizzo = $repEatDb->sqlInjectionFilter($param['indirizzo']);
		} else  return 'Missing argument: indirizzo';

		if (isset($param['license_key'])){
			$license_key = $repEatDb->sqlInjectionFilter($param['license_key']);
		} else  return 'Missing argument: license_key';

		if (isset($param['user'])){
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		$queryText = 'CALL registerRestaurant(\'' . $nome_ristorante . '\', \'' . $indirizzo . '\', ' . $license_key . ', ' . $user .');';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function checkLicenseValidity($param){
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'SELECT IFNULL((SELECT CURRENT_DATE) < '.
						'(SELECT L.data_attivazione + INTERVAL (IF(Lv.durata_validita = 0, NULL, Lv.durata_validita)) DAY ' .
						'FROM Licenza L INNER JOIN Ristorante R ON L.chiave = R.license_key INNER JOIN Livello Lv ON L.livello = Lv.id_livello ' .
						'WHERE R.id_ristorante = '. $ristorante . '), 1) AS is_valid;';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateRestaurant($param){ // TODO: check isset($var) to make dynamic query
		global $repEatDb;
		if (isset($param['nome_ristorante'])){
			$nome_ristorante = $repEatDb->sqlInjectionFilter($param['nome_ristorante']);
		} else  return 'Missing argument: nome_ristorante';

		if (isset($param['indirizzo'])){
			$indirizzo = $repEatDb->sqlInjectionFilter($param['indirizzo']);
		} else  return 'Missing argument: indirizzo';

		if (isset($param['limite_consegna_ordine'])){
			$limite_consegna_ordine = $repEatDb->sqlInjectionFilter($param['limite_consegna_ordine']);
		} else  return 'Missing argument: limite_consegna_ordine';

		if (isset($param['license_key'])){
			$license_key = $repEatDb->sqlInjectionFilter($param['license_key']);
		} else  return 'Missing argument: license_key';

		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'UPDATE Ristorante SET nome_ristorante = \'' . $nome_ristorante . '\', indirizzo = \''. $indirizzo . '\', limite_consegna_ordine = '. $limite_consegna_ordine . ', license_key = '. $license_key . ' WHERE id_ristorante = '. $ristorante . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;
	}

	function getRestaurant($param){
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'SELECT * FROM Ristorante WHERE id_ristorante = ' . $ristorante;
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;
	}


	function listRestaurants(){
		global $repEatDb;
		$queryText = 'SELECT id_ristorante, nome_ristorante FROM Ristorante';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

//get_restaurant_detailed(id), TODO AS COMPOSITE FUNCTION IN PHP


	function updateUser($param){ //TODO: check if working isset($var) to make dynamic query
		global $repEatDb;
		$dynamic_set = "id_utente = id_utente";
		if (isset($param['username'])){
			$username = $repEatDb->sqlInjectionFilter($param['username']);
			$dynamic_set = $dynamic_set . ', username = \'' . $username . '\'';
		};

		if (isset($param['mail'])){
			$mail = $repEatDb->sqlInjectionFilter($param['mail']);
			$dynamic_set = $dynamic_set . ', mail = \'' . $mail . '\'';
		};

		if (isset($param['password'])){
			$password = $repEatDb->sqlInjectionFilter($param['password']);
			$dynamic_set = $dynamic_set . ', password = \'' . password_hash($password, PASSWORD_DEFAULT) . '\'';
		};

		if (isset($param['pref_theme'])){
			$pref_theme = $repEatDb->sqlInjectionFilter($param['pref_theme']);
			$dynamic_set = $dynamic_set . ', pref_theme = \'' . $pref_theme . '\'';
		};

		if (isset($param['user'])){
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		$queryText = 'UPDATE Utente SET ' . $dynamic_set . ' WHERE id_utente = ' . $user . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

// delete_user(id), -- DONT IMPLEMENT IT!!

	function setPrivilege($param){
		global $repEatDb;
		if (isset($param['privilegi'])){
			$privilegi = $repEatDb->sqlInjectionFilter($param['privilegi']);
		} else  return 'Missing argument: privilegi';

		if (isset($param['target_user'])){
			$target_user = $repEatDb->sqlInjectionFilter($param['target_user']);
		} else  return 'Missing argument: target_user';

		$queryText = 'UPDATE Utente SET privilegi = ' . $privilegi . ' WHERE id_utente = ' . $target_user . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}
	function getUser($param){
		global $repEatDb;
		if (isset($param['user'])){
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		$queryText = 'SELECT id_utente, username, mail, pref_theme, privilegi, ristorante FROM Utente WHERE id_utente = ' . $user . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function listUsers($param){
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'SELECT id_utente, username, privilegi FROM Utente WHERE ristorante = ' . $ristorante . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}
// get_staff_stats(id, rest_id) -- invalid restaurant id TODO!!

// available components: stanza, tavolo, menu, piatto

	function addRoom($param){
		global $repEatDb;

		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'INSERT INTO Stanza (ristorante) VALUE (' . $ristorante . ');';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function addTable($param){
		global $repEatDb;
		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';

		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'INSERT INTO Tavolo (stanza, ristorante) VALUE (' . $stanza . ', ' . $ristorante . ');';
		
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function addMenu($param){
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'INSERT INTO Menu (ristorante) VALUE (' . $ristorante . ');';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function addDish($param){
		global $repEatDb;
		/*
		if (isset($param['nome_piatto'])){
			$nome_piatto = $repEatDb->sqlInjectionFilter($param['nome_piatto']);
		} else  return 'Missing argument: nome_piatto';

		if (isset($param['categoria'])){
			$categoria = $repEatDb->sqlInjectionFilter($param['categoria']);
		} else  return 'Missing argument: categoria';

		if (isset($param['prezzo'])){
			$prezzo = $repEatDb->sqlInjectionFilter($param['prezzo']);
		} else  return 'Missing argument: prezzo';

		if (isset($param['ingredienti'])){
			$ingredienti = $repEatDb->sqlInjectionFilter($param['ingredienti']);
		} else  return 'Missing argument: ingredienti';

		if (isset($param['allergeni'])){
			$allergeni = $repEatDb->sqlInjectionFilter($param['allergeni']);
		} else  return 'Missing argument: allergeni';
		*/

		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'INSERT INTO Piatto (ristorante) VALUE (' . $ristorante . ');'; //TODO check for allergeni in set format
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateRoom($param){
		global $repEatDb;
		if (isset($param['nome_stanza'])){
			$nome_stanza = $repEatDb->sqlInjectionFilter($param['nome_stanza']);
		} else  return 'Missing argument: nome_stanza';

		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';

		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'UPDATE Stanza SET nome_stanza = \'' . $nome_stanza . '\' WHERE id_stanza = ' . $stanza . ' AND ristorante = ' . $ristorante . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateTable($param){
		global $repEatDb;
		if (isset($param['percentX'])){
			$percentX = $repEatDb->sqlInjectionFilter($param['percentX']);
		} else  return 'Missing argument: percentX';

		if (isset($param['percentY'])){
			$percentY = $repEatDb->sqlInjectionFilter($param['percentY']);
		} else  return 'Missing argument: percentY';

		if (isset($param['tavolo'])){
			$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		} else  return 'Missing argument: tavolo';

		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';

		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'UPDATE Tavolo SET percentX = ' . $percentX . ', percentY = ' . $percentY . ' WHERE id_Tavolo = ' . $tavolo . ' AND stanza = ' . $stanza . ' AND ristorante = ' . $ristorante . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateMenu($param){
		global $repEatDb;
		if (isset($param['orario_inizio'])){
			$orario_inizio = $repEatDb->sqlInjectionFilter($param['orario_inizio']);
		} else  return 'Missing argument: orario_inizio';

		if (isset($param['orario_fine'])){
			$orario_fine = $repEatDb->sqlInjectionFilter($param['orario_fine']);
		} else  return 'Missing argument: orario_fine';

		if (isset($param['menu'])){
			$menu = $repEatDb->sqlInjectionFilter($param['menu']);
		} else  return 'Missing argument: menu';

		$queryText = 'UPDATE Menu SET orarioInizio = \'' . $orario_inizio . '\', orarioFine = \'' . $orario_fine . '\' WHERE id_Menu = ' . $menu . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateDish($param){
		global $repEatDb;
		if (isset($param['nome_piatto'])){
			$nome_piatto = $repEatDb->sqlInjectionFilter($param['nome_piatto']);
		} else  return 'Missing argument: nome_piatto';

		if(!preg_match("/^[^,]+$/", $nome_piatto)){	//necessary for the GROUP_CONCAT in listRooms
			return 'Argument nome_piatto can not contain commas';
		}

		if (isset($param['categoria'])){
			$categoria = $repEatDb->sqlInjectionFilter($param['categoria']);
		} else  return 'Missing argument: categoria';

		if (isset($param['prezzo'])){
			$prezzo = $repEatDb->sqlInjectionFilter($param['prezzo']);
		} else  return 'Missing argument: prezzo';

		if (isset($param['ingredienti'])){
			$ingredienti = $repEatDb->sqlInjectionFilter($param['ingredienti']);
		} else  return 'Missing argument: ingredienti';

		if (isset($param['allergeni'])){
			$allergeni = $repEatDb->sqlInjectionFilter($param['allergeni']);
		} else  return 'Missing argument: allergeni';

		if (isset($param['piatto'])){
			$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		} else  return 'Missing argument: piatto';

		$queryText = 'UPDATE Piatto SET nome = \'' . $nome_piatto . '\', categoria = \'' . $categoria . '\', prezzo = ' . $prezzo . ', ingredienti = \'' . $ingredienti . '\', allergeni = \'' . $allergeni . '\' WHERE id_Piatto = ' . $piatto . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getRoom($param){
		global $repEatDb;
		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'SELECT * FROM Stanza WHERE id_Stanza = ' . $stanza . ' AND ristorante = ' . $ristorante . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getTable($param){
		global $repEatDb;
		if (isset($param['tavolo'])){
			$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		} else  return 'Missing argument: tavolo';
		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'SELECT * FROM Tavolo WHERE id_Tavolo = ' . $tavolo . ' AND stanza = ' . $stanza . ' AND ristorante = ' . $ristorante . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getCurrentDishes($param){
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'SELECT DISTINCT(P.id_piatto), P.* ' .
						' FROM Piatto P INNER JOIN ComposizioneMenu CM ON P.id_piatto = CM.piatto ' .
						' WHERE CM.menu IN (SELECT id_menu ' .
										' FROM Menu ' .
										' WHERE Ristorante = ' . $ristorante .
											' AND orarioInizio <= current_time() ' .
											' AND orarioFine >= current_time()) ' .
						' ORDER BY P.categoria;'; 
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getDish($param){
		global $repEatDb;
		if (isset($param['piatto'])){
			$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		} else  return 'Missing argument: piatto';

		$queryText = 'SELECT * FROM Piatto WHERE id_Piatto = ' . $piatto . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function listRooms($param){
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'SELECT id_stanza, nome_stanza, GROUP_CONCAT(id_tavolo, ":", percentX, ":", percentY, ":", stato) AS tavoli ' . 
						' FROM Stanza S LEFT OUTER JOIN Tavolo T ON T.stanza = S.id_stanza AND T.ristorante = S.ristorante ' . 
						' WHERE S.ristorante = ' . $ristorante . 
						' GROUP BY S.id_stanza ' .
						' ORDER BY T.id_tavolo;';

		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function listDishes($param){
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'SELECT * ' . 
						' FROM Piatto P ' .
						' WHERE P.ristorante = ' . $ristorante .
						' ORDER BY P.categoria;';

		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function listMenus($param){
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'SELECT M.id_menu, M.orarioInizio, M.orarioFine, GROUP_CONCAT(P.id_piatto, ":", P.nome, ":", P.categoria, ":", P.prezzo) AS piatti ' .
						' FROM Menu M LEFT OUTER JOIN ComposizioneMenu CM ON M.id_menu = CM.menu LEFT OUTER JOIN Piatto P ON CM.piatto = P.id_piatto ' .
						' WHERE M.ristorante = ' . $ristorante . 
						' GROUP BY M.id_menu;';

		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function removeDish($param){
		global $repEatDb;
		if (isset($param['piatto'])){
			$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		} else  return 'Missing argument: piatto';

		$queryText = 'DELETE FROM Piatto WHERE id_Piatto = ' . $piatto . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function removeMenu($param){
		global $repEatDb;
		if (isset($param['menu'])){
			$menu = $repEatDb->sqlInjectionFilter($param['menu']);
		} else  return 'Missing argument: menu';

		$queryText = 'DELETE FROM Menu WHERE id_Menu = ' . $menu . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function removeTable($param){
		global $repEatDb;
		if (isset($param['tavolo'])){
			$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		} else  return 'Missing argument: tavolo';

		$queryText = 'DELETE FROM Tavolo WHERE id_Tavolo = ' . $tavolo . ';'; 
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function removeRoom($param){
		global $repEatDb;
		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';

		$queryText = 'DELETE FROM Stanza WHERE id_Stanza = ' . $stanza . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function addDishToMenu($param){
		global $repEatDb;
		if (isset($param['menu'])){
			$menu = $repEatDb->sqlInjectionFilter($param['menu']);
		} else  return 'Missing argument: menu';

		if (isset($param['piatto'])){
			$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		} else  return 'Missing argument: piatto';

		$queryText = 'INSERT INTO ComposizioneMenu (menu, piatto) VALUE (' . $menu . ', ' . $piatto . ');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}
	
	function removeDishFromMenu($param){
		global $repEatDb;
		if (isset($param['menu'])){
			$menu = $repEatDb->sqlInjectionFilter($param['menu']);
		} else  return 'Missing argument: menu';

		if (isset($param['piatto'])){
			$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		} else  return 'Missing argument: piatto';

		$queryText = 'DELETE FROM ComposizioneMenu WHERE menu = ' . $menu . ' AND piatto = ' . $piatto . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}


	function makeOrder($param){
		global $repEatDb;
		if (isset($param['user'])){	//utente ordine
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		if (isset($param['piatto'])){
			$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		} else  return 'Missing argument: piatto';

		if (isset($param['quantita'])){
			$quantita = $repEatDb->sqlInjectionFilter($param['quantita']);
		} else  return 'Missing argument: quantita';

		if (isset($param['note'])){
			$note = $repEatDb->sqlInjectionFilter($param['note']);
		} else  return 'Missing argument: note';

		if (isset($param['tavolo'])){
			$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		} else  return 'Missing argument: tavolo';

		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';

		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'CALL makeOrder(' . $user . ', ' . $piatto . ', ' . $quantita . ', \'' . $note . '\', ' . $tavolo . ', ' . $stanza . ', ' . $ristorante . '); ';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getOrdersWaiting($param){
		global $repEatDb;
		if (isset($param['user'])){
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		$queryText = 'SELECT O.id_ordine, O.quantita, P.nome, O.note, C.tavolo, C.stanza, SEC_TO_TIME(CURRENT_TIMESTAMP() - O.ts_ordine ) AS attesa ' . 
		' FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto INNER JOIN Piatto P ON O.piatto = P.id_piatto ' .
		' WHERE ts_preparazione IS NULL' .
			' AND C.ristorante = (SELECT U.ristorante' .
								' FROM Utente U' .
								' WHERE id_utente = ' . $user . ') ' .
		' ORDER BY SEC_TO_TIME(CURRENT_TIMESTAMP() - O.ts_ordine ) DESC;';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getOrdersReady($param){
		global $repEatDb;
		if (isset($param['user'])){
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		$queryText = 'SELECT O.id_ordine, O.quantita, P.nome, O.note, C.tavolo, C.stanza, SEC_TO_TIME(CURRENT_TIMESTAMP() - O.ts_ordine ) AS attesa ' . 
		' FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto INNER JOIN Piatto P ON O.piatto = P.id_piatto ' .
		' WHERE ts_preparazione IS NOT NULL' .
			' AND ts_consegna IS NULL' .
			' AND C.ristorante = (SELECT U.ristorante ' .
								' FROM Utente U '.
								' WHERE id_utente = ' . $user . ') ' .
		' ORDER BY SEC_TO_TIME(CURRENT_TIMESTAMP() - O.ts_ordine ) DESC;';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function setPrepared($param){
		global $repEatDb;
		if (isset($param['user'])){	//utente preparazione
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		if (isset($param['ordine'])){
			$ordine = $repEatDb->sqlInjectionFilter($param['ordine']);
		} else  return 'Missing argument: ordine';

		$queryText = 'UPDATE Ordine SET ts_preparazione=CURRENT_TIMESTAMP, utente_preparazione = ' . $user . ' WHERE id_ordine = ' . $ordine . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}
	
	function setDelivered($param){
		global $repEatDb;
		if (isset($param['user'])){	//utente consegna
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		if (isset($param['ordine'])){
			$ordine = $repEatDb->sqlInjectionFilter($param['ordine']);
		} else  return 'Missing argument: ordine';

		$queryText = 'UPDATE Ordine SET ts_consegna=CURRENT_TIMESTAMP, utente_consegna = ' . $user . ' WHERE id_ordine = ' . $ordine . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function review($param){
		global $repEatDb;
		if (isset($param['valutazione'])){
			$valutazione = $repEatDb->sqlInjectionFilter($param['valutazione']);
		} else  return 'Missing argument: valutazione';

		if (isset($param['recensione'])){
			$recensione = $repEatDb->sqlInjectionFilter($param['recensione']);
		} else  return 'Missing argument: recensione';

		if (isset($param['tavolo'])){
			$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		} else  return 'Missing argument: tavolo';

		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';

		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'UPDATE Conto SET valutazione = ' . $valutazione . ', recensione = \'' . $recensione . '\' WHERE ts_pagamento IS NULL AND tavolo = ' . $tavolo . ' AND stanza = ' . $stanza . ' AND ristorante = ' . $ristorante . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function payCheck($param){
		global $repEatDb;
		if (isset($param['tipo_pagamento'])){
			$tipo_pagamento = $repEatDb->sqlInjectionFilter($param['tipo_pagamento']);
		} else  return 'Missing argument: tipo_pagamento';

		if (isset($param['tavolo'])){
			$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		} else  return 'Missing argument: tavolo';

		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';

		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		$queryText = 'UPDATE Conto SET tipo_pagamento = \'' . $tipo_pagamento . '\', ts_pagamento = CURRENT_TIMESTAMP WHERE ts_pagamento IS NULL AND tavolo = ' . $tavolo . ' AND stanza = ' . $stanza . ' AND ristorante = ' . $ristorante . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getMaxTableWait($param){
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';

		if (isset($param['tavolo'])){
			$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		} else  return 'Missing argument: tavolo';

		$queryText = 'SELECT MIN(ts_ordine) ' .
		' FROM Ordine ' .
		' WHERE conto = (SELECT id_conto ' .
						' FROM Conto ' .
						' WHERE ts_pagamento IS NULL ' .
							' AND ristorante = ' . $ristorante . 
							' AND stanza = ' . $stanza . 
							' AND tavolo = ' . $tavolo . ');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}
                    
	function getCheck($param){ // should be done after an update to calculate total
		global $repEatDb;
		if (isset($param['ristorante'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		} else  return 'Missing argument: ristorante';

		if (isset($param['stanza'])){
			$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		} else  return 'Missing argument: stanza';

		if (isset($param['tavolo'])){
			$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		} else  return 'Missing argument: tavolo';

		$queryText = ' SELECT \'Permanenza:\' AS piatto, null AS quantita, DATE_FORMAT(SEC_TO_TIME(CURRENT_TIMESTAMP - ts_primo_ordine), \'%H:%i:%s\') AS prezzo ' .
					' FROM Conto ' .
					' WHERE ts_pagamento IS NULL AND tavolo = ' . $tavolo . ' AND stanza = ' . $stanza . ' AND ristorante = ' . $ristorante .
					' UNION ALL ' .
					' SELECT P.nome AS piatto, SUM(O.quantita) AS quantita, CAST(SUM(P.prezzo*O.quantita) AS DECIMAL(5, 2)) AS prezzo ' .
					' FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto INNER JOIN Piatto P ON O.piatto = P.id_piatto ' .
					' WHERE C.ts_pagamento IS NULL AND C.tavolo = ' . $tavolo . ' AND C.stanza = ' . $stanza . ' AND C.ristorante = ' . $ristorante .
					' GROUP BY P.nome' .
					' UNION ALL ' .
					' SELECT \'Totale:\', null, CAST(totale AS DECIMAL(5, 2)) AS totale ' .
					' FROM Conto ' .
					' WHERE ts_pagamento IS NULL AND tavolo = ' . $tavolo . ' AND stanza = ' . $stanza . ' AND ristorante = ' . $ristorante . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	} 
	

	function writeMessage($param){
		global $repEatDb;
		if (isset($param['from_user'])){
			$from_user = $repEatDb->sqlInjectionFilter($param['from_user']);
		} else  return 'Missing argument: from_user';

		if (isset($param['to_user'])){
			$to_user = $repEatDb->sqlInjectionFilter($param['to_user']);
		} else  return 'Missing argument: to_user';

		if (isset($param['msg'])){
			$msg = $repEatDb->sqlInjectionFilter($param['msg']);
		} else  return 'Missing argument: msg';

		$queryText = 'INSERT INTO Messaggio (from_user, to_user, msg, ts) VALUE (' . $from_user . ', ' . $to_user . ', \'' . $msg . '\', CURRENT_TIMESTAMP);';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	} 

	function getChats($param){
		global $repEatDb;
		if (isset($param['user'])){
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		$queryText = 'SELECT V.*, U.username AS other_name, (SELECT M2.msg   '.
							' FROM Messaggio M2  '.
							' WHERE M2.id_msg =  '.
								' (SELECT MAX(M3.id_msg)  '.
								' FROM Messaggio M3   '.
								' WHERE (M3.to_user = ' . $user . ' '.
									' AND M3.from_user = V.other) '.
									' OR (M3.from_user = ' . $user . ' '.
										' AND M3.to_user = V.other))) AS last_msg   '.
						' FROM (SELECT SUM(num) AS unread_msgs, IF (SUBSTRING_INDEX(speakers, "-", 1) <> ' . $user . ', SUBSTRING_INDEX(speakers, "-", 1), SUBSTRING_INDEX(SUBSTRING_INDEX(speakers, "-", -1), "-", 1)) AS other '.
							' FROM (SELECT SUM(if(to_user = ' . $user . ', !is_read, 0)) AS num, CONCAT(IF(from_user < to_user, from_user, to_user), "-", IF(from_user > to_user, from_user, to_user)) AS speakers '.
								' FROM Messaggio '.
								' GROUP BY from_user, to_user '.
								' HAVING from_user = ' . $user . ' OR to_user = ' . $user . ') AS T '.
							' GROUP BY speakers) AS V INNER JOIN Utente U ON V.other = U.id_utente';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function readMessages($param){
		global $repEatDb;
		if (isset($param['user'])){
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		if (isset($param['dest'])){
			$dest = $repEatDb->sqlInjectionFilter($param['dest']);
		} else  return 'Missing argument: dest';

		$queryText = 'CALL readMessages(' . $user . ', ' . $dest . ');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function sendRequest($param){
		global $repEatDb;
		if (isset($param['user'])){
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		if (isset($param['target_restaurant'])){
			$ristorante = $repEatDb->sqlInjectionFilter($param['target_restaurant']);
		} else  return 'Missing argument: target_restaurant';

		if (isset($param['msg'])){
			$msg = $repEatDb->sqlInjectionFilter($param['msg']);
		} else  return 'Missing argument: msg';

		$queryText = 'CALL sendRequest(' . $user . ', ' . $ristorante . ', \'' . $msg . '\');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function processRequest($param){
		global $repEatDb;
		if (isset($param['req'])){
			$req = $repEatDb->sqlInjectionFilter($param['req']);
		} else  return 'Missing argument: req';

		if (isset($param['accepted'])){
			$accepted = $repEatDb->sqlInjectionFilter($param['accepted']);
		} else  return 'Missing argument: accepted';

		$queryText = 'CALL processRequest(' . $req . ', ' . $accepted . ');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function existsRequest($param){
		global $repEatDb;
		if (isset($param['user'])){
			$user = $repEatDb->sqlInjectionFilter($param['user']);
		} else  return 'Missing argument: user';

		$queryText = 'SELECT COUNT(*) AS num_requests FROM Messaggio WHERE from_user = ' . $user . ' AND is_req = 1 AND is_read = 0;';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;
	}


?>
