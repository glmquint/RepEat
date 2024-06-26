/**Schermata relativa al ruolo di cassa
 * 
 * La cassa seleziona un tavolo dalla mappa che gli viene presentata e permette ai clienti di lasciare facoltativamente una recensione sul servizio ricevuto.
 * In seguito, ottiene il conto totale di ciò che è stato ordinato al tavolo e salva il pagamento liberando conseguentemente il tavolo
 */


function loadCashierDashboard(parentDiv, user, ristorante){
    //Come sempre, vengono chiusi tutti i timer tranne che per la notifica di nuovi messaggi
    intervalArr.map((a) => {
        clearInterval(a);
        intervalArr= [];
    })

    notifUnreadMessages(user);
                                                        
    droom = document.createElement('div');
    h3stanza = document.createElement('h3');
    //titolo della sezione
    h3stanza.appendChild(document.createTextNode('Stanze:'))
    droom.appendChild(h3stanza);

    //pulsanti disabilitati permettono una legenda per ricordare il codice colore utilizzato
    //per rappresentare lo stato attuale dei tavoli
    legendatext = "Legenda tavoli: ";

    ilibero = document.createElement('input');
    ilibero.type="radio";
    ilibero.name = "legenda";
    ilibero.id="legendalibero";
    ilibero.disabled = true
    llibero = document.createElement('label');
    llibero.htmlFor = "legendalibero"
    llibero.appendChild(document.createTextNode('libero'));
    llibero.classList.add('rtavolo');
    llibero.classList.add('libero');

    iordinato = document.createElement('input');
    iordinato.type="radio";
    iordinato.name = "legenda";
    iordinato.id="legendaordinato";
    iordinato.disabled = true
    lordinato = document.createElement('label');
    lordinato.htmlFor = "legendaordinato"
    lordinato.appendChild(document.createTextNode('ordinato'));
    lordinato.classList.add('rtavolo');
    lordinato.classList.add('ordinato');

    ipronto = document.createElement('input');
    ipronto.type="radio";
    ipronto.name = "legenda";
    ipronto.id="legendapronto";
    ipronto.disabled = true
    lpronto = document.createElement('label');
    lpronto.htmlFor = "legendapronto"
    lpronto.appendChild(document.createTextNode('da servire'));
    lpronto.classList.add('rtavolo');
    lpronto.classList.add('pronto');

    iservito = document.createElement('input');
    iservito.type="radio";
    iservito.name = "legenda";
    iservito.id="legendaservito";
    iservito.disabled = true
    lservito = document.createElement('label');
    lservito.htmlFor = "legendaservito"
    lservito.appendChild(document.createTextNode('servito'));
    lservito.classList.add('rtavolo');
    lservito.classList.add('servito');

    //è possibile cambiare la visualizzazione delle stanze da 'compatta' (default)
    //a 'reale', ossia seconddo la disposizione impostata dall'amministratore per imitare la disposizione attuale dei tavoli
    //Le stanze vengono schemattizzate in piante pressochè quadrate
    ichangeview = document.createElement('input');
    ichangeview.type = "checkbox";
    ichangeview.name="changeview";
    ichangeview.id="changeview";
    ichangeview.addEventListener('change', function(){for (const stanza of document.getElementsByClassName('stanza')) {
        stanza.classList.toggle('real');
    }})
    lchangeview = document.createElement('label');
    lchangeview.htmlFor = "changeview";
    lchangeview.classList.add('box');
    lchangeview.classList.add('glow-orange');
    lchangeview.classList.add('release-press');
    lchangeview.appendChild(document.createTextNode('Disposizione reale'))



    droom.appendChild(ichangeview);
    droom.appendChild(lchangeview);
    droom.appendChild(document.createTextNode('(default: compatta) Attenzione: assicurarsi che non esistano più tavoli sovrapposti nella visualizzazione reale'))
    droom.appendChild(h3stanza);
    droom.appendChild(document.createTextNode(legendatext));
    droom.appendChild(ilibero)
    droom.appendChild(llibero);
    droom.appendChild(iordinato)
    droom.appendChild(lordinato);
    droom.appendChild(ipronto)
    droom.appendChild(lpronto);
    droom.appendChild(iservito)
    droom.appendChild(lservito);
    
    dconto = document.createElement('div');
    h3conto = document.createElement('h3');
    h3conto.appendChild(document.createTextNode('Conto:'));
    checkcontainer = document.createElement('div');
    checkcontainer.id = 'conto';
    dconto.appendChild(h3conto);
    dconto.appendChild(checkcontainer);

    parentDiv.appendChild(droom);
    parentDiv.appendChild(dconto);

    //riempimento delle stanze con i tavoli
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listRooms&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            if (response['data'].length == 0){
                pnoroom = document.createElement('p');
                pnoroom.appendChild(document.createTextNode('Non è stata trovata alcuna stanza. Chiedi al tuo amministratore di aggiungere una stanza per iniziare...'));
                droom.appendChild(pnoroom);
            } else {
                console.log(response);
                response['data'].forEach((stanza, index_stanza) => {
                    dthis_stanza = document.createElement('div');
                    //titolo sella sezione
                    dthis_stanza.classList.add('stanza');
                    h5Stanza = document.createElement('h5');
                    h5Stanza.appendChild(document.createTextNode(stanza['nome_stanza']));
                    droom.appendChild(h5Stanza);
                    if(stanza['tavoli'] != null){
                        stanza['tavoli'].split(',').forEach((tavolo, index_tavolo) => {
                            //i tavoli sono radio buttons nel contesto della propria stanza
                            rtavolo = document.createElement('input');
                            rtavolo.type = 'radio';
                            rtavolo.name = 'tavolo';
                            rtavolo.id = 'tavolo-' + index_stanza + ':' + index_tavolo;
                            rtavolo.value = stanza['id_stanza'] + ':' + tavolo.split(':')[0];
                            
                            rtavolo.addEventListener('change', function(){if(this.checked) buildReviewCheck(this.value, this.nextSibling.innerText, ristorante, checkcontainer)})
                            lrtavolo = document.createElement('label');
                            lrtavolo.appendChild(document.createTextNode(alphabet[stanza['id_stanza']] + tavolo.split(':')[0]));
                            lrtavolo.htmlFor = 'tavolo-' + index_stanza + ':' + index_tavolo;
                            lrtavolo.classList.add('rtavolo');
                            //posizione 'reale' caricata dinamicamente sulle informazioni tenute nel database
                            lrtavolo.style.left = Number(tavolo.split(':')[1]) * (9/10) + '%';
                            lrtavolo.style.top = Number(tavolo.split(':')[2]) * (9/10) + '%';

                            dthis_stanza.appendChild(rtavolo);
                            dthis_stanza.appendChild(lrtavolo);
                            //ogni 3 secondi si effettua il refresh delle stanze per aggiornare lo stato dei singoli tavoli
                            intervalArr.push(setInterval(function intervalSetTableStatus() {
                                AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getTable&tavolo='+ tavolo.split(':')[0] + '&stanza='+ stanza['id_stanza'] + '&ristorante='+ ristorante, true, null, 
                                function(response2){
                                    if (response2['responseCode'] != 0) {
                                        sendAlert('qualcosa è andato storto: ' + response2['message'], 'error');
                                    } else {
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).nextSibling.classList.remove('libero');   
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).nextSibling.classList.remove('ordinato');   
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).nextSibling.classList.remove('pronto');   
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).nextSibling.classList.remove('servito');   
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).nextSibling.classList.add(response2['data'][0]['stato']);   
                                    }
                                }); 
                            
                                }, 3000)); 
                            
                        });
                    } else {
                        dthis_stanza.appendChild(document.createTextNode('Non è stato trovato alcun tavolo. Chiedi al tuo amministratore di aggiungere un tavolo per iniziare...'))
                    }
                    droom.appendChild(dthis_stanza);
                });
            }
        }
    });


}

//alla selezione del tavolo, appare una schermata perchè i clienti possano lasciare una valutazione e, sempre a scelta, una recensione del servizio ricevuto
//Di fatto, queste informazioni non verranno poi più utilizzate ma, in un ottica più ampia, possono consentire altre operazioni di data-analytic riguardo la qualità del servizio.
function buildReviewCheck(stanza_tavolo, nome_stanza_tavolo, ristorante, parentDiv) {
    sentiment = ['sentiment_very_dissatisfied', 'sentiment_dissatisfied', 'sentiment_neutral', 'sentiment_satisfied', 'sentiment_very_satisfied'];

    h5review = document.createElement('h5');
    h5review.appendChild(document.createTextNode('Recensisci la tua esperienza al tavolo ' + nome_stanza_tavolo));
    preview = document.createElement('p');
    preview.appendChild(document.createTextNode('In questa sezione i clienti hanno l\'opportunità di lasciare un voto e se possibile una recensione riguardo al servizio ottenuto'));

    dstarcontainer = document.createElement('div');
    dstarcontainer.id = 'starcontainer';
    //valutazione sotto forma di stelle/livello di gradimento (1-5)
    for (let i = 1; i < 6; i++) {
        this_star = document.createElement('input');
        this_star.type = 'radio';
        this_star.name = 'star';
        this_star.id = 'star-' + i;
        this_star.value = i;
        this_star.addEventListener('change', function(){if (this.checked) document.getElementById('starcontainer').value = this.value});
        lthis_star = document.createElement('label');
        lthis_star.htmlFor = 'star-' + i;
        lthis_star.classList.add('material-icons');
        lthis_star.classList.add('pill');
        lthis_star.appendChild(document.createTextNode(sentiment[i - 1]));

        dstarcontainer.appendChild(this_star);
        dstarcontainer.appendChild(lthis_star);
        
    }

    //spazio per la recensione
    tarecensione = document.createElement('textarea');
    tarecensione.id = 'recensione';
    tarecensione.placeholder = 'lascia un commento...';

    breview = document.createElement('button');
    breview.appendChild(document.createTextNode('invia'));
    breview.addEventListener('click', function(){review(document.getElementById('starcontainer').value, document.getElementById('recensione').value, stanza_tavolo.split(':')[0], stanza_tavolo.split(':')[1], ristorante, nome_stanza_tavolo, parentDiv)});
    bskip = document.createElement('button');
    bskip.appendChild(document.createTextNode('salta'));
    bskip.addEventListener('click', function(){review(null, null, stanza_tavolo.split(':')[0], stanza_tavolo.split(':')[1], ristorante, nome_stanza_tavolo, parentDiv) })

    while (parentDiv.lastChild) {
        parentDiv.removeChild(parentDiv.firstChild);
    }

    parentDiv.appendChild(h5review);
    parentDiv.appendChild(preview);
    parentDiv.appendChild(dstarcontainer);
    parentDiv.appendChild(tarecensione);
    parentDiv.appendChild(breview);
    parentDiv.appendChild(bskip);


}

//Dopo la valutazione viene restituito il totale del conto
function buildPayCheck(stanza, tavolo, nome_stanza_tavolo, ristorante, parentDiv) {
    dcheckcontainer = document.createElement('div');
    dcheckcontainer.id = 'checkcontainer';
    while (parentDiv.lastChild) {
        parentDiv.removeChild(parentDiv.firstChild);
    }

    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getCheck&stanza='+ stanza + '&tavolo='+ tavolo + '&ristorante='+ ristorante , true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            if (response['data'].length == 0){
                pnodish = document.createElement('p');
                pnodish.appendChild(document.createTextNode('Sembra che non sia stato ancora ordinato alcun piatto...'));
                dcheckcontainer.appendChild(pnodish);
            } else {

                //viene costruita dinamicamente la tabella con tutto il conto ordinato dal tavolo
                tableCheck = document.createElement('table');
                tcheckcaption = tableCheck.createCaption();
                tcheckcaption.appendChild(document.createTextNode('Conto per il tavolo tavolo ' + nome_stanza_tavolo + ', con permanenza di ' + ((response['data'][0]['prezzo'].split(':')[0] == '00')?'':response['data'][0]['prezzo'].split(':')[0] + ' or'+((response['data'][0]['prezzo'].split(':')[0] == '01')?'a':'e')+', ') + 
                                                                                                                                        response['data'][0]['prezzo'].split(':')[1] + ' minut'+((response['data'][0]['prezzo'].split(':')[1] == '01')?'o':'i')+' e ' + 
                                                                                                                                        response['data'][0]['prezzo'].split(':')[2] + ' second'+((response['data'][0]['prezzo'].split(':')[2] == '01')?'o':'i')));



                response['data'].forEach((elem_piatto, index_piatto) => {
                    if (index_piatto != 0) {

                    var row = tableCheck.insertRow(-1);
                    
                    quantita = row.insertCell(0);
                    piatto = row.insertCell(1);
                    prezzo = row.insertCell(2);
                    
                    // il totale
                    quantita.appendChild(document.createTextNode((elem_piatto['quantita'] != null)?elem_piatto['quantita'] + 'x ':''));
                    piatto.appendChild(document.createTextNode(elem_piatto['piatto']));
                    prezzo.appendChild(document.createTextNode(elem_piatto['prezzo']+'€'));


                    }
                });
                //E' possibile inserire il metodo di pagamento
                smetodfopagamento = document.createElement('select');
                smetodfopagamento.id = 'metodopagamento';
                lsmetodfopagamento = document.createElement('label');
                lsmetodfopagamento.appendChild(document.createTextNode('Metodo di pagamento:'));
                lsmetodfopagamento.htmlFor = 'metodopagamento';

                metodi = ['carta', 'bancomat', 'contanti'];

                metodi.forEach(metodo => {
                    this_option = document.createElement('option');
                    this_option.value = metodo;
                    this_option.appendChild(document.createTextNode(metodo));
                    smetodfopagamento.appendChild(this_option);
                });

                bpaga = document.createElement('button');
                bpaga.appendChild(document.createTextNode('paga'));
                bpaga.addEventListener('click', function(){payCheck(document.getElementById('metodopagamento').value, tavolo, stanza, ristorante)});
                
                dcheckcontainer.appendChild(tableCheck);
                dcheckcontainer.appendChild(lsmetodfopagamento);
                dcheckcontainer.appendChild(smetodfopagamento);
                dcheckcontainer.appendChild(bpaga);
                
            }
        }
    });

    
    parentDiv.appendChild(dcheckcontainer);
}

/*-------------------------------------------------------------------------------------------------*/

function payCheck(tipo_pagamento, tavolo, stanza, ristorante) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=payCheck&tipo_pagamento='+ tipo_pagamento + '&stanza='+ stanza + '&tavolo='+ tavolo + '&ristorante='+ ristorante , true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('pagamento effettuato', 'success');
        }
    });

}

function review(valutazione, recensione, stanza, tavolo, ristorante, nome_stanza_tavolo, parentDiv) {
    if (valutazione == undefined && recensione != null) {
        sendAlert('selezionare almeno un livello di gradimento', 'error');
    } else {
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=review&valutazione='+ valutazione + '&recensione='+ recensione + '&stanza='+ stanza + '&tavolo='+ tavolo + '&ristorante='+ ristorante , true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
            } else {
                if (valutazione != null || recensione != null) {               
                    sendAlert('grazie per il tuo feedback!', 'info');
                }
                buildPayCheck(stanza, tavolo, nome_stanza_tavolo, ristorante, parentDiv);
            }
        });
    }
}
