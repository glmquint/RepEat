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

	function check_existing_fieldValue($field, $value){
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

	function addLicenseLevel($id_livello, $max_dipendenti, $max_tavoli, $max_menu, $max_stanze, $durata_validita, $prezzo){  // CURRENTLY NOT PUBLICLY AVAILABLE
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'INSERT INTO Livello (id_livello, max_dipendenti, max_tavoli, max_menu, max_stanze, durata_validita, prezzo) VALUES ('.
											$id_livello . ', ' . $max_dipendenti . ', ' . $max_tavoli . ', ' . $max_menu . ', ' . $max_stanze . ', ' . $durata_validita . ', ' . $prezzo . ');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;
	}

	function listLevels(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT * FROM Livello';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function generateKey($level){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'INSERT INTO Licenza (chiave, data_acquisto, livello) VALUE (FLOOR(RAND()*4294967295, CURRENT_DATE, ' . $level . ');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;
	}

	function registerRestaurant($nome_ristorante, $indirizzo, $license_key){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'INSERT INTO Ristorante (nome_ristorante, indirizzo, license_key) VALUE (\'' . $nome_ristorante . '\', \'' . $indirizzo . '\', ' . $license_key .');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function checkLicenseValidity($restaurant){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT IFNULL((SELECT CURRENT_DATE) < '.
						'(SELECT L.data_attivazione + INTERVAL (IF(Lv.durata_validita = 0, NULL, Lv.durata_validita)) DAY ' .
						'FROM Licenza L INNER JOIN Ristorante R ON L.chiave = R.license_key INNER JOIN Livello Lv ON L.livello = Lv.id_livello ' .
						'WHERE R.id_ristorante = '. $restaurant . '), 1);';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateRestaurant(){ // TODO: check isset($var) to make dynamic query
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Ristorante SET nome_ristorante = \'' . $nome_ristorante . '\', indirizzo = \''. $indirizzo . '\', limite_consegna_ordine = '. $limite_consegna_ordine . ', license_key = '. $license_key . ' WHERE id_ristorante = '. $ristorante . ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;
	}

	function getRestaurant($id){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT * FROM Ristorante WHERE id_ristorante = ' . $ristorante;
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;
	}


	function listRestaurants(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT nome_ristorante FROM Ristorante';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

//get_restaurant_detailed(id), TODO AS COMPOSITE FUNCTION IN PHP


	function updateUser(id, ...){ //TODO: check isset($var) to make dynamic query
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Utente SET username = \'' . $username . '\', mail = \'' . $mail . '\', password = \'' . password_hash($password, PASSWORD_DEFAULT) . '\', pref_theme = \'' . $pref_theme . '\', ristorante = ' . $ristorante . ' WHERE id_utente = ' . $utente . ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

// delete_user(id), -- DONT IMPLEMENT IT!!

	function setPrivilege(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Utente SET privilegi = ' . $privilegi . ' WHERE id_utente = ' . $target_user ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}
	function getUser(id){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT * FROM Utente WHERE id_utente = ' . $user . ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function listUsers(rest_id){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT id_utente, username FROM Utente WHERE ristorante = ' . $ristorante . ';';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}
// get_staff_stats(id, rest_id) -- invalid restaurant id TODO!!

// available components: stanza, tavolo, menu, piatto

	function addRoom(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'INSERT INTO Stanza (nome_stanza, ristorante) VALUE (\'' . $nome_stanza . '\', ' . $ristorante');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function addTable(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'INSERT INTO Tavolo (stanza, ristorante) VALUE (' . $stanza . ', ' . $ristorante . ');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function addMenu(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'INSERT INTO Menu (ristorante) VALUE (' . $ristorante . ');';
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function addDish(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'INSERT INTO Piatto (nome, categoria, prezzo, ingredienti, allergeni, ristorante) VALUE (\'' . $nome_piatto '\', \'' . $categoria . '\', ' . $prezzo . ', \'' . $ingredienti . '\', \'' . $allergeni . '\', ' . $ristorante . ');'; //TODO check for allergeni in set format
						
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateRoom(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Stanza SET nome_stanza = \'' . $nome_stanza . '\' WHERE id_Stanza = ' . $stanza . ';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateTable(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Tavolo SET percentX = ' . $percentX . ', percentY = ' . $percentY . ' WHERE id_Tavolo = ' . $tavolo';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateMenu(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Menu SET orarioInizio = \'' . $orario_inizio . '\', orarioFine = \'' . $orario_fine . '\' WHERE id_Menu = ' . $menu';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function updateDish(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Piatto SET nome = \'' . $nome_piatto . '\', prezzo = ' . $prezzo . ', ingredienti = \'' . $ingredienti . '\', allergeni = \'' . $allergeni . '\' WHERE id_Piatto = ' . $piatto . ';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getRoom(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT * FROM Stanza WHERE id_Stanza = ' . $stanza . ';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getTable(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT * FROM Tavolo WHERE id_Tavolo = ' . $tavolo';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getMenu(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT * FROM Menu WHERE id_Menu = ' . $menu . ';'; // probably different: details on every dish, ordered by user-defined grouped categories (.. GROUP BY cat1 UNION .. GROUP BY cat2 ..)
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getDish(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT * FROM Piatto WHERE id_Piatto = ' . $piatto . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function removeDish(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'DELETE FROM Piatto WHERE id_Piatto = ' . $piatto . ';';	
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function removeMenu(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'DELETE FROM Menu WHERE id_Menu = ' . $menu . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function removeTable(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'DELETE FROM Tavolo WHERE id_Tavolo = ' . $tavolo . ';'; /
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function removeRoom(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'DELETE FROM Stanza WHERE id_Stanza = ' . $stanza . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function addDishToMenu(id_menu, id_piatto){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'INSERT INTO ComposizioneMenu (menu, piatto) VALUE (' . $menu . ', ' . $piatto . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}
	
	function removeDishFromMenu(id_menu, id_piatto){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'DELETE FROM ComposizioneMenu WHERE menu = ' . $menu . ' AND piatto = ' . $piatto . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}


	function makeOrder(){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'CALL makeOrder(' . $utente_ordine . ', ' . $piatto . ', ' . $quantita . ', \'' . $note . '\', ' . $tavolo . ', ' . $stanza . ', ' . $ristorante . '); ';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getOrdersWaiting(id){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
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

	function getOrdersReady(id){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT O.* , P.*
		' FROM Ordine O INNER JOIN Conto C ON O.conto = C.id_conto INNER JOIN Piatto P ON O.piatto = P.id_piatto ' .'
		' WHERE ts_preparazione IS NOT NULL' .
			' AND ts_consegna IS NULL' .
			' AND C.ristorante = (SELECT U.ristorante ' .
								' FROM Utente U '.
								' WHERE id_utente = ' . $id . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	fuction setPrepared(id, order_id){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Ordine SET ts_preparazione=CURRENT_TIMESTAMP, utente_preparazione = ' . $utente_preparazione . ' WHERE id_ordine = ' . $odine . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}
	
	fuction setPrepared(id, order_id){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Ordine SET ts_consegna=CURRENT_TIMESTAMP, utente_consegna = ' . $utente_consegna . ' WHERE id_ordine = ' . $odine . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function review(check_id, valutazione, recensione, tipo_pagamento){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'UPDATE Conto SET valutazione = ' . $valutazione . ', recensione = \'' . $valutazione . '\', tipo_pagamento = \'' . $tipo_pagamento . '\', ts_pagamento = CURRENT_TIMESTAMP WHERE id_conto = ' . $conto . ';';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function getMaxTableWait(ristorante, stanza, table_id){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
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
                    
	function getCheck(id, table_id){ // should be done after an update to calculate total
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'SELECT * FROM Conto WHERE ristorante = ' . $ristorante . ' AND stanza = ' . $stanza . ' AND tavolo = ' . $tavolo . ' AND ts_pagamento IS NULL;';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	} 


	function writeMessage(id, ...){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'INSERT INTO Messaggio (from_user, to_user, msg, ts) VALUE (' . $from_user . ', ' . $to_user . ', \'' . $msg . '\', CURRENT_TIMESTAMP);';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	} 

	function getChats(id){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
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

	function readMessages(id, dest){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'CALL readMessages(' . $user . ', ' . $dest . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function sendRequest(id, dest){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'CALL sendRequest(' . $user . ', ' . $ristorante . ', \'' . $msg . '\');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}

	function processRequest(id, dest){
		global $trackMyMoviesDb;
		$parameters = $trackMyMoviesDb->sqlInjectionFilter($parameters);
		$queryText = 'CALL processRequest(' . $req . ', ' . $accepted . ');';
		$result = $trackMyMoviesDb->performQuery($queryText);
		$trackMyMoviesDb->closeConnection();
		return $result;

	}


?>