<?php
    /**Pagina dedicata alla stampa dei menu disponibili
     * 
     * Passando l'id del menu tra i parametri get, è possibile avere una copia riassuntiva dei piatti,
     * con le relative caratteristiche, riassunti in un menu di cui partirà automaticamente il wizard di stampa
     * 
     * Questa funzionalità non è stata testata con fogli di dimensioni diverse dall'A4.
     * I risultati migliori sono stati ottenuti con il browser chrome fornito nel pacchetto per l'esame.
     */
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
    <link href='https://fonts.googleapis.com/css?family=Fredoka One&text=RepEat' rel='stylesheet'><!--Necessario per il font personalizzato del logo-->
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <link rel="shortcut icon" type="image/x-icon" href="../css/img/favicon.ico" />
    <title>RepEat</title>
</head>

<body onload="loadPrintMenu(<?php echo (isset($_GET['menu'])?$_GET['menu'] . ', ' . $_SESSION['ristorante']:''); ?>)">
    <div id="print-menu">
    <img src="../css/img/logo_title.svg" alt="RepEat">
    </div>
</body>
</html>
