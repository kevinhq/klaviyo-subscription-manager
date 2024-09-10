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

    // Handle GET actions (edit and delete)
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $action = $_GET['action'];
        $id = intval($_GET['id']);

        if ($action === 'edit') {
            // Fetch the list details and populate the form
            $list = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
            // You'll need to modify your form to use these values
        } elseif ($action === 'delete') {
            // Delete the list
            $wpdb->delete($table_name, array('id' => $id), array('%d'));
            echo "<div class='updated'><p>List deleted successfully.</p></div>";
        }
    }

    // Handle form submission (CRUD operations)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process form data and handle CRUD
    }

    // Fetch all lists
    $lists = $wpdb->get_results("SELECT * FROM $table_name");

    // Output admin page HTML
    ?>
    <div class="wrap">
        <h1>Klaviyo Lists</h1>
        <form method="POST" action="">
            <table>
                <tr>
                    <td>API Key:</td><td><input type="text" name="api_key" required></td>
                </tr>
                <tr>
                    <td>List ID:</td><td><input type="text" name="list_id" required></td>
                </tr>
                <tr>
                    <td>List Name:</td><td><input type="text" name="list_name" required></td>
                </tr>
                <tr>
                    <td>URL:</td><td><input type="text" name="url" required></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Save List"></td>
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
