<head>
<script src="../../js/ajax/ajaxManager.js"></script>
<script src="../../js/ajax/preferences.js"></script>
</head>
<?php
    session_start();
    if (isset($_SESSION)) {
        print_r($_SESSION);
    }
    

?>
<p>Here are your preferences!</p>
<span id="preferences-body" onload ="loadPreferences(<?php echo $_SESSION['user_id'] ?>)">

</span>