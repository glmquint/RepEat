function loadMessages(user) {
    AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=getChats&user='+ user, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            //console.log(response);
            body = document.getElementsByTagName('body')[0];
            while (body.firstChild) {
                body.removeChild(body.lastChild);
            }
            response['data'].forEach(row => {
                this_div = document.createElement('div');
                this_div.classList.add('chat-picker');
                this_div.appendChild(document.createTextNode('(' + row['unread_msgs'] + ') ' + row['other_name'] + '::::::::' + row['last_msg']));
                //this_btn = document.createElement('button');
                this_div.addEventListener("click", function(){readMessages(user, row['other'])});
                //this_btn.appendChild(document.createTextNode('Vai alla chat'));
                //this_div.appendChild(this_btn);
                body.appendChild(this_div);

            })
        }
    })
};

function readMessages(user, dest){
    AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=readMessages&user=' + user + '&dest=' + dest, true, null,
    function(response){
        if(response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            console.log(response);
            body = document.getElementsByTagName('body')[0];
            while (body.firstChild) {
                body.removeChild(body.lastChild);
            }
            response['data'].forEach(row => {
                this_div = document.createElement('div');
                this_div.classList.add('message');
                this_content = document.createTextNode('(' + ((row['from_user'] == user)?'io':row['other_name']) + ') ' + row['ts'] + '[' + row['msg'] + ']' + ((row['is_req'] == 1)?((row['is_read'] == 1)?'Accettata':'Non accettata'):((row['is_read'] == 1)?'Letto':'Non letto')));                
                this_div.appendChild(this_content);
                if (row['is_req']) {
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
                body.appendChild(this_div);
                
            });
            this_btn = document.createElement('button');
            this_btn.addEventListener("click", function(){loadMessages(user)});
            this_btn.appendChild(document.createTextNode('Torna alle chat'));
            body.appendChild(this_btn);

        }
    })
};

function processRequest(request, accepted) {
    AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=processRequest&req='+request+'&accepted='+accepted, true, null,
    function(response){
        if(response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            alert('La richiesta è stata ' + ((accepted)?'accettata':'rifiutata') + ' con successo');
        }
    });
}