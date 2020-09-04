function load(){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listLevels', true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                console.log(response['message'])
            } else {
                console.table(response['data']);
                table = document.getElementById('offers');
                
                response['data'].forEach((offer, index_offer) => {
                    console.log(offer);
                    row = table.insertRow(index_offer);
                    
                    for (property in offer){
                        cell = row.insertCell(-1);
                        if (property != 'prezzo' && offer[property] == 0) {
                            iinf = document.createElement('i');
                            iinf.classList.add('material-icons');
                            iinf.appendChild(document.createTextNode('all_inclusive'));
                            cell.appendChild(iinf);
                        } else{
                        cell.appendChild(document.createTextNode((property != 'prezzo')?((offer[property] == 0)?'all_inclusive':offer[property]):offer[property]+'€')); //CLEANUP
                        }
                    }

                    bgenerate = document.createElement('button');
                    bgenerate.appendChild(document.createTextNode('acquista')) ;
                    bgenerate.value = offer['livello'];
                    bgenerate.addEventListener('click', function(){generateKey(this.value)});
                    cell = row.insertCell(-1);
                    cell.appendChild(bgenerate);



                    
                });
                
                tablecaption = table.createCaption();
                h3offers = document.createElement('h3')
                h3offers.appendChild(document.createTextNode('Le nostre offerte'));
                tablecaption.appendChild(h3offers);
                tablehead = table.createTHead();
                row = tablehead.insertRow(0);
                for (const property in response['data'][0]) {
                    cell = row.insertCell(Object.keys(response['data'][0]).indexOf(property));
                    cell.appendChild(document.createTextNode(property));
                }
            }
        })
};


function generateKey(level){
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=generateKey&level=' + level, true, null, 
        function (response) {
            if (response['responseCode'] != 0) {
                console.log(response['message'])
            } else {
                key_holder = document.getElementById('key');
                console.log(response['data']);
                if (key_holder.firstChild) {                    
                    key_holder.removeChild(key_holder.firstChild);
                }
                key_holder.appendChild(document.createTextNode('La tua chiave è: ' + response['data'][0]['rand_key'] + '. Non dirla a nessuno e inseriscila nel campo License key nel pannello di controllo del tuo ristorante'));
        }
    })
};