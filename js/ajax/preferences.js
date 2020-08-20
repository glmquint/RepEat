function loadPreferences(user){
    AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=getUser&user='+ user, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            console.log(response);
            body = document.getElementById('preferences-body');
            row = response['data'][0];
            this_username = document.createElement('input');
            this_username.type = 'text';
            this_username.value = row['username'];
            body.appendChild(this_username);
        }
    });
}