<?php

class ChatFlow_Pro_Activator {
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'chatflow_pro_conversations';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            conversation text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        register_activation_hook( __FILE__, 'chatflow_pro_install' );
    }
}
