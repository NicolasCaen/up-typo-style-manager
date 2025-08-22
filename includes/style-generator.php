<?php
/**
 * Style Generator Functions
 * Génère les styles CSS pour les blocs WordPress
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Génère le CSS pour un style donné
 */
function utsm_generate_style_css($style_data) {
    if (!$style_data || !isset($style_data['styles'])) {
        return '';
    }
    
    $css = '';
    $typography = $style_data['styles']['typography'] ?? [];
    
    if (!empty($typography)) {
        $css .= utsm_generate_typography_css($typography);
    }
    
    return $css;
}

/**
 * Génère le CSS pour la typographie
 */
function utsm_generate_typography_css($typography) {
    $css_rules = [];
    
    if (!empty($typography['fontSize'])) {
        $css_rules[] = 'font-size: ' . $typography['fontSize'];
    }
    
    if (!empty($typography['fontFamily'])) {
        $css_rules[] = 'font-family: ' . $typography['fontFamily'];
    }
    
    if (!empty($typography['letterSpacing'])) {
        $css_rules[] = 'letter-spacing: ' . $typography['letterSpacing'];
    }
    
    if (!empty($typography['lineHeight'])) {
        $css_rules[] = 'line-height: ' . $typography['lineHeight'];
    }
    
    return implode('; ', $css_rules);
}

/**
 * Génère les styles pour tous les blocs
 */
function utsm_generate_all_styles_css() {
    $styles = utsm_file_get_all_styles();
    $css_output = '';
    
    foreach ($styles as $slug => $style) {
        $css = utsm_generate_style_css($style);
        if ($css && !empty($style['blockTypes'])) {
            foreach ($style['blockTypes'] as $block_type) {
                $css_output .= ".wp-block-{$block_type}.is-style-{$slug} { {$css} }\n";
            }
        }
    }
    
    return $css_output;
}
