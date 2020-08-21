function loadPreferences(user){
    AjaxManager.performAjaxRequest('GET', '../ajax/dbInterface.php?function=getUser&user='+ user, true, null, 
    function(response){
        if (response['responseCode'] != 0) {
            alert('qualcosa è andato storto: ' + response['message']);
        } else {
            console.log(response);
            body = document.getElementById('preferences-body');
            row = response['data'][0];

            this_user = document.createElement('input');
            this_user.type = 'number';
            this_user.value = row['id_utente'];
            this_user.id = 'user';
            this_user.hidden = true;
            this_user.readOnly = true;

            this_username = document.createElement('input');
            this_username.type = 'text';
            this_username.value = row['username'];
            this_username.id = "Username";
            this_username_label = document.createElement('label');
            this_username_label.htmlFor = "Username";
            this_username_label.appendChild(document.createTextNode('Username'))

            this_mail = document.createElement('input');
            this_mail.type = 'text';
            this_mail.value = row['mail'];
            this_mail.id = "Mail";
            this_mail_label = document.createElement('label');
            this_mail_label.htmlFor = "Mail";
            this_mail_label.appendChild(document.createTextNode('Mail'))

            this_pwd = document.createElement('input');
            this_pwd.type = 'password';
            this_pwd.placeholder = 'Password';
            this_pwd.id = "Password";
            this_pwd_label = document.createElement('label');
            this_pwd_label.htmlFor = "Password";
            this_pwd_label.appendChild(document.createTextNode('Password'))

            this_conf_pwd = document.createElement('input');
            this_conf_pwd.type = 'password';
            this_conf_pwd.placeholder = 'Conferma password';
            this_conf_pwd.id = "Conferma-Password";
            this_conf_pwd_label = document.createElement('label');
            this_conf_pwd_label.htmlFor = "Conferma-Password";
            this_conf_pwd_label.appendChild(document.createTextNode('Conferma Password'))

            this_theme = document.createElement('select');
            this_theme.id = "pref-theme";
            option_light = document.createElement('option');
            option_light.value = 'light';
            option_light.appendChild(document.createTextNode('Light'))
            option_dark = document.createElement('option');
            option_dark.value = 'dark';
            option_dark.appendChild(document.createTextNode('Dark'))
            if (row['pref_theme'] == 'light') {
                this_theme.appendChild(option_light);
                this_theme.appendChild(option_dark);
            }else {
                this_theme.appendChild(option_dark);
                this_theme.appendChild(option_light);
            }
            this_theme_label = document.createElement('label');
            this_theme_label.htmlFor = "pref-theme";
            this_theme_label.appendChild(document.createTextNode('Tema preferito'));

            update_btn = document.createElement('button');
            update_btn.addEventListener("click", function(){updateUser()});
            update_btn_label = document.createElement('label');
            update_btn_label.htmlFor = "update_btn";
            update_btn_label.appendChild(document.createTextNode('Invia'))
            update_btn.appendChild(update_btn_label);

            body.appendChild(this_user);
            body.appendChild(this_username);
            body.appendChild(this_username_label);
            body.appendChild(this_mail);
            body.appendChild(this_mail_label);
            body.appendChild(this_pwd);
            body.appendChild(this_pwd_label);
            body.appendChild(this_conf_pwd);
            body.appendChild(this_conf_pwd_label);
            body.appendChild(this_theme);
            body.appendChild(this_theme_label);
            body.appendChild(update_btn);
        }
    });
};

function updateUser(){
    user = document.getElementById('user').value;
    pwd = document.getElementById("Password").value;
    conf_pwd = document.getElementById("Conferma-Password").value;
    if (pwd != conf_pwd) {
        alert("La password non coincide tra i due campi Password e Conferma Password");
    } else {
        url = '../ajax/dbInterface.php?function=updateUser&user='+ user;
        if (pwd != '') {
            url += '&password=' + pwd 
        }
        username = document.getElementById('Username').value;
        mail = document.getElementById('Mail').value;
        pref_theme = document.getElementById('pref-theme').value;
        url += '&username=' + username + '&mail=' + mail + '&pref_theme=' + pref_theme;
        console.log(url);
        AjaxManager.performAjaxRequest('POST', url , true, null, 
        function (response){
            if (response['responseCode'] != 0) {
                alert('qualcosa è andato storto: ' + response['message']);
            } else {
                alert('preferenze aggiornate correttamente');
            }
        })
    }
}