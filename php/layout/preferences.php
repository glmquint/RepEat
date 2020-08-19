<p>Here are your preferences!</p>
<?php
    session_start();
    if (isset($_SESSION)) {
        print_r($_SESSION);
    }
    

?>