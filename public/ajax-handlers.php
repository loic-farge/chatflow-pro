<?php

require CHATFLOW_PRO_DIR . '/vendor/autoload.php';

use LucianoTonet\GroqPHP\Groq;

function chatflow_pro_handle_ajax() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatflow_pro_conversations';

    $options = get_option('chatflow_pro_options');

    $groq = new Groq($options['api_key']);

    $conversationHistory = json_decode(stripslashes($_POST['history']), true) ?? [];
    $userInput = sanitize_text_field($_POST['message']);

    $conversationHistory[] = [
        'role' => 'user',
        'content' => $userInput
    ];

    // Retrieve the system and base info options, replace placeholders
    $systemMessage = $options['system'] ?? '';
    $baseInfo = $options['base_info'] ?? '';

    // Replace the placeholder in the system message with actual base info content
    $systemMessage = str_replace('{{base_info}}', $baseInfo, $systemMessage);

    // Prepend the system message to the conversation history
    array_unshift($conversationHistory, [
        'role' => 'system',
        'content' => $systemMessage
    ]);

    $chatCompletion = $groq->chat()->completions()->create([
        'temperature' => (float) $options['temperature'],
        'model' => $options['model'],
        'messages' => $conversationHistory
    ]);

    $apiResponse = $chatCompletion['choices'][0]['message']['content'];

    $conversationHistory[] = [
        'role' => 'assistant',
        'content' => $apiResponse
    ];

    $wpdb->insert(
        $table_name,
        [
            'time' => current_time('mysql'),
            'conversation' => json_encode($conversationHistory)
        ]
    );

    wp_send_json(['message' => $apiResponse, 'history' => $conversationHistory]);
}

add_action('wp_ajax_nopriv_chatflow_pro_chat', 'chatflow_pro_handle_ajax');
add_action('wp_ajax_chatflow_pro_chat', 'chatflow_pro_handle_ajax');
