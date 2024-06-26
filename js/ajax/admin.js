
/**Schermata relativa al ruolo di admin
 * 
 * In questo pannello è possibile amministrare ogni aspetto relativo al proprio ristorante,
 * a partire dal ruolo del proprio staff, la disposizione di stanze e tavoli,
 * il contenuto dei menu con gli orari di validità e i piatti che ne fanno parte con tutte le loro informazioni
 */


function loadStaffSettings(parentDiv, user, ristorante){
    //Come sempre, vengono chiusi tutti i timer tranne che per la notifica di nuovi messaggi
    intervalArr.map((a) => {
        clearInterval(a);
        intervalArr= [];
    })

    notifUnreadMessages(user);        

    //Per ogni utente associato al ristorante, l'admin può conferire o revocare privilegi riguardo ai ruoli
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listUsers&ristorante='+ ristorante, true, null, 
            function(response){
                if (response['responseCode'] != 0) {
                    sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
                } else {
                    console.table(response['data']);
                    h3staff = document.createElement('h3');
                    h3staff.appendChild(document.createTextNode('Staff'));
                    parentDiv.appendChild(h3staff);
                    response['data'].forEach(row => {
                        this_user = document.createElement('div');
                        this_user.classList.add('user');

                        this_user.appendChild(document.createTextNode(row['username'] + ', ruoli: '));

                        form = document.createElement('form');
                        form.id = 'form-'+row['id_utente'];
                        form.addEventListener("change", function(){updatePrivilege(row['id_utente'], this.childNodes)});

                        //checkbox per il ruolo di admin
                        cbadmin = document.createElement('input');
                        cbadmin.type = 'checkbox';
                        cbadmin.id = 'admin-'+row['id_utente'];
                        cbadmin.checked = (row['privilegi'] & 8);
                        cbadmin.disabled = (user == row['id_utente']);
                        lcbadmin = document.createElement('label');
                        lcbadmin.htmlFor = 'admin-'+row['id_utente'];
                        lcbadmin.appendChild(document.createTextNode('admin'));
                        
                        //checkbox per il ruolo di cameriere
                        cbcameriere = document.createElement('input');
                        cbcameriere.type = 'checkbox';
                        cbcameriere.id = 'cameriere-'+row['id_utente'];
                        cbcameriere.checked = (row['privilegi'] & 1);
                        cbcameriere.disabled = (user == row['id_utente'] || cbadmin.checked);
                        lcbcameriere = document.createElement('label');
                        lcbcameriere.htmlFor = 'cameriere-'+row['id_utente'];
                        lcbcameriere.appendChild(document.createTextNode('cameriere'));
                        
                        //checkbox per il ruolo di cuoco
                        cbcuoco = document.createElement('input');
                        cbcuoco.type = 'checkbox';
                        cbcuoco.id = 'cuoco-'+row['id_utente'];
                        cbcuoco.checked = (row['privilegi'] & 2);
                        cbcuoco.disabled = (user == row['id_utente'] || cbadmin.checked);
                        lcbcuoco = document.createElement('label');
                        lcbcuoco.htmlFor = 'cuoco-'+row['id_utente'];
                        lcbcuoco.appendChild(document.createTextNode('cuoco'));
                        
                        //checkbox per il ruolo di cassa
                        cbcassa = document.createElement('input');
                        cbcassa.type = 'checkbox';
                        cbcassa.id = 'cassa-'+row['id_utente'];
                        cbcassa.checked = (row['privilegi'] & 4);
                        cbcassa.disabled = (user == row['id_utente'] || cbadmin.checked);
                        lcbcassa = document.createElement('label');
                        lcbcassa.htmlFor = 'cassa-'+row['id_utente'];
                        lcbcassa.appendChild(document.createTextNode('cassa'));
                        

                        form.appendChild(cbadmin);
                        form.appendChild(lcbadmin);
                        form.appendChild(cbcameriere);
                        form.appendChild(lcbcameriere);
                        form.appendChild(cbcuoco);
                        form.appendChild(lcbcuoco);
                        form.appendChild(cbcassa);
                        form.appendChild(lcbcassa);

                        this_user.appendChild(form);
                        parentDiv.appendChild(this_user);
                    });
                }
            });
}

//L'admin può visualizzare e modificare tutte le informazioni riguardo il proprio ristorante, oltre che la licenza attualmente in uso
function loadRestaurantSettings(parentDiv, ristorante){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getRestaurant&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            console.log(response);
            //titolo della sezione
            h3Ristorante = document.createElement('h3');
            h3Ristorante.appendChild(document.createTextNode('Ristorante'));
            parentDiv.appendChild(h3Ristorante);

            row = response['data'][0];

            //input per il nome del ristorante
            inome = document.createElement('input');
            inome.id = 'nome_ristorante';
            inome.type = 'text';
            inome.value = row['nome_ristorante'];
            linome = document.createElement('label');
            linome.htmlFor = 'nome_ristorante';
            linome.appendChild(document.createTextNode('Nome ristorante:'));

            //input per l'indirizzo del ristorante
            addr = document.createElement('input');
            addr.id = 'indirizzo';
            addr.type = 'text';
            addr.value = row['indirizzo'];
            laddr = document.createElement('label');
            laddr.htmlFor = 'indirizzo';
            laddr.appendChild(document.createTextNode('Indirizzo:'));

            //input per il limite di ritardo consentito per la consegna di un ordine
            lco = document.createElement('input');
            lco.id = 'limite_consegna_ordine';
            lco.type = 'number';
            lco.value = row['limite_consegna_ordine'];
            llco = document.createElement('label');
            llco.htmlFor = 'limite_consegna_ordine';
            llco.appendChild(document.createTextNode('Limite consegna ordine (ossia i minuti prima che la consegna di un ordine possa essere considerata in ritardo):'));

            //input per la licenza in uso
            license_key = document.createElement('input');
            license_key.id = 'license_key';
            license_key.type = 'number';
            license_key.value = row['license_key'];
            llicense_key = document.createElement('label');
            llicense_key.htmlFor = 'license_key';
            llicense_key.appendChild(document.createTextNode('License key:'));

            updateButton = document.createElement('button');
            updateButton.appendChild(document.createTextNode('Aggiorna'));
            updateButton.addEventListener('click', function(){updateRestaurant(inome.value, addr.value, lco.value, license_key.value, ristorante)});

            parentDiv.appendChild(linome);
            parentDiv.appendChild(document.createElement('br'));

            parentDiv.appendChild(inome);
            parentDiv.appendChild(document.createElement('br'));

            parentDiv.appendChild(laddr);
            parentDiv.appendChild(document.createElement('br'));

            parentDiv.appendChild(addr);
            parentDiv.appendChild(document.createElement('br'));

            parentDiv.appendChild(llco);
            parentDiv.appendChild(document.createElement('br'));

            parentDiv.appendChild(lco);
            parentDiv.appendChild(document.createElement('br'));

            parentDiv.appendChild(llicense_key);
            parentDiv.appendChild(document.createElement('br'));

            parentDiv.appendChild(license_key);
            parentDiv.appendChild(document.createElement('br'));

            parentDiv.appendChild(updateButton);

        }
    })
}

//l'admin può aggiungere stanze, rinominarle e, per ciascuna, aggiungere tavoli con le relative coordinate (usate per la visualizzazione 'reale' nelle mappe per i camerieri e per la cassa)
function loadRoomSettings(parentDiv, ristorante){
    while (parentDiv.lastChild) {
        parentDiv.removeChild(parentDiv.firstChild);
    }
    alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']; //traduzione rapida tra un numero e una lettera
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listRooms&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            console.log('room:');
            console.log(response);
            //titolo della sezione
            h3Stanze = document.createElement('h3');
            h3Stanze.appendChild(document.createTextNode('Stanze e Tavoli'));
            parentDiv.appendChild(h3Stanze);
            
            //se non è stata trovata alcuna stanza..
            if (response['data'].length == 0){
                pnoroom = document.createElement('p');
                pnoroom.appendChild(document.createTextNode('Aggiungi una stanza per iniziare...'));
                parentDiv.appendChild(pnoroom);
            } else {
                response['data'].forEach((stanza, index_stanza) => {
                    console.log(stanza['id_stanza']);
                    dstanza = document.createElement('div');
                    dstanza.classList.add('stanza');
                    //nome della stanza come lettera
                    h3Stanza = document.createElement('h3');
                    h3Stanza.appendChild(document.createTextNode('stanza ' + alphabet[stanza['id_stanza']] + ': '));
                    //nome reale della stanza
                    inome_stanza = document.createElement('input');
                    inome_stanza.type = 'text';
                    inome_stanza.value = stanza['nome_stanza'];
                    inome_stanza.id = 'is-'+index_stanza;
                    bnome_stanza = document.createElement('button');
                    bnome_stanza.appendChild(document.createTextNode('Aggiorna nome'));
                    bnome_stanza.addEventListener('click', function(){updateRoom('is-'+index_stanza, index_stanza, ristorante)});
                    lbnome_stanza = document.createElement('label');
                    lbnome_stanza.htmlFor ='is-'+index_stanza;
                    lbnome_stanza.appendChild(document.createTextNode('Nome stanza: '))
                    dstanza.appendChild(h3Stanza);
                    dstanza.appendChild(lbnome_stanza);
                    dstanza.appendChild(inome_stanza);
                    dstanza.appendChild(bnome_stanza);
                    if(stanza['tavoli'] != null){
                        dtavolocontainer = document.createElement('div');
                        dtavolocontainer.classList.add('table-container');
                        stanza['tavoli'].split(',').forEach((tavolo, index_tavolo) => {


                            dtavolo = document.createElement('div');
                            dtavolo.classList.add('tavolo');
                            //ogni tavolo è mnemonicamente rappresentato dalla lettera della propria stanza e dal proprio numero all'interno della stessa
                            nometavolo = document.createElement('b');
                            nometavolo.appendChild(document.createTextNode(alphabet[stanza['id_stanza']] + tavolo.split(':')[0]));
                            dtavolo.appendChild(nometavolo);

                            //le coordinate x e y del tavolo all'interno della stanza
                            //(poichè sfrutta le proprietà CSS left e top per il rendering, (0,0) rappresenta l'angolo in alto a sinistra della stanza)
                            ipx = document.createElement('input');
                            ipx.id = 'px-' + index_stanza + '-' + index_tavolo;
                            ipx.type = 'number';
                            ipx.value = tavolo.split(':')[1];
                            lipx = document.createElement('label');
                            lipx.htmlFor = 'px-' + index_stanza + '-' + index_tavolo;
                            lipx.appendChild(document.createTextNode('Percent X:'));

                            ipy = document.createElement('input');
                            ipy.id = 'py-' + index_stanza + '-' + index_tavolo;
                            ipy.type = 'number';
                            ipy.value = tavolo.split(':')[2];
                            lipy = document.createElement('label');
                            lipy.htmlFor = 'py-' + index_stanza + '-' + index_tavolo;
                            lipy.appendChild(document.createTextNode('Percent Y:'));

                            btavolo = document.createElement('button');
                            btavolo.appendChild(document.createTextNode('Aggiorna'));
                            btavolo.addEventListener('click', function(){updateTable('px-' + index_stanza + '-' + index_tavolo, 'py-'+ index_stanza + '-' + index_tavolo, index_tavolo, index_stanza, ristorante)});

                            dtavolo.appendChild(document.createElement('br'));
                            dtavolo.appendChild(lipx);
                            dtavolo.appendChild(document.createElement('br'));
                            dtavolo.appendChild(ipx);
                            dtavolo.appendChild(document.createElement('br'));
                            dtavolo.appendChild(lipy);
                            dtavolo.appendChild(document.createElement('br'));
                            dtavolo.appendChild(ipy);
                            dtavolo.appendChild(document.createElement('br'));
                            dtavolo.appendChild(btavolo);
                            dtavolo.appendChild(document.createElement('br'));
                            
                            dtavolocontainer.appendChild(dtavolo);
                        });
                        dstanza.appendChild(dtavolocontainer);
                    }

                    baddtable = document.createElement('button');
                    iadd = document.createElement('i');
                    iadd.classList.add('material-icons');
                    iadd.appendChild(document.createTextNode('add_circle'));
                    baddtable.appendChild(iadd);
                    baddtable.addEventListener('click', function(){addTable(index_stanza, ristorante, parentDiv)});
                    dstanza.appendChild(baddtable);
                    parentDiv.appendChild(dstanza);
                });
            }
            baddRoom = document.createElement('button');
            iadd = document.createElement('i');
            iadd.classList.add('material-icons');
            iadd.appendChild(document.createTextNode('add_circle'));
            baddRoom.appendChild(iadd);
            baddRoom.addEventListener('click', function(){addRoom(ristorante, parentDiv)});
            parentDiv.appendChild(baddRoom);
        }
    });
}

//la stessa lista di allergeni impiegata all'interno del database
allergeni = ['pesce', 'molluschi', 'latticini', 'glutine', 
    'frutta a guscio', 'crostacei', 'arachidi', 
    'lupini', 'uova', 'solfiti', 'soia', 'sesamo', 
    'senape', 'sedano', 'piccante', 'surgelato'];

//l'admin può aggiungere piatti al proprio repertorio e di ciascuno modificarne le informazioni
function loadDishSettings(parentDiv, ristorante){
    while (parentDiv.lastChild) {
        parentDiv.removeChild(parentDiv.firstChild);
    }
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listDishes&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            console.log('Dish:');
            console.log(response);
            //titolo della sezione
            h3Piatti = document.createElement('h3');
            h3Piatti.appendChild(document.createTextNode('Piatti'));
            parentDiv.appendChild(h3Piatti);
            
            //se non è stato creato ancora alcun piatto..
            if (response['data'].length == 0){
                pnoroom = document.createElement('p');
                pnoroom.appendChild(document.createTextNode('Aggiungi quali piatti vengono proposti...'));
                parentDiv.appendChild(pnoroom);
            } else {
                response['data'].forEach((piatto, index_piatto) => {
                    this_div = document.createElement('div');
                    this_div.classList.add('piatto');
                    

                    iindex_piatto = document.createElement('input');
                    iindex_piatto.type = 'number';
                    iindex_piatto.id = 'indexpiatto-'+index_piatto;
                    iindex_piatto.value = piatto['id_piatto'];
                    iindex_piatto.hidden = true;
                    iindex_piatto.readOnly = true;

                    this_div.appendChild(iindex_piatto);

                    //input per il nome del piatto
                    inome_piatto = document.createElement('input');
                    inome_piatto.type = "text";
                    inome_piatto.id = 'nomepiatto-' + index_piatto;
                    inome_piatto.value = piatto['nome'];
                    inome_piatto.pattern = /[^,]+/;
                    linome_piatto = document.createElement('label');
                    linome_piatto.htmlFor = 'nomepiatto-' + index_piatto;
                    linome_piatto.appendChild(document.createTextNode('nome piatto'))

                    this_div.appendChild(linome_piatto);
                    this_div.appendChild(document.createElement('br'));
                    this_div.appendChild(inome_piatto);
                    
                    this_div.appendChild(document.createElement('br'));

                    //input per la categora del piatto (utile per raggruppare i piatti nel ruolo di cameriere)
                    icategoria_piatto = document.createElement('input');
                    icategoria_piatto.type = "text";
                    icategoria_piatto.id = 'categoriapiatto-' + index_piatto;
                    icategoria_piatto.placeholder = '(primi, bevande, dessert...)'
                    icategoria_piatto.value = piatto['categoria'];
                    licategoria_piatto = document.createElement('label');
                    licategoria_piatto.htmlFor = 'categoriapiatto-' + index_piatto;
                    licategoria_piatto.appendChild(document.createTextNode('categoria'))

                    this_div.appendChild(licategoria_piatto);
                    this_div.appendChild(document.createElement('br'));
                    this_div.appendChild(icategoria_piatto);
                    
                    this_div.appendChild(document.createElement('br'));

                    //il prezzo del piatto
                    iprezzo_piatto = document.createElement('input');
                    iprezzo_piatto.type = "number";
                    iprezzo_piatto.id = 'prezzopiatto-' + index_piatto;
                    iprezzo_piatto.value = piatto['prezzo'];
                    liprezzo_piatto = document.createElement('label');
                    liprezzo_piatto.htmlFor = 'prezzopiatto-' + index_piatto;
                    liprezzo_piatto.appendChild(document.createTextNode('prezzo €'))

                    this_div.appendChild(liprezzo_piatto);
                    this_div.appendChild(document.createElement('br'));
                    this_div.appendChild(iprezzo_piatto);
                    
                    this_div.appendChild(document.createElement('br'));

                    //gli ingredienti/una breve descrizione del piatto
                    taingredienti_piatto = document.createElement('textarea');
                    taingredienti_piatto.id = 'ingredientipiatto-' + index_piatto;
                    taingredienti_piatto.placeholder = '(Breve descrizione del piatto con ingredienti)'
                    taingredienti_piatto.value = piatto['ingredienti'];
                    ltaingredienti_piatto = document.createElement('label');
                    ltaingredienti_piatto.htmlFor = 'ingredientipiatto-' + index_piatto;
                    ltaingredienti_piatto.appendChild(document.createTextNode('ingredenti'))

                    this_div.appendChild(ltaingredienti_piatto);
                    this_div.appendChild(document.createElement('br'));
                    this_div.appendChild(taingredienti_piatto);
                    
                    this_div.appendChild(document.createElement('br'));

                    //gli allergeni contenuti nel piatto, a partire dalla lista di allergeni riconosciuti, più l'informazione di un piatto surgelato o piccante
                    lallergeni = document.createElement('label');
                    lallergeni.appendChild(document.createTextNode('allergeni'));
                    this_div.appendChild(lallergeni);
                    this_div.appendChild(document.createElement('br'));

                    allergeni.forEach((allergene, index_allergene) => {
                        cballergene = document.createElement('input');
                        cballergene.type = 'checkbox';
                        cballergene.id = 'allergene' + ':' + allergene + '-' + index_piatto;
                        if (piatto['allergeni'] != null && piatto['allergeni'].includes(allergene)) cballergene.checked = true;
                        lcballergene = document.createElement('label');
                        lcballergene.htmlFor = 'allergene' + ':' + allergene + '-' + index_piatto;
                        lcballergene.appendChild(document.createTextNode(allergene));
                        if (index_allergene % 3 == 1) {
                            lcballergene.classList.add('glow-fuchsia');
                        } else if (index_allergene % 3 == 2){
                            lcballergene.classList.add('glow-orange');
                        }
                        this_div.appendChild(cballergene);
                        this_div.appendChild(lcballergene);
                    });


                    this_div.appendChild(document.createElement('br'));

                    this_button = document.createElement('button');
                    this_button.appendChild(document.createTextNode('aggiorna'));
                    this_button.onclick = function(){updateDish(index_piatto, parentDiv, document.getElementsByClassName('menu-container')[0], ristorante)};
                    this_div.appendChild(this_button);
                    parentDiv.appendChild(this_div);
                });
            }
            baddRoom = document.createElement('button');
            iadd = document.createElement('i');
            iadd.classList.add('material-icons');
            iadd.appendChild(document.createTextNode('add_circle'));
            baddRoom.appendChild(iadd);
            baddRoom.addEventListener('click', function(){addDish(ristorante, parentDiv, document.getElementsByClassName('menu-container')[0])});
            parentDiv.appendChild(baddRoom);

        }
    });
}

//l'admin può aggiungere menu che possono essere resi disponibili solo in alcune fasce della giornata,
//inoltre è quì possibile anche aggiungere o rimuovere i singoli piatti dai menu creati
function loadMenuSettings(parentDiv, ristorante){
    while (parentDiv.lastChild) {
        parentDiv.removeChild(parentDiv.firstChild);
    }
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listMenus&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            console.log('Menu:');
            console.log(response);
            //titolo della sezione
            h3Menu = document.createElement('h3');
            h3Menu.appendChild(document.createTextNode('Menu'));
            parentDiv.appendChild(h3Menu);
            
            //se non è stato ancora creato alcun menu..
            if (response['data'].length == 0){
                pnoroom = document.createElement('p');
                pnoroom.appendChild(document.createTextNode('Crea un nuovo menu e aggiungici dei piatti...'));
                parentDiv.appendChild(pnoroom);
            } else {
                response['data'].forEach((menu, index_menu) => {
                    this_div = document.createElement('div');
                    this_div.classList.add('menu');
                    /*for (property in menu){
                        this_div.innerHTML += property + '=' + menu[property] + ', ';
                    }*/
                    h5menu = document.createElement('h5');
                    h5menu.appendChild(document.createTextNode('menu: ' + (Number(index_menu) + 1)));   //per qualche motivo solo ai programmatori piace partire da 0..

                    this_div.appendChild(h5menu);

                    iindexmenu = document.createElement('input');
                    iindexmenu.type = 'number';
                    iindexmenu.id = 'indexmenu-'+index_menu;
                    iindexmenu.value = menu['id_menu'];
                    iindexmenu.hidden = true;
                    iindexmenu.readOnly = true;

                    this_div.appendChild(iindexmenu);

                    //input per la scelta degli orari di inizio e fine validità del menu
                    iora_inizio = document.createElement('input');
                    iora_inizio.type = 'time';
                    iora_inizio.id = 'orainizio-' + index_menu;
                    iora_inizio.value = menu['orarioInizio'].slice(0, 5);
                    liora_inizio = document.createElement('label');
                    liora_inizio.htmlFor = 'orainizio-' + index_menu;
                    liora_inizio.appendChild(document.createTextNode('Ora inizio: '));

                    this_div.appendChild(liora_inizio);
                    this_div.appendChild(iora_inizio);

                    iora_fine = document.createElement('input');
                    iora_fine.type = 'time';
                    iora_fine.id = 'orafine-' + index_menu;
                    iora_fine.value = menu['orarioFine'].slice(0, 5);
                    liora_fine = document.createElement('label');
                    liora_fine.htmlFor = 'orafine-' + index_menu;
                    liora_fine.appendChild(document.createTextNode('Ora fine: '));

                    this_div.appendChild(liora_fine);
                    this_div.appendChild(iora_fine);
                    
                    this_button = document.createElement('button');
                    this_button.appendChild(document.createTextNode('aggiorna'));
                    this_button.onclick = function(){updateMenu(index_menu)};
                    this_div.appendChild(this_button);

                    //pulsante per stampare il menu 
                    bprintmenu = document.createElement('button');
                    bprintmenu.appendChild(document.createTextNode('Stampa questo menu'));
                    bprintmenu.addEventListener('click', function(){window.open("./printMenu.php?menu=" + menu['id_menu'], '_blank')})
                    this_div.appendChild(bprintmenu)

                    //i piatti attualmente presenti nel menu
                    if(menu['piatti'] != null){
                        menu['piatti'].split(',').forEach((piatto, index_piatto) => {
                            dpiatto = document.createElement('div');
                            dpiatto.classList.add('piattoinmenu');

                            iindexpiattoinmenu = document.createElement('input');
                            iindexpiattoinmenu.type = 'number';
                            iindexpiattoinmenu.id = 'indexpiattoinmenu-'+index_piatto;
                            iindexpiattoinmenu.value = piatto.split(':')[0];
                            iindexpiattoinmenu.hidden = true;
                            iindexpiattoinmenu.readOnly = true;

                            dpiatto.appendChild(iindexpiattoinmenu);
            
                            dpiatto.appendChild(document.createTextNode('Nome: ' + piatto.split(':')[1] + ', Categoria: ' + ((piatto.split(':')[2] == '')?'nessuna': piatto.split(':')[2]) + ', Prezzo: ' + piatto.split(':')[3] + '€'));

                            brempiatto = document.createElement('button');
                            irimuovi = document.createElement('i');
                            irimuovi.classList.add('material-icons');
                            irimuovi.appendChild(document.createTextNode('cancel'));
                            brempiatto.appendChild(irimuovi);
                            brempiatto.addEventListener('click', function(){removeDishFromMenu(index_piatto, index_menu, parentDiv, ristorante)})
                            dpiatto.appendChild(brempiatto);
                            this_div.appendChild(dpiatto);
                        });
                    }
                    parentDiv.appendChild(this_div);

                    //una datalist con tutti i piatti tra cui scegliere da aggiungere al menu.
                    //Questo tag permette di iniziare a digitare il nome del piatto che si cerca
                    //per poi utilizzare l'id del piatto perchè questo venga correttamente aggiunto
                    listpiatti = document.createElement('input');
                    listpiatti.id = "inputdatalist-piatti-"+index_menu;
                    listpiatti.setAttribute('list', "datalist-piatti-"+index_menu);
                    llistpiatti = document.createElement('label');
                    llistpiatti.appendChild(document.createTextNode('Aggiungi un piatto tra quelli disponibili:'));
                    llistpiatti.htmlFor = "inputdatalist-piatti-"+index_menu;
                    
                    datalistpiatti = document.createElement('datalist');
                    datalistpiatti.id = "datalist-piatti-"+index_menu;

                    buildDishList(ristorante, datalistpiatti);
                

                    listpiatti.addEventListener('change', function(){addDishToMenu(index_menu, parentDiv, ristorante)})

                    parentDiv.appendChild(llistpiatti);
                    parentDiv.appendChild(listpiatti);
                    parentDiv.appendChild(datalistpiatti);

                });
            }

            baddMenu = document.createElement('button');
            iadd = document.createElement('i');
            iadd.classList.add('material-icons');
            iadd.appendChild(document.createTextNode('add_circle'));
            baddMenu.appendChild(iadd);
            baddMenu.addEventListener('click', function(){addMenu(ristorante, parentDiv)});
            parentDiv.appendChild(baddMenu);

        }
    });
}

/*--------------------------------------------------------------------*/

function updatePrivilege(target_user, nodeList) {
    admin = nodeList[0];
    cameriere = nodeList[2];
    cuoco = nodeList[4];
    cassa = nodeList[6];

    if (admin.checked) {
        cameriere.checked = cuoco.checked = cassa.checked = true;
        cameriere.disabled = cuoco.disabled = cassa.disabled = true;        

    } else {
        cameriere.disabled = cuoco.disabled = cassa.disabled = false;
    }
    privilegi = cameriere.checked + 2 * cuoco.checked + 4 * cassa.checked + 8 * admin.checked;

    console.log(target_user + ':' + privilegi);

    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=setPrivilege&target_user='+ target_user+'&privilegi='+privilegi, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        }
    })
}

function updateRestaurant(nome_ristorante, indirizzo, limite_consegna_ordine, license_key, ristorante) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=updateRestaurant&nome_ristorante='+nome_ristorante+'&indirizzo='+indirizzo+'&limite_consegna_ordine='+limite_consegna_ordine+'&license_key='+license_key+'&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('informazioni aggiornate con successo', 'success');
        }
    })
}

function updateTable(percentX, percentY, tavolo, stanza, ristorante) {
    percentX = document.getElementById(percentX).value;
    console.log(percentX);
    percentY = document.getElementById(percentY).value;
    console.log(percentY);
    if (percentX < 0 || percentY > 100 || percentY < 0 || percentY > 100) {
        sendAlert('inserire valori nel range (0-100) nei campi percentX e percentY', 'error');
        return;
    }
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=updateTable&percentX='+percentX+'&percentY='+percentY+'&tavolo='+tavolo+'&stanza='+stanza+'&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('informazioni aggiornate con successo', 'success');
        }
    })

}

function updateRoom(nome_stanza, stanza, ristorante) {

    nome_stanza = document.getElementById(nome_stanza).value;
    console.log(nome_stanza);
    console.log(stanza);
    console.log(ristorante);
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=updateRoom&nome_stanza='+nome_stanza+'&stanza='+stanza+'&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('informazioni aggiornate con successo', 'success');
        }
    })

}

function updateDish(piatto, parentDiv, menucontainer, ristorante) {

    nome = document.getElementById('nomepiatto-'+piatto).value;
    categoria = document.getElementById('categoriapiatto-'+piatto).value;
    prezzo = document.getElementById('prezzopiatto-'+piatto).value;
    ingredienti = document.getElementById('ingredientipiatto-'+piatto).value;

    list_allergeni = '';

    allergeni.forEach(allergene => {
        list_allergeni += (document.getElementById('allergene' + ':' + allergene + '-' + piatto).checked) ? ((list_allergeni == '')?allergene:','+allergene) : '';
    });

    piatto = document.getElementById('indexpiatto-'+piatto).value;
    
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=updateDish&nome_piatto='+nome+'&categoria='+categoria+'&prezzo='+ prezzo+'&ingredienti='+ ingredienti+'&allergeni='+ list_allergeni+'&piatto='+ piatto, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('informazioni aggiornate con successo', 'success');
            loadMenuSettings(menucontainer, ristorante); //necessario per ricostruire la lista di piatti disponibili per i menu
        }
    })

}

function updateMenu(menu){
    orario_inizio = document.getElementById('orainizio-'+menu).value;
    orario_fine = document.getElementById('orafine-'+menu).value;
    if (orario_fine < orario_inizio) {
        sendAlert('l\'orario di fine validità del menu non può essere minore del suo orario di inizio', 'error');
        return;
    }
    menu = document.getElementById('indexmenu-'+menu).value;
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=updateMenu&orario_inizio='+orario_inizio+'&orario_fine='+ orario_fine+'&menu='+ menu, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('informazioni aggiornate con successo', 'success');
        }
    })


}

function addTable(stanza, ristorante, parentDiv){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=addTable&stanza='+stanza+'&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            loadRoomSettings(parentDiv, ristorante);
        }
    })

}

function addRoom(ristorante, parentDiv){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=addRoom&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            loadRoomSettings(parentDiv, ristorante);
        }
    })

}

function addDish(ristorante, parentDiv, menucontainer){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=addDish&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            loadDishSettings(parentDiv, ristorante);
            loadMenuSettings(menucontainer, ristorante); //necessario per ricostruire la lista di piatti disponibili per i menu
        }
    })

}

function addMenu(ristorante, parentDiv){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=addMenu&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            loadMenuSettings(parentDiv, ristorante);
        }
    })

}

function removeDishFromMenu(piatto, menu, parentDiv, ristorante) {
    piatto = document.getElementById('indexpiattoinmenu-'+piatto).value;
    menu = document.getElementById('indexmenu-'+menu).value;

    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=removeDishFromMenu&piatto='+ piatto + '&menu='+ menu, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            loadMenuSettings(parentDiv, ristorante)
        }
    })

}

function addDishToMenu(menu, parentDiv, ristorante) {
    piatto = document.getElementById("inputdatalist-piatti-"+menu).value;
    menu = document.getElementById('indexmenu-'+menu).value;

    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=addDishToMenu&piatto='+ piatto + '&menu='+ menu, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            loadMenuSettings(parentDiv, ristorante);
        }
    })

}

function buildDishList(ristorante, parentlist){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listDishes&ristorante='+ ristorante, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            if (response['data'].length == 0){
                parentlist.previousSibling.placeholder = '(nessun piatto trovato...)';
            } else {
                parentlist.previousSibling.placeholder = '--seleziona un piatto--';
                response['data'].forEach(elem_piatto => {
                    this_option = document.createElement('option');
                    this_option.value = elem_piatto['id_piatto'];
                    this_option.appendChild(document.createTextNode(elem_piatto['nome']));
                    parentlist.appendChild(this_option);
                });
            }
        }
    })

}
