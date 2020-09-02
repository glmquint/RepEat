function loadCashierDashboard(parentDiv, user, ristorante){
    intervalArr.map((a) => {
        clearInterval(a);
        intervalArr= [];
    })

    notifUnreadMessages(user);
                                                        
    droom = document.createElement('div');
    h3stanza = document.createElement('h3');
    h3stanza.appendChild(document.createTextNode('Stanze:'))
    droom.appendChild(h3stanza);
    
    dconto = document.createElement('div');
    h3conto = document.createElement('h3');
    h3conto.appendChild(document.createTextNode('Conto:'));
    checkcontainer = document.createElement('div');
    checkcontainer.id = 'conto';
    dconto.appendChild(h3conto);
    dconto.appendChild(checkcontainer);

    parentDiv.appendChild(droom);
    parentDiv.appendChild(dconto);


    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listRooms&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            if (response['data'].length == 0){
                pnoroom = document.createElement('p');
                pnoroom.appendChild(document.createTextNode('Non è stata trovata alcuna stanza. Chiedi al tuo amministratore di aggiungere una stanza per iniziare...'));
                droom.appendChild(pnoroom);
            } else {
                console.log(response);
                response['data'].forEach((stanza, index_stanza) => {
                    dthis_stanza = document.createElement('div');
                    dthis_stanza.classList.add('stanza');
                    h5Stanza = document.createElement('h5');
                    h5Stanza.appendChild(document.createTextNode(stanza['nome_stanza']));
                    dthis_stanza.appendChild(h5Stanza);
                    if(stanza['tavoli'] != null){
                        stanza['tavoli'].split(',').forEach((tavolo, index_tavolo) => {
                            rtavolo = document.createElement('input');
                            rtavolo.type = 'radio';
                            rtavolo.name = 'tavolo';
                            rtavolo.id = 'tavolo-' + index_stanza + ':' + index_tavolo;
                            rtavolo.value = stanza['id_stanza'] + ':' + tavolo.split(':')[0];
                            rtavolo.classList.add('rtavolo');

                            intervalArr.push(setInterval(function intervalSetTableStatus() {
                                AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getTable&tavolo='+ tavolo.split(':')[0] + '&stanza='+ stanza['id_stanza'] + '&ristorante='+ ristorante, true, null, 
                                function(response2){
                                    if (response2['responseCode'] != 0) {
                                        alert('qualcosa è andato storto: ' + response2['message']);
                                    } else {
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).classList.remove('libero');   
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).classList.remove('ordinato');   
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).classList.remove('pronto');   
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).classList.remove('servito');   
                                        document.getElementById('tavolo-' + index_stanza + ':' + index_tavolo).classList.add(response2['data'][0]['stato']);   
                                    }
                                }); 
                            
                                }, 3000)); 
                            
                            rtavolo.addEventListener('change', function(){if(this.checked) buildReviewCheck(this.value, this.nextSibling.innerText, ristorante, checkcontainer)})
                            lrtavolo = document.createElement('label');
                            lrtavolo.appendChild(document.createTextNode(alphabet[stanza['id_stanza']] + tavolo.split(':')[0]));
                            lrtavolo.htmlFor = 'tavolo-' + index_stanza + ':' + index_tavolo;

                            dthis_stanza.appendChild(rtavolo);
                            dthis_stanza.appendChild(lrtavolo);
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



function buildReviewCheck(stanza_tavolo, nome_stanza_tavolo, ristorante, parentDiv) {
    h5review = document.createElement('h5');
    h5review.appendChild(document.createTextNode('Recensisci la tua esperienza al tavolo ' + nome_stanza_tavolo));
    preview = document.createElement('p');
    preview.appendChild(document.createTextNode('In questa sezione i clienti hanno l\'opportunità di lasciare un voto e se possibile una recensione riguardo al servizio ottenuto'));

    dstarcontainer = document.createElement('div');
    dstarcontainer.id = 'starcontainer';
    for (let i = 1; i < 6; i++) {
        this_star = document.createElement('input');
        this_star.type = 'radio';
        this_star.name = 'star';
        this_star.id = 'star-' + i;
        this_star.value = i;
        this_star.addEventListener('change', function(){if (this.checked) document.getElementById('starcontainer').value = this.value});
        lthis_star = document.createElement('label');
        lthis_star.appendChild(document.createTextNode(i));

        dstarcontainer.appendChild(this_star);
        dstarcontainer.appendChild(lthis_star);
        
    }

    tarecensione = document.createElement('textarea');
    tarecensione.id = 'recensione';
    tarecensione.placeholder = 'lascia un commento...';

    breview = document.createElement('button');
    breview.appendChild(document.createTextNode('invia'));
    breview.addEventListener('click', function(){review(document.getElementById('starcontainer').value, document.getElementById('recensione').value, stanza_tavolo.split(':')[0], stanza_tavolo.split(':')[1], ristorante);  buildPayCheck(stanza_tavolo.split(':')[0], stanza_tavolo.split(':')[1], nome_stanza_tavolo, ristorante, parentDiv)})
    bskip = document.createElement('button');
    bskip.appendChild(document.createTextNode('salta'));
    bskip.addEventListener('click', function(){review(null, null, stanza_tavolo.split(':')[0], stanza_tavolo.split(':')[1], ristorante); buildPayCheck(stanza_tavolo.split(':')[0], stanza_tavolo.split(':')[1], nome_stanza_tavolo, ristorante, parentDiv)})

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

function buildPayCheck(stanza, tavolo, nome_stanza_tavolo, ristorante, parentDiv) {
    dcheckcontainer = document.createElement('div');
    dcheckcontainer.id = 'checkcontainer';
    while (parentDiv.lastChild) {
        parentDiv.removeChild(parentDiv.firstChild);
    }

    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getCheck&stanza='+ stanza + '&tavolo='+ tavolo + '&ristorante='+ ristorante , true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            if (response['data'].length == 0){
                pnodish = document.createElement('p');
                pnodish.appendChild(document.createTextNode('Sembra che non sia stato ancora ordinato alcun piatto...'));
                dcheckcontainer.appendChild(pnodish);
            } else {

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
                    
                    // Add some text to the new cells:
                    quantita.appendChild(document.createTextNode((elem_piatto['quantita'] != null)?elem_piatto['quantita'] + 'x ':''));
                    piatto.appendChild(document.createTextNode(elem_piatto['piatto']));
                    prezzo.appendChild(document.createTextNode(elem_piatto['prezzo']+'€'));


                    }
                });
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

function payCheck(tipo_pagamento, tavolo, stanza, ristorante) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=payCheck&tipo_pagamento='+ tipo_pagamento + '&stanza='+ stanza + '&tavolo='+ tavolo + '&ristorante='+ ristorante , true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            alert('pagamento effettuato');
        }
    });

}

function review(valutazione, recensione, stanza, tavolo, ristorante) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=review&valutazione='+ valutazione + '&recensione='+ ((recensione == null)?'':recensione) + '&stanza='+ stanza + '&tavolo='+ tavolo + '&ristorante='+ ristorante , true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            if (valutazione != null || recensione != null) {               
                alert('grazie per il tuo feedback!');
            }
        }
    });
}
