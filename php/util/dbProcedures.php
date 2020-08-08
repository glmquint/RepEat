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
		return $result; ;
	}
	*/

?>