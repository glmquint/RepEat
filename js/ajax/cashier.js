function loadCashierDashboard(parentDiv, user, ristorante){
    intervalArr.map((a) => {
        clearInterval(a);
        arr = [];
    })

    notifUnreadMessages(user);
                                                        
        AjaxManager.performAjaxRequest('GET', './ajax/dbInterface.php?function=getOrdersWaiting&user='+ user, true, null, 
        function(response){
            if (response['responseCode'] != 0) {
                alert('qualcosa è andato storto: ' + response['message']);
            } else {

            }
        });

}