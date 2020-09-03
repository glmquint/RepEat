<!DOCTYPE html>
<html lang="en" class=<?php if (isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode']){echo "dark-mode";}else{echo "";}?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/neonmorphism.css">
    <link rel="shortcut icon" type="image/x-icon" href="./css/img/favicon.ico" />
    <title>RepEat</title>
</head>
<body>
    <img id="logo-title" src="./css/img/logo_title.svg" alt="RepEat">
    
    <button class = "light-switch" onclick="document.getElementsByTagName('html')[0].classList.toggle('dark-mode'); document.cookie='dark-mode = '+ document.getElementsByTagName('html')[0].classList.length +';expires=Wed, 18 Dec 2023 12:00:00 GMT'"></button>

    <p>Go <a href="./php/ajax/dbInterface.php">here</a> to test the developer api!</p>
    <p>
    <?php
        session_start();
        require_once __DIR__ . "/php/config.php";
        include DIR_UTIL . "sessionUtil.php";

        if (!isLogged()){
                echo 'Login <a href="./php/login.php">here</a>';
        } else {
            echo 'Welcome ' . $_SESSION['username'] . '! You can go to <a href="./php/home.php">home</a> or logout <a href="./php/logout.php">here</a>';
        }

    ?></p>
    <p>Explore our <a href="./php/license.php">offers</a>!</p>
</body>
</html>