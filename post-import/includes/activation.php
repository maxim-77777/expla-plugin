<?php
// activation
function cpi_activate_plugin() {
    if (!wp_next_scheduled('cpi_import_event')) {
        wp_schedule_event(time(), 'daily', 'cpi_import_event');
    }
}

// deactivation
function cpi_deactivate_plugin() {
    wp_clear_scheduled_hook('cpi_import_event');
}

add_action('cpi_import_event', 'cpi_import_posts_from_api');
