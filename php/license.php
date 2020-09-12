<?php
    /**Pagina dedicata alle offerte per le license di utilizzo dell'appicazione
     * 
     * Viene quì caricata dinamicamente una tabella che riassume i vari livelli di licenze disponibili all'acquisto.
     * In questo caso è sufficiente premere il tasto acquista per generare una chiave con le relative prorpietà.
     * Questa dovrà essere inserita dall'admin nell'apposito sezione del pannello di controllo del ristorante.
     * Allo scadere di una licenza o al superamento di uno dei suoi limiti, l'applicazione in un caso bloccherà l'accesso
     * alle funzioni principali di tutti i ruoli (che riprenderanno a funzionare solo all'aggiornamento della chiave), 
     * nell'altro invierà una notifica del raggiungimento del limite.
     * 
     * Questa pagina è sempre raggiungibile, (volontariamente) anche ad utenti non autenticari in modo che siano pubbliche le offerte proposte.
     */
?>
<!DOCTYPE html>
<html lang="it" class=<?php if (isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode']){echo "dark-mode";}else{echo "";}?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/ajax/ajaxManager.js"></script>
    <script src="../js/ajax/license.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"><!--Necessario per accedere velocemente ad alcune icone (l'alternativa è scaricare il pack e usarle come risorsa interna ma questa soluzione è la più rapida)-->
    <link rel="shortcut icon" type="image/x-icon" href="../css/img/favicon.ico" />
    <link rel="stylesheet" href="../css/neonmorphism.css">
    <title>repEat</title>
</head>
<body onLoad="load()">
<button class = "light-switch" onclick="document.getElementsByTagName('html')[0].classList.toggle('dark-mode'); document.cookie='dark-mode = '+ document.getElementsByTagName('html')[0].classList.length +';path=/577923_quint;expires=Wed, 18 Dec 2023 12:00:00 GMT'"></button>
<button id="index-btn" onclick="window.location.href='../index.php'"><img src="../css/img/logo_squared.svg" alt="index"></button>

    <table id='offers'></table>
    <p id='key'></p>
</body>
</html>