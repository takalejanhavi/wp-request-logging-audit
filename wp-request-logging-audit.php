<?php
/**
 * Plugin Name: Request Logging Audit
 * Description: Logs basic request data and provides an admin interface to review recent activity.
 * Version: 1.0
 * Author: Janhavi Takale
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'rla_log_request');

function rla_log_request() {
    if (is_admin()) {
        return;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'rla_logs';

    $wpdb->insert(
        $table,
        [
            'request_uri' => sanitize_text_field($_SERVER['REQUEST_URI']),
            'method'      => sanitize_text_field($_SERVER['REQUEST_METHOD']),
            'user_id'     => get_current_user_id(),
            'created_at'  => current_time('mysql'),
        ]
    );
}

register_activation_hook(__FILE__, 'rla_create_table');

function rla_create_table() {
    global $wpdb;

    $table = $wpdb->prefix . 'rla_logs';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        request_uri TEXT NOT NULL,
        method VARCHAR(10) NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('admin_menu', 'rla_register_admin_page');

function rla_register_admin_page() {
    add_menu_page(
        'Request Logs',
        'Request Logs',
        'manage_options',
        'rla-logs',
        'rla_render_admin_page'
    );
}

function rla_render_admin_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'rla_logs';

    $logs = $wpdb->get_results(
        "SELECT * FROM $table ORDER BY created_at DESC LIMIT 20"
    );

    echo '<div class="wrap"><h1>Recent Requests</h1><table class="widefat">';
    echo '<thead><tr><th>URI</th><th>Method</th><th>User</th><th>Time</th></tr></thead><tbody>';

    foreach ($logs as $log) {
        echo '<tr>';
        echo '<td>' . esc_html($log->request_uri) . '</td>';
        echo '<td>' . esc_html($log->method) . '</td>';
        echo '<td>' . esc_html($log->user_id) . '</td>';
        echo '<td>' . esc_html($log->created_at) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
}
