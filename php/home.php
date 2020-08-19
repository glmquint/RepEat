<?php
	session_start();
    require_once __DIR__ . "/config.php";
    require_once DIR_UTIL . "/sessionUtil.php";

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
    <script src="../js/ajax/ajaxManager.js"></script>
    <title>repEat</title>
</head>
<body>
    <h1>Welcome home</h1>
    <?php
        echo '<iframe id="main-iframe" src="./layout/mainHome.php" frameborder="0" title = "main iframe" width=100% height=500px></iframe>';
        include DIR_LAYOUT . "mainNavBar.php";
    ?>
    <p>You can logout <a href="../php/logout.php">here</a></p>
</body>
</html>