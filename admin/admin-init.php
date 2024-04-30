<?php

// Enqueue admin scripts and styles
function chatflow_pro_admin_enqueue_scripts() {
    wp_enqueue_script('chatflow-pro-admin-script', plugins_url('/assets/js/admin.js', __FILE__));
    wp_enqueue_style('chatflow-pro-admin-style', plugins_url('/assets/css/admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'chatflow_pro_admin_enqueue_scripts');

function chatflow_pro_add_admin_menu() {
    add_menu_page(
        'ChatFlow Pro',                   // Page title
        'ChatFlow Pro',                   // Menu title
        'manage_options',                 // Capability
        'chatflow_pro_main_menu',         // Menu slug
        'chatflow_pro_main_page',         // Function to display the page (optional, can be empty)
        'dashicons-testimonial',          // Icon URL
        6                                 // Position
    );

    // You can leave the function 'chatflow_pro_main_page' empty if it only serves as a container
    function chatflow_pro_main_page() {

    }
}

add_action('admin_menu', 'chatflow_pro_add_admin_menu');

function chatflow_pro_add_admin_submenu() {
    // Adding the Historic submenu, reusing the main menu slug for the first submenu item
    add_submenu_page(
        'chatflow_pro_main_menu',          // Parent slug
        'ChatFlow Pro Conversations',           // Page title
        'Conversations',                        // Menu title
        'manage_options',                  // Capability
        'chatflow_pro_main_menu',          // Menu slug, same as parent for the first item
        'chatflow_pro_conversations_page'  // Function to display the page
    );

    // Adding the Settings submenu
    add_submenu_page(
        'chatflow_pro_main_menu',          // Parent slug
        'ChatFlow Pro Settings',           // Page title
        'Settings',                        // Menu title
        'manage_options',                  // Capability
        'chatflow_pro_settings',           // Menu slug
        'chatflow_pro_create_admin_page'   // Function to display the page
    );
}

add_action('admin_menu', 'chatflow_pro_add_admin_submenu');

// beginning show conversations
function chatflow_pro_conversations_page() {
    if (isset($_GET['conversation_id'])) {
        chatflow_pro_view_conversation_details(); // function to display conversation details
    } else {
        global $wpdb;
        $table_name = $wpdb->prefix . 'chatflow_pro_conversations';
        $conversations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time DESC");

        echo '<div class="wrap"><h1>Chat Conversations</h1>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Time</th><th>Actions</th></tr></thead>';
        echo '<tbody>';

        foreach ($conversations as $conversation) {
            echo '<tr>';
            echo '<td>' . esc_html($conversation->id) . '</td>';
            echo '<td>' . esc_html($conversation->time) . '</td>';
            echo '<td><a href="' . esc_url(admin_url('admin.php?page=chatflow_pro_main_menu&conversation_id=' . $conversation->id)) . '">View</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table></div>';
    }
}

function chatflow_pro_view_conversation_details() {
    if (!isset($_GET['conversation_id']) || !is_numeric($_GET['conversation_id'])) {
        echo '<div class="wrap"><h1>Invalid Conversation ID</h1></div>';
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'chatflow_pro_conversations';
    $conversation_id = intval($_GET['conversation_id']);
    $conversation = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $conversation_id));

    if (!$conversation) {
        echo '<div class="wrap"><h1>Conversation Not Found</h1></div>';
        return;
    }

    echo '<div class="wrap">';
    echo '<h1>Conversation Details</h1>';
    echo '<p>Conversation ID: ' . esc_html($conversation->id) . '</p>';
    echo '<p>Time: ' . esc_html($conversation->time) . '</p>';
    echo '<h2>Messages</h2>';
    $messages = json_decode($conversation->conversation);
    foreach ($messages as $message) {
        echo '<p><strong>' . esc_html(ucfirst($message->role)) . ':</strong> ' . esc_html($message->content) . '</p>';
    }
    echo '</div>';
}

if (isset($_GET['page']) && $_GET['page'] === 'chatflow_pro_conversations' && isset($_GET['conversation_id'])) {
    add_action('admin_init', 'chatflow_pro_view_conversation_details');
}

// end show conversations

function chatflow_pro_create_admin_page() {
    require_once plugin_dir_path(__FILE__) . '/settings-page.php';
    chatflow_pro_settings_page_html();
}

// Section description callback
function chatflow_pro_section_developers_cb() {
    echo '<p>Enter your settings below:</p>';
}

// API Key field callback
function chatflow_pro_field_api_key_cb() {
    $options = get_option('chatflow_pro_options');
    ?>
    <input type="text" name="chatflow_pro_options[api_key]" value="<?php echo esc_attr($options['api_key'] ?? ''); ?>">
    <?php
}

// Callback for the temperature field
function chatflow_pro_field_temperature_cb() {
    $options = get_option('chatflow_pro_options');
    ?>
    <input type="number" step="0.1" min="0" max="2" name="chatflow_pro_options[temperature]" value="<?php echo esc_attr($options['temperature'] ?? '0'); ?>">
    <?php
}

// Callback for the model dropdown field
function chatflow_pro_field_model_cb() {
    $options = get_option('chatflow_pro_options');
    $models = [
        'gemma-7b-it' => 'Gemma 7B IT',
        'llama2-70b-4096' => 'Llama2 70B 4096',
        'llama3-70b-8192' => 'Llama3 70B 8192',
        'llama3-8b-8192' => 'Llama3 8B 8192',
        'mixtral-8x7b-32768' => 'Mixtral 8x7B 32768'
    ];
    ?>
    <select name="chatflow_pro_options[model]">
        <?php foreach ($models as $key => $label): ?>
            <option value="<?php echo esc_attr($key); ?>" <?php selected($options['model'] ?? 'llama3-8b-8192', $key); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

// Callback for the base information field
function chatflow_pro_field_base_info_cb() {
    $options = get_option('chatflow_pro_options');
    ?>
    <textarea name="chatflow_pro_options[base_info]" rows="10" cols="50"><?php echo esc_textarea($options['base_info'] ?? ''); ?></textarea>
    <?php
}

// Callback for the default assistant message field
function chatflow_pro_field_default_message_cb() {
    $options = get_option('chatflow_pro_options');
    ?>
    <textarea name="chatflow_pro_options[default_message]" rows="10" cols="50"><?php echo esc_textarea($options['default_message'] ?? ''); ?></textarea>
    <?php
}

// Callback for the default assistant message field
function chatflow_pro_field_system_cb() {
    $options = get_option('chatflow_pro_options');
    ?>
    <textarea name="chatflow_pro_options[system]" rows="10" cols="50"><?php echo esc_textarea($options['system'] ?? ''); ?></textarea>
    <help>Use tag {{base_info}} to input base info field into your system message</help>
    <?php
}

// Initialize settings
function chatflow_pro_settings_init() {
    register_setting('chatflow_pro_options', 'chatflow_pro_options');

    add_settings_section(
        'chatflow_pro_section_developers',
        __('Your Section Description', 'chatflow-pro'),
        'chatflow_pro_section_developers_cb', // Callback function for the section description
        'chatflow_pro'
    );

    add_settings_field(
        'chatflow_pro_field_api_key', // ID
        __('API Key', 'chatflow-pro'), // Title
        'chatflow_pro_field_api_key_cb', // Callback function for the field
        'chatflow_pro', // Page
        'chatflow_pro_section_developers' // Section
    );

    // Temperature field
    add_settings_field(
        'chatflow_pro_field_temperature',
        __('Temperature', 'chatflow-pro'),
        'chatflow_pro_field_temperature_cb',
        'chatflow_pro',
        'chatflow_pro_section_developers'
    );

    // Model dropdown field
    add_settings_field(
        'chatflow_pro_field_model',
        __('Model', 'chatflow-pro'),
        'chatflow_pro_field_model_cb',
        'chatflow_pro',
        'chatflow_pro_section_developers'
    );

    // System
    add_settings_field(
        'chatflow_pro_field_system',
        __('System', 'chatflow-pro'),
        'chatflow_pro_field_system_cb',
        'chatflow_pro',
        'chatflow_pro_section_developers'
    );

    // Base Information JSON text area
    add_settings_field(
        'chatflow_pro_field_base_info',
        __('Base Information', 'chatflow-pro'),
        'chatflow_pro_field_base_info_cb',
        'chatflow_pro',
        'chatflow_pro_section_developers'
    );

    // Default Assistant Message
    add_settings_field(
        'chatflow_pro_field_default_message',
        __('Default Assistant Message', 'chatflow-pro'),
        'chatflow_pro_field_default_message_cb',
        'chatflow_pro',
        'chatflow_pro_section_developers'
    );
}

add_action('admin_init', 'chatflow_pro_settings_init');
