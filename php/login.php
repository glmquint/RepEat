    <?php
        require_once __DIR__ . "/config.php";
        require_once DIR_UTIL . 'userAuth.php';
        require_once DIR_UTIL . "sessionUtil.php";
    
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $loginres = login($_POST);
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <link rel="shortcut icon" type="image/x-icon" href="../css/img/favicon.ico" />
   <script src="../js/alertsManager.js"></script>
    <title>RepEat</title>
</head>
<body>
<!--button class = "light-switch" onclick="document.getElementsByTagName('html')[0].classList.toggle('dark-mode'); document.cookie='dark-mode = '+ document.getElementsByTagName('html')[0].classList.length +';expires=Wed, 18 Dec 2023 12:00:00 GMT'"></button-->

    <div id="access-container">
        <h2>Login</h2>
        <form action="" method="post">
        <!--input type="text" name="function" id="function" value='login' readonly hidden-->
        <label for="username">username</label><br><input type="text" name="username" id="username" autofocus required><br>
        <label for="password">password</label><br><input type="password" name="password" id="password" required><br>
        <input type="submit" value="submit">
        </form>

        <p>Don't have an account? Register <a href="./register.php">here</a></p>
        <p>Back to <a href="../index.php">index</a></p>
    </div>
    <div id="alert-container"></div>
    <?php
        if (isset($_POST['username']) && isset($_POST['password'])) {
            echo '<script>sendAlert(\'' . $loginres . '\', \'error\');</script>';
        }
    ?>

</body>
</html>