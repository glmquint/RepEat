<?php
	
	//setSession: set $_SESSION properly
	function setSession($user_id, $username, $pref_theme, $privilegi, $ristorante){
		$_SESSION['user_id'] = $user_id;
		$_SESSION['username'] = $username;
		$_SESSION['pref_theme'] = $pref_theme;		
		$_SESSION['privilegi'] = $privilegi;
		$_SESSION['ristorante'] = $ristorante;
	}

	//isLogged: check if user has logged in and, if it is the case, returns the username
	function isLogged(){		
		if(isset($_SESSION['user_id']))
			return $_SESSION['user_id'];
		else
			return false;
	}

?>