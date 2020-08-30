function loadMainHome(user) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getUser&user='+ user, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            console.log(response);
            body = document.getElementById('main-container');
            while (body.firstChild) {
                body.removeChild(body.firstChild);
            }
            row = response['data'][0];
            ristorante = (row['ristorante'] != null)?row['ristorante']:-1;
            privilegi = (row['privilegi'] != null)?row['privilegi']:-1;
        
            if (ristorante == -1) {
                loadMissingRestaurant(user);
            } else{
                AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=checkLicenseValidity&ristorante='+ ristorante, true, null, 
                function(response){
                    console.log(response['data'][0]['is_valid'] == 0);
                    if (response['responseCode'] != 0) {
                        alert('qualcosa è andato storto: ' + response['message']);
                    } else if (response['data'][0]['is_valid'] == 0){
                        if (privilegi != 15) {
                            alert('licenza scaduta o disabilitata, contattare l\'amministratore per aggiornare la licensa in uso');
                        } else {
                            t = document.createElement('div');
                            t.innerHTML = 'Licenza scaduta o disabilitata, puoi acquistarne una tra quelle proposte <a href="./license.php" target="_blank" rel="noopener noreferrer">quì</a>';
                            body.appendChild(t);
                            loadRestaurantSettings(body, ristorante);
                        }
                    } else {
                        main_banner = document.createElement('p');
                        main_banner.appendChild(document.createTextNode('This is the main home'));
                        p = document.createElement('p');
                        body.appendChild(main_banner);
                        body.appendChild(p);
                        if (privilegi == 0) {
                            p.appendChild(document.createTextNode('Sembra che tu non abbia alcun ruolo assegnato. Chiedi ad un amministratore di cambiare i tuoi privilegi'));    
                        } else {
                            p.appendChild(document.createTextNode('Seleziona il tuo ruolo tra quelli disponibili: '));    
                            sr = document.createElement('select');
                            sr.name = "select-role";
                            sr.id = "select-role";
                            sr.addEventListener("change", function(){loadRole(document.getElementById('select-role').value, user, ristorante)});
                            if(privilegi & 8){
                                option = document.createElement('option');
                                option.value = 'admin';
                                option.appendChild(document.createTextNode('admin'));
                                sr.appendChild(option);
                            }
                            if(privilegi & 1){
                                option = document.createElement('option');
                                option.value = 'cameriere';
                                option.appendChild(document.createTextNode('cameriere'));
                                sr.appendChild(option);
                            }
                            if(privilegi & 2){
                                option = document.createElement('option');
                                option.value = 'cuoco';
                                option.appendChild(document.createTextNode('cuoco'));
                                sr.appendChild(option);
                            }
                            if(privilegi & 4){
                                option = document.createElement('option');
                                option.value = 'cassa';
                                option.appendChild(document.createTextNode('cassa'));
                                sr.appendChild(option);
                            }
                            body.appendChild(sr);
                            role_div = document.createElement('div');
                            role_div.id = 'role-view';
                            body.appendChild(role_div);
                            loadRole(document.getElementById('select-role').value, user, ristorante);
                        }
            
                    }
                });
            
            }
        }
    });
}


function loadRole(role, user, ristorante) {
    rw = document.getElementById('role-view');
    while (rw.firstChild) {
        rw.removeChild(rw.lastChild);
    }
    switch (role) {
        case 'admin':
            console.log('admin');
            staffDiv = document.createElement('div');
            staffDiv.classList.add('staff');
            loadStaffSettings(staffDiv, user, ristorante);
            rw.appendChild(staffDiv);

            restaurantDiv = document.createElement('div');
            restaurantDiv.classList.add('restaurant');
            loadRestaurantSettings(restaurantDiv, ristorante);
            rw.appendChild(restaurantDiv);

            roomDiv = document.createElement('div');
            roomDiv.classList.add('room');
            loadRoomSettings(roomDiv, ristorante);  
            rw.appendChild(roomDiv);

            dishDiv = document.createElement('div');
            dishDiv.classList.add('dish');
            loadDishSettings(dishDiv, ristorante); 
            rw.appendChild(dishDiv);

            menuDiv = document.createElement('div');
            menuDiv.classList.add('menu');
            loadMenuSettings(menuDiv, ristorante); 
            rw.appendChild(menuDiv);


            break;
        case 'cameriere':
            console.log('cameriere');
            waiterDiv = document.createElement('div');
            waiterDiv.classList.add('waiter');
            loadWaiterDashboard(waiterDiv, user, ristorante);
            rw.appendChild(waiterDiv);

            break;
        case 'cuoco':
            console.log('cuoco');
            chefDiv = document.createElement('div');
            chefDiv.classList.add('chef');
            loadChefDashboard(chefDiv, user, ristorante);
            rw.appendChild(chefDiv);

            break;
        case 'cassa':
            console.log('cassa');
            cashierDiv = document.createElement('div');
            cashierDiv.classList.add('cashier');
            loadCashierDashboard(cashierDiv, user, ristorante);
            rw.appendChild(cashierDiv);

            break;
    
        default:
            break;
    }
}

/*--------------------------------------------------------------------*/

function loadWaiterDashboard(parentDiv, user, ristorante){
    droom = document.createElement('div');
    h3stanza = document.createElement('h3');
    h3stanza.appendChild(document.createTextNode('Stanze:'))
    droom.appendChild(h3stanza);
    parentDiv.appendChild(droom);
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listRooms&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
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
                            rtavolo.classList.add(tavolo.split(':')[3]);
                            rtavolo.addEventListener('change', function(){if(this.checked) selectTable(this.value)})
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

    dmenu = document.createElement('div');
    h3menu = document.createElement('h3');
    h3menu.appendChild(document.createTextNode('Menu:'))
    dmenu.appendChild(h3menu);
    parentDiv.appendChild(dmenu);
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getCurrentDishes&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
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
                    if (piatto['categoria'] != previous_category) {
                        h5categoria = document.createElement('h5');
                        h5categoria.appendChild(document.createTextNode(((piatto['categoria'] != '')?piatto['categoria']:'senza-categoria') + ':'));
                        dmenu.appendChild(h5categoria);
                        previous_category = piatto['categoria']
                    }
                    dpiatto = document.createElement('div');
                    dpiatto.classList.add('piatto');
                    dpiatto.id = 'piatto-' + piatto['id_piatto'];
                    dpiatto.appendChild(document.createTextNode(piatto['nome']));

                    /*
                    iquantity = document.createElement('input');
                    iquantity.type= 'number';
                    iquantity.value = 1;
                    iquantity.id = 'quantity-' + piatto['id_piatto'];
                    liquantity = document.createElement('label');
                    liquantity.htmlFor = 'quantity-' + piatto['id_piatto'];
                    liquantity.appendChild(document.createTextNode('quantità:'));
                    */

                    notes = document.createElement('textarea');
                    notes.id = 'notes-' + piatto['id_piatto'];
                    notes.placeholder = '(Facoltativo)';
                    lnotes = document.createElement('label');
                    lnotes.htmlFor = 'notes-' + piatto['id_piatto'];
                    lnotes.appendChild(document.createTextNode('Note:'))
                    
                    baddpiatto = document.createElement('button');
                    baddpiatto.value = piatto['id_piatto']+':'+piatto['nome'];
                    baddpiatto.appendChild(document.createTextNode('+'));
                    baddpiatto.disabled = true;
                    baddpiatto.addEventListener('click', function(){addDishToOrder(this.value, 1)} );
                    bremovepiatto = document.createElement('button');
                    bremovepiatto.value = piatto['id_piatto']+':'+piatto['nome'];
                    bremovepiatto.appendChild(document.createTextNode('-'));
                    bremovepiatto.disabled = true;
                    bremovepiatto.addEventListener('click', function(){addDishToOrder(this.value, -1)} );

                    //dpiatto.appendChild(liquantity);
                    //dpiatto.appendChild(iquantity);
                    dpiatto.appendChild(lnotes);
                    dpiatto.appendChild(notes);
                    dpiatto.appendChild(baddpiatto);
                    dpiatto.appendChild(bremovepiatto);



                    dmenu.appendChild(dpiatto);
                });
            }
        }

    });

    //TODO
    dorder = document.createElement('div');
    dorder.id = 'ordine';
    h3order = document.createElement('h3');
    h3order.appendChild(document.createTextNode('Ordine:'))
    dorder.appendChild(h3order);
    pnomenu = document.createElement('p');
    pnomenu.appendChild(document.createTextNode('Seleziona un tavolo per iniziare...'));
    dorderlist = document.createElement('div');
    dorderlist.id = 'orderlist';
    dorderlist.appendChild(pnomenu);
    
    bsendorder = document.createElement('button');
    bsendorder.appendChild(document.createTextNode('Invia ordine'));
    bsendorder.addEventListener('click', function(){makeOrder(user)})

    dorder.appendChild(dorderlist);
    dorder.appendChild(bsendorder);

    parentDiv.appendChild(dorder);

    //TODO
    dready = document.createElement('div');
    dready.id = 'pronti';
    h3ready = document.createElement('h3');
    h3ready.appendChild(document.createTextNode('Da consegnare:'))
    dready.appendChild(h3ready);
    parentDiv.appendChild(dready);
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getOrdersReady&user='+ user, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            console.log('ready');
            console.log(response);
            if (response['data'].length == 0){
                pnomenu = document.createElement('p');
                pnomenu.appendChild(document.createTextNode('Nessun piatto in atteesa di essere consegnato...'));
                dready.appendChild(pnomenu);
            } else {
                console.log(response);
                previous_category = 'undefined';
                response['data'].forEach((piatto, index_piatto) => {
                    if (piatto['categoria'] != previous_category) {
                    
                    }
                });
            }
        }

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

function selectTable(stanza_tavolo) {
    while (document.getElementById('orderlist').lastChild) {
        document.getElementById('orderlist').removeChild(document.getElementById('orderlist').firstChild)
    }
    for (let i = 0; i < document.getElementsByTagName('button').length; i++) {
        document.getElementsByTagName('button')[i].disabled = false;
    }
    stanza = stanza_tavolo.split(':')[0];
    tavolo = stanza_tavolo.split(':')[1];
    console.log(stanza + ' ' + tavolo);
}

function makeOrder(utente) { //utente_ordine, piatto, quantita, note, tavolo, stanza, ristorante
    if (document.getElementById('orderlist') == null) {
        alert('Nessun piatto ordinato')
    } else {
        orderlist = document.getElementById('orderlist')
        for (const piatto of orderlist.childNodes) {
            console.log(piatto);
        }
    }
}

/*--------------------------------------------------------------------*/

function loadChefDashboard(parentDiv, user, ristorante){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listMenus&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {

        }
    });

}

/*--------------------------------------------------------------------*/

                                                         function loadCashierDashboard(parentDiv, user, ristorante){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listMenus&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {

        }
    });

}
