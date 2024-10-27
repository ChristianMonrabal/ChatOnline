function actualizarMensajesSinLeer() {
    $.ajax({
        url: '../actions/get_unread_messages.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            for (let amigoId in data) {
                let spanElement = $('#unread-' + amigoId);
                if (data[amigoId] > 0) {
                    spanElement.text(data[amigoId]).show();
                } else {
                    spanElement.text('').hide();
                }
            }
        }
    });
}

setInterval(actualizarMensajesSinLeer, 2000);