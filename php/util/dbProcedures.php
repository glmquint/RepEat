<?php
	require_once __DIR__ . "/../config.php";
    require_once DIR_UTIL . "repEatDbManager.php"; //includes Database Class


	function login ($username, $password){   
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

	function register ($mail, $username, $password){   
		if ($mail != null && $username != null && $password != null){
			global $repEatDb;
			$queryText = "insert into user (username, mail, password) VALUES ('" . $username . "', '" . $mail . "', '" . password_hash($password, PASSWORD_DEFAULT) . "'); -- select id from user where ;";
			$insertResult =$repEatDb->performQuery($queryText);
			$repEatDb->closeConnection();
			if (!is_null($insertResult))
				header('location: ./../../register.php?errorMessage=' . $insertResult);

			return login($username, $password);

    	} else
    		return 'You should insert something';
    	
    	return 'Username and password not valid.';
	}
	
	function authenticate ($username, $password){   
		global $repEatDb;
		$username =$repEatDb->sqlInjectionFilter($username);
		$password =$repEatDb->sqlInjectionFilter($password);

		$queryText = "select userId, password, privilegi from user where username='" . $username . /*"' AND password='" . $password . */"'";

		$result = $repEatDb->performQuery($queryText);
		$numRow = mysqli_num_rows($result);
		if ($numRow != 1)
			return [-1, null];
		
		$repEatDb->closeConnection();
		$userRow = $result->fetch_assoc();
		$repEatDb->closeConnection();
		if (!password_verify($password, $userRow['password']))
			return [-1, null];
		return [$userRow['userId'], $userRow['privilegi']];
	}

	function signin ($mail, $username, $password){  
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

	function check_existing_fieldValue ($field, $value){ // FIX: This seems redundant...
		echo "\nfield: " . $field;
		echo "\nvalue: " . $value;
		global $repEatDb;
		if (is_null($repEatDb))
			echo "\nrepEat is null!: ";
		$value = $repEatDb->sqlInjectionFilter($param['value']);

		$queryText = "select * from user where " . $field . " = '" . $value . "'";

		$result = $repEatDb->performQuery($queryText);
		$numRow = mysqli_num_rows($result);
		$repEatDb->closeConnection();
		return $numRow;
		
	}

	/*--------------------------------------------------------*/

	function addLicenseLevel($param){  // CURRENTLY NOT AVAILABLE
		global $repEatDb;
		$id_livello = $repEatDb->sqlInjectionFilter($param['id_livello']);
		$max_dipendenti = $repEatDb->sqlInjectionFilter($param['max_dipendenti']);
		$max_tavoli = $repEatDb->sqlInjectionFilter($param['max_tavoli']);
		$max_menu = $repEatDb->sqlInjectionFilter($param['max_menu']);
		$max_stanze = $repEatDb->sqlInjectionFilter($param['max_stanze']);
		$durata_validita = $repEatDb->sqlInjectionFilter($param['durata_validita']);
		$prezzo = $repEatDb->sqlInjectionFilter($param['prezzo']);
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
		$level = $repEatDb->sqlInjectionFilter($param['level']);
		$queryText = 'CALL generateKey(' . $level . ');';		
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;
	}

	function registerRestaurant($param){
		global $repEatDb;
		$nome_ristorante = $repEatDb->sqlInjectionFilter($param['nome_ristorante']);
		$indirizzo = $repEatDb->sqlInjectionFilter($param['indirizzo']);
		$license_key = $repEatDb->sqlInjectionFilter($param['license_key']);
		$queryText = 'INSERT INTO Ristorante (nome_ristorante, indirizzo, license_key) VALUE (\'' . $nome_ristorante . '\', \'' . $indirizzo . '\', ' . $license_key .');';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function checkLicenseValidity($param){
		global $repEatDb;
		$restaurant = $repEatDb->sqlInjectionFilter($param['restaurant']);
		$queryText = 'SELECT IFNULL((SELECT CURRENT_DATE) < '.
						'(SELECT L.data_attivazione + INTERVAL (IF(Lv.durata_validita = 0, NULL, Lv.durata_validita)) DAY ' .
						'FROM Licenza L INNER JOIN Ristorante R ON L.chiave = R.license_key INNER JOIN Livello Lv ON L.livello = Lv.id_livello ' .
						'WHERE R.id_ristorante = '. $restaurant . '), 1);';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateRestaurant($param){ // TODO: check isset($var) to make dynamic query
		global $repEatDb;
		$nome_ristorante = $repEatDb->sqlInjectionFilter($param['nome_ristorante']);
		$indirizzo = $repEatDb->sqlInjectionFilter($param['indirizzo']);
		$limite_consegna_ordine = $repEatDb->sqlInjectionFilter($param['limite_consegna_ordine']);
		$license_key = $repEatDb->sqlInjectionFilter($param['license_key']);
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$queryText = 'UPDATE Ristorante SET nome_ristorante = \'' . $nome_ristorante . '\', indirizzo = \''. $indirizzo . '\', limite_consegna_ordine = '. $limite_consegna_ordine . ', license_key = '. $license_key . ' WHERE id_ristorante = '. $ristorante . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;
	}

	function getRestaurant($param){
		global $repEatDb;
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$queryText = 'SELECT * FROM Ristorante WHERE id_ristorante = ' . $ristorante;
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;
	}


	function listRestaurants(){
		global $repEatDb;
		$queryText = 'SELECT nome_ristorante FROM Ristorante';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

//get_restaurant_detailed(id), TODO AS COMPOSITE FUNCTION IN PHP


	function updateUser($param){ //TODO: check isset($var) to make dynamic query
		global $repEatDb;
		$username = $repEatDb->sqlInjectionFilter($param['username']);
		$mail = $repEatDb->sqlInjectionFilter($param['mail']);
		$password = $repEatDb->sqlInjectionFilter($param['password']);
		$pref_theme = $repEatDb->sqlInjectionFilter($param['pref_theme']);
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$utente = $repEatDb->sqlInjectionFilter($param['utente']);
		$queryText = 'UPDATE Utente SET username = \'' . $username . '\', mail = \'' . $mail . '\', password = \'' . password_hash($password, PASSWORD_DEFAULT) . '\', pref_theme = \'' . $pref_theme . '\', ristorante = ' . $ristorante . ' WHERE id_utente = ' . $utente . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

// delete_user(id), -- DONT IMPLEMENT IT!!

	function setPrivilege($param){
		global $repEatDb;
		$privilegi = $repEatDb->sqlInjectionFilter($param['privilegi']);
		$target_user = $repEatDb->sqlInjectionFilter($param['target_user']);
		$queryText = 'UPDATE Utente SET privilegi = ' . $privilegi . ' WHERE id_utente = ' . $target_user . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}
	function getUser($param){
		global $repEatDb;
		$user = $repEatDb->sqlInjectionFilter($param['user']);
		$queryText = 'SELECT * FROM Utente WHERE id_utente = ' . $user . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function listUsers($param){
		global $repEatDb;
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$queryText = 'SELECT id_utente, username FROM Utente WHERE ristorante = ' . $ristorante . ';';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}
// get_staff_stats(id, rest_id) -- invalid restaurant id TODO!!

// available components: stanza, tavolo, menu, piatto

	function addRoom($param){
		global $repEatDb;
		$nome_stanza = $repEatDb->sqlInjectionFilter($param['nome_stanza']);
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$queryText = 'INSERT INTO Stanza (nome_stanza, ristorante) VALUE (\'' . $nome_stanza . '\', ' . $ristorante . ');';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function addTable($param){
		global $repEatDb;
		$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$queryText = 'INSERT INTO Tavolo (stanza, ristorante) VALUE (' . $stanza . ', ' . $ristorante . ');';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function addMenu($param){
		global $repEatDb;
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$queryText = 'INSERT INTO Menu (ristorante) VALUE (' . $ristorante . ');';
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function addDish($param){
		global $repEatDb;
		$nome_piatto = $repEatDb->sqlInjectionFilter($param['nome_piatto']);
		$categoria = $repEatDb->sqlInjectionFilter($param['categoria']);
		$prezzo = $repEatDb->sqlInjectionFilter($param['prezzo']);
		$ingredienti = $repEatDb->sqlInjectionFilter($param['ingredienti']);
		$allergeni = $repEatDb->sqlInjectionFilter($param['allergeni']);
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$queryText = 'INSERT INTO Piatto (nome, categoria, prezzo, ingredienti, allergeni, ristorante) VALUE (\'' . $nome_piatto . '\', \'' . $categoria . '\', ' . $prezzo . ', \'' . $ingredienti . '\', \'' . $allergeni . '\', ' . $ristorante . ');'; //TODO check for allergeni in set format
						
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateRoom($param){
		global $repEatDb;
		$nome_stanza = $repEatDb->sqlInjectionFilter($param['nome_stanza']);
		$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		$queryText = 'UPDATE Stanza SET nome_stanza = \'' . $nome_stanza . '\' WHERE id_Stanza = ' . $stanza . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateTable($param){
		global $repEatDb;
		$percentX = $repEatDb->sqlInjectionFilter($param['percentX']);
		$percentY = $repEatDb->sqlInjectionFilter($param['percentY']);
		$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		$queryText = 'UPDATE Tavolo SET percentX = ' . $percentX . ', percentY = ' . $percentY . ' WHERE id_Tavolo = ' . $tavolo . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateMenu($param){
		global $repEatDb;
		$orario_inizio = $repEatDb->sqlInjectionFilter($param['orario_inizio']);
		$orario_fine = $repEatDb->sqlInjectionFilter($param['orario_fine']);
		$parameters = $repEatDb->sqlInjectionFilter($param['parameters']);
		$queryText = 'UPDATE Menu SET orarioInizio = \'' . $orario_inizio . '\', orarioFine = \'' . $orario_fine . '\' WHERE id_Menu = ' . $menu . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function updateDish($param){
		global $repEatDb;
		$nome_piatto = $repEatDb->sqlInjectionFilter($param['nome_piatto']);
		$prezzo = $repEatDb->sqlInjectionFilter($param['prezzo']);
		$ingredienti = $repEatDb->sqlInjectionFilter($param['ingredienti']);
		$allergeni = $repEatDb->sqlInjectionFilter($param['allergeni']);
		$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		$queryText = 'UPDATE Piatto SET nome = \'' . $nome_piatto . '\', prezzo = ' . $prezzo . ', ingredienti = \'' . $ingredienti . '\', allergeni = \'' . $allergeni . '\' WHERE id_Piatto = ' . $piatto . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getRoom($param){
		global $repEatDb;
		$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		$queryText = 'SELECT * FROM Stanza WHERE id_Stanza = ' . $stanza . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getTable($param){
		global $repEatDb;
		$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		$queryText = 'SELECT * FROM Tavolo WHERE id_Tavolo = ' . $tavolo . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getMenu($param){
		global $repEatDb;
		$menu = $repEatDb->sqlInjectionFilter($param['menu']);
		$queryText = 'SELECT * FROM Menu WHERE id_Menu = ' . $menu . ';'; // probably different: details on every dish, ordered by user-defined grouped categories (.. GROUP BY cat1 UNION .. GROUP BY cat2 ..)
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getDish($param){
		global $repEatDb;
		$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		$queryText = 'SELECT * FROM Piatto WHERE id_Piatto = ' . $piatto . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function removeDish($param){
		global $repEatDb;
		$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		$queryText = 'DELETE FROM Piatto WHERE id_Piatto = ' . $piatto . ';';	
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function removeMenu($param){
		global $repEatDb;
		$menu = $repEatDb->sqlInjectionFilter($param['menu']);
		$queryText = 'DELETE FROM Menu WHERE id_Menu = ' . $menu . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function removeTable($param){
		global $repEatDb;
		$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		$queryText = 'DELETE FROM Tavolo WHERE id_Tavolo = ' . $tavolo . ';'; 
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function removeRoom($param){
		global $repEatDb;
		$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		$queryText = 'DELETE FROM Stanza WHERE id_Stanza = ' . $stanza . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function addDishToMenu($param){
		global $repEatDb;
		$menu = $repEatDb->sqlInjectionFilter($param['menu']);
		$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		$queryText = 'INSERT INTO ComposizioneMenu (menu, piatto) VALUE (' . $menu . ', ' . $piatto . ');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}
	
	function removeDishFromMenu($param){
		global $repEatDb;
		$menu = $repEatDb->sqlInjectionFilter($param['menu']);
		$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		$queryText = 'DELETE FROM ComposizioneMenu WHERE menu = ' . $menu . ' AND piatto = ' . $piatto . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}


	function makeOrder($param){
		global $repEatDb;
		$utente_ordine = $repEatDb->sqlInjectionFilter($param['utente_ordine']);
		$piatto = $repEatDb->sqlInjectionFilter($param['piatto']);
		$quantita = $repEatDb->sqlInjectionFilter($param['quantita']);
		$note = $repEatDb->sqlInjectionFilter($param['note']);
		$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$queryText = 'CALL makeOrder(' . $utente_ordine . ', ' . $piatto . ', ' . $quantita . ', \'' . $note . '\', ' . $tavolo . ', ' . $stanza . ', ' . $ristorante . '); ';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getOrdersWaiting($param){
		global $repEatDb;
		$user = $repEatDb->sqlInjectionFilter($param['user']);
		$queryText = 'SELECT O.*, P.* ' . 
		' FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto INNER JOIN Piatto P ON O.piatto = P.id_piatto ' .
		' WHERE ts_preparazione IS NULL' .
			' AND C.ristorante = (SELECT U.ristorante' .
								' FROM Utente U' .
								' WHERE id_utente = ' . $user . ');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getOrdersReady($param){
		global $repEatDb;
		$user = $repEatDb->sqlInjectionFilter($param['user']);
		$queryText = 'SELECT O.* , P.* ' .
		' FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto INNER JOIN Piatto P ON O.piatto = P.id_piatto ' .
		' WHERE ts_preparazione IS NOT NULL' .
			' AND ts_consegna IS NULL' .
			' AND C.ristorante = (SELECT U.ristorante ' .
								' FROM Utente U '.
								' WHERE id_utente = ' . $user . ');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function setPrepared($param){
		global $repEatDb;
		$utente_preparazione = $repEatDb->sqlInjectionFilter($param['utente_preparazione']);
		$ordine = $repEatDb->sqlInjectionFilter($param['ordine']);
		$queryText = 'UPDATE Ordine SET ts_preparazione=CURRENT_TIMESTAMP, utente_preparazione = ' . $utente_preparazione . ' WHERE id_ordine = ' . $ordine . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}
	
	function setReady($param){
		global $repEatDb;
		$utente_consegna = $repEatDb->sqlInjectionFilter($param['utente_consegna']);
		$ordine = $repEatDb->sqlInjectionFilter($param['ordine']);
		$queryText = 'UPDATE Ordine SET ts_consegna=CURRENT_TIMESTAMP, utente_consegna = ' . $utente_consegna . ' WHERE id_ordine = ' . $ordine . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function review($param){
		global $repEatDb;
		$valutazione = $repEatDb->sqlInjectionFilter($param['valutazione']);
		$recensione = $repEatDb->sqlInjectionFilter($param['recensione']);
		$tipo_pagamento = $repEatDb->sqlInjectionFilter($param['tipo_pagamento']);
		$conto = $repEatDb->sqlInjectionFilter($param['conto']);
		$queryText = 'UPDATE Conto SET valutazione = ' . $valutazione . ', recensione = \'' . $recensione . '\', tipo_pagamento = \'' . $tipo_pagamento . '\', ts_pagamento = CURRENT_TIMESTAMP WHERE id_conto = ' . $conto . ';';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function getMaxTableWait($param){
		global $repEatDb;
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
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
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$stanza = $repEatDb->sqlInjectionFilter($param['stanza']);
		$tavolo = $repEatDb->sqlInjectionFilter($param['tavolo']);
		$queryText = 'SELECT * FROM Conto WHERE ristorante = ' . $ristorante . ' AND stanza = ' . $stanza . ' AND tavolo = ' . $tavolo . ' AND ts_pagamento IS NULL;';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	} 


	function writeMessage($param){
		global $repEatDb;
		$from_user = $repEatDb->sqlInjectionFilter($param['from_user']);
		$to_user = $repEatDb->sqlInjectionFilter($param['to_user']);
		$msg = $repEatDb->sqlInjectionFilter($param['msg']);
		$queryText = 'INSERT INTO Messaggio (from_user, to_user, msg, ts) VALUE (' . $from_user . ', ' . $to_user . ', \'' . $msg . '\', CURRENT_TIMESTAMP);';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	} 

	function getChats($param){
		global $repEatDb;
		$user = $repEatDb->sqlInjectionFilter($param['user']);
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
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function readMessages($param){
		global $repEatDb;
		$user = $repEatDb->sqlInjectionFilter($param['user']);
		$dest = $repEatDb->sqlInjectionFilter($param['dest']);
		$queryText = 'CALL readMessages(' . $user . ', ' . $dest . ');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function sendRequest($param){
		global $repEatDb;
		$user = $repEatDb->sqlInjectionFilter($param['user']);
		$ristorante = $repEatDb->sqlInjectionFilter($param['ristorante']);
		$msg = $repEatDb->sqlInjectionFilter($param['msg']);
		$queryText = 'CALL sendRequest(' . $user . ', ' . $ristorante . ', \'' . $msg . '\');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}

	function processRequest($param){
		global $repEatDb;
		$req = $repEatDb->sqlInjectionFilter($param['req']);
		$accepted = $repEatDb->sqlInjectionFilter($param['accepted']);
		$queryText = 'CALL processRequest(' . $req . ', ' . $accepted . ');';
		$result = $repEatDb->performQuery($queryText);
		$repEatDb->closeConnection();
		return $result;

	}


?>