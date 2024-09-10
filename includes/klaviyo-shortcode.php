<?php

// Register the shortcode
add_shortcode('klaviyo_subscription_form', 'klaviyo_subscription_form_shortcode');

function klaviyo_subscription_form_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'klaviyo_details';

    // Fetch available lists
    $lists = $wpdb->get_results("SELECT * FROM $table_name");

    ob_start();
    ?>
    <form method="POST" action="">
        <table>
            <?php foreach ($lists as $list) : ?>
            <tr>
                <td><input type="checkbox" name="klaviyo_lists[]" value="<?php echo esc_attr($list->id); ?>"></td>
                <td><?php echo esc_html($list->list_name); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2">
                    <input type="email" name="email" required placeholder="Enter your email">
                    <input type="submit" value="Subscribe">
                </td>
            </tr>
        </table>
    </form>
    <?php
    return ob_get_clean();
}

// Handle form submission
add_action('init', 'klaviyo_handle_subscription');

function klaviyo_handle_subscription() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
        global $wpdb;
        $email = sanitize_email($_POST['email']);
        $selected_lists = isset($_POST['klaviyo_lists']) ? $_POST['klaviyo_lists'] : [];

        foreach ($selected_lists as $list_id) {
            // Save to wp_klaviyo_subscriptions
            $wpdb->insert(
                $wpdb->prefix . 'klaviyo_subscriptions',
                array(
                    'email' => $email,
                    'list_id' => intval($list_id),
                    'created_at' => current_time('mysql'),
                )
            );

            // Get Klaviyo API details
            $klaviyo_detail = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}klaviyo_details WHERE id = %d", $list_id));

            if ($klaviyo_detail && $klaviyo_detail->url) {
                $response = klaviyo_subscribe_to_list($email, $list_id, $klaviyo_detail->api_key, $klaviyo_detail->url);
                
                // Handle the API response if necessary
                foreach ($response as $message) {
                    if (strpos($message, 'failed') !== false) {
                        // Log or display error messages as needed
                        error_log($message);
                    }
                }
            }
        }

        // Redirect on success
        wp_redirect(add_query_arg('subscription', 'success'));
        exit;
    }
}