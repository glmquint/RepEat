<?php
        /**Pagina di registrazione utenti
         * 
         * Quì gli utenti hanno la possibilità di registrare il proprio account o,
         * nel caso ne avessero già uno, di passare all apagina di login.
         * Al momento della scelta della password, apparirà un tooltip ricapitolativo
         * delle condizioni necessarie perchè la password sia considerata sicura.
         * Non è richiesta, ma viene apprezzata, l'inclusione di simboli.
         */

        session_start();
        require_once __DIR__ . "/config.php";
        require_once DIR_UTIL . 'userAuth.php';
        require_once DIR_UTIL . "sessionUtil.php";
    
        if (isset($_POST['username']) && isset($_POST['mail']) && isset($_POST['password'])) {
            $registerres = register($_POST);
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <link rel="shortcut icon" type="image/x-icon" href="../css/img/favicon.ico" />
    <script src="../js/alertsManager.js"></script>
    <script src="../js/accessBehaviour.js"></script>
    <title>RepEat</title>
</head>
<body>
<button class = "light-switch" onclick="document.getElementsByTagName('html')[0].classList.toggle('dark-mode'); document.cookie='dark-mode = '+ document.getElementsByTagName('html')[0].classList.length +';path=/577923_quint;expires=Wed, 18 Dec 2023 12:00:00 GMT'"></button>
<button id="index-btn" onclick="window.location.href='../index.php'"><img src="../css/img/logo_squared.svg" alt="index"></button>

    <div id="access-container">
        <h2>Register</h2>
        <form action="./register.php" method="post">
        <!--input type="text" name="function" id="function" value='register' readonly hidden-->
        <label for="username">username</label><br><input type="text" name="username" id="username" required autofocus><br>
        <label for="mail">mail</label><br><input type="mail" name="mail" id="mail" pattern="^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$" required onkeyup="this.setAttribute('value', this.value);"><br>
        <label for="password">password</label><br><input class="tooltip" type="password" name="password" id="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$" required onkeyup="this.setAttribute('value', this.value); checkPwd(this.value)"><div class="tooltiptext" ><ul><li id="pwdlength">8 caratteri</li><li id="pwdupper">maiuscole</li><li id="pwdlower">minuscole</li><li id="pwdnumber">numeri</li></ul> </div><br> <!--Minimo 8 caratter, almeno una lettera maiuscola, almeno una lettera minuscola ed un numero-->
        <input type="submit" value="invia">
        </form>

        <p>Sei già iscritto? Accedi <a href="./login.php">quì</a></p>
    </div>
    <div id="alert-container"></div>
    <?php
        if (isset($_POST['username']) && isset($_POST['password'])) {
            echo '<script>sendAlert(\'' . $registerres . '\', \'error\');</script>';
        }
    ?>

</body>
</html>