    <?php
        require_once __DIR__ . "/config.php";
        require_once DIR_UTIL . 'userAuth.php';
        require_once DIR_UTIL . "sessionUtil.php";
    
        if (isset($_POST['function'])) {
            echo '<span>' . login($_POST) . '</span>';
        }
        if (isLogged()){
                header('Location: ./home.php');
                exit;
        }	
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>repEat</title>
</head>
<body>
    <?php

    ?>

    <form action="" method="post">
    <input type="text" name="function" id="function" value='login' readonly hidden>
    <input type="text" name="username" id="username"><label for="username">username</label><br>
    <input type="password" name="password" id="password"><label for="password">password</label><br>
    <input type="submit" value="submit">
    </form>

    <p>Don't have an account? Register <a href="./register.php">here</a></p>
    <p>Back to <a href="../index.php">index</a></p>
 
</body>
</html>