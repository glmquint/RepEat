<?php
	
	//setSession: set $_SESSION properly
	function setSession($userId){
		$_SESSION['userId'] = $userId;
	}

	//isLogged: check if user has logged in and, if it is the case, returns the username
	function isLogged(){		
		if(isset($_SESSION['userId']))
			return $_SESSION['userId'];
		else
			return false;
	}

?>