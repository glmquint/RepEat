<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>RepEat</h1>
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
</body>
</html>