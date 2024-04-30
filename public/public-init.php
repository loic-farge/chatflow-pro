<?php

// Enqueue public scripts and styles
function chatflow_pro_public_enqueue_scripts() {
    wp_enqueue_script('chatflow-pro-public-script', CHATFLOW_PRO_URL . 'assets/js/public.js', array('jquery'), null, true);
    wp_enqueue_style('chatflow-pro-public-style', CHATFLOW_PRO_URL . 'assets/css/public.css');
    wp_localize_script('chatflow-pro-public-script', 'chatflowProAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'chatflow_pro_public_enqueue_scripts');

require_once plugin_dir_path( __FILE__ ) . 'chat-interface.php';
require_once plugin_dir_path( __FILE__ ) . 'ajax-handlers.php';