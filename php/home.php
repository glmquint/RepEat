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
<html lang="en" class=<?php if (isset($_SESSION['pref_theme']) && $_SESSION['pref_theme'] == 'dark'){echo "dark-mode";}else{echo "";}?>>
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
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <title>repEat</title>
</head>
<body onLoad = "loadMainHome(<?php echo $_SESSION['user_id'] ?>)">
    <h1>Welcome home</h1>
    <button class = "light-switch" onclick="document.getElementsByTagName('html')[0].classList.toggle('dark-mode'); document.cookie='dark-mode = '+ document.getElementsByTagName('html')[0].classList.length +';expires=Wed, 18 Dec 2023 12:00:00 GMT'"></button>

    <span id="main-container"></span>
    <?php
        //echo '<iframe id="main-iframe" src="./layout/mainHome.php" frameborder="0" title = "main iframe" width=100% height=500px></iframe>';
        //echo '<link rel="import" id="main-link" href="./layout/mainHome.php">';
        include DIR_LAYOUT . "mainNavBar.php";
    ?>
    <p>You can logout <a href="../php/logout.php">here</a></p>
</body>
</html>