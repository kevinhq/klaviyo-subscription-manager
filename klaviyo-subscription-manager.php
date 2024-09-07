<?php
/*
Plugin Name: Klaviyo Subscription Manager
Description: A plugin to manage subscriptions and interact with Klaviyo.
Version: 1.0
Author: Your Name
*/

// Hook into admin menu and widgets
add_action('admin_menu', 'klaviyo_subscription_manager_menu');
add_action('widgets_init', 'klaviyo_subscription_manager_register_widget');

function klaviyo_subscription_manager_menu() {
    add_menu_page('Klaviyo Subscription Manager', 'Klaviyo Subscription Manager', 'manage_options', 'klaviyo-subscription-manager', 'klaviyo_subscription_manager_page');
}

function klaviyo_subscription_manager_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'klaviyo_details';

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $wpdb->insert($table_name, array(
                'api_key' => sanitize_text_field($_POST['api_key']),
                'list_id' => sanitize_text_field($_POST['list_id']),
                'url' => sanitize_text_field($_POST['url']),
                'list_name' => sanitize_text_field($_POST['list_name']),
            ));
        }

        if ($_POST['action'] === 'update') {
            $wpdb->update($table_name, array(
                'api_key' => sanitize_text_field($_POST['api_key']),
                'list_id' => sanitize_text_field($_POST['list_id']),
                'url' => sanitize_text_field($_POST['url']),
                'list_name' => sanitize_text_field($_POST['list_name']),
            ), array('id' => intval($_POST['id'])));
        }

        if ($_POST['action'] === 'delete') {
            $wpdb->delete($table_name, array('id' => intval($_POST['id'])));
        }
    }

    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<div class="wrap">';
    echo '<h1>Klaviyo Subscription Manager</h1>';
    echo '<h2>Add New</h2>';
    echo '<form method="post">';
    echo '<input type="hidden" name="action" value="add">';
    echo '<p><label>API Key: <input type="text" name="api_key" /></label></p>';
    echo '<p><label>List ID: <input type="text" name="list_id" /></label></p>';
    echo '<p><label>URL: <input type="text" name="url" /></label></p>';
    echo '<p><label>List Name: <input type="text" name="list_name" /></label></p>';
    echo '<p><input type="submit" value="Add" /></p>';
    echo '</form>';

    echo '<h2>Existing Entries</h2>';
    echo '<table class="widefat">';
    echo '<thead><tr><th>ID</th><th>API Key</th><th>List ID</th><th>URL</th><th>List Name</th><th>Actions</th></tr></thead>';
    echo '<tbody>';

    foreach ($results as $row) {
        echo '<tr>';
        echo '<td>' . esc_html($row->id) . '</td>';
        echo '<td>' . esc_html($row->api_key) . '</td>';
        echo '<td>' . esc_html($row->list_id) . '</td>';
        echo '<td>' . esc_html($row->url) . '</td>';
        echo '<td>' . esc_html($row->list_name) . '</td>';
        echo '<td>';
        echo '<form method="post" style="display:inline;">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="' . esc_attr($row->id) . '">
                <input type="submit" value="Update">
              </form>';
        echo '<form method="post" style="display:inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="' . esc_attr($row->id) . '">
                <input type="submit" value="Delete">
              </form>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}

function klaviyo_subscription_manager_register_widget() {
    require_once(plugin_dir_path(__FILE__) . 'includes/klaviyo-subscription-widget.php');
    register_widget('Klaviyo_Subscription_Widget');
}

register_activation_hook(__FILE__, 'klaviyo_subscription_manager_install');

function klaviyo_subscription_manager_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'klaviyo_details';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        api_key varchar(255) NOT NULL,
        list_id varchar(255) NOT NULL,
        url varchar(255) NOT NULL,
        list_name varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
