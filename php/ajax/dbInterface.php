<?php


session_start();

require_once __DIR__ . "/../config.php";
require_once DIR_UTIL . "dbProcedures.php";
require_once DIR_AJAX_UTIL . "AjaxResponse.php";

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

    echo 'begin REQUEST';
    if (isset($_REQUEST)) print_r($_REQUEST);
    echo 'begin POST';
    if (isset($_POST)) print_r($_POST);
    if (isset($_REQUEST['function'])) {
        if (isset($_GET['function']) && !in_array($_GET['function'], $available_get_functions)) {
            die('This function does not exists or must be accessed by a POST request');
        }
        $result = $_REQUEST['function']($_REQUEST);
        if (is_bool($result) || is_array($result)) {
    echo 'begin SESSION';
    if (isset($_SESSION)) print_r($_SESSION);
            print_r($result);
        } else {
            print_r(mysqli_fetch_all($result));
        }
        
    }
    
?>

<p><a href="../logout.php">Logout</a></p>
