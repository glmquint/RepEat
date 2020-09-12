/**Schermata relativa al ruolo di cameriere
 * 
 * Il cameriere seleziona un tavolo dalla mappa che gli viene presentata ed effettua nuovi ordini a partire dai piatti 
 * disponibili nell'attuale fascia oraria, in funzione delle impostazioni scelte dall'amministratore
 */

alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

function loadWaiterDashboard(parentDiv, user, ristorante){
    //Come sempre, vengono chiusi tutti i timer tranne che per la notifica di nuovi messaggi
    intervalArr.map((a) => {
        clearInterval(a);
        intervalArr= [];
    })

    notifUnreadMessages(user);

    var dishArr = []

    droom = document.createElement('div');
    h3stanza = document.createElement('h3');
    h3stanza.appendChild(document.createTextNode('Stanze:'));
    
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
    parentDiv.appendChild(droom);

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
                            
                            rtavolo.addEventListener('change', function(){if(this.checked) selectTable(this.value, this.nextSibling.innerText)})
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

    dready = document.createElement('div');
    dready.id = 'pronti';
    h3ready = document.createElement('h3');
    h3ready.appendChild(document.createTextNode('Da consegnare:'));
    dreadylist = document.createElement('div');
    dreadylist.id = 'readylist';

    dready.appendChild(h3ready);
    dready.appendChild(dreadylist);


    parentDiv.appendChild(dready);

    intervalArr.push(setInterval(function intervalgetOrdersReady() {
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getOrdersReady&user='+ user, true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
            } else {
                console.log('ready');
                console.log(response);
                while (dreadylist.lastChild) {
                    dreadylist.removeChild(dreadylist.firstChild);
                }
            
                if (response['data'].length == 0){
                    pnomenu = document.createElement('p');
                    pnomenu.appendChild(document.createTextNode('Nessun piatto in attesa di essere consegnato...'));
                    dreadylist.appendChild(pnomenu);
                } else {
                    response['data'].forEach((ordine, index_ordine) => {
                        this_order = document.createElement('div');
                        this_order.classList.add('ordine');


    
                        porder = document.createElement('p');
                        porder.appendChild(document.createTextNode(ordine['quantita'] + 'x ' + ordine['nome'] + 
                                                                    ((ordine['note'] != '')?'[' + ordine['note'] + ']':'') + 
                                                                    ' al tavolo ' + alphabet[ordine['stanza']] + ordine['tavolo'] + 
                                                                    ' ordinato ' + ((ordine['attesa'].split(':')[0] == '00')?'':ordine['attesa'].split(':')[0] + ' or'+((ordine['attesa'].split(':')[0] == '01')?'a':'e')+', ') + 
                                                                    ordine['attesa'].split(':')[1] + ' minut'+((ordine['attesa'].split(':')[1] == '01')?'o':'i')+' e ' + 
                                                                    ordine['attesa'].split(':')[2] + ' second'+((ordine['attesa'].split(':')[2] == '01')?'o':'i')+' fa '))

                        borderready = document.createElement('button');
                        borderready.value = ordine['id_ordine'];
                        borderready.addEventListener('click', function () {setDelivered(user, this.value)});
                        borderready.appendChild(document.createTextNode('completa'));

                        this_order.appendChild(porder);
                        this_order.appendChild(borderready);
    
    



                        dreadylist.appendChild(this_order);
                    });
                }
            }
        
        });
    return intervalgetOrdersReady;    
    }(), 1000)); //... grazie a https://stackoverflow.com/a/6685505 per l'idea di chiamare una funzione che ritorna se stessa (così da evitare il primo delay della setInterval)


    dmenu = document.createElement('div');
    h3menu = document.createElement('h3');
    h3menu.appendChild(document.createTextNode('Menu:'));
    searchinmenu = document.createElement('input');
    searchinmenu.type = 'text';
    searchinmenu.placeholder = 'cerca (usa spazio per multi-search)'
    ddishlist = document.createElement('div');
    //datalist con i piatti filtrabili per nome con i termini inseriti nella barra di ricerca
    ddishlist.id = 'dishlist';
    searchinmenu.addEventListener('keyup', function(){updateDishList(dishArr, this.value, ddishlist)});
    dmenu.appendChild(h3menu);
    dmenu.appendChild(searchinmenu);
    dmenu.appendChild(ddishlist);
    parentDiv.appendChild(dmenu);
    //seleziona solo i piatti presenti nei menu disponibili nell'attuale fascia oraria
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getCurrentDishes&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            console.log('available dishes');
            console.log(response);
            if (response['data'].length == 0){
                pnomenu = document.createElement('p');
                pnomenu.appendChild(document.createTextNode('Non è stato trovato alcun piatto. Assicurati che esista un menu attivo in questa fascia oraria, oppure chiedi al tuo amministratore di aggiungere un piatto per iniziare...'));
                dmenu.appendChild(pnomenu);
            } else {
                previous_category = 'undefined';
                response['data'].forEach((piatto, index_piatto) => {
                    dishArr.push(piatto);
                });
                buildDish(dishArr, ddishlist);
            }
        }

    });

    
    dorder = document.createElement('div');
    dorder.id = 'ordine';
    h3order = document.createElement('h3');
    h3order.appendChild(document.createTextNode('Ordine:'))
    dorder.appendChild(h3order);
    pnomenu = document.createElement('p');
    pnomenu.appendChild(document.createTextNode('Seleziona un tavolo per iniziare...'));
    dtableorder = document.createElement('div');
    dtableorder.id = 'tableorder';
    dtableorder.appendChild(pnomenu);
    dorderlist = document.createElement('div');
    dorderlist.id = 'orderlist';
    
    bsendorder = document.createElement('button');
    bsendorder.appendChild(document.createTextNode('Invia ordine'));
    bsendorder.addEventListener('click', function(){makeOrder(user, ristorante)})

    dorder.appendChild(dtableorder);
    dorder.appendChild(dorderlist);
    dorder.appendChild(bsendorder);

    parentDiv.appendChild(dorder);

    
    
}

/*--------------------------------------------------------------------------*/

function updateDishList(dishArr, searchterm, parentDiv) {
    //filtra i nomi che contengono almeno uno dei termini inseriti (case-insensitive)
    // tecnicamente è possibile inserire regex valido... ma poco male, magari è utile
    pattern = new RegExp('('+searchterm.replace(/\s+/g, '|')+')', 'i'); 
    var tmpArr = dishArr.filter(dish => pattern.test(dish['nome']));
    buildDish(tmpArr, parentDiv);
}

function buildDish(arr, parentDiv){
    while (parentDiv.lastChild) {
        parentDiv.removeChild(parentDiv.firstChild);
    }
    
    previous_category = 'undefined';
    arr.forEach((piatto, index_piatto) => {
        if (piatto['categoria'] != previous_category) {
            h5categoria = document.createElement('h5');
            h5categoria.appendChild(document.createTextNode(((piatto['categoria'] != '')?piatto['categoria']:'senza-categoria') + ':'));
            parentDiv.appendChild(h5categoria);
            previous_category = piatto['categoria']
        }
        dpiatto = document.createElement('div');
        dpiatto.classList.add('piatto');
        dpiatto.id = 'piatto-' + piatto['id_piatto'];
        dpiatto.appendChild(document.createTextNode(piatto['nome']));


        notes = document.createElement('textarea');
        notes.id = 'notes-' + piatto['id_piatto'];
        notes.placeholder = 'Note (facoltativo)';
        
        baddpiatto = document.createElement('button');
        baddpiatto.value = piatto['id_piatto']+':'+piatto['nome'];
        iadd = document.createElement('i');
        iadd.classList.add('material-icons');
        iadd.appendChild(document.createTextNode('add_circle'));
        baddpiatto.appendChild(iadd);
        //baddpiatto.disabled = true;
        baddpiatto.addEventListener('click', function(){addDishToOrder(this.value, 1)} );
        bremovepiatto = document.createElement('button');
        bremovepiatto.value = piatto['id_piatto']+':'+piatto['nome'];
        iremove = document.createElement('i');
        iremove.classList.add('material-icons');
        iremove.appendChild(document.createTextNode('remove_circle'));
        bremovepiatto.appendChild(iremove);
        bremovepiatto.addEventListener('click', function(){addDishToOrder(this.value, -1)} );

        dpiatto.appendChild(notes);
        dpiatto.appendChild(baddpiatto);
        dpiatto.appendChild(bremovepiatto);


        parentDiv.appendChild(dpiatto);
    });
}

function addDishToOrder(piatto, quantity) {
    id_piatto = piatto.split(':')[0];
    nome_piatto = piatto.split(':')[1];
    notes = document.getElementById('notes-' + id_piatto).value;
    if (document.getElementById('order-dish-' + id_piatto) == null) {
        dpiatto = document.createElement('div');
        dpiatto.classList.add('piatto');
        dpiatto.id = 'order-dish-' + id_piatto;
    
    
        pcontent = document.createElement('p');
        pcontent.appendChild(document.createTextNode(quantity + 'x ' + nome_piatto + ((notes == null || notes == '')?'':' [' + notes + ']')));

        iidpiatto = document.createElement('input');
        iidpiatto.type = 'number';
        iidpiatto.value = id_piatto;
        iidpiatto.readOnly = true;
        iidpiatto.hidden = true;
        tanote = document.createElement('textarea');
        tanote.value = notes;
        tanote.readOnly = true;
        tanote.hidden = true;

        dpiatto.appendChild(pcontent);
        dpiatto.appendChild(iidpiatto);
        dpiatto.appendChild(tanote);
        document.getElementById('orderlist').appendChild(dpiatto);
        
    } else {
        newquantity = Number(document.getElementById('order-dish-' + id_piatto).childNodes[0].innerText.split('x')[0]) + Number(quantity);
        if (newquantity == 0) {
            document.getElementById('order-dish-' + id_piatto).remove();
        }else{
            document.getElementById('order-dish-' + id_piatto).childNodes[0].innerText = newquantity + 'x ' + nome_piatto + ((notes == null || notes == '')?'':' [' + notes + ']') ;
            document.getElementById('order-dish-' + id_piatto).childNodes[2].value = notes;
        }
    }

}

function selectTable(stanza_tavolo, nome_stanza_tavolo) {
    while (document.getElementById('orderlist').lastChild) {
        document.getElementById('orderlist').removeChild(document.getElementById('orderlist').firstChild)
    }
    while (document.getElementById('tableorder').lastChild) {
        document.getElementById('tableorder').removeChild(document.getElementById('tableorder').firstChild)
    }
    ptableorder = document.createElement('p');
    ptableorder.value = stanza_tavolo;
    ptableorder.appendChild(document.createTextNode('Ordine per il tavolo ' + nome_stanza_tavolo));
    document.getElementById('tableorder').appendChild(ptableorder);

    stanza = stanza_tavolo.split(':')[0];
    tavolo = stanza_tavolo.split(':')[1];
    console.log(stanza + ' ' + tavolo);
}

function makeOrder(utente, ristorante) { 
    if (document.getElementById('orderlist') == null) {
        sendAlert('Nessun piatto ordinato', 'info');
    } else {
        orderlist = document.getElementById('orderlist');
        if (orderlist.childNodes.length == 0) {
            sendAlert('selezionare almeno un piatto', 'info');
            return
        }
        for (const elem_piatto of orderlist.childNodes) {
            piatto = elem_piatto.childNodes[1].value;
            quantita = elem_piatto.childNodes[0].innerText.split('x')[0];
            note = elem_piatto.childNodes[2].value;
            if (document.getElementById('tableorder').firstChild.value == null) {
                sendAlert('selezionare un tavolo', 'info');
                return
            }
            stanza = document.getElementById('tableorder').firstChild.value.split(':')[0];
            tavolo = document.getElementById('tableorder').firstChild.value.split(':')[1];
            AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=makeOrder&user='+ utente + '&piatto='+ piatto + '&quantita='+ quantita + '&note='+ note + '&tavolo='+ tavolo + '&stanza='+ stanza + '&ristorante='+ ristorante, true, null, 
            function(response){
                if (response['responseCode'] != 0) {
                    sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
                } else {
                    sendAlert('ordine registrato con successo', 'success');
                }
            });        
        }
    }
}

function setDelivered(user, ordine) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=setDelivered&user='+ user + '&ordine='+ ordine, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('ordine completato con successo', 'success');
        }
    });

}