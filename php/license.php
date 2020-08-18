<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/ajax/ajaxManager.js"></script>
    <title>repEat</title>
</head>
<script>
function load(){
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=listLevels', true, null, 
        function(response){
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
                    this_button = document.createElement('button');
                    this_button.innerHTML = 'Generate';
                    this_button.onclick = function(){generateKey(row['id_livello'])};
                    this_key_holder = document.createElement('p');
                    this_key_holder.id = row['id_livello'];
                    this_div.appendChild(this_button);
                    this_div.appendChild(this_key_holder);
                    body.appendChild(this_div);
                });
            }
        })
};


function generateKey(level){
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=generateKey&level=' + level, true, null, 
        function (response) {
            if (response['responseCode'] != 0) {
                console.log(response['message'])
            } else {
                key_holder = document.getElementById(level);
                console.log(response['data']);
                key_holder.innerHTML = 'Your key is: ' + response['data'][0]['rand_key'];
        }
    })
};
    </script>
<body onLoad="load()">
    <p>Back to <a href="../index.php">index</a></p>
    
</body>
</html>