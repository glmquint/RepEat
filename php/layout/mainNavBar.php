<!--script>
function loadMainContent(content) {
    switch (content) {
        case 'Messages':
            page = './layout/messages.php'
            break;
        case 'Home':
            page = './layout/mainHome.php'
            break;
        case 'Preferences':
            page = './layout/preferences.php'
            break;
    
        default:
            return;
            break;
    }
    l = document.getElementById('main-link');
    l.href = page;
}
</script-->
<nav><button onclick="loadMessages(<?php echo $_SESSION['user_id'] . ', ' . (isset($_SESSION['ristorante'])?$_SESSION['ristorante']:'-1') ?>)" > <div id="notif-unread"></div> Messages </button>
    <button onclick="loadMainHome(<?php echo $_SESSION['user_id']/* . ', ' . ((isset($_SESSION['ristorante']) && $_SESSION['ristorante'] != null)?$_SESSION['ristorante']:'-1') . ', ' . ((isset($_SESSION['privilegi']) && $_SESSION['privilegi'] != null)?(($_SESSION['privilegi'] == 0)?15:$_SESSION['privilegi']):'-1') */?>)"><div id="notif-home"> Home </button>
    <button onclick="loadPreferences(<?php echo $_SESSION['user_id'] ?>)"> Preferences </button>
</nav>