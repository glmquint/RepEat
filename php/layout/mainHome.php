<head>
<meta charset="UTF-8">
<script src="../../js/ajax/ajaxManager.js"></script>
</head>
<script>


    function sendRequest(user, ristorante, msg) {
        AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=sendRequest&user=' + user + '&ristorante=' + ristorante + '&msg=' + msg, true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                alert('qualcosa è andato storto: ' + response['message']);
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
            if ($_SESSION['privilegi'] == null) {
                echo '<p>Sembra che tu non abbia alcun ruolo assegnato. Chiedi ad un amministratore di cambiare il tuo ruolo</p>';
            } else {
                if ($_SESSION['privilegi'] == 0) {
                    echo '<p>Sei un amministratore: puoi fare quello che vuoi';
                } else {
                    echo '<p>Ecco i tuoi roli disponibili: </p>';
                    echo ($_SESSION['privilegi'] & 1)?'cameriere ':'';
                    echo ($_SESSION['privilegi'] & 2)?'cuoco ':'';
                    echo ($_SESSION['privilegi'] & 4)?'cassa ':'';
                }
            }
        }
?>
<!--input type="text" name="function" id="function" value="sendRequest" readonly hidden>
<input type="number" name="user" id="" value = $_SESSION['user_id'] readonly hidden>
<select name="ristorante" id="" value="1">primo rist</select>
<textarea name="note" cols="30" rows="10" form="send-req-form"></textarea><label for="note">note</label>
<input type="submit" value="Invia">
</div-->


