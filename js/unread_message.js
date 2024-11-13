// Define la función que actualizará los mensajes no leídos
function actualizarMensajesSinLeer() {
    // Realiza una solicitud AJAX para obtener los mensajes no leídos
    $.ajax({
        url: '../actions/get_unread_messages.php', // URL del archivo PHP que retorna los mensajes no leídos
        type: 'GET', // Tipo de solicitud
        dataType: 'json', // Especifica que se espera una respuesta en formato JSON
        success: function(data) { // Función a ejecutar si la solicitud es exitosa
            // Itera sobre cada ID de amigo en el objeto de datos recibido
            for (let amigoId in data) {
                // Selecciona el elemento <span> que muestra la cantidad de mensajes no leídos para el amigo correspondiente
                let spanElement = $('#unread-' + amigoId);
                // Si el número de mensajes no leídos es mayor a 0
                if (data[amigoId] > 0) {
                    // Actualiza el texto del elemento <span> con el número de mensajes no leídos y lo muestra
                    spanElement.text(data[amigoId]).show();
                } else {
                    // Si no hay mensajes no leídos, limpia el texto del elemento <span> y lo oculta
                    spanElement.text('').hide();
                }
            }
        }
    });
}

// Establece un intervalo para ejecutar la función cada 2000 milisegundos (2 segundos)
setInterval(actualizarMensajesSinLeer, 2000);
