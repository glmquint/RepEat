<head>
<meta charset="UTF-8">
<script src="../../js/ajax/ajaxManager.js"></script>
<script src="../../js/ajax/missingRestaurant.js"></script>
</head>
<body onload="load(<?php echo $_SESSION['user_id']?>)" id="main-body">
    <p>Sembra che tu non sia iscritto ad alcun ristorante!</p>
    <p>Se l'amministratore del tuo ristorante è già iscritto a repEat, puoi inviare una richiesta di partecipazione da quest'elenco:</p>
    <input type="text" name="function" id="function" value="sendRequest" readonly hidden>
    <input type="number" name="user" id="user" value ="<?php echo $_SESSION['user_id']?>" readonly hidden>
    <select name = "ristorante" id="select-ristorante">
        <option>--Selezionare un ristorante--</option>
    </select>
    <textarea name="msg" cols="30" rows="10" form="send-req-form" id="msg"></textarea><label for="msg">note</label>
    <button onclick="sendRequest(document.getElementById('user').value, document.getElementById('select-ristorante').value, document.getElementById('msg').value)">Invia</button>
    <p>Oppure, se ne sei un amministratore, puoi registrare il tuo ristorante quì:</p>
    <input type="text" name="nome_ristorante" id="nome_ristorante"><label for="nome_ristorante">Nome ristorante</label>
    <input type="text" name="indirizzo" id="indirizzo"><label for="indirizzo">Indirizzo</label>
    <input type="number" name="license_key" id="license_key"><label for="license_key">License key</label>
    <button onclick = "registerRestaurant(document.getElementById('nome_ristorante').value, document.getElementById('indirizzo').value, document.getElementById('license_key').value, <?php echo $_SESSION['user_id']?>)">Invia</button>
    <p>Se non hai una licenza, puoi acquistarne una tra quelle proposte <a href="../license.php" target="_blank" rel="noopener noreferrer">quì</a></p>
</body>
