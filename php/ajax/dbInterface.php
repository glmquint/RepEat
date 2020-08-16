<?php


    require_once __DIR__ . "/../config.php";
    require_once DIR_UTIL . "dbProcedures.php";
    require_once DIR_AJAX_UTIL . "AjaxResponse.php";

    if (isLogged()) {
        session_start();
    }

    $response = new AjaxResponse();
    
    $available_post_functions = [
        'login', 
        'register'];
    $available_get_functions = [
        'addLicenseLevel',
        'listLevels',
        'generateKey',
        'registerRestaurant',
        'checkLicenseValidity',
        'updateRestaurant',
        'getRestaurant',
        'listRestaurants',
        'updateUser',
        'setPrivilege',
        'getUser',
        'listUsers',
        'addRoom',
        'addTable',
        'addMenu',
        'addDish',
        'updateRoom',
        'updateTable',
        'updateMenu',
        'updateDish',
        'getRoom',
        'getTable',
        'getMenu',
        'getDish',
        'removeDish',
        'removeMenu',
        'removeTable',
        'removeRoom',
        'addDishToMenu',
        'removeDishFromMenu',
        'makeOrder',
        'getOrdersWaiting',
        'getOrdersReady',
        'setPrepared',
        'setPrepared',
        'review',
        'getMaxTableWait',
        'getCheck',
        'writeMessage',
        'getChats',
        'readMessages',
        'sendRequest',
        'processRequest'];

    echo '<br>begin SESSION: ';
    if (isset($_SESSION)) print_r($_SESSION);
    echo '<br>begin REQUEST: ';
    if (isset($_REQUEST)) print_r($_REQUEST);
    echo '<br>begin POST: ';
    if (isset($_POST)) print_r($_POST);
    echo '<br> <br>';

    if (isset($_REQUEST['function'])) {
        if (!in_array($_REQUEST['function'], $available_get_functions) && !in_array($_REQUEST['function'], $available_post_functions)) {
            die('This function does not exists');
        }
        if ((isset($_GET['function']) && !in_array($_GET['function'], $available_get_functions)) || (isset($_POST['function']) && !in_array($_POST['function'], $available_post_functions))) {
            die('Wrong method for this function');
        }

        $result = $_REQUEST['function']($_REQUEST);
        if (is_bool($result) || is_array($result) || is_string($result)) {
            print_r($result);
         } else 
            print_r(mysqli_fetch_all($result));
        
    }
    
?>

<form action="../util/dbProcedures.php" method="post">
<input type="text" name="function" id="function" value='login' readonly>
<input type="text" name="username" id="username">
<input type="password" name="password" id="password">
<button type="submit">Invia</button>
</form>

<p><a href="../logout.php">Logout</a></p>
