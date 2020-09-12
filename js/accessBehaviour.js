/**Comportamento specifico per il tooltip che appare alla scelta di una password sicura, 
 * durante la registrazione di un account */

function checkPwd(pwd){
    if (/[a-zA-Z\d]{8,}/.test(pwd)) {   //8 caratteri
        document.getElementById('pwdlength').style.color='green';
    } else {
        document.getElementById('pwdlength').style.color='red';
    }
    if (/(?=.*[A-Z])/.test(pwd)) {      //almeno una maiuscola
        document.getElementById('pwdupper').style.color='green';
    } else {
        document.getElementById('pwdupper').style.color='red';
    }
    if (/(?=.*[a-z])/.test(pwd)) {      //almeno una minuscola
        document.getElementById('pwdlower').style.color='green';
    } else {
        document.getElementById('pwdlower').style.color='red';
    }
    if (/(?=.*\d)/.test(pwd)) {         //almeno un numero
        document.getElementById('pwdnumber').style.color='green';
    } else {
        document.getElementById('pwdnumber').style.color='red';
    }
    if (/(?=.*[@$!%*#?&])/.test(pwd)) { //opzionale: almeno un simbolo
        if (document.getElementById('pwdschar') == null) {
            pwdschar = document.createElement('li');
            pwdschar.id = "pwdschar";
            pwdschar.appendChild(document.createTextNode('simboli (nice)'));
            pwdschar.style.color = 'green';
            ulist = document.getElementById('pwdnumber').parentElement.appendChild(pwdschar);
        }
    } else {
        if (document.getElementById('pwdschar') != null) {
            document.getElementById('pwdschar').remove();            
        }
    }
}