<?php


    require_once __DIR__ . "/../config.php";
    require_once DIR_UTIL . "dbProcedures.php";
    require_once DIR_AJAX_UTIL . "AjaxResponse.php";
    require_once DIR_UTIL . "/userAuth.php";


    session_start();
    updateSessionVars($_SESSION['user_id']);

    

    $available_functions = [
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
        'listRooms',
        'listDishes',
        'listDishesSimilar',
        'listMenus',
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
        'processRequest',
        'existsRequest'];

    /*echo '<br>begin SESSION: ';
    if (isset($_SESSION)) print_r($_SESSION);
    echo '<br>begin REQUEST: ';
    if (isset($_REQUEST)) print_r($_REQUEST);
    echo '<br>begin POST: ';
    if (isset($_POST)) print_r($_POST);
    echo '<br> <br>';*/

    if (isset($_REQUEST['function'])) {
        if (!in_array($_REQUEST['function'], $available_functions)) {
            $response = new AjaxResponse(1, 'The function ' . $_REQUEST['function'] . ' does not exists');
            echo json_encode($response);
            die();
        }

        if (array_key_exists('user', $_REQUEST) && (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $_REQUEST['user'])) {
            $response = new AjaxResponse(1, 'You don\'t have permission to operate on this user');
            echo json_encode($response);
            die();
        }
        if (array_key_exists('ristorante', $_REQUEST) && (!isset($_SESSION['ristorante']) || $_SESSION['ristorante'] != $_REQUEST['ristorante'])) {
            $response = new AjaxResponse(1, 'You don\'t have permission to operate on this restaurant');
            echo json_encode($response);
            die();
        }

        
        $raw_result = $_REQUEST['function']($_REQUEST);
        /*if($raw_result[0] == null){
            die(print_r($raw_result));
        }*/
        if (is_bool($raw_result[0])){
            $response = new AjaxResponse(!$raw_result[0], $raw_result[1]);
            echo json_encode($response);
        }else if(is_string($raw_result)) { //is_array($raw_result) || 
            $response = new AjaxResponse(-1, $raw_result);
            echo json_encode($response);
        } else {
            //echo json_encode((mysqli_fetch_fields($raw_result)[0]));
            //die(print_r(mysqli_fetch_fields($raw_result)));

            $result = Array();
            foreach (mysqli_fetch_all($raw_result[0]) as $row ) {
                $arr_row = Array();
                foreach (mysqli_fetch_fields($raw_result[0]) as $key => $value) {
                    //echo $value->name . '=' . $row[$key] . ' , ';
                    $arr_row[$value->name] = $row[$key];
                }
                array_push($result, $arr_row);
            }
            $response = new AjaxResponse(0, '', $result);
            echo json_encode($response);
         }
    }
    
?>

