<nav id="main-nav"><div id="notif-container"><div id="notif-unread"></div></div>
    <input type="radio" name="nav-btn" id="messages"> </input><label for="messages" onclick="loadMessages(<?php echo $_SESSION['user_id'] . ', ' . (isset($_SESSION['ristorante'])?$_SESSION['ristorante']:'-1') ?>)" class="material-icons box glow-fuchsia">mail</label>
    <input type="radio" name="nav-btn" id="home"> </input><label for="home" onclick="loadMainHome(<?php echo $_SESSION['user_id']?>)" class="material-icons box">home</label>
    <input type="radio" name="nav-btn" id="preferences"> </input><label for="preferences" onclick="loadPreferences(<?php echo $_SESSION['user_id'] ?>)" class="material-icons box glow-orange">settings</label>
</nav>