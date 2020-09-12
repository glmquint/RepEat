/**Generalizzazione dei messaggi informativi
 * 
 * Questi rimpiazzano fondamentalmente gli alert, con un'apparenza meno invasiva
 * e più uniforme allo stile del resto dell'applicazione
 */

function sendAlert(msg, type = 'info') {
    let msgbox = document.createElement('div')
    alertmsg = document.createElement('p');
    icon = document.createElement('i');
    icon.classList.add('material-icons');
    switch (type) {
        case 'error':
            icon.appendChild(document.createTextNode('report'))
            msgbox.classList.add('error-box')
            break;
        case 'info':
            icon.appendChild(document.createTextNode('info'))
            msgbox.classList.add('info-box')
            break;
        case 'success':
            icon.appendChild(document.createTextNode('done'))
            msgbox.classList.add('success-box')
            break;
            
        default:
            return;
    }
    alertmsg.appendChild(document.createTextNode(msg));
    
    msgbox.appendChild(icon);
    msgbox.appendChild(alertmsg);
            
    alertcontainer = document.getElementById('alert-container');
    alertcontainer.appendChild(msgbox);
    msgbox.classList.add('fade-in');
    setTimeout(() => {
        msgbox.classList.add('fade-out');
        setTimeout(function(){msgbox.remove()}, 2000);
    }, 5000);
}