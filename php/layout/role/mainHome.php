<head>
<meta charset="UTF-8">
<script src="../../js/ajax/ajaxManager.js"></script>
</head>
<script>


    /*function sendRequest(user, ristorante, msg) {
        AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=sendRequest&user=' + user + '&ristorante=' + ristorante + '&msg=' + msg, true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                alert('qualcosa è andato storto: ' + response['message']);
            } else {
                alert('Richiesta inviata con successo. Attendere che questa venga accettata da un amministratore');
                window.location.reload();
            }
        })
    };*/

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
            echo '<p>This is the main HHHHHHHome!</p>';
            if ($_SESSION['privilegi'] == null) {
                echo '<p>Sembra che tu non abbia alcun ruolo assegnato. Chiedi ad un amministratore di cambiare i tuoi privilegi</p>';
            } else {
                if ($_SESSION['privilegi'] == 0) {
                    $privs = 15;
                } else {
                    $privs = $_SESSION['privilegi'];
                }
                echo '<p>Seleziona il tuo ruolo tra quelli disponibili: </p>';
                echo '<select name="select-role" id="select-role" onChange = "document.getElementById(\'role-iframe\').src = \'./role/\'+this.value+\'.php\'">';
                echo ($privs & 8)?'<option value="admin">admin</option> ':'';
                echo ($privs & 1)?'<option value="cameriere">cameriere</option> ':'';
                echo ($privs & 2)?'<option value="cuoco">cuoco</option> ':'';
                echo ($privs & 4)?'<option value="cassa">cassa</option> ':'';
                echo '</select>';

                echo '<iframe id="role-iframe" src="./role/' . (($privs & 8)?'admin':(($privs & 1)?'cameriere':(($privs & 2)?'cuoco':'cassa'))) . '.php" frameborder="0" title = "role iframe" width=100% height=250px ></iframe>';
            }
        }
?>
<!--input type="text" name="function" id="function" value="sendRequest" readonly hidden>
<input type="number" name="user" id="" value = $_SESSION['user_id'] readonly hidden>
<select name="ristorante" id="" value="1">primo rist</select>
<textarea name="note" cols="30" rows="10" form="send-req-form"></textarea><label for="note">note</label>
<input type="submit" value="Invia"-->

</div>


