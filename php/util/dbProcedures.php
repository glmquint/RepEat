<?php
	require_once __DIR__ . "/../config.php";
    require_once DIR_UTIL . "repEatDbManager.php"; //includes Database Class
	 
	/* MACRO FUNCTION
	function func($parameters){  
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT * '
						. 'FROM  '
						. 'WHERE ';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;
	}
	*/

	function login($username, $password){   
		if ($username != null && $password != null){
			$user = authenticate($username, $password);
    		if ($user[0] > 0){
    			session_start();
    			setSession($username, $user[0], $user[1]);
    			return null;
    		}

    	} else
    		return 'You should insert something';
    	
    	return 'Username and password not valid.';
	}

	function register($mail, $username, $password){   
		if ($mail != null && $username != null && $password != null){
			global $repEatDB;
			$queryText = "insert into user (username, mail, password) VALUES ('" . $username . "', '" . $mail . "', '" . password_hash($password, PASSWORD_DEFAULT) . "'); -- select id from user where ;";
			$insertResult = $repEatDB->performQuery($queryText);
			$repEatDB->closeConnection();
			if (!is_null($insertResult))
				header('location: ./../../register.php?errorMessage=' . $insertResult);

			return login($username, $password);

    	} else
    		return 'You should insert something';
    	
    	return 'Username and password not valid.';
	}
	
	function authenticate ($username, $password){   
		global $repEatDB;
		$username = $repEatDB->sqlInjectionFilter($username);
		$password = $repEatDB->sqlInjectionFilter($password);

		$queryText = "select userId, password, privilegi from user where username='" . $username . /*"' AND password='" . $password . */"'";

		$result = $repEatDB->performQuery($queryText);
		$numRow = mysqli_num_rows($result);
		if ($numRow != 1)
			return [-1, null];
		
		$repEatDB->closeConnection();
		$userRow = $result->fetch_assoc();
		$repEatDB->closeConnection();
		if (!password_verify($password, $userRow['password']))
			return [-1, null];
		return [$userRow['userId'], $userRow['privilegi']];
	}

	function signin($mail, $username, $password){  
		$uniq_fields = array("username", "mail"); 
		if ($mail != null && $username != null && $password != null){
			
			for ($i = 0; $i < count($uniq_fields); $i++){
				if(check_existing_fieldValue($uniq_fields[$i], $_POST[$uniq_fields[$i]])){
					return "This " . $uniq_fields[$i] . " is already taken";
				}
			}

    	} else
    		return 'Every field is obligatory';
    	
    	return null;
	}

	function check_existing_fieldValue($field, $value){ // FIX: This seems redundant...
		echo "\nfield: " . $field;
		echo "\nvalue: " . $value;
		global $repEatDB;
		if (is_null($repEatDB))
			echo "\nrepEat is null!: ";
		$value = $repEatDB->sqlInjectionFilter($value);

		$queryText = "select * from user where " . $field . " = '" . $value . "'";

		$result = $repEatDB->performQuery($queryText);
		$numRow = mysqli_num_rows($result);
		$repEatDB->closeConnection();
		return $numRow;
		
	}

	function addLicenseLevel($id_livello, $max_dipendenti, $max_tavoli, $max_menu, $max_stanze, $durata_validita, $prezzo){  // CURRENTLY NOT AVAILABLE
		global $trackMyMoviesDb;
		$id_livello = $trackMyMoviesDb->sqlInjectionFilter($id_livello);
		$max_dipendenti = $trackMyMoviesDb->sqlInjectionFilter($max_dipendenti);
		$max_tavoli = $trackMyMoviesDb->sqlInjectionFilter($max_tavoli);
		$max_menu = $trackMyMoviesDb->sqlInjectionFilter($max_menu);
		$max_stanze = $trackMyMoviesDb->sqlInjectionFilter($max_stanze);
		$durata_validita = $trackMyMoviesDb->sqlInjectionFilter($durata_validita);
		$prezzo = $trackMyMoviesDb->sqlInjectionFilter($prezzo);
		$queryText = 'INSERT INTO Livello (id_livello, max_dipendenti, max_tavoli, max_menu, max_stanze, durata_validita, prezzo) VALUES ('.
											$id_livello . ', ' . $max_dipendenti . ', ' . $max_tavoli . ', ' . $max_menu . ', ' . $max_stanze . ', ' . $durata_validita . ', ' . $prezzo . ');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;
	}

	function listLevels(){
		global $trackMyMoviesDb;
		$queryText = 'SELECT * FROM Livello';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function generateKey($level){
		global $trackMyMoviesDb;
		$level = $trackMyMoviesDb->sqlInjectionFilter($level);
		$queryText = 'INSERT INTO Licenza (chiave, data_acquisto, livello) VALUE (FLOOR(RAND()*4294967295, CURRENT_DATE, ' . $level . ');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;
	}

	function registerRestaurant($nome_ristorante, $indirizzo, $license_key){
		global $trackMyMoviesDb;
		$nome_ristorante = $trackMyMoviesDb->sqlInjectionFilter($nome_ristorante);
		$indirizzo = $trackMyMoviesDb->sqlInjectionFilter($indirizzo);
		$license_key = $trackMyMoviesDb->sqlInjectionFilter($license_key);
		$queryText = 'INSERT INTO Ristorante (nome_ristorante, indirizzo, license_key) VALUE (\'' . $nome_ristorante . '\', \'' . $indirizzo . '\', ' . $license_key .');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function checkLicenseValidity($restaurant){
		global $trackMyMoviesDb;
		$restaurant = $trackMyMoviesDb->sqlInjectionFilter($restaurant);
		$queryText = 'SELECT IFNULL((SELECT CURRENT_DATE) < '.
						'(SELECT L.data_attivazione + INTERVAL (IF(Lv.durata_validita = 0, NULL, Lv.durata_validita)) DAY ' .
						'FROM Licenza L INNER JOIN Ristorante R ON L.chiave = R.license_key INNER JOIN Livello Lv ON L.livello = Lv.id_livello ' .
						'WHERE R.id_ristorante = '. $restaurant . '), 1);';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateRestaurant($nome_ristorante, $indirizzo, $limite_consegna_ordine, $license_key, $ristorante){ // TODO: check isset($var) to make dynamic query
		global $trackMyMoviesDb;
		$nome_ristorante = $trackMyMoviesDb->sqlInjectionFilter($nome_ristorante);
		$indirizzo = $trackMyMoviesDb->sqlInjectionFilter($indirizzo);
		$limite_consegna_ordine = $trackMyMoviesDb->sqlInjectionFilter($limite_consegna_ordine);
		$license_key = $trackMyMoviesDb->sqlInjectionFilter($license_key);
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$queryText = 'UPDATE Ristorante SET nome_ristorante = \'' . $nome_ristorante . '\', indirizzo = \''. $indirizzo . '\', limite_consegna_ordine = '. $limite_consegna_ordine . ', license_key = '. $license_key . ' WHERE id_ristorante = '. $ristorante . ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;
	}

	function getRestaurant($ristorante){
		global $trackMyMoviesDb;
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$queryText = 'SELECT * FROM Ristorante WHERE id_ristorante = ' . $ristorante;
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;
	}


	function listRestaurants(){
		global $trackMyMoviesDb;
		$queryText = 'SELECT nome_ristorante FROM Ristorante';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

//get_restaurant_detailed(id), TODO AS COMPOSITE FUNCTION IN PHP


	function updateUser($username, $mail, $password, $pref_theme, $ristorante, $utente){ //TODO: check isset($var) to make dynamic query
		global $trackMyMoviesDb;
		$username = $trackMyMoviesDb->sqlInjectionFilter($username);
		$mail = $trackMyMoviesDb->sqlInjectionFilter($mail);
		$password = $trackMyMoviesDb->sqlInjectionFilter($password);
		$pref_theme = $trackMyMoviesDb->sqlInjectionFilter($pref_theme);
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$utente = $trackMyMoviesDb->sqlInjectionFilter($utente);
		$queryText = 'UPDATE Utente SET username = \'' . $username . '\', mail = \'' . $mail . '\', password = \'' . password_hash($password, PASSWORD_DEFAULT) . '\', pref_theme = \'' . $pref_theme . '\', ristorante = ' . $ristorante . ' WHERE id_utente = ' . $utente . ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

// delete_user(id), -- DONT IMPLEMENT IT!!

	function setPrivilege($privilegi, $target_user){
		global $trackMyMoviesDb;
		$privilegi = $trackMyMoviesDb->sqlInjectionFilter($privilegi);
		$target_user = $trackMyMoviesDb->sqlInjectionFilter($target_user);
		$queryText = 'UPDATE Utente SET privilegi = ' . $privilegi . ' WHERE id_utente = ' . $target_user ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}
	function getUser($user){
		global $trackMyMoviesDb;
		$user = $trackMyMoviesDb->sqlInjectionFilter($user);
		$queryText = 'SELECT * FROM Utente WHERE id_utente = ' . $user . ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function listUsers($ristorante){
		global $trackMyMoviesDb;
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$queryText = 'SELECT id_utente, username FROM Utente WHERE ristorante = ' . $ristorante . ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}
// get_staff_stats(id, rest_id) -- invalid restaurant id TODO!!

// available components: stanza, tavolo, menu, piatto

	function addRoom($nome_stanza, $ristorante){
		global $trackMyMoviesDb;
		$nome_stanza = $trackMyMoviesDb->sqlInjectionFilter($nome_stanza);
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$queryText = 'INSERT INTO Stanza (nome_stanza, ristorante) VALUE (\'' . $nome_stanza . '\', ' . $ristorante');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function addTable($stanza, $ristorante){
		global $trackMyMoviesDb;
		$stanza = $trackMyMoviesDb->sqlInjectionFilter($stanza);
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$queryText = 'INSERT INTO Tavolo (stanza, ristorante) VALUE (' . $stanza . ', ' . $ristorante . ');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function addMenu($ristorante){
		global $trackMyMoviesDb;
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$queryText = 'INSERT INTO Menu (ristorante) VALUE (' . $ristorante . ');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function addDish($nome_piatto, $categoria, $prezzo, $ingredienti, $allergeni, $ristorante){
		global $trackMyMoviesDb;
		$nome_piatto = $trackMyMoviesDb->sqlInjectionFilter($nome_piatto);
		$categoria = $trackMyMoviesDb->sqlInjectionFilter($categoria);
		$prezzo = $trackMyMoviesDb->sqlInjectionFilter($prezzo);
		$ingredienti = $trackMyMoviesDb->sqlInjectionFilter($ingredienti);
		$allergeni = $trackMyMoviesDb->sqlInjectionFilter($allergeni);
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$queryText = 'INSERT INTO Piatto (nome, categoria, prezzo, ingredienti, allergeni, ristorante) VALUE (\'' . $nome_piatto '\', \'' . $categoria . '\', ' . $prezzo . ', \'' . $ingredienti . '\', \'' . $allergeni . '\', ' . $ristorante . ');'; //TODO check for allergeni in set format
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateRoom($nome_stanza, $stanza){
		global $trackMyMoviesDb;
		$nome_stanza = $trackMyMoviesDb->sqlInjectionFilter($nome_stanza);
		$stanza = $trackMyMoviesDb->sqlInjectionFilter($stanza);
		$queryText = 'UPDATE Stanza SET nome_stanza = \'' . $nome_stanza . '\' WHERE id_Stanza = ' . $stanza . ';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateTable($percentX, $percentY, $tavolo){
		global $trackMyMoviesDb;
		$percentX = $trackMyMoviesDb->sqlInjectionFilter($percentX);
		$percentY = $trackMyMoviesDb->sqlInjectionFilter($percentY);
		$tavolo = $trackMyMoviesDb->sqlInjectionFilter($tavolo);
		$queryText = 'UPDATE Tavolo SET percentX = ' . $percentX . ', percentY = ' . $percentY . ' WHERE id_Tavolo = ' . $tavolo';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateMenu($orario_inizio, $orario_fine, $menu){
		global $trackMyMoviesDb;
		$orario_inizio = $trackMyMoviesDb->sqlInjectionFilter($orario_inizio);
		$orario_fine = $trackMyMoviesDb->sqlInjectionFilter($orario_fine);
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Menu SET orarioInizio = \'' . $orario_inizio . '\', orarioFine = \'' . $orario_fine . '\' WHERE id_Menu = ' . $menu';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateDish($nome_piatto, $prezzo, $ingredienti, $allergeni, $piatto){
		global $trackMyMoviesDb;
		$nome_piatto = $trackMyMoviesDb->sqlInjectionFilter($nome_piatto);
		$prezzo = $trackMyMoviesDb->sqlInjectionFilter($prezzo);
		$ingredienti = $trackMyMoviesDb->sqlInjectionFilter($ingredienti);
		$allergeni = $trackMyMoviesDb->sqlInjectionFilter($allergeni);
		$piatto = $trackMyMoviesDb->sqlInjectionFilter($piatto);
		$queryText = 'UPDATE Piatto SET nome = \'' . $nome_piatto . '\', prezzo = ' . $prezzo . ', ingredienti = \'' . $ingredienti . '\', allergeni = \'' . $allergeni . '\' WHERE id_Piatto = ' . $piatto . ';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getRoom($stanza){
		global $trackMyMoviesDb;
		$stanza = $trackMyMoviesDb->sqlInjectionFilter($stanza);
		$queryText = 'SELECT * FROM Stanza WHERE id_Stanza = ' . $stanza . ';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getTable($tavolo){
		global $trackMyMoviesDb;
		$tavolo = $trackMyMoviesDb->sqlInjectionFilter($tavolo);
		$queryText = 'SELECT * FROM Tavolo WHERE id_Tavolo = ' . $tavolo';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getMenu($menu){
		global $trackMyMoviesDb;
		$menu = $trackMyMoviesDb->sqlInjectionFilter($menu);
		$queryText = 'SELECT * FROM Menu WHERE id_Menu = ' . $menu . ';'; // probably different: details on every dish, ordered by user-defined grouped categories (.. GROUP BY cat1 UNION .. GROUP BY cat2 ..)
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getDish($piatto){
		global $trackMyMoviesDb;
		$piatto = $trackMyMoviesDb->sqlInjectionFilter($piatto);
		$queryText = 'SELECT * FROM Piatto WHERE id_Piatto = ' . $piatto . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function removeDish($piatto){
		global $trackMyMoviesDb;
		$piatto = $trackMyMoviesDb->sqlInjectionFilter($piatto);
		$queryText = 'DELETE FROM Piatto WHERE id_Piatto = ' . $piatto . ';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function removeMenu($menu){
		global $trackMyMoviesDb;
		$menu = $trackMyMoviesDb->sqlInjectionFilter($menu);
		$queryText = 'DELETE FROM Menu WHERE id_Menu = ' . $menu . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function removeTable($tavolo){
		global $trackMyMoviesDb;
		$tavolo = $trackMyMoviesDb->sqlInjectionFilter($tavolo);
		$queryText = 'DELETE FROM Tavolo WHERE id_Tavolo = ' . $tavolo . ';'; /
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function removeRoom($stanza){
		global $trackMyMoviesDb;
		$stanza = $trackMyMoviesDb->sqlInjectionFilter($stanza);
		$queryText = 'DELETE FROM Stanza WHERE id_Stanza = ' . $stanza . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function addDishToMenu($menu, $piatto){
		global $trackMyMoviesDb;
		$menu = $trackMyMoviesDb->sqlInjectionFilter($menu);
		$piatto = $trackMyMoviesDb->sqlInjectionFilter($piatto);
		$queryText = 'INSERT INTO ComposizioneMenu (menu, piatto) VALUE (' . $menu . ', ' . $piatto . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}
	
	function removeDishFromMenu($menu, $piatto){
		global $trackMyMoviesDb;
		$menu = $trackMyMoviesDb->sqlInjectionFilter($menu);
		$piatto = $trackMyMoviesDb->sqlInjectionFilter($piatto);
		$queryText = 'DELETE FROM ComposizioneMenu WHERE menu = ' . $menu . ' AND piatto = ' . $piatto . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}


	function makeOrder($utente_ordine, $piatto, $quantita, $note, $tavolo, $stanza, $ristorante){
		global $trackMyMoviesDb;
		$utente_ordine = $trackMyMoviesDb->sqlInjectionFilter($utente_ordine);
		$piatto = $trackMyMoviesDb->sqlInjectionFilter($piatto);
		$quantita = $trackMyMoviesDb->sqlInjectionFilter($quantita);
		$note = $trackMyMoviesDb->sqlInjectionFilter($note);
		$tavolo = $trackMyMoviesDb->sqlInjectionFilter($tavolo);
		$stanza = $trackMyMoviesDb->sqlInjectionFilter($stanza);
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$queryText = 'CALL makeOrder(' . $utente_ordine . ', ' . $piatto . ', ' . $quantita . ', \'' . $note . '\', ' . $tavolo . ', ' . $stanza . ', ' . $ristorante . '); ';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getOrdersWaiting($user){
		global $trackMyMoviesDb;
		$user = $trackMyMoviesDb->sqlInjectionFilter($user);
		$queryText = 'SELECT O.*, P.* ' . 
		' FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto INNER JOIN Piatto P ON O.piatto = P.id_piatto ' .
		' WHERE ts_preparazione IS NULL' .
			' AND C.ristorante = (SELECT U.ristorante' .
								' FROM Utente U' .
								' WHERE id_utente = ' . $user . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getOrdersReady($user){
		global $trackMyMoviesDb;
		$user = $trackMyMoviesDb->sqlInjectionFilter($user);
		$queryText = 'SELECT O.* , P.* ' .
		' FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto INNER JOIN Piatto P ON O.piatto = P.id_piatto ' .
		' WHERE ts_preparazione IS NOT NULL' .
			' AND ts_consegna IS NULL' .
			' AND C.ristorante = (SELECT U.ristorante ' .
								' FROM Utente U '.
								' WHERE id_utente = ' . $user . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	fuction setPrepared($utente_preparazione, $odine{
		global $trackMyMoviesDb;
		$utente_preparazione = $trackMyMoviesDb->sqlInjectionFilter($utente_preparazione);
		$odine = $trackMyMoviesDb->sqlInjectionFilter($odine);
		$queryText = 'UPDATE Ordine SET ts_preparazione=CURRENT_TIMESTAMP, utente_preparazione = ' . $utente_preparazione . ' WHERE id_ordine = ' . $odine . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}
	
	fuction setPrepared($utente_consegna, $odine){
		global $trackMyMoviesDb;
		$utente_consegna = $trackMyMoviesDb->sqlInjectionFilter($utente_consegna);
		$odine = $trackMyMoviesDb->sqlInjectionFilter($odine);
		$queryText = 'UPDATE Ordine SET ts_consegna=CURRENT_TIMESTAMP, utente_consegna = ' . $utente_consegna . ' WHERE id_ordine = ' . $odine . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function review($valutazione, $recensione, $tipo_pagamento, $conto){
		global $trackMyMoviesDb;
		$valutazione = $trackMyMoviesDb->sqlInjectionFilter($valutazione);
		$recensione = $trackMyMoviesDb->sqlInjectionFilter($recensione);
		$tipo_pagamento = $trackMyMoviesDb->sqlInjectionFilter($tipo_pagamento);
		$conto = $trackMyMoviesDb->sqlInjectionFilter($conto);
		$queryText = 'UPDATE Conto SET valutazione = ' . $valutazione . ', recensione = \'' . $recensione . '\', tipo_pagamento = \'' . $tipo_pagamento . '\', ts_pagamento = CURRENT_TIMESTAMP WHERE id_conto = ' . $conto . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getMaxTableWait($ristorante, $stanza, $tavolo){
		global $trackMyMoviesDb;
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$stanza = $trackMyMoviesDb->sqlInjectionFilter($stanza);
		$tavolo = $trackMyMoviesDb->sqlInjectionFilter($tavolo);
		$queryText = 'SELECT MIN(ts_ordine) ' .
		' FROM Ordine ' .
		' WHERE conto = (SELECT id_conto ' .
						' FROM Conto ' .
						' WHERE ts_pagamento IS NULL ' .
							' AND ristorante = ' . $ristorante . 
							' AND stanza = ' . $stanza . 
							' AND tavolo = ' . $tavolo . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}
                    
	function getCheck($ristorante, $stanza, $tavolo){ // should be done after an update to calculate total
		global $trackMyMoviesDb;
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$stanza = $trackMyMoviesDb->sqlInjectionFilter($stanza);
		$tavolo = $trackMyMoviesDb->sqlInjectionFilter($tavolo);
		$queryText = 'SELECT * FROM Conto WHERE ristorante = ' . $ristorante . ' AND stanza = ' . $stanza . ' AND tavolo = ' . $tavolo . ' AND ts_pagamento IS NULL;';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	} 


	function writeMessage($from_user, $to_user, $msg){
		global $trackMyMoviesDb;
		$from_user = $trackMyMoviesDb->sqlInjectionFilter($from_user);
		$to_user = $trackMyMoviesDb->sqlInjectionFilter($to_user);
		$msg = $trackMyMoviesDb->sqlInjectionFilter($msg);
		$queryText = 'INSERT INTO Messaggio (from_user, to_user, msg, ts) VALUE (' . $from_user . ', ' . $to_user . ', \'' . $msg . '\', CURRENT_TIMESTAMP);';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	} 

	function getChats($user){
		global $trackMyMoviesDb;
		$user = $trackMyMoviesDb->sqlInjectionFilter($user);
		$queryText = 'SELECT SUM(!M.is_read) AS unread_msgs, M.from_user, ' .
						' (SELECT M2.msg ' .
						' FROM Messaggio M2 '.
						' WHERE M2.id_msg = (SELECT MAX(M3.id_msg) ' .
											' FROM Messaggio M3 ' .
											' WHERE M3.from_user = M.from_user ' .
												' AND M3.to_user = ' . $user . ')) AS last_msg ' .
						' FROM Messaggio M ' .
						' WHERE M.to_user = ' . $user .
						' GROUP BY M.from_user;';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function readMessages($user, $dest){
		global $trackMyMoviesDb;
		$user = $trackMyMoviesDb->sqlInjectionFilter($user);
		$dest = $trackMyMoviesDb->sqlInjectionFilter($dest);
		$queryText = 'CALL readMessages(' . $user . ', ' . $dest . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function sendRequest($user, $ristorante, $msg){
		global $trackMyMoviesDb;
		$user = $trackMyMoviesDb->sqlInjectionFilter($user);
		$ristorante = $trackMyMoviesDb->sqlInjectionFilter($ristorante);
		$msg = $trackMyMoviesDb->sqlInjectionFilter($msg);
		$queryText = 'CALL sendRequest(' . $user . ', ' . $ristorante . ', \'' . $msg . '\');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function processRequest($req, $accepted){
		global $trackMyMoviesDb;
		$req = $trackMyMoviesDb->sqlInjectionFilter($req);
		$accepted = $trackMyMoviesDb->sqlInjectionFilter($accepted);
		$queryText = 'CALL processRequest(' . $req . ', ' . $accepted . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}


?>