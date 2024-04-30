jQuery(document).ready(function($) {
    function nl2br(str) {
        return str.replace(/\n/g, '<br>');
    }

    function sendChatbotMessage() {
        var input = document.getElementById('chatbot_input');
        var message = input.value.trim();
        if (!message) return;  // Prevent sending empty messages
        input.value = '';

        var messagesContainer = document.getElementById('chatbot_messages');

        // Retrieve history from local storage or start new if none exists
        var history = localStorage.getItem('chatflowProHistory') ? JSON.parse(localStorage.getItem('chatflowProHistory')) : [];

        // Append user message to history and immediately for better UX
        messagesContainer.innerHTML += "<p class='user'>" + nl2br(message) + "</p>";
        history.push({ role: 'user', content: message });

        jQuery.post(chatflowProAjax.ajaxurl, {
            'action': 'chatflow_pro_chat',
            'message': message,
            'history': JSON.stringify(history)  // Passing history as a stringified JSON
        }, function(response) {
            // Append response from the server
            messagesContainer.innerHTML += "<p class='assistant'>" + nl2br(response.message) + "</p>";
            history.push({ role: 'assistant', content: response.message }); // Append assistant response
            localStorage.setItem('chatflowProHistory', JSON.stringify(history)); // Save updated history
            updateScroll(); // Optional: keep the chat scrolled to the bottom
        }).fail(function(error) {
            console.error("Error sending message: ", error);
            messagesContainer.innerHTML += "<p class='assistant'>Error sending message.</p>";
        });
    }

    function updateScroll() {
        var element = document.getElementById('chatbot_messages');
        element.scrollTop = element.scrollHeight;
    }

    window.sendChatbotMessage = sendChatbotMessage; // Make it globally accessible if needed

    // Load history on page load from local storage and display
    var loadedHistory = localStorage.getItem('chatflowProHistory');
    if (loadedHistory) {
        loadedHistory = JSON.parse(loadedHistory);
        var messagesContainer = document.getElementById('chatbot_messages');
        loadedHistory.forEach(function(message) {
            if (message.role === 'user') {
                messagesContainer.innerHTML += "<p class='user'>" + nl2br(message.content) + "</p>";
            } else if (message.role === 'assistant') {
                messagesContainer.innerHTML += "<p class='assistant'>" + nl2br(message.content) + "</p>";
            }
        });
        updateScroll();  // Ensure the chat is scrolled to the latest message
    }
});
