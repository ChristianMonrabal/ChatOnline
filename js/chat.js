// Espera a que el documento esté completamente cargado
$(document).ready(function() {
    // Inicializa una variable para almacenar el intervalo de actualización
    let currentInterval;
    // Inicializa una variable para rastrear si el usuario está al final del chat
    let isUserAtBottom = true;

    // Función para verificar si el usuario está al final de la caja de chat
    function checkIfUserIsAtBottom() {
        // Obtiene el elemento de la caja de chat
        var chatBox = $('#chat-box')[0];
        // Actualiza la variable isUserAtBottom verificando la posición del scroll
        isUserAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 1;
    }

    // Función para cargar mensajes del amigo seleccionado
    function loadMessages(amigo_id) {
        // Realiza una solicitud AJAX para cargar los mensajes
        $.ajax({
            url: '../actions/load_messages.php', // URL del archivo PHP que carga los mensajes
            type: 'GET', // Tipo de solicitud
            data: { amigo_id: amigo_id }, // Datos enviados en la solicitud
            success: function(data) {
                // Actualiza el contenido de la caja de chat con los mensajes recibidos
                $('#chat-box').html(data);

                // Si el usuario está al final de la caja de chat, desplaza hacia abajo
                if (isUserAtBottom) {
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                }
            }
        });
    }

    // Evento al hacer clic en un amigo para iniciar el chat
    $('.friend').on('click', function() {
        var amigo_id = $(this).data('id'); // Obtiene el ID del amigo de los datos del elemento
        var nombreAmigo = $(this).find('div:first').text().trim(); // Obtiene el nombre del amigo
        // Actualiza el encabezado del chat con el nombre del amigo
        $('#chat-header').text('Chat con ' + nombreAmigo);
        $('#chat-form').show(); // Muestra el formulario del chat

        // Si hay un intervalo activo, lo limpia
        if (currentInterval) {
            clearInterval(currentInterval);
        }

        // Carga los mensajes del amigo seleccionado
        loadMessages(amigo_id);

        // Configura un intervalo para cargar mensajes cada segundo
        currentInterval = setInterval(function() {
            loadMessages(amigo_id);
        }, 1000);

        // Maneja el evento de envío del formulario de chat
        $('#chat-form').off('submit').on('submit', function(e) {
            e.preventDefault(); // Previene el comportamiento por defecto del formulario
            // Realiza una solicitud AJAX para enviar un mensaje
            $.ajax({
                type: 'POST', // Tipo de solicitud
                url: '../actions/chat_action.php', // URL del archivo PHP que procesa el envío de mensajes
                data: $(this).serialize() + '&receptor_id=' + amigo_id, // Datos del formulario serializados más el ID del receptor
                success: function(response) {
                    // Añade la respuesta del servidor (mensaje enviado) a la caja de chat
                    $('#chat-box').append(response);
                    // Limpia el campo de mensaje
                    $('textarea[name="mensaje"]').val('');
                    // Desplaza la caja de chat hacia abajo
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                }
            });
        });
    });

    // Evento de desplazamiento en la caja de chat
    $('#chat-box').on('scroll', function() {
        checkIfUserIsAtBottom(); // Verifica si el usuario está al final del chat
    });

    // Maneja el evento de envío del formulario de búsqueda
    $('#search-form').on('submit', function(e) {
        e.preventDefault(); // Previene el comportamiento por defecto del formulario
        // Realiza una solicitud AJAX para buscar amigos
        $.ajax({
            type: 'POST', // Tipo de solicitud
            url: '../actions/search_action.php', // URL del archivo PHP que maneja la búsqueda
            data: $(this).serialize(), // Datos del formulario serializados
            success: function(data) {
                // Actualiza la sección de resultados de búsqueda con los datos recibidos
                $('#search-results').html(data);
            }
        });
    });
});
