function loadChefDashboard(parentDiv, user, ristorante){
    intervalArr.map((a) => {
        clearInterval(a);
        arr = [];
    })

    notifUnreadMessages(user);

    h3orders = document.createElement('h3');
    h3orders.appendChild(document.createTextNode('Ordini'))
    reqorderslist = document.createElement('div');
    reqorderslist.id = 'reqorderslist';

    parentDiv.appendChild(h3orders);
    parentDiv.appendChild(reqorderslist);

    intervalArr.push(setInterval(function intervalNotifmessages() {     // id_ordine, quantita, nome_piatto, note, tavolo, stanza, attesa
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getOrdersWaiting&user='+ user, true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                alert('qualcosa è andato storto: ' + response['message']);
            } else {
                while (reqorderslist.lastChild) {
                    reqorderslist.removeChild(reqorderslist.firstChild);
                }
            
                if (response['data'].length == 0){
                    pnoorders = document.createElement('p');
                    pnoorders.appendChild(document.createTextNode('Nessun ordine...'));
                    reqorderslist.appendChild(pnoorders);
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
                        borderready.addEventListener('click', function () {setPrepared(user, this.value)});
                        borderready.appendChild(document.createTextNode('completa'));

                        this_order.appendChild(porder);
                        this_order.appendChild(borderready);

                        reqorderslist.appendChild(this_order);
                    });
                }

            }
        });
        return intervalNotifmessages;
    }(), 1000)); //... grazie a https://stackoverflow.com/a/6685505 per l'idea di chiamare una funzione che ritorna se stessa (così da evitare il primo delay della setInterval)

}

function setPrepared(user, ordine) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=setPrepared&user='+ user + '&ordine='+ ordine, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            alert('ordine completato con successo');
        }
    });

}