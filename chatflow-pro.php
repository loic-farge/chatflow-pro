<?php
/*
Plugin Name: ChatFlow Pro
Plugin URI: http://example.com/chatflow-pro
Description: An advanced AI-powered chatbot to engage your visitors.
Version: 1.0.0
Author: Loic Farge
Author URI: http://example.com
License: GPL2
*/

// Define plugin root directory path constant
if ( !defined('CHATFLOW_PRO_DIR') ) {
    define('CHATFLOW_PRO_DIR', plugin_dir_path(__FILE__));
}

// Define plugin URL constant
if ( !defined('CHATFLOW_PRO_URL') ) {
    define('CHATFLOW_PRO_URL', plugin_dir_url(__FILE__));
}

// Activation hook
function chatflow_pro_activate() {
    // Code to run on activation, e.g., creating database tables
    require_once plugin_dir_path( __FILE__ ) . 'includes/activator.php';
    ChatFlow_Pro_Activator::activate();
}
register_activation_hook(__FILE__, 'chatflow_pro_activate');

// Deactivation hook
function chatflow_pro_deactivate() {
    // Code to clean up on deactivation, e.g., removing options
    require_once plugin_dir_path( __FILE__ ) . 'includes/deactivator.php';
    ChatFlow_Pro_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'chatflow_pro_deactivate');

// Include the admin initialization file
require_once plugin_dir_path( __FILE__ ) . 'public/public-init.php';

// Include the admin initialization file
require_once plugin_dir_path( __FILE__ ) . 'admin/admin-init.php';
