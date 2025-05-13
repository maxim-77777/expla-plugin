<?php
/*
Plugin Name: Custom Post Import
Description: Import posts 
Version: 1.0
Author: PM 
*/

require_once plugin_dir_path(__FILE__) . 'includes/import.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';

require_once plugin_dir_path(__FILE__) . 'includes/activation.php';

register_activation_hook(__FILE__, 'cpi_activate_plugin');
register_deactivation_hook(__FILE__, 'cpi_deactivate_plugin');
