function load(user){
    AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=existsRequest&user=' + user, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: '+ response['message']);
        } else {
            if(response['data'][0]['num_requests'] == 0){
                AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=listRestaurants', true, null, 
                function(response){
                    if (response['responseCode'] != 0) {
                        alert('qualcosa è andato storto: '+ response['message']);
                    } else {
                        console.table(response['data']);
                        sr = document.getElementById('select-ristorante'); //not a side-request-forgery reference
                        console.log(sr);
                        response['data'].forEach(row => {
                            console.log(row)
                            this_option = document.createElement('option');
                            this_option.value = row['id_ristorante'];
                            this_option.appendChild(document.createTextNode(row['nome_ristorante']));
                            sr.appendChild(this_option);
                            
                        });

                    }
                });
            } else {
                this_body = document.getElementById('main-body');
                while (this_body.firstChild) {
                    this_body.removeChild(this_body.lastChild);
                }
                this_replacement = document.createElement('p');
                this_replacement.appendChild(document.createTextNode('La tua richiesta è stata inviata correttamente. Attendi una risposta da parte di un amministratore'));
                this_body.appendChild(this_replacement);

            }
                
        }

    });
}

function registerRestaurant(nome_ristorante, indirizzo, license_key, user) {
    AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=registerRestaurant&nome_ristorante='+nome_ristorante+'&indirizzo='+indirizzo+'&license_key='+license_key+'&user=' + user, true, null, 
    function(response){
        //console.log(response);
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: '+ response['message']);
        } else {
            alert('Registrazione avvenuta con successo!');
            window.location.reload();
        }
    });
};
