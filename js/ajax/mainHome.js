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
            loadDishSettings(dishDiv, ristorante);  //TODO: implement
            rw.appendChild(dishDiv);

            menuDiv = document.createElement('div');
            menuDiv.classList.add('menu');
            loadMenuSettings(menuDiv, ristorante);  //TODO: implement
            rw.appendChild(menuDiv);


            break;
        case 'cameriere':
            console.log('cameriere');
            break;
        case 'cuoco':
            console.log('cuoco');
            break;
        case 'cassa':
            console.log('cassa');
            break;
    
        default:
            break;
    }
}

/*--------------------------------------------------------------------*/
