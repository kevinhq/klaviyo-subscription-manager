<?php
/**
 * Plugin Name: Klaviyo Subscription Plugin
 * Description: A WordPress plugin for managing Klaviyo subscription lists and integrating forms using shortcodes.
 * Version: 1.0
 * Author: k@kevinhq.com
 */

defined('ABSPATH') or die('No script kiddies please!');

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/klaviyo-database.php';
require_once plugin_dir_path(__FILE__) . 'admin/klaviyo-admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/klaviyo-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/klaviyo-api-handler.php';

// Plugin activation hook - Create tables
register_activation_hook(__FILE__, 'klaviyo_plugin_create_tables');

