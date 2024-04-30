<?php

function chatflow_pro_settings_page_html() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    settings_errors('chatflow_pro_options');

    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // Output security fields for the registered setting "chatflow_pro_options"
            settings_fields('chatflow_pro_options');
            // Output settings sections and their fields (this will do nothing until further setup)
            do_settings_sections('chatflow_pro');
            // Output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}
