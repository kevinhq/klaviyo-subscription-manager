<?php
class Klaviyo_Subscription_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'klaviyo_subscription_widget', // Base ID
            'Klaviyo Subscription Widget', // Name
            array('description' => __('A widget to display subscription form for Klaviyo.', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'klaviyo_details';
        
        // Get the latest entry or specific logic
        $entries = $wpdb->get_results("SELECT * FROM $table_name");

        echo $args['before_widget'];
        if (!empty($entries)) {
            echo '<div class="klaviyo-subscription-widget">';
            echo '<h2>Subscribe to Our Lists</h2>';
            foreach ($entries as $entry) {
                echo '<div class="klaviyo-subscription-entry">';
                echo '<h3>' . esc_html($entry->list_name) . '</h3>';
                echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
                echo '<input type="hidden" name="action" value="klaviyo_subscription_form">';
                echo '<input type="hidden" name="list_id" value="' . esc_attr($entry->list_id) . '">';
                echo '<p><label>Email: <input type="email" name="email" required /></label></p>';
                echo '<p><input type="submit" value="Subscribe" /></p>';
                echo '</form>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>No subscription lists available.</p>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        // Admin form for widget configuration (optional)
    }

    public function update($new_instance, $old_instance) {
        // Save widget options (optional)
    }
}

// Handle form submissions
add_action('admin_post_klaviyo_subscription_form', 'klaviyo_subscription_form_handler');

function klaviyo_subscription_form_handler() {
    if (isset($_POST['email']) && isset($_POST['list_id'])) {
        $email = sanitize_email($_POST['email']);
        $list_id = sanitize_text_field($_POST['list_id']);
        
        // Perform subscription logic (e.g., call Klaviyo API)

        wp_redirect(home_url()); // Redirect after form submission
        exit;
    }
}
