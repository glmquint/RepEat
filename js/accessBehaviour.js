function checkPwd(pwd){
    if (/[a-zA-Z\d]{8,}/.test(pwd)) {
        document.getElementById('pwdlength').style.color='green';
    } else {
        document.getElementById('pwdlength').style.color='red';
    }
    if (/(?=.*[A-Z])/.test(pwd)) {
        document.getElementById('pwdupper').style.color='green';
    } else {
        document.getElementById('pwdupper').style.color='red';
    }
    if (/(?=.*[a-z])/.test(pwd)) {
        document.getElementById('pwdlower').style.color='green';
    } else {
        document.getElementById('pwdlower').style.color='red';
    }
    if (/(?=.*\d)/.test(pwd)) {
        document.getElementById('pwdnumber').style.color='green';
    } else {
        document.getElementById('pwdnumber').style.color='red';
    }
    if (/(?=.*[@$!%*#?&])/.test(pwd)) {
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