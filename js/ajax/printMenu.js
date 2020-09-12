/**Stampa del menu
 * 
 * Una volta premuto stampa menu dalla lista dei menu nel ruolo di amministratore, viene impaginato il contenuto del menu selezionato ed avviata una stampa.
 * Questa funzionalità non è stata testata con altri formati diversi dall'A4.
 * Lo stile utilizzato è a malapena adattato a partire da quello utilizzato fin'ora ma non è impossibile l'implementazione di stili personalizzati (magari anche user-defined) per questa sezione
 */

function loadPrintMenu(menu, ristorante) {
    AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getMenu&menu='+menu+'&ristorante='+ristorante, true, null,
    function(response){
        if(response['responseCode'] != 0) {
            sendAlert('qualcosa è andato storto: ' + response['message'], 'error');
        } else {
            print_menu = document.getElementById('print-menu');
            prev_category = 'undefined';

            response['data'].forEach(piatto => {
                //divisorio per piatti nella stessa categoria
                if (piatto['categoria'] != prev_category) {
                    h3category = document.createElement('h3');
                    h3category.appendChild(document.createTextNode(piatto['categoria']));
                    dl = document.createElement('dl');
                    print_menu.appendChild(h3category);
                    print_menu.appendChild(dl);
                    prev_category = piatto['categoria'];
                }
                //il menu viene semplicemente implementato a partire da una definition-list con stile personalizzato
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