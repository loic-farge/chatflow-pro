<?php

function chatflow_pro_chat_shortcode() {
    ob_start();
    ?>
    <div id="chatflow_pro_chatbot">
        <div id="chatbot_messages">
            <!-- Initial greeting or load from options -->
            <p class="assistant"><?php echo get_option('chatflow_pro_options')['default_message']; ?></p>
        </div>
        <div id="chatbot_commands">
            <input type="text" id="chatbot_input" placeholder="Type your message...">
            <button onclick="sendChatbotMessage()">Send</button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('chatflow_pro', 'chatflow_pro_chat_shortcode');
