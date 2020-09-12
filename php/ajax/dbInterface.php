<?php
    /**Interfaccia con cui avvengono tutte le interazioni con il database dall'esterno
     * 
     * L'implementazione di tutte le funzioni disponibili avviene in dbProcedures,
     * alcune di queste fanno riferimento a stored procedures implementate in sql
     * e si mantengono aggiornate tramite alcuni trigger (anch'essi sviluppati in sql).
     * 
     * Ogni azione avviene tramite una richiesta GET o POST che contiene sempre il parametro function
     * che viene confrontato con la lista di funzioni attualmente disponibili.
     * I due parametri 'user' e 'ristorante' vengono confrontati con i relativi valori
     * nelle variabili di sessioni, così da garantire la legittimità della richiesta */


    require_once __DIR__ . "/../config.php";
    require_once DIR_UTIL . "dbProcedures.php";
    require_once DIR_AJAX_UTIL . "AjaxResponse.php";
    require_once DIR_UTIL . "/userAuth.php";


    session_start();
    if (isLogged()) {        
        updateSessionVars($_SESSION['user_id']);
    }

    

    $available_functions = [
        //'addLicenseLevel', //available only to site administrator
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
        'getCurrentDishes',
        'getDish',
        'listRooms',
        'listDishes',
        'listMenus',
        'getMenu',
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
        'setDelivered',
        'review',
        'payCheck',
        'getMaxTableWait',
        'getCheck',
        'writeMessage',
        'getChats',
        'readMessages',
        'sendRequest',
        'processRequest',
        'existsRequest'];


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

        // La funzione richiesta viene eseguita con le altre variabili in $_REQUEST come parametri
        $raw_result = $_REQUEST['function']($_REQUEST);


        if (is_bool($raw_result[0])){
            // Se viene ritornato un booleano, questo deve essere negato (in modo da avere 0 => nessun errore, 1 => qualche errore)
            $response = new AjaxResponse(!$raw_result[0], $raw_result[1]);
            echo json_encode($response);
        }else if(is_string($raw_result)) {
            // La natura di stringa della risposta indica un errore nell'esecuzione della query
            $response = new AjaxResponse(-1, $raw_result);
            echo json_encode($response);
        } else {
            // In caso di query che ritorna con successo un resultset, questo verrà
            // sempre ritornato come un array bidimensionale (row x field)

            $result = Array();
            foreach (mysqli_fetch_all($raw_result[0]) as $row ) {
                $arr_row = Array();
                foreach (mysqli_fetch_fields($raw_result[0]) as $key => $value) {
                    $arr_row[$value->name] = $row[$key];
                }
                array_push($result, $arr_row);
            }
            $response = new AjaxResponse(0, '', $result);
            echo json_encode($response);
         }
    }
    
?>

