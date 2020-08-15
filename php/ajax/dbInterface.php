<?php


//session_start();

require_once __DIR__ . "/../config.php";
require_once DIR_UTIL . "dbProcedures.php";
require_once DIR_AJAX_UTIL . "AjaxResponse.php";

    $response = new AjaxResponse();
    
    /*$available_functions = ['login',
    'register',
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
    'processRequest'];*/

    if (isset($_GET['function'])) {
        $result = $_GET['function']($_GET);
        if (is_bool($result)) {
            echo $result;
        } else {
            print_r(mysqli_fetch_all($result));
        }
        
    }
    
?>