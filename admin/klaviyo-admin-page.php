<?php

// Add admin menu
add_action('admin_menu', 'klaviyo_admin_menu');

function klaviyo_admin_menu() {
    add_menu_page(
        'Klaviyo Settings',
        'Klaviyo Lists',
        'manage_options',
        'klaviyo-lists',
        'klaviyo_admin_page',
        'dashicons-email',
        20
    );
}

// Render the admin page
function klaviyo_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'klaviyo_details';

    $edit_mode = false;
    $list_to_edit = null;

    // Handle GET actions (edit and delete)
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $action = $_GET['action'];
        $id = intval($_GET['id']);

        if ($action === 'edit') {
            $edit_mode = true;
            $list_to_edit = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        } elseif ($action === 'delete') {
            // Delete the list
            $wpdb->delete($table_name, array('id' => $id), array('%d'));
            echo "<div class='updated'><p>List deleted successfully.</p></div>";
        }
    }

    // Handle form submission (CRUD operations)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $api_key = sanitize_text_field($_POST['api_key']);
        $list_id = sanitize_text_field($_POST['list_id']);
        $list_name = sanitize_text_field($_POST['list_name']);
        $url = esc_url_raw($_POST['url']);

        if (isset($_POST['id'])) {
            // Update existing list
            $wpdb->update(
                $table_name,
                array('api_key' => $api_key, 'list_id' => $list_id, 'list_name' => $list_name, 'url' => $url),
                array('id' => $_POST['id']),
                array('%s', '%s', '%s', '%s'),
                array('%d')
            );
            echo "<div class='updated'><p>List updated successfully.</p></div>";
        } else {
            // Insert new list
            $wpdb->insert(
                $table_name,
                array('api_key' => $api_key, 'list_id' => $list_id, 'list_name' => $list_name, 'url' => $url),
                array('%s', '%s', '%s', '%s')
            );
            echo "<div class='updated'><p>New list added successfully.</p></div>";
        }
    }

    // Fetch all lists
    $lists = $wpdb->get_results("SELECT * FROM $table_name");

    // Output admin page HTML
    ?>
    <div class="wrap">
        <h1>Klaviyo Lists</h1>
        <form method="POST" action="">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?php echo $list_to_edit->id; ?>">
            <?php endif; ?>
            <table>
                <tr>
                    <td>API Key:</td>
                    <td><input type="text" name="api_key" value="<?php echo $edit_mode ? esc_attr($list_to_edit->api_key) : ''; ?>" required></td>
                </tr>
                <tr>
                    <td>List ID:</td>
                    <td><input type="text" name="list_id" value="<?php echo $edit_mode ? esc_attr($list_to_edit->list_id) : ''; ?>" required></td>
                </tr>
                <tr>
                    <td>List Name:</td>
                    <td><input type="text" name="list_name" value="<?php echo $edit_mode ? esc_attr($list_to_edit->list_name) : ''; ?>" required></td>
                </tr>
                <tr>
                    <td>URL:</td>
                    <td><input type="text" name="url" value="<?php echo $edit_mode ? esc_attr($list_to_edit->url) : ''; ?>" required></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="<?php echo $edit_mode ? 'Update List' : 'Save List'; ?>"></td>
                </tr>
            </table>
        </form>

        <h2>Existing Lists</h2>
        <table>
            <tr><th>List Name</th><th>Actions</th></tr>
            <?php foreach ($lists as $list): ?>
            <tr>
                <td><?php echo esc_html($list->list_name); ?></td>
                <td>
                    <a href="?page=klaviyo-lists&action=edit&id=<?php echo $list->id; ?>">Edit</a> | 
                    <a href="?page=klaviyo-lists&action=delete&id=<?php echo $list->id; ?>" onclick="return confirm('Are you sure you want to delete this list?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php
}
