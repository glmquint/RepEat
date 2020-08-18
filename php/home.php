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
    <script src="../js/ajax/ajaxManager.js"></script>
    <title>repEat</title>
</head>
<body>
    <h1>Welcome home</h1>
    <?php
        if (isset($_SESSION)) {
            print_r($_SESSION);
        }
        echo '<iframe id="main-iframe" src="./layout/mainHome.php" frameborder="0" title = "main iframe"></iframe>';
        include DIR_LAYOUT . "mainNavBar.php";
    ?>
    <p>You can logout <a href="../php/logout.php">here</a></p>
</body>
</html>