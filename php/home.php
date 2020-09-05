<?php
	session_start();
    require_once __DIR__ . "/config.php";
    require_once DIR_UTIL . "/sessionUtil.php";

    if (!isLogged()){
		    header('Location: ../index.php');
		    exit;
    }	
?>

<!DOCTYPE html>
<html lang="it" class=<?php if (isset($_SESSION['pref_theme']) && $_SESSION['pref_theme'] == 'dark'){echo "dark-mode";}else{echo "";}?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/ajax/ajaxManager.js"></script>
    <script src="../js/ajax/preferences.js"></script>
    <script src="../js/ajax/messages.js"></script>
    <script src="../js/ajax/mainHome.js"></script>
    <script src="../js/ajax/admin.js"></script>
    <script src="../js/ajax/waiter.js"></script>
    <script src="../js/ajax/chef.js"></script>
    <script src="../js/ajax/cashier.js"></script>
    <script src="../js/ajax/missingRestaurant.js"></script>
    <script src="../js/alertsManager.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href='https://fonts.googleapis.com/css?family=Fredoka One&text=RepEat' rel='stylesheet'>
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <link rel="shortcut icon" type="image/x-icon" href="../css/img/favicon.ico" />
    <title>RepEat</title>
</head>
<body onLoad = "loadMainHome(<?php echo $_SESSION['user_id'] ?>)">
<button id="index-btn" onclick="window.location.href='../index.php'"><img src="../css/img/logo_squared.svg" alt="index"></button>

    <h1 id="main-h1">RepEat</h1>
    <button class = "light-switch" onclick="document.getElementsByTagName('html')[0].classList.toggle('dark-mode'); document.cookie='dark-mode = '+ document.getElementsByTagName('html')[0].classList.length +';expires=Wed, 18 Dec 2023 12:00:00 GMT'"></button>

    <div id="main-container"></div>
    <?php
        include DIR_LAYOUT . "mainNavBar.php";
    ?>
    <div id="alert-container">
        <!--p class="error-box">Errore: spiegazione dell'errore</p><p class="info-box">Errore: spiegazione dell'errore</p><p class="success-box">Errore: spiegazione dell'errore</p-->
    </div>
</body>
</html>