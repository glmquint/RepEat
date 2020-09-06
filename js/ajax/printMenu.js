function loadPrintMenu(menu, ristorante) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getMenu&menu='+menu+'&ristorante='+ristorante, true, null,
    function(response){
        if(response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            print_menu = document.getElementById('print-menu');
            prev_category = 'undefined';

            response['data'].forEach(piatto => {
                if (piatto['categoria'] != prev_category) {
                    h3category = document.createElement('h3');
                    h3category.appendChild(document.createTextNode(piatto['categoria']));
                    dl = document.createElement('dl');
                    print_menu.appendChild(h3category);
                    print_menu.appendChild(dl);
                    prev_category = piatto['categoria'];
                }
                dpiatto = document.createElement('dt');
                dpiatto.appendChild(document.createTextNode(piatto['nome']));

                dprezzo = document.createElement('dd');
                dprezzo.appendChild(document.createTextNode(piatto['prezzo'] + ' €'));

                dl.appendChild(dpiatto);
                dl.appendChild(dprezzo);

                if (piatto['ingredienti'] != ''){
                    pingredienti = document.createElement('p');
                    pingredienti.appendChild(document.createTextNode('Ingredienti: ' + piatto['ingredienti']));
                    dl.appendChild(pingredienti);
                }
                
                if (piatto['allergeni'] != ''){
                    pallergeni = document.createElement('p');
                    pallergeni.appendChild(document.createTextNode('Allergeni: ' + piatto['allergeni']));
                    dl.appendChild(pallergeni);
                }
                
                print_menu.appendChild(dl);
            });
        }
        window.print();
    });
}