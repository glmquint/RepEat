/**Script principale per il caricamento delle pagine adeguate nella schermata della main home
 * 
 * Vengono anzitutto ricavate le informazioni principali dell'utente a partire dal suo id salvato tra le variabili di sessione.
 * Nel caso questi non sia associato ad alcun ristorante viene caricata la pagina di invio richiesta partecipazione/creazione di un ristorante.
 * Altrimenti, in funzione del proprio ruolo, vengono mostrate e rese disponibili le relative schermate
 */

var intervalArr = [];   //lista di tutte le setInterval, così da poterne gestire più facilmente l'avvio e la chiusura

function loadMainHome(user) {
    //Come sempre, vengono chiusi tutti i timer tranne che per la notifica di nuovi messaggi
    intervalArr.map((a) => {
        clearInterval(a);
        intervalArr= [];
    })

    notifUnreadMessages(user);

    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getUser&user='+ user, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            // console.log(response);
            body = document.getElementById('main-container');
            while (body.firstChild) {
                body.removeChild(body.firstChild);
            }
            row = response['data'][0];
            ristorante = (row['ristorante'] != null)?row['ristorante']:-1;
            privilegi = (row['privilegi'] != null)?row['privilegi']:-1;
        
            if (ristorante == -1) {
                //se l'utente non è associato ad alcun ristorante, caricare la schermata per l'invio richiesta pertecipazione/creazione del ristorante
                loadMissingRestaurant(user);
            } else{
                //viene prima controllata la validitò della licenza in uso dal proprio ristorante
                AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=checkLicenseValidity&ristorante='+ ristorante, true, null, 
                function(response){
                    console.log(response['data'][0]['is_valid'] == 0);
                    //in caso di licenza scaduta..
                    if (response['responseCode'] != 0) {
                        sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
                    } else if (response['data'][0]['is_valid'] == 0){
                        //..per i non admin vengono bloccate tutte le funzionalità di ruolo
                        if (privilegi != 15) {
                            sendAlert('licenza scaduta o disabilitata, contattare l\'amministratore per aggiornare la licensa in uso', 'info');
                        //..gli admin possono rapidamente passare alla sezione delle offerte ed aggiungere la nuova chiave.
                        //E' possibile effettuare modifiche alle impostazioni del ristorante ma queste non potranno mai essere impiegate finchè non si aggiorna la licenza
                        } else {
                            t = document.createElement('div');
                            t.innerHTML = 'Licenza scaduta o disabilitata, puoi acquistarne una tra quelle proposte <a href="./license.php" target="_blank" rel="noopener noreferrer">quì</a>';
                            body.appendChild(t);
                            restaurantDiv = document.createElement('div');
                            restaurantDiv.classList.add('restaurant-container');
                            loadRestaurantSettings(restaurantDiv, ristorante);
                            // rw = document.createElement('div');
                            // rw.id = 'role-view';
                            // rw.appendChild(restaurantDiv);
                            // body.appendChild(rw);
                            body.appendChild(restaurantDiv);
                        }
                    } else {
                        p = document.createElement('p');
                        body.appendChild(p);
                        //di default, i nuovi utenti non hanno alcun ruolo assegnato all'interno del ristorante
                        if (privilegi == 0) {
                            p.appendChild(document.createTextNode('Sembra che tu non abbia alcun ruolo assegnato. Chiedi ad un amministratore di cambiare i tuoi privilegi'));    
                        //se l'utente ha almeno un ruolo assegnato viene fornita una lista per passare rapidamente tra i ruoli disponibili.
                        //La ruolo assegnato di default segue la scala di privilegi admin > cameriere > cuoco > cassa
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

// Viene caticata la corretta schermata in funzione del ruolo selezionato
function loadRole(role, user, ristorante) {
    rw = document.getElementById('role-view');
    while (rw.firstChild) {
        rw.removeChild(rw.lastChild);
    }
    switch (role) {
        case 'admin':
            console.log('admin');
            adminDiv = document.createElement('div');
            adminDiv.classList.add('admin-container');

            staffDiv = document.createElement('div');
            staffDiv.classList.add('staff-container');
            loadStaffSettings(staffDiv, user, ristorante);
            adminDiv.appendChild(staffDiv);

            restaurantDiv = document.createElement('div');
            restaurantDiv.classList.add('restaurant-container');
            loadRestaurantSettings(restaurantDiv, ristorante);
            adminDiv.appendChild(restaurantDiv);

            roomDiv = document.createElement('div');
            roomDiv.classList.add('room-container');
            loadRoomSettings(roomDiv, ristorante);  
            adminDiv.appendChild(roomDiv);

            dishDiv = document.createElement('div');
            dishDiv.classList.add('dish-container');
            loadDishSettings(dishDiv, ristorante); 
            adminDiv.appendChild(dishDiv);

            menuDiv = document.createElement('div');
            menuDiv.classList.add('menu-container');
            loadMenuSettings(menuDiv, ristorante); 
            adminDiv.appendChild(menuDiv);

            rw.appendChild(adminDiv);


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



