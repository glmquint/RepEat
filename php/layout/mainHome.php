<head>
<meta charset="UTF-8">
<script src="../../js/ajax/ajaxManager.js"></script>
</head>
<script>


    function sendRequest(user, ristorante, msg) {
        AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=sendRequest&user=' + user + '&ristorante=' + ristorante + '&msg=' + msg, true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                console.log(response)
            } else {
                alert('Richiesta inviata con successo. Attendere che questa venga accettata da un amministratore');
                window.location.reload();
            }
        })
    };

</script>
<div id="main-container">
    
<?php
        require_once __DIR__ . "/../config.php";
        require_once DIR_UTIL . "/userAuth.php";
        session_start();
        updateSessionVars($_SESSION['user_id']);
        if (isset($_SESSION)) {
            print_r($_SESSION);
        }

        if (!isset($_SESSION['ristorante']) || $_SESSION['ristorante'] == null) {
            include DIR_LAYOUT . "missingRestaurants.php";
        } else {
            echo '<p>This is the main home!</p>';
        }
?>
<!--input type="text" name="function" id="function" value="sendRequest" readonly hidden>
<input type="number" name="user" id="" value = $_SESSION['user_id'] readonly hidden>
<select name="ristorante" id="" value="1">primo rist</select>
<textarea name="note" cols="30" rows="10" form="send-req-form"></textarea><label for="note">note</label>
<input type="submit" value="Invia">
</div-->


