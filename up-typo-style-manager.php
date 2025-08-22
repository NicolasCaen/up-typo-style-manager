<?php
/**
 * Plugin Name: UP Typo Style Manager
 * Description: Gestionnaire de styles pour blocs WordPress
 * Version: 1.2.0
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
require_once UTSM_PLUGIN_DIR . 'admin/sections-page.php';

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
    // Menu principal
    add_menu_page(
        'UP Typo Style Manager',
        'Style Manager',
        'manage_options',
        'up-typo-style-manager',
        'utsm_main_admin_page',
        'dashicons-admin-customizer',
        30
    );
    
    // Sous-page: Style de polices
    add_submenu_page(
        'up-typo-style-manager',
        'Style de polices',
        'Style de polices',
        'manage_options',
        'up-typo-style-manager-fonts',
        'utsm_admin_page'
    );
    
    // Sous-page: Style de sections
    add_submenu_page(
        'up-typo-style-manager',
        'Style de sections',
        'Style de sections',
        'manage_options',
        'up-typo-style-manager-sections',
        'utsm_sections_admin_page'
    );
    
}

// Page principale avec cards
function utsm_main_admin_page() {
    ?>
    <div class="wrap">
        <h1>UP Typo Style Manager</h1>
        <p>Gérez les styles de votre thème WordPress avec des outils avancés.</p>
        
        <div class="utsm-dashboard-cards">
            <div class="utsm-card">
                <div class="utsm-card-icon">
                    <span class="dashicons dashicons-editor-textcolor"></span>
                </div>
                <div class="utsm-card-content">
                    <h3>Style de polices</h3>
                    <p>Créez et gérez les styles typographiques pour vos blocs WordPress.</p>
                    <a href="<?php echo admin_url('admin.php?page=up-typo-style-manager-fonts'); ?>" class="button button-primary">
                        Gérer les polices
                    </a>
                </div>
            </div>
            
            <div class="utsm-card">
                <div class="utsm-card-icon">
                    <span class="dashicons dashicons-admin-appearance"></span>
                </div>
                <div class="utsm-card-content">
                    <h3>Style de sections</h3>
                    <p>Créez et gérez les styles de couleurs pour vos sections et blocs.</p>
                    <a href="<?php echo admin_url('admin.php?page=up-typo-style-manager-sections'); ?>" class="button button-primary">
                        Gérer les sections
                    </a>
                </div>
            </div>
            
            <div class="utsm-card utsm-card-disabled">
                <div class="utsm-card-icon">
                    <span class="dashicons dashicons-editor-expand"></span>
                </div>
                <div class="utsm-card-content">
                    <h3>Style d'espacement</h3>
                    <p>Configurez les marges, paddings et espacements de vos éléments.</p>
                    <span class="button button-secondary disabled">
                        Bientôt disponible
                    </span>
                </div>
            </div>
            
            <div class="utsm-card utsm-card-disabled">
                <div class="utsm-card-icon">
                    <span class="dashicons dashicons-admin-customizer"></span>
                </div>
                <div class="utsm-card-content">
                    <h3>Style de bordures</h3>
                    <p>Créez des styles de bordures et d'ombres pour vos blocs.</p>
                    <span class="button button-secondary disabled">
                        Bientôt disponible
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .utsm-dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    
    .utsm-card {
        background: #fff;
        border: 1px solid #c3c4c7;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .utsm-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    
    .utsm-card-disabled {
        opacity: 0.6;
    }
    
    .utsm-card-disabled:hover {
        transform: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .utsm-card-icon {
        text-align: center;
        margin-bottom: 15px;
    }
    
    .utsm-card-icon .dashicons {
        font-size: 48px;
        width: 48px;
        height: 48px;
        color: #2271b1;
    }
    
    .utsm-card-disabled .utsm-card-icon .dashicons {
        color: #a7aaad;
    }
    
    .utsm-card-content h3 {
        margin: 0 0 10px 0;
        font-size: 18px;
        color: #1d2327;
    }
    
    .utsm-card-content p {
        margin: 0 0 15px 0;
        color: #646970;
        line-height: 1.5;
    }
    
    .utsm-card-content .button {
        width: 100%;
        text-align: center;
        justify-content: center;
    }
    
    .button.disabled {
        cursor: not-allowed;
        pointer-events: none;
    }
    </style>
    <?php
}


// Charger les scripts admin
add_action('admin_enqueue_scripts', 'utsm_enqueue_admin_scripts');
function utsm_enqueue_admin_scripts($hook) {
    if (strpos($hook, 'up-typo-style-manager') === false) return;
    
    wp_enqueue_script('utsm-admin', UTSM_PLUGIN_URL . 'assets/admin.js', array('jquery'), '1.0.0', true);
    wp_localize_script('utsm-admin', 'utsm_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('utsm_nonce')
    ));
}