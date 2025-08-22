<?php
/**
 * Plugin Name: UP Typo Style Manager
 * Description: Gestionnaire de styles pour blocs WordPress
 * Version: 1.0.0
 * Author: gehin nicolas
 */

if (!defined('ABSPATH')) {
    exit;
}

define('UTSM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UTSM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Inclure les fichiers nécessaires
require_once UTSM_PLUGIN_DIR . 'includes/file-manager.php';
require_once UTSM_PLUGIN_DIR . 'includes/style-generator.php';
require_once UTSM_PLUGIN_DIR . 'admin/admin-page.php';

// Activation du plugin
register_activation_hook(__FILE__, 'utsm_activate');
function utsm_activate() {
    $styles_dir = get_stylesheet_directory() . '/styles/blocks';
    
    if (!file_exists($styles_dir)) {
        wp_mkdir_p($styles_dir);
    }
}

// Ajouter le menu d'administration
add_action('admin_menu', 'utsm_add_admin_menu');
function utsm_add_admin_menu() {
    add_menu_page(
        'UP Typo Style Manager',
        'Style Manager',
        'manage_options',
        'up-typo-style-manager',
        'utsm_admin_page',
        'dashicons-admin-customizer',
        30
    );
}

// Charger les scripts admin
add_action('admin_enqueue_scripts', 'utsm_enqueue_admin_scripts');
function utsm_enqueue_admin_scripts($hook) {
    if ($hook != 'toplevel_page_up-typo-style-manager') return;
    
    wp_enqueue_script('utsm-admin', UTSM_PLUGIN_URL . 'assets/admin.js', array('jquery'), '1.0.0', true);
    wp_localize_script('utsm-admin', 'utsm_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('utsm_nonce')
    ));
}