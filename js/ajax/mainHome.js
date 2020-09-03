var intervalArr = [];

function loadMainHome(user) {
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
                        sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
                    } else if (response['data'][0]['is_valid'] == 0){
                        if (privilegi != 15) {
                            sendAlert('licenza scaduta o disabilitata, contattare l\'amministratore per aggiornare la licensa in uso', 'info');
                        } else {
                            t = document.createElement('div');
                            t.innerHTML = 'Licenza scaduta o disabilitata, puoi acquistarne una tra quelle proposte <a href="./license.php" target="_blank" rel="noopener noreferrer">quì</a>';
                            body.appendChild(t);
                            loadRestaurantSettings(body, ristorante);
                        }
                    } else {
                        //main_banner = document.createElement('p');
                        //main_banner.appendChild(document.createTextNode('This is the main home'));
                        p = document.createElement('p');
                        //body.appendChild(main_banner);
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



