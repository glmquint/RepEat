<head>
<script src="../../js/ajax/ajaxManager.js"></script>
<script src="../../js/ajax/messages.js"></script>
</head>
<?php
    session_start();
    if (isset($_SESSION)) {
        print_r($_SESSION);
    }
    

?>

<p>Here are your messages!</p>
<body id="messages-body" onload ="loadMessages(<?php echo $_SESSION['user_id'] ?>)">
</body>