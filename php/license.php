<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/ajax/ajaxManager.js"></script>
    <title>repEat</title>
</head>
<script>function load(){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listLevels', true, null, 
        function(response)
            {
            if (response['responseCode'] != 0) {
                console.log(response['message'])
            } else {
                console.table(response['data']);
                body = document.getElementsByTagName('body')[0];
                response['data'].forEach(row => {
                    console.log(row)
                    this_div = document.createElement('div');
                    for (property in row){
                        this_div.innerHTML += property + '=' + row[property] + ', ';
                    }
                    body.appendChild(this_div)
                });
            }
            })
}</script>
<body onLoad="load()">
    <p>Back to <a href="../index.php">index</a></p>
    
</body>
</html>