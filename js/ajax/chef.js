/**Schermata relativa al ruolo di cuoco
 * 
 * Il cuoco ha soltanto una lista con tutti gli ordini effettuati dal ruolo cameriere, ordinati per evidenziare i piatti con attesa maggiore.
 * Una volta preparato, il piatto può essere rimosso dalla lista che passerà allo stato in attesa di essere consegnato, il chè verrà automaticamente notificato al ruolo cameriere
 */


function loadChefDashboard(parentDiv, user, ristorante){
    //Come sempre, vengono chiusi tutti i timer tranne che per la notifica di nuovi messaggi
    intervalArr.map((a) => {
        clearInterval(a);
        intervalArr= [];
    })

    notifUnreadMessages(user);

    h3orders = document.createElement('h3');
    h3orders.appendChild(document.createTextNode('Ordini'))
    reqorderslist = document.createElement('div');
    reqorderslist.id = 'reqorderslist';

    parentDiv.appendChild(h3orders);
    parentDiv.appendChild(reqorderslist);

    intervalArr.push(setInterval(function intervalNotifmessages() {
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getOrdersWaiting&user='+ user, true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
            } else {
                while (reqorderslist.lastChild) {
                    reqorderslist.removeChild(reqorderslist.firstChild);
                }
            
                if (response['data'].length == 0){
                    pnoorders = document.createElement('p');
                    pnoorders.appendChild(document.createTextNode('Nessun ordine...'));
                    reqorderslist.appendChild(pnoorders);
                } else {
                    //dinamicamente si costruisce la lista con il nome dei piatti ordinati, la quantità, il tavolo che ha effettuato l'ordine e il tempo trascorso dall'ordine
                    response['data'].forEach((ordine, index_ordine) => {
                        this_order = document.createElement('div');
                        this_order.classList.add('ordine');

                        porder = document.createElement('p');
                        porder.appendChild(document.createTextNode(ordine['quantita'] + 'x ' + ordine['nome'] + 
                                                                    ((ordine['note'] != '')?'[' + ordine['note'] + ']':'') + 
                                                                    ' al tavolo ' + alphabet[ordine['stanza']] + ordine['tavolo'] + 
                                                                    ' ordinato ' + ((ordine['attesa'].split(':')[0] == '00')?'':ordine['attesa'].split(':')[0] + ' or'+((ordine['attesa'].split(':')[0] == '01')?'a':'e')+', ') + //difficile superare l'ora nella preparazione di un piatto..
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

/*---------------------------------------------------------------*/

function setPrepared(user, ordine) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=setPrepared&user='+ user + '&ordine='+ ordine, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('ordine completato con successo', 'success');
        }
    });

}