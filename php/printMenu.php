<?php
	session_start();
    require_once __DIR__ . "/config.php";
    require_once DIR_UTIL . "/sessionUtil.php";

    if (!isLogged() || !isset($_SESSION['ristorante'])){
        die(print_r($_SESSION));
		    header('Location: ../index.php');
		    exit;
    }	

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/ajax/ajaxManager.js"></script>
    <script src="../js/ajax/printMenu.js"></script>
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <title>RepEat</title>
</head>
<button id="index-btn" onclick="window.location.href='../index.php'"><img src="../css/img/logo_squared.svg" alt="index"></button>

<body onload="loadPrintMenu(<?php echo (isset($_GET['menu'])?$_GET['menu'] . ', ' . $_SESSION['ristorante']:''); ?>)">
<div id="print-menu"></div>
</body>
</html>
