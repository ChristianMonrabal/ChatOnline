$(document).ready(function() {
    let currentInterval;
    let isUserAtBottom = true;

    function checkIfUserIsAtBottom() {
        var chatBox = $('#chat-box')[0];
        isUserAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 1;
    }

    function loadMessages(amigo_id) {
        $.ajax({
            url: '../actions/load_messages.php',
            type: 'GET',
            data: { amigo_id: amigo_id },
            success: function(data) {
                $('#chat-box').html(data);

                if (isUserAtBottom) {
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                }
            }
        });
    }

    function formatMessage(mensaje) {
        const words = mensaje.split(' ');
        let formattedMessage = '';
        let line = '';

        words.forEach(word => {
            if (line.length + word.length + 1 <= 40) {  // +1 para el espacio
                line += (line.length ? ' ' : '') + word;
            } else {
                formattedMessage += line + '\n';  // Añade la línea y un salto de línea
                line = word;  // Empieza una nueva línea con la palabra actual
            }
        });

        if (line.length > 0) {
            formattedMessage += line;  // Añade la última línea si existe
        }

        return formattedMessage;
    }

    $('.friend').on('click', function() {
        var amigo_id = $(this).data('id');
        $('#chat-header').text('Chat con ' + $(this).text());
        $('#chat-form').show();

        if (currentInterval) {
            clearInterval(currentInterval);
        }

        loadMessages(amigo_id);

        currentInterval = setInterval(function() {
            loadMessages(amigo_id);
        }, 1000);

        $('#chat-form').off('submit').on('submit', function(e) {
            e.preventDefault();
            
            // Obtener el mensaje y formatearlo
            let mensaje = $('textarea[name="mensaje"]').val();
            let mensajeFormateado = formatMessage(mensaje);  // Llama a la función de formateo

            $.ajax({
                type: 'POST',
                url: '../actions/chat_action.php',
                data: {
                    mensaje: mensajeFormateado,
                    receptor_id: amigo_id
                },
                success: function(response) {
                    $('#chat-box').append(response);
                    $('textarea[name="mensaje"]').val('');
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                }
            });
        });
    });

    $('#chat-box').on('scroll', function() {
        checkIfUserIsAtBottom();
    });

    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '../actions/search_action.php',
            data: $(this).serialize(),
            success: function(data) {
                $('#search-results').html(data);
            }
        });
    });
});
