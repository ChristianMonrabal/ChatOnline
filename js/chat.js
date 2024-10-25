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
            $.ajax({
                type: 'POST',
                url: '../actions/chat_action.php',
                data: $(this).serialize() + '&receptor_id=' + amigo_id,
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