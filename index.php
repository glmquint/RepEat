<!DOCTYPE html>
<html lang="it" class=<?php if (isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode']){echo "dark-mode";}else{echo "";}?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/neonmorphism.css">
    <link rel="shortcut icon" type="image/x-icon" href="./css/img/favicon.ico" />
    <title>RepEat</title>
</head>
<body>
    <img id="logo-title" src="./css/img/logo_title.svg" alt="RepEat">
    
    <button class = "light-switch" onclick="document.getElementsByTagName('html')[0].classList.toggle('dark-mode'); document.cookie='dark-mode = '+ document.getElementsByTagName('html')[0].classList.length +';expires=Wed, 18 Dec 2023 12:00:00 GMT'"></button>

    <div id="index-page">
    <!--p>Go <a href="./php/ajax/dbInterface.php">here</a> to test the developer api!</p-->

        <span id="index-btn-container">
        <?php
            session_start();
            require_once __DIR__ . "/php/config.php";
            include DIR_UTIL . "sessionUtil.php";

            if (!isLogged()){
                    echo '<button id="access-btn" onclick="window.location.href=\'./php/login.php\'">Accedi</button>';
            } else {
                echo '<p>Bentornato ' . $_SESSION['username'] . '!</p> <button id="access-btn" onclick="window.location.href=\'./php/home.php\'">Torna alla home</button> <p>oppure</p> <button id="btn-index-logout" onclick="window.location.href=\'./php/logout.php\'">Disconnettiti</button> ';
            }

        ?>
        <button onclick="window.location.href='./php/license.php'">Scopri le nostre offerte</button>
        </span>
    <div>
    <h2>Cosa</h2>
    <p class="pill info-box">RepEat è una webapp progettata per semplificare, assistere e rendere più efficiente il lavoro nei ristoranti. </p>
    <p>La divisione in ruoli permette di centralizzare le informazioni in continuo aggiornamento e allo stesso tempo garantisce una facilità di utilizzo, facendo trasparire solo i dati rilevanti alle singole funzioni svolte dal personale. 
        Questo permette, inoltre, di rendere il servizio adattabile alle diverse situazioni di utilizzo, sia che ci si trovi in cucina, alla cassa o tra i tavoli.</p>
    <p>La personalizzazione dello spazio di lavoro, dei piatti, dei menù serviti e degli orari delle routine permettono di automatizzare molti dei compiti ai quali sarà richiesto di pensare principalmente una sola volta, alla prima configurazione.</p>
    </div>
    <div>
    <h2>Per chi</h2>
    <p>RepEat si rivolge a tutte le piccole e medie imprese che operano nel settore della ristorazione, che ricerano un servizio affidabile e di supporto al processo organizzativo della propria attività, senza necessariamente investire in attrezzatura specializzata.</p><p class="pill success-box"> Essendo un'applicazione web, infatti, RepEat è accessibile da qualunque dispositivo dotato di uno dei maggiori browser moderni.</p>
    </div>
    <div><h2>Come</h2>
    <p>Esistono due modi per iniziare ad usare RepEat, a seconda del ruolo da amministratore o dipendente del proprio ristorante. Entrambi iniziano dalla registrazione del proprio account nell'area <a href="./php/register.php">registrazione</a>.</p>
    <p> Si può stare tranquilli che le password non sono e mai saranno tenute in chiaro nei nostri servers.</p>
    <h3>Per gli amministratori</h3>
    <p>Al primo accesso è possibile inserire tutti i dati del proprio ristorante nella seconda delle due senzioni che si presentano davanti. Viene anche richiesta una chiave di attivazione che è possibile acquistare dall'apposita sezione delle <a href="./php/license.php">nostre offerte</a>. 
    Allo scadere del proprio abbonamento, o al raggiungimento di uno dei limiti del proprio livello, l'applicazione si occuperà di notificare ciò e bloccare le conseguenti funzioni.</p>
    <p> Sarà sempre possibile modificare queste informazioni anche in futuro.</p>
    <h4>Admin</h4>
    <p>Il pannello di controllo dell'amministratore permette prima di tutto di passare a qualunque altro ruolo, potendo così monitorare l'andamento in ogni settore. In seguito è possibile aggiungere stanze, con i rispettivi tavoli, aggiungere i piatti disponibili con i relativi parametri e creare nuovo menù. Per ognuno dei menù è possibile specificare quali piatti gli appartengono e quale sia la sua fascia di attività, così da rendere immediatamente visibili i piatti corretti in base all'orario.</p>
    <p>Le richieste di partecipzaione verranno automaticamente notificate e rese disponibili nella sezione dei messaggi, nella quale sarà possibile accettare o rifiutare le singole richieste.</p>
    <h3>Per il personale</h3>
    <p>Al primo accesso è possibile cercare il proprio ristorante tra l'elenco nella prima delle due senzioni che si presentano davanti. E' anche possibile, e raccomandato, aggiungere una nota in modo da far presente la propria identità.</p>
    <p class="pill error-box">Non sarà possibile inviare altre richieste finchè quella precedente non sarà stata accettata o rifiutata.</p>
    <p>Una volta all'interno dello staff del proprio ristorante sarà sempre possibile passare da un ruolo a l'altro, in base ai permessi concessi dall'amministratore.</p>
    <h4>Cameriere</h4>
    <p>Viene, innanzitutto, mostrata una mappa dei tavoli del ristorante divisi per stanza e colorati in funzione del loro stato attuale, seguita dalla lista degli ordini che sono stati preparati e che devono essere serviti.</p>
    <p>In seguito è presente la lista dei piatti disponibili, ossia solo quelli appartenenti ai menu attivi nell'attuale fascia oraria. E' anche possibile cercare tra la lista dei piatti con una barra di ricerca che filtrerà solo i piatti che contengono almeno una delle parole chiave inserite. E' quì che si svolge la creazione degli ordini, dove è possibile, dopo aver selezionato l'apposito tavolo, aggiungere o togliere piatti con eventuali note, su richiesta dei clienti. </p>
    <p>Infine viene riportato un riassunto dell'ordine, in modo che possa essere revisionato prima di essere inviato in cucina.</p>

    <h4>Cuoco</h4>
    <p>L'unica lista presente contiene l'elenco di tutti gli ordini effettuati ed in attesa di processazione, in ordine di arrivo decrescente, in modo da evidenziare sempre quelli che stanno aspettando da più tempo.</p>

    <h4>Cassa</h4>
    <p>Viene, innanzitutto, mostrata una mappa dei tavoli del ristorante divisi per stanza e colorati in funzione del loro stato attuale.</p>
    <p>Selezionato un tavolo, viene lasciata l'opportunità ai clienti di lasciare una valutazione complessiva del servizio e, in maniera facoltativa, lo spazio per una recensione più approfondita.</p>
    <p>Anche decidendo di saltare la valurazione del servizio, viene riportato il conto del tavolo, comprendente il riassunto di tutti gli ordini effettuati fino a quel momento.</p>
    </div>
    <div><h2>In più</h2>
    <p>E' presente, nella sezione apposita, un semplice sistema di chat tra tutti i dipendenti di uno stesso ristorante, con adeguato meccanismo di notifiche e segnalazione di lettura, al fine di riunire ogni eventuale informazione all'interno della sola applicazione. </p>
    <p>Infine, l'intera interfaccia è stata studiata per minimizzare le distrazioni e focalizzare l'attenzione sulle informazioni. A tal riguardo, è presente anche una versione 'dark-mode' alternabile in qualunque momento e selezionabile come predefinita nelle impostazioni, per garantire una leggibilità continua ed un affaticamento visivo ridotto.</p>
    </div>    
</div>
</body>
</html>