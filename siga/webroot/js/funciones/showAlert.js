function showAlert(message, tipo){
    document.getElementById('message-'+tipo).innerHTML=message;
    $("#alert-"+tipo).slideDown();
    setTimeout(function () {
        $("#alert-"+tipo).slideUp();
    }, 4000);
}