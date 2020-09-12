    <?php
        /**Pagina di autenticazione utenti
         * 
         * Quì gli utenti hanno la possibilità di effettuare il login del proprio account o,
         * nel caso non ne avessero uno, di passare all apagina di registrazione per crearne uno.
         */


        session_start();
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
<html lang="it" class=<?php if (isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode']){echo "dark-mode";}else{echo "";}?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"><!--Necessario per accedere velocemente ad alcune icone (l'alternativa è scaricare il pack e usarle come risorsa interna ma questa soluzione è la più rapida)-->
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <link rel="shortcut icon" type="image/x-icon" href="../css/img/favicon.ico" />
   <script src="../js/alertsManager.js"></script>
    <title>RepEat</title>
</head>
<body>
<button class = "light-switch" onclick="document.getElementsByTagName('html')[0].classList.toggle('dark-mode'); document.cookie='dark-mode = '+ document.getElementsByTagName('html')[0].classList.length +';path=/577923_quint;expires=Wed, 18 Dec 2023 12:00:00 GMT'"></button>

    <button id="index-btn" onclick="window.location.href='../index.php'"><img src="../css/img/logo_squared.svg" alt="index"></button>
    <div id="access-container">
        <h2>Login</h2>
        <form action="./login.php" method="post">
        <label for="username">username</label><br><input type="text" name="username" id="username" autofocus required onkeyup="this.classList.remove('invalid')"><br>
        <label for="password">password</label><br><input type="password" name="password" id="password" required onkeyup="this.classList.remove('invalid')"><br>
        <input type="submit" value="invia">
        </form>

        <p>Non hai un account? Registrati <a href="./register.php">quì</a></p>
    </div>
    <div id="alert-container"></div>
    <?php
        if (isset($_POST['username']) && isset($_POST['password'])) {
            echo '<script>sendAlert(\'' . $loginres . '\', \'error\');document.getElementById(\'username\').classList.add(\'invalid\');document.getElementById(\'password\').classList.add(\'invalid\');</script>';
        }
    ?>

</body>
</html>