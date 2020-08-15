<?php
	
	//setSession: set $_SESSION properly
	function setSession($userId){
		$_SESSION['user_id'] = $userId;
	}

	//isLogged: check if user has logged in and, if it is the case, returns the username
	function isLogged(){		
		if(isset($_SESSION['user_id']))
			return $_SESSION['user_id'];
		else
			return false;
	}

?>