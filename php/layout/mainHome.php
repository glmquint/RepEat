<head>
    <script src="../../js/ajax/ajaxManager.js"></script>
</head>
<script>
    AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=listRestaurants', true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            console.log(response['message'])
        } else {
            console.table(response['data']);
            sr = document.getElementById('select-ristorante'); //not a side-request-forgery reference
            console.log(sr);
            response['data'].forEach(row => {
                console.log(row)
                this_option = document.createElement('option');
                this_option.value = row['id_ristorante'];
                this_option.appendChild(document.createTextNode(row['nome_ristorante']));
                sr.appendChild(this_option);
                
            });
            /*this_textarea = document.createElement('textarea');
            this_textarea.form = "send-req-form";
            this_label = document.createElement('label');
            this_label.for = 'note';
            this_label.appendChild(document.createTextNode('note'));
            srf.appendChild(this_textarea);
            srf.appendChild(this_label);
            this_submit = document.createElement('input');
            this_submit.type = 'submit';
            this_submit.value = 'Invia';
            srf.appendChild(this_submit);
            <textarea name="note" cols="30" rows="10" form="send-req-form"></textarea><label for="note">note</label>
<input type="submit" value="Invia">*/

        }
    })

</script>
<div id="main-container" onload="alert('ciao')">
    
<?php
        session_start();
        if (!isset($_SESSION['ristorante']) || $_SESSION['ristorante'] == null) {
            echo '<p>Sembra che tu non sia iscritto ad alcun ristorante!</p>';
            echo '<p>Se l\'amministratore del tuo ristorante è già iscritto a repEat, puoi inviare una richiesta di partecipazione da quest\'elenco:</p>';
            echo '<form action="../ajax/dbInterface.php" method="get" id="send-req-form">
            <input type="text" name="function" id="function" value="sendRequest" readonly hidden>
            <input type="number" name="user" id="" value ="' . $_SESSION['user_id'] . '" readonly hidden>
            <select name = "ristorante" id="select-ristorante">
                <option>--Selezionare un ristorante--</option>
            </select>
            <textarea name="msg" cols="30" rows="10" form="send-req-form"></textarea><label for="note">note</label>
            <input type="submit" value="Invia">

            </form>';
            echo '<p>Oppure, se ne sei un amministratore, puoi registrare il tuo ristorante quì:</p>';
            echo '<form action="" method="post" id="register-restaurant-form"></form>';

        } else {
            echo '<p>This is the main home!</p>';
        }
?>
<option value=""></option>
<!--input type="text" name="function" id="function" value="sendRequest" readonly hidden>
<input type="number" name="user" id="" value = $_SESSION['user_id'] readonly hidden>
<select name="ristorante" id="" value="1">primo rist</select>
<textarea name="note" cols="30" rows="10" form="send-req-form"></textarea><label for="note">note</label>
<input type="submit" value="Invia">
</div>--


