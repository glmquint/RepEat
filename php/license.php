<!DOCTYPE html>
<html lang="en" class=<?php if (isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode']){echo "dark-mode";}else{echo "";}?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/ajax/ajaxManager.js"></script>
    <script src="../js/ajax/license.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="shortcut icon" type="image/x-icon" href="../css/img/favicon.ico" />
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <title>repEat</title>
</head>
<body onLoad="load()">
<a id="index-btn" href="../index.php"><button href="../index.php"><img src="../css/img/logo_squared.svg" alt="index"></button></a>

    <table id='offers'></table>
    <p id='key'></p>
</body>
</html>