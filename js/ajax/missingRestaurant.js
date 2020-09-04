function loadMissingRestaurant(user){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=existsRequest&user=' + user, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: '+ response['message'], 'error');
        } else {
            this_body = document.getElementById('main-container');
            while (this_body.firstChild) {
                this_body.removeChild(this_body.lastChild);
            }
            if(response['data'][0]['num_requests'] == 0){
                AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listRestaurants', true, null, 
                function(response){
                    if (response['responseCode'] != 0) {
                        sendAlert('qualcosa è andato storto: '+ response['message'], 'error');
                    } else {
                        console.table(response['data']);
                        p1 = document.createElement('p');
                        p1.appendChild(document.createTextNode('Sembra che tu non sia iscritto ad alcun ristorante!'));

                        p2 = document.createElement('p');
                        p2.appendChild(document.createTextNode('Se l\'amministratore del tuo ristorante è già iscritto a RepEat, puoi inviare una richiesta di partecipazione da quest\'elenco:'));

                        sr = document.createElement('select');
                        sr.id = 'select-ristorante';
                        this_option = document.createElement('option');
                        this_option.value = -1;
                        this_option.appendChild(document.createTextNode('--Seleziona il tuo ristorante--'));
                        sr.appendChild(this_option);

                        response['data'].forEach(row => {
                            console.log(row)
                            this_option = document.createElement('option');
                            this_option.value = row['id_ristorante'];
                            this_option.appendChild(document.createTextNode(row['nome_ristorante']));
                            sr.appendChild(this_option);
                            
                        });
                        ta = document.createElement('textarea');
                        ta.id = 'msg';
                        ta.name = 'msg';
                        ta.placeholder = 'fai sapere chi sei...';

                        lta = document.createElement('label');
                        lta.appendChild(document.createTextNode('note:'));

                        srb = document.createElement('button');
                        srb.addEventListener("click", function(){sendRequest(user, document.getElementById('select-ristorante').value, document.getElementById('msg').value)});
                        srb.appendChild(document.createTextNode('Invia'));

                        p3 = document.createElement('p');
                        p3.appendChild(document.createTextNode('Oppure, se ne sei un amministratore, puoi registrare il tuo ristorante quì:'));

                        inr = document.createElement('input');
                        inr.type = 'text';
                        inr.name = 'nome_ristorante';
                        inr.id = 'nome_ristorante';
                        linr = document.createElement('label');
                        linr.htmlFor = 'nome_ristorante';
                        linr.appendChild(document.createTextNode('Nome ristorante:'));

                        ii = document.createElement('input');
                        ii.type = 'text';
                        ii.name = 'indirizzo';
                        ii.id = 'indirizzo';
                        lii = document.createElement('label');
                        lii.htmlFor = 'indirizzo';
                        lii.appendChild(document.createTextNode('Indirizzo:'));

                        ilk = document.createElement('input');
                        ilk.type = 'number';
                        ilk.name = 'license_key';
                        ilk.id = 'license_key';
                        lilk = document.createElement('label');
                        lilk.htmlFor = 'license_key';
                        lilk.appendChild(document.createTextNode('License key:'));

                        rrb = document.createElement('button');
                        rrb.addEventListener('click', function(){registerRestaurant(document.getElementById('nome_ristorante').value, document.getElementById('indirizzo').value, document.getElementById('license_key').value, user)});
                        rrb.appendChild(document.createTextNode('Invia'));


                        p4 = document.createElement('p');

                        p4.innerHTML = 'Se non hai una licenza, puoi acquistarne una tra quelle proposte <a href="./license.php" target="_blank" rel="noopener noreferrer">quì</a>';
                    
                        this_body = document.getElementById('main-container');
                        
                        this_body.appendChild(p1);

                        dadmin = document.createElement('div');
                        dadmin.id = 'missingadmin';
                        dadmin.appendChild(p2);
                        dadmin.appendChild(sr);
                        dadmin.appendChild(document.createElement('br'));
                        dadmin.appendChild(lta);
                        dadmin.appendChild(document.createElement('br'));
                        dadmin.appendChild(ta);
                        dadmin.appendChild(srb);

                        dpersonel = document.createElement('div');
                        dpersonel.id = 'missingpersonel';
                        dpersonel.appendChild(p3);
                        dpersonel.appendChild(linr);
                        dpersonel.appendChild(document.createElement('br'));
                        dpersonel.appendChild(inr);
                        dpersonel.appendChild(document.createElement('br'));
                        dpersonel.appendChild(lii);
                        dpersonel.appendChild(document.createElement('br'));
                        dpersonel.appendChild(ii);
                        dpersonel.appendChild(document.createElement('br'));
                        dpersonel.appendChild(lilk);
                        dpersonel.appendChild(document.createElement('br'));
                        dpersonel.appendChild(ilk);
                        dpersonel.appendChild(rrb);
                        dpersonel.appendChild(p4);

                        this_body.appendChild(dadmin);
                        this_body.appendChild(dpersonel);

                    }
                });
            } else {
                this_replacement = document.createElement('p');
                this_replacement.appendChild(document.createTextNode('La tua richiesta è stata inviata correttamente. Attendi una risposta da parte di un amministratore'));
                this_replacement.classList.add('info-box');
                this_body.appendChild(this_replacement);

            }
                
        }

    });
}

function registerRestaurant(nome_ristorante, indirizzo, license_key, user) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=registerRestaurant&nome_ristorante='+nome_ristorante+'&indirizzo='+indirizzo+'&license_key='+license_key+'&user=' + user, true, null, 
    function(response){
        //console.log(response);
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: '+ response['message'], 'error');
        } else {
            window.location.reload();
            sendAlert('Registrazione avvenuta con successo!', 'success');
        }
    });
};

function sendRequest(user, ristorante, msg) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=sendRequest&user=' + user + '&target_restaurant=' + ristorante + '&msg=' + msg, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('Richiesta inviata con successo. Attendere che questa venga accettata da un amministratore', 'success');
            window.location.reload();
        }
    })
};

