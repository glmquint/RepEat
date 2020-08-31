function loadMessages(user, ristorante) {
    intervalArr.map((a) => {
        clearInterval(a);
        arr = [];
    })

    notifUnreadMessages(user);

    body = document.getElementById('main-container');
    while (body.firstChild) {
        body.removeChild(body.lastChild);
    }
    dchatpickercontainer = document.createElement('div');
    dchatpickercontainer.id = 'chatpickercontainer';

    intervalArr.push(setInterval(function intervalgetChats() {
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getChats&user='+ user, true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                alert('qualcosa è andato storto: ' + response['message']);
            } else {
                console.log(response);



                while (dchatpickercontainer.firstChild) {
                    dchatpickercontainer.removeChild(dchatpickercontainer.lastChild);
                }
                response['data'].forEach(row => {
                    this_div = document.createElement('div');
                    this_div.classList.add('chat-picker');
                    this_div.appendChild(document.createTextNode('(' + row['unread_msgs'] + ') ' + row['other_name'] + '::::::::' + row['last_msg']));
                    //this_btn = document.createElement('button');
                    this_div.addEventListener("click", function(){readMessages(user, row['other'], ristorante)});
                    //this_btn.appendChild(document.createTextNode('Vai alla chat'));
                    //this_div.appendChild(this_btn);
                    dchatpickercontainer.appendChild(this_div);

                });
                
            }
        });
        return intervalgetChats;
    }(), 1000)); //... grazie a https://stackoverflow.com/a/6685505 per l'idea di chiamare una funzione che ritorna se stessa (così da evitare il primo delay della setInterval)

    body.appendChild(dchatpickercontainer);

    if (ristorante != '-1') {
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listUsers&ristorante='+ristorante, true, null,
        function (response) {
            if (response['responseCode'] != 0) {
                alert('qualcosa è andato storto: ' + response['message']);
            } else {
                body.appendChild(document.createTextNode('Inizia una chat con un tuo collega:'));
                select_user = document.createElement('select');
                select_user.name = 'user-select';
                select_user.id = 'user-select';

                
                response['data'].forEach(row => {
                    this_option = document.createElement('option');
                    this_option.value = row['id_utente'];
                    this_option.appendChild(document.createTextNode(row['username']));
                    select_user.appendChild(this_option);
                    
                });
                go_to_chat = document.createElement('button');
                go_to_chat.appendChild(document.createTextNode('Vai alla chat'));
                go_to_chat.addEventListener("click", function () {readMessages(user, document.getElementById('user-select').value, ristorante);})
                body.appendChild(select_user);
                body.appendChild(go_to_chat);
            }
        });
    }

};

function readMessages(user, dest, ristorante){
    body = document.getElementById('main-container');
    while (body.firstChild) {
        body.removeChild(body.lastChild);
    }
    this_btn = document.createElement('button');
    this_btn.addEventListener("click", function(){loadMessages(user, ristorante)});
    this_btn.appendChild(document.createTextNode('Torna alle chat'));
    body.appendChild(this_btn);
    
    dmsgcontainer = document.createElement('div');
    dmsgcontainer.id = 'msgcontainer';

    intervalArr.map((a) => {
        clearInterval(a);
        arr = [];
    })
    notifUnreadMessages(user);

    intervalArr.push(setInterval(function intervalReadMessages() {
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=readMessages&user=' + user + '&dest=' + dest, true, null,
        function(response){
            if(response['responseCode'] != 0) {
                alert('qualcosa è andato storto: ' + response['message']);
            } else {
            console.log(response);
            
            while (dmsgcontainer.firstChild) {
                dmsgcontainer.removeChild(dmsgcontainer.lastChild);
            }
            response['data'].forEach(row => {
                this_div = document.createElement('div');
                this_div.classList.add('message');
                this_content = document.createTextNode('(' + ((row['from_user'] == user)?'io':row['other_name']) + ') ' + row['ts'] + '[' + row['msg'] + ']' + ((row['is_req'] == 1)?((row['is_read'] == 1)?'Processata':'Non processata'):((row['is_read'] == 1)?'Letto':'Non letto')));                
                this_div.appendChild(this_content);
                if (row['is_req'] == 1) {
                    this_div.classList.add('request-msg');
                    if(row['to_user'] == user && row['is_read'] == 0){
                        accept_btn = document.createElement('button');
                        accept_btn.addEventListener("click", function(){processRequest(row['id_msg'], 1)});
                        accept_btn.appendChild(document.createTextNode('accetta'));
                        this_div.appendChild(accept_btn);
                        refuse_btn = document.createElement('button');
                        refuse_btn.addEventListener("click", function(){processRequest(row['id_msg'], 0)});
                        refuse_btn.appendChild(document.createTextNode('rifiuta'));
                        this_div.appendChild(refuse_btn);
                        
                    }
                    
                } 
                dmsgcontainer.appendChild(this_div);
                
            });
        }
    });
    return intervalReadMessages;
    }(), 1000)); //... grazie a https://stackoverflow.com/a/6685505 per l'idea di chiamare una funzione che ritorna se stessa (così da evitare il primo delay della setInterval)


    msg_bar = document.createElement('div');
    msg_bar.classList.add('message-bar');
    msg_box = document.createElement('textarea');
    msg_box.id = 'msg-box';
    msg_box.addEventListener("keydown", function(e){if(e.keyCode == 13 && e.ctrlKey){writeMessage(user, dest, document.getElementById('msg-box').value, ristorante)}})
    msg_box.placeholder = '(anche Ctrl+Enter per inviare)'
    msg_send = document.createElement('button');
    msg_send.appendChild(document.createTextNode('Invia'))
    msg_send.addEventListener("click", function () { writeMessage(user, dest, document.getElementById('msg-box').value, ristorante)});
    msg_bar.appendChild(msg_box);
    msg_bar.appendChild(msg_send);
    body.appendChild(dmsgcontainer);
    body.appendChild(msg_bar);

};

function processRequest(request, accepted) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=processRequest&req='+request+'&accepted='+accepted, true, null,
    function(response){
        if(response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            alert('La richiesta è stata ' + ((accepted)?'accettata':'rifiutata') + ' con successo');
        }
    });
};

function writeMessage(from_user, to_user, msg, ristorante) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=writeMessage&from_user='+from_user+'&to_user='+to_user+'&msg='+msg, true, null,
    function(response){
        if(response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            readMessages(from_user, to_user, ristorante);
        }
    });
}

function notifUnreadMessages(user) {
    intervalArr.push(setInterval(function intervalNotifmessages() {
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getChats&user='+ user, true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                alert('qualcosa è andato storto: ' + response['message']);
            } else {

                num_unread = 0;
                response['data'].forEach(row => {
                    num_unread += row['unread_msgs'] ;
                });

                document.getElementById('notif-unread').innerText = (num_unread == 0)?'':num_unread;
                
            }
        });
        return intervalNotifmessages;
    }(), 10000)); //... grazie a https://stackoverflow.com/a/6685505 per l'idea di chiamare una funzione che ritorna se stessa (così da evitare il primo delay della setInterval)

}