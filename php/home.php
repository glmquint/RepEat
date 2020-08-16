<?php
	session_start();
    require_once __DIR__ . "/config.php";
    include DIR_UTIL . "sessionUtil.php";

    if (!isLogged()){
		    header('Location: ./../index.php');
		    exit;
    }	
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>repEat</title>
</head>
<body>
    <h1>Welcome home</h1>
    <?php
        if (isset($_SESSION)) {
            print_r($_SESSION);
        }
    ?>
    <p>You can logout <a href="../php/logout.php">here</a></p>
</body>
</html>