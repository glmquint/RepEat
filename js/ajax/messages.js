function loadMessages(user, ristorante) {
    intervalArr.map((a) => {
        clearInterval(a);
        intervalArr= [];
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
                sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
            } else {
                console.log(response);



                while (dchatpickercontainer.firstChild) {
                    dchatpickercontainer.removeChild(dchatpickercontainer.lastChild);
                }
                response['data'].forEach(row => {
                    this_div = document.createElement('div');
                    this_div.classList.add('chat-picker');

                    unreadbadbe = document.createElement('div');
                    unreadbadbe.classList.add('notif-unread');
                    unreadbadbe.appendChild(document.createTextNode(row['unread_msgs']));

                    othername = document.createElement('div');
                    othername.appendChild(document.createTextNode(row['other_name']));

                    lastmsg = document.createElement('div'),
                    lastmsg.appendChild(document.createTextNode(row['last_msg']));


                    this_div.appendChild(unreadbadbe);
                    this_div.appendChild(othername);
                    this_div.appendChild(lastmsg);
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
                sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
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
    
    lback = document.createElement('label');
    lback.appendChild(document.createTextNode('arrow_back'));
    lback.classList.add("material-icons");
    this_btn.appendChild(lback);

    body.appendChild(this_btn);
    
    dmsgcontainer = document.createElement('div');
    dmsgcontainer.id = 'msgcontainer';

    intervalArr.map((a) => {
        clearInterval(a);
        intervalArr= [];
    })
    notifUnreadMessages(user);

    firsttime = true;
    intervalArr.push(setInterval(function intervalReadMessages() {
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=readMessages&user=' + user + '&dest=' + dest, true, null,
        function(response){
            if(response['responseCode'] != 0) {
                sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
            } else {
            console.log(response);
            
            while (dmsgcontainer.firstChild) {
                dmsgcontainer.removeChild(dmsgcontainer.lastChild);
            }
            prevdate = '';
            response['data'].forEach(row => {
                this_div = document.createElement('div');
                this_div.classList.add('message');
                this_content = document.createElement('p');
                this_metadata = document.createElement('p');
                this_metadata.classList.add('metadata');
                this_metadata.appendChild(document.createTextNode(row['ts'].split(' ')[1]))

                iread = document.createElement('i');
                iread.classList.add('material-icons');
                if (row['from_user'] == user){ //row['other_name']
                this_div.classList.add('io');
                }
                if (row['is_req'] == 1) {
                    if (row['is_read'] == 1){
                        this_div.classList.add('success-box');
                    } else {
                        this_div.classList.add('error-box');
                    }
                } else {
                    if (row['is_read'] == 1){
                        iread.appendChild(document.createTextNode('done_all'))
                    } else {
                        iread.appendChild(document.createTextNode('done'))
                    }
                }
                this_metadata.appendChild(iread);
                this_content.appendChild(document.createTextNode(row['msg']));                
                
                this_div.appendChild(this_content);
                this_div.appendChild(this_metadata);
                if (row['ts'].split(' ')[0] != prevdate) {
                    this_date = document.createElement('div');
                    this_date.classList.add('message');
                    this_date.classList.add('date');
                    this_date.appendChild(document.createTextNode(row['ts'].split(' ')[0]));
                    dmsgcontainer.appendChild(this_date);
                    prevdate = row['ts'].split(' ')[0]
                }
                dmsgcontainer.appendChild(this_div);
                if (row['is_req'] == 1) {
                    this_request = document.createElement('div')
                    this_div.classList.add('request-msg');
                    if(row['to_user'] == user && row['is_read'] == 0){
                        accept_btn = document.createElement('button');
                        accept_btn.addEventListener("click", function(){processRequest(row['id_msg'], 1)});
                        accept_btn.classList.add('button-confirm')
                        this_request.appendChild(accept_btn);
                        refuse_btn = document.createElement('button');
                        refuse_btn.addEventListener("click", function(){processRequest(row['id_msg'], 0)});
                        refuse_btn.classList.add('button-cancel')
                        this_request.appendChild(refuse_btn);
                        
                    }
                    dmsgcontainer.appendChild(this_request);
                    
                }
            });
            if (firsttime) {
                dmsgcontainer.scrollTop = dmsgcontainer.scrollHeight; 
                firsttime = false;  
            }
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
    isend = document.createElement('i');
    isend.classList.add('material-icons');
    isend.appendChild(document.createTextNode('send'));
    msg_send.appendChild(isend)
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
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            sendAlert('La richiesta è stata ' + ((accepted)?'accettata':'rifiutata') + ' con successo', 'success');
        }
    });
};

function writeMessage(from_user, to_user, msg, ristorante) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=writeMessage&from_user='+from_user+'&to_user='+to_user+'&msg='+msg, true, null,
    function(response){
        if(response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
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
                sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
            } else {

                num_unread = 0;
                response['data'].forEach(row => {
                    num_unread += Number(row['unread_msgs']) ;
                });

                document.getElementById('notif-unread').innerText = (num_unread == 0)?'':num_unread;
                
            }
        });
        return intervalNotifmessages;
    }(), 10000)); //... grazie a https://stackoverflow.com/a/6685505 per l'idea di chiamare una funzione che ritorna se stessa (così da evitare il primo delay della setInterval)

}