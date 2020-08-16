<?php

    require_once __DIR__ . "/../config.php";
    require_once DIR_UTIL . "repEatDbManager.php"; //includes Database Class
    require_once DIR_UTIL . "sessionUtil.php"; //includes session login

    function login ($param){   
        if (isset($param['username']) && $param['username'] != null) {
            $username = $param['username'];
        } else return 'Missing argument: username';
        if (isset($param['password']) && $param['password'] != null) {
            $password = $param['password'];
        } else return 'Missing argument: password';

        $user = authenticate($username, $password);
        if ($user[0] > 0){
            session_start();
            setSession($username, $user[0], $user[1]);
            return null;
        }

        
        return 'Username and password not valid.';
    }

    function register ($param){ 
        global $repEatDb;
        if (isset($param['mail']) && $param['mail'] != null){
            $mail = $repEatDb->sqlInjectionFilter($param['mail']);
        } else  return 'Missing argument: mail';

        if (isset($param['username']) && $param['username'] != null){
            $username = $repEatDb->sqlInjectionFilter($param['username']);
        } else  return 'Missing argument: username';

        if (isset($param['password']) && $param['password'] != null){
            $password = $repEatDb->sqlInjectionFilter($param['password']);
        } else  return 'Missing argument: password';

        $queryText = "insert into Utente (username, mail, password) VALUES ('" . $username . "', '" . $mail . "', '" . password_hash($password, PASSWORD_DEFAULT) . "');";
        $insertResult =$repEatDb->performQuery($queryText);
        $repEatDb->closeConnection();
        if (!$insertResult) return 'A problem occured while inserting the user. Try again later!';

            //header('location: ./../../register.php?errorMessage=' . $insertResult);

        return login($param);

        
        //return 'Username and password not valid.';
    }
    
    function authenticate ($username, $password){   
        global $repEatDb;
        $username = $repEatDb->sqlInjectionFilter($username);
        $password = $repEatDb->sqlInjectionFilter($password);

        $queryText = "select id_utente, password, privilegi from Utente where username='" . $username . /*"' AND password='" . $password . */"'";

        $result = $repEatDb->performQuery($queryText);
        $numRow = mysqli_num_rows($result);
        if ($numRow != 1)
            return [-1, null];
        
        $repEatDb->closeConnection();
        $userRow = $result->fetch_assoc();
        $repEatDb->closeConnection();
        if (!password_verify($password, $userRow['password']))
            return [-1, null];
        return [$userRow['id_utente'], $userRow['privilegi']];
    }

    /*function signin ($mail, $username, $password){  
        $uniq_fields = array("username", "mail"); 
        if ($mail != null && $username != null && $password != null){
            
            for ($i = 0; $i < count($uniq_fields); $i++){
                if(check_existing_fieldValue($uniq_fields[$i], $_POST[$uniq_fields[$i]])){
                    return "This " . $uniq_fields[$i] . " is already taken";
                }
            }

        } else
            return 'Every field is obligatory';
        
        return null;
    }

    function check_existing_fieldValue ($field, $value){ // FIX: This seems redundant...
        echo "\nfield: " . $field;
        echo "\nvalue: " . $value;
        global $repEatDb;
        if (is_null($repEatDb))
            echo "\nrepEat is null!: ";
        if (isset($param['value'])){
            $value = $repEatDb->sqlInjectionFilter($param['value']);
        } else  return 'Missing argument: value';


        $queryText = "select * from Utente where " . $field . " = '" . $value . "'";

        $result = $repEatDb->performQuery($queryText);
        $numRow = mysqli_num_rows($result);
        $repEatDb->closeConnection();
        return $numRow;
        
    }*/

?>