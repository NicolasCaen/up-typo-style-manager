<?php

/**
 * Récupère les données du thème (font-sizes et font-families)
 */
function utsm_file_get_theme_data() {
    $theme_json_path = get_stylesheet_directory() . '/theme.json';
    
    if (!file_exists($theme_json_path)) {
        return ['fontSizes' => [], 'fontFamilies' => []];
    }
    
    $theme_data = json_decode(file_get_contents($theme_json_path), true);
    
    return [
        'fontSizes' => $theme_data['settings']['typography']['fontSizes'] ?? [],
        'fontFamilies' => $theme_data['settings']['typography']['fontFamilies'] ?? []
    ];
}

function utsm_file_get_styles_directory() {
    return get_stylesheet_directory() . '/styles/blocks';
}

function utsm_file_get_styles_url() {
    return get_stylesheet_directory_uri() . '/styles/blocks';
}

function utsm_file_get_all_styles() {
    $dir = utsm_file_get_styles_directory();
    $styles = [];
    
    if (!is_dir($dir)) return $styles;
    
    $files = glob($dir . '/core-*.json');
    
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        if ($data && isset($data['slug'])) {
            $styles[$data['slug']] = $data;
        }
    }
    
    return $styles;
}

function utsm_file_get_style($slug) {
    $file = utsm_file_get_styles_directory() . '/core-' . $slug . '.json';
    
    if (!file_exists($file)) return null;
    
    $content = file_get_contents($file);
    return json_decode($content, true);
}

function utsm_file_add_or_update_style($data) {
    if (!wp_verify_nonce($data['wsm_nonce'], 'wsm_style_nonce')) {
        return false;
    }
    
    $slug = sanitize_title($data['slug']);
    $old_slug = $data['old_slug'];
    
    // Supprimer l'ancien fichier si modification
    if ($old_slug && $old_slug !== $slug) {
        utsm_file_delete_style($old_slug);
    }
    
    $style_data = [
        '$schema' => 'https://schemas.wp.org/trunk/theme.json',
        'version' => 3,
        'title' => sanitize_text_field($data['name']),
        'slug' => $slug,
        'blockTypes' => array_map('sanitize_text_field', $data['block_types'] ?? []),
        'styles' => [
            'typography' => [
                'fontSize' => sanitize_text_field($data['font_size']) ?: 'inherit',
                'fontFamily' => sanitize_text_field($data['font_family']) ?: 'inherit',
                'letterSpacing' => sanitize_text_field($data['letter_spacing']) ?: 'inherit',
                'lineHeight' => sanitize_text_field($data['line_height']) ?: 'inherit',
                'fontWeight' => sanitize_text_field($data['font_weight']) ?: 'inherit',
                'fontStyle' => sanitize_text_field($data['font_style']) ?: 'inherit',
                'textTransform' => sanitize_text_field($data['text_transform']) ?: 'inherit',
            ]
        ]
    ];
    
    // Nettoyer les valeurs vides mais garder 'inherit'
    $style_data['styles']['typography'] = array_filter($style_data['styles']['typography'], function($value) {
        return $value !== null && $value !== '';
    });
    
    $file_path = utsm_file_get_styles_directory() . '/core-' . $slug . '.json';
    
    // Créer le dossier s'il n'existe pas
    $dir = utsm_file_get_styles_directory();
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    
    return file_put_contents($file_path, json_encode($style_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function utsm_file_delete_style($slug) {
    $file_path = utsm_file_get_styles_directory() . '/core-' . $slug . '.json';
    
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    
    return false;
}

/**
 * Applique un style aux éléments globaux du thème
 */
function utsm_file_apply_to_theme_elements($style_slug, $element_type) {
    $style_data = utsm_file_get_style($style_slug);
    if (!$style_data) return false;
    
    $theme_json_path = get_stylesheet_directory() . '/theme.json';
    if (!file_exists($theme_json_path)) return false;
    
    $theme_data = json_decode(file_get_contents($theme_json_path), true);
    if (!$theme_data) return false;
    
    // Mapper les types d'éléments selon la documentation WordPress
    // Seuls certains éléments sont supportés dans theme.json elements
    $element_map = [
        'heading-h1' => 'h1',
        'heading-h2' => 'h2', 
        'heading-h3' => 'h3',
        'heading-h4' => 'h4',
        'heading-h5' => 'h5',
        'heading-h6' => 'h6',
        'body' => 'body',
        'quote' => 'cite',  // cite est supporté, pas blockquote
        'cite' => 'cite',
        'button' => 'button',
        'link' => 'link',   // WordPress utilise 'link' pas 'a'
        'caption' => 'caption'
        // Note: p, ul, li, blockquote ne sont PAS supportés dans theme.json elements
    ];
    
    $theme_element = $element_map[$element_type] ?? $element_type;
    
    // Traitement spécial pour body (styles globaux)
    if ($theme_element === 'body') {
        // Créer la structure si elle n'existe pas
        if (!isset($theme_data['styles'])) {
            $theme_data['styles'] = [];
        }
        if (!isset($theme_data['styles']['typography'])) {
            $theme_data['styles']['typography'] = [];
        }
        
        // Appliquer les styles de typographie au niveau global
        if (isset($style_data['styles']['typography'])) {
            $typography = $style_data['styles']['typography'];
            foreach ($typography as $key => $value) {
                if ($value && $value !== 'inherit') {
                    $theme_data['styles']['typography'][$key] = $value;
                }
            }
        }
    } else {
        // Créer la structure si elle n'existe pas pour les éléments spécifiques
        if (!isset($theme_data['styles'])) {
            $theme_data['styles'] = [];
        }
        if (!isset($theme_data['styles']['elements'])) {
            $theme_data['styles']['elements'] = [];
        }
        if (!isset($theme_data['styles']['elements'][$theme_element])) {
            $theme_data['styles']['elements'][$theme_element] = [];
        }
        
        // Appliquer les styles de typographie
        if (isset($style_data['styles']['typography'])) {
            if (!isset($theme_data['styles']['elements'][$theme_element]['typography'])) {
                $theme_data['styles']['elements'][$theme_element]['typography'] = [];
            }
            
            $typography = $style_data['styles']['typography'];
            foreach ($typography as $key => $value) {
                if ($value && $value !== 'inherit') {
                    $theme_data['styles']['elements'][$theme_element]['typography'][$key] = $value;
                }
            }
        }
    }
    
    // Sauvegarder le theme.json avec gestion d'erreur
    $result = file_put_contents($theme_json_path, json_encode($theme_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    
    // Debug: log pour vérifier l'écriture
    if ($result === false) {
        error_log('UTSM: Erreur lors de l\'écriture du fichier theme.json: ' . $theme_json_path);
        error_log('UTSM: Permissions du fichier: ' . (is_writable($theme_json_path) ? 'OK' : 'NON ACCESSIBLE'));
        return false;
    } else {
        error_log('UTSM: Fichier theme.json mis à jour avec succès. Octets écrits: ' . $result);
        error_log('UTSM: Element appliqué: ' . $element_type . ' -> ' . $theme_element);
        return $result;
    }
}