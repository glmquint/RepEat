    <?php
        require_once __DIR__ . "/config.php";
        require_once DIR_UTIL . 'userAuth.php';
        require_once DIR_UTIL . "sessionUtil.php";
    
        if (isset($_POST['username']) && isset($_POST['password'])) {
            echo '<span>' . login($_POST) . '</span>';
        }
        if (isLogged()){
                header('Location: ./home.php');
                exit;
        }	
?>


<!DOCTYPE html>
<html lang="en" class=<?php if (isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode']){echo "dark-mode";}else{echo "";}?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <title>repEat</title>
</head>
<body>
<!--button class = "light-switch" onclick="document.getElementsByTagName('html')[0].classList.toggle('dark-mode'); document.cookie='dark-mode = '+ document.getElementsByTagName('html')[0].classList.length +';expires=Wed, 18 Dec 2023 12:00:00 GMT'"></button-->

    <?php

    ?>

    <form action="" method="post">
    <!--input type="text" name="function" id="function" value='login' readonly hidden-->
    <label for="username">username</label><input type="text" name="username" id="username"><br>
    <label for="password">password</label><input type="password" name="password" id="password"><br>
    <input type="submit" value="submit">
    </form>

    <p>Don't have an account? Register <a href="./register.php">here</a></p>
    <p>Back to <a href="../index.php">index</a></p>
 
</body>
</html>