<?php
	
	//setSession: set $_SESSION properly
	function setSession($username, $user_id, $privilegi){
		$_SESSION['username'] = $username;
		$_SESSION['user_id'] = $user_id;
		$_SESSION['privilegi'] = $privilegi;		
	}

	//isLogged: check if user has logged in and, if it is the case, returns the username
	function isLogged(){		
		if(isset($_SESSION['user_id']))
			return $_SESSION['user_id'];
		else
			return false;
	}

?>