<?php
function utsm_sections_admin_page() {
    ?>
    <div class="wrap">
        <h1>Gestionnaire de Styles de Sections</h1>
        
        <?php
        // Gestion des actions (ajout, modification, suppression)
        if (isset($_POST['utsm_add_section_style'])) {
            $result = utsm_sections_add_or_update_style($_POST);
            if ($result) {
                echo '<div class="notice notice-success"><p>Style de section ajouté/modifié avec succès!</p></div>';
            }
        }
        
        if (isset($_GET['delete'])) {
            $result = utsm_sections_delete_style($_GET['delete']);
            if ($result) {
                echo '<div class="notice notice-success"><p>Style de section supprimé avec succès!</p></div>';
            }
        }
        
        // Gestion de l'application aux éléments du thème pour les sections
        if (isset($_POST['apply_section_to_element']) && isset($_POST['style_slug']) && isset($_POST['element_type'])) {
            $style_slug = sanitize_text_field($_POST['style_slug']);
            $element_type = sanitize_text_field($_POST['element_type']);
            
            $result = utsm_sections_apply_to_theme_elements($style_slug, $element_type);
            if ($result) {
                echo '<div class="notice notice-success"><p>Style de section appliqué à l\'élément "' . $element_type . '" avec succès!</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Erreur lors de l\'application du style de section.</p></div>';
            }
        }
        
        // Gestion de l'application aux blocs du thème pour les sections
        if (isset($_POST['apply_section_to_block']) && isset($_POST['style_slug']) && isset($_POST['block_type'])) {
            $style_slug = sanitize_text_field($_POST['style_slug']);
            $block_type = sanitize_text_field($_POST['block_type']);
            
            $result = utsm_sections_apply_to_theme_blocks($style_slug, $block_type);
            if ($result) {
                echo '<div class="notice notice-success"><p>Style de section appliqué au bloc "' . $block_type . '" avec succès!</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Erreur lors de l\'application du style de section au bloc.</p></div>';
            }
        }
        
        // Récupérer le style à modifier
        $edit_style = isset($_GET['edit']) ? $_GET['edit'] : null;
        ?>
        
        <div class="utsm-admin-container">
            <div class="utsm-form-container">
                <h2>Ajouter/Modifier un style de section</h2>
                <?php utsm_sections_display_form($edit_style); ?>
            </div>
            
            <div class="utsm-list-container">
                <h2>Styles de sections existants</h2>
                <?php utsm_sections_display_styles_list(); ?>
            </div>
        </div>
        
        <?php if (isset($_GET['edit'])): ?>
        <div class="utsm-apply-section">
            <h3>Appliquer le style aux éléments du thème</h3>
            <?php utsm_sections_display_apply_buttons($_GET['edit']); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <style>
    .utsm-admin-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }
    
    .utsm-form-container, .utsm-list-container {
        background: #fff;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    
    .utsm-form-group {
        margin-bottom: 15px;
    }
    
    .utsm-form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .utsm-form-group input, .utsm-form-group select {
        width: 100%;
    }
    
    .utsm-color-input {
        width: 100px !important;
        height: 40px;
        border: 1px solid #ccc;
        border-radius: 3px;
        cursor: pointer;
    }
    
    .utsm-style-item {
        padding: 10px;
        border: 1px solid #eee;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .utsm-style-actions a {
        margin-left: 10px;
    }
    
    .utsm-filter-group {
        margin-bottom: 15px;
    }
    
    .utsm-filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .utsm-style-item[data-block-types] {
        display: block;
    }
    
    .utsm-style-item.hidden {
        display: none !important;
    }
    
    .utsm-color-preview {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 3px;
        border: 1px solid #ccc;
        margin-left: 10px;
        vertical-align: middle;
    }
    
    @media (max-width: 768px) {
        .utsm-admin-container {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
}

function utsm_sections_display_form($style_slug = null) {
    $style_data = $style_slug ? utsm_sections_get_style($style_slug) : null;
    $theme_data = utsm_file_get_theme_data();
    
    // Afficher la palette de couleurs en haut
    utsm_sections_display_color_palette($theme_data);
    
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('utsm_section_style_nonce', 'utsm_section_nonce'); ?>
        
        <input type="hidden" name="old_slug" value="<?php echo $style_data['slug'] ?? ''; ?>">
        
        <div class="utsm-form-group">
            <label>Nom du style</label>
            <input type="text" name="name" id="section-style-name" value="<?php echo $style_data['title'] ?? ''; ?>" required>
        </div>
        
        <div class="utsm-form-group">
            <label>Slug</label>
            <input type="text" name="slug" id="section-style-slug" value="<?php echo $style_data['slug'] ?? ''; ?>" required>
        </div>
        
        <div class="utsm-form-group">
            <label>Couleur de fond</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <select name="background_color" onchange="updateColorPreview(this, 'bg-preview')" style="flex: 1;">
                    <option value="">Sélectionner...</option>
                    <?php 
                    $current_bg = $style_data['styles']['color']['background'] ?? '';
                    // Convertir le format vers le format du formulaire pour la comparaison
                    $current_bg_converted = '';
                    if (preg_match('/var\(--wp--preset--color--(.+)\)/', $current_bg, $matches)) {
                        // Format CSS: var(--wp--preset--color--accent-1)
                        $current_bg_converted = 'var:preset|color|' . $matches[1];
                    } elseif (strpos($current_bg, 'var:preset|color|') === 0) {
                        // Format déjà correct: var:preset|color|accent-1
                        $current_bg_converted = $current_bg;
                    }
                    foreach ($theme_data['colors'] as $color):
                        $option_value = 'var:preset|color|' . $color['slug'];
                    ?>
                        <option value="<?php echo $option_value; ?>" data-color="<?php echo esc_attr($color['color']); ?>" 
                            <?php selected($current_bg_converted == $option_value); ?>>
                            <?php echo $color['name']; ?> (<?php echo $color['color']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="bg-preview" class="utsm-color-preview-box" style="width: 40px; height: 40px; border: 2px solid #ccc; border-radius: 4px; background: <?php echo $current_bg ? utsm_convert_color_for_preview($current_bg, $theme_data) : 'transparent'; ?>; background-image: <?php echo !$current_bg || utsm_convert_color_for_preview($current_bg, $theme_data) === 'transparent' ? 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)' : 'none'; ?>; background-size: 8px 8px; background-position: 0 0, 0 4px, 4px -4px, -4px 0px;"></div>
            </div>
        </div>
        
        <div class="utsm-form-group">
            <label>Couleur de texte</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <select name="text_color" onchange="updateColorPreview(this, 'text-preview')" style="flex: 1;">
                    <option value="">Sélectionner...</option>
                    <?php 
                    $current_text = $style_data['styles']['color']['text'] ?? '';
                    $current_text_converted = '';
                    if (preg_match('/var\(--wp--preset--color--(.+)\)/', $current_text, $matches)) {
                        // Format CSS: var(--wp--preset--color--base-3)
                        $current_text_converted = 'var:preset|color|' . $matches[1];
                    } elseif (strpos($current_text, 'var:preset|color|') === 0) {
                        // Format déjà correct: var:preset|color|base-3
                        $current_text_converted = $current_text;
                    }
                    foreach ($theme_data['colors'] as $color):
                        $option_value = 'var:preset|color|' . $color['slug'];
                    ?>
                        <option value="<?php echo $option_value; ?>" data-color="<?php echo esc_attr($color['color']); ?>" 
                            <?php selected($current_text_converted == $option_value); ?>>
                            <?php echo $color['name']; ?> (<?php echo $color['color']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="text-preview" class="utsm-color-preview-box" style="width: 40px; height: 40px; border: 2px solid #ccc; border-radius: 4px; background: <?php echo $current_text ? utsm_convert_color_for_preview($current_text, $theme_data) : 'transparent'; ?>; background-image: <?php echo !$current_text || utsm_convert_color_for_preview($current_text, $theme_data) === 'transparent' ? 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)' : 'none'; ?>; background-size: 8px 8px; background-position: 0 0, 0 4px, 4px -4px, -4px 0px;"></div>
            </div>
        </div>
        
        <div class="utsm-form-group">
            <label>Couleur de bordure</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <select name="border_color" onchange="updateColorPreview(this, 'border-preview')" style="flex: 1;">
                    <option value="">Sélectionner...</option>
                    <?php 
                    $current_border = $style_data['styles']['border']['color'] ?? '';
                    $current_border_converted = '';
                    if (preg_match('/var\(--wp--preset--color--(.+)\)/', $current_border, $matches)) {
                        // Format CSS: var(--wp--preset--color--accent-1)
                        $current_border_converted = 'var:preset|color|' . $matches[1];
                    } elseif (strpos($current_border, 'var:preset|color|') === 0) {
                        // Format déjà correct: var:preset|color|accent-1
                        $current_border_converted = $current_border;
                    }
                    foreach ($theme_data['colors'] as $color):
                        $option_value = 'var:preset|color|' . $color['slug'];
                    ?>
                        <option value="<?php echo $option_value; ?>" data-color="<?php echo esc_attr($color['color']); ?>" 
                            <?php selected($current_border_converted == $option_value); ?>>
                            <?php echo $color['name']; ?> (<?php echo $color['color']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="border-preview" class="utsm-color-preview-box" style="width: 40px; height: 40px; border: 2px solid #ccc; border-radius: 4px; background: <?php echo $current_border ? utsm_convert_color_for_preview($current_border, $theme_data) : 'transparent'; ?>; background-image: <?php echo !$current_border || utsm_convert_color_for_preview($current_border, $theme_data) === 'transparent' ? 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)' : 'none'; ?>; background-size: 8px 8px; background-position: 0 0, 0 4px, 4px -4px, -4px 0px;"></div>
            </div>
        </div>
        
        <div class="utsm-form-group">
            <label>Block Types (Sections)</label>
            <select name="block_types[]" multiple style="height: 120px;">
                <option value="core/group" <?php echo isset($style_data['blockTypes']) && in_array('core/group', $style_data['blockTypes']) ? 'selected' : ''; ?>>Group</option>
                <option value="core/columns" <?php echo isset($style_data['blockTypes']) && in_array('core/columns', $style_data['blockTypes']) ? 'selected' : ''; ?>>Columns</option>
                <option value="core/column" <?php echo isset($style_data['blockTypes']) && in_array('core/column', $style_data['blockTypes']) ? 'selected' : ''; ?>>Column</option>
                <option value="core/cover" <?php echo isset($style_data['blockTypes']) && in_array('core/cover', $style_data['blockTypes']) ? 'selected' : ''; ?>>Cover</option>
                <option value="core/media-text" <?php echo isset($style_data['blockTypes']) && in_array('core/media-text', $style_data['blockTypes']) ? 'selected' : ''; ?>>Media Text</option>
            </select>
        </div>
        
        <!-- Styles pour les blocs internes -->
        <div class="utsm-form-group">
            <h3>Styles pour les blocs internes</h3>
            <div id="internal-blocks-container">
                <?php 
                // Afficher les blocs existants s'il y en a
                if (isset($style_data['styles']['blocks']) && is_array($style_data['styles']['blocks'])) {
                    $block_index = 0;
                    foreach ($style_data['styles']['blocks'] as $block_type => $block_styles) {
                        echo utsm_sections_render_internal_block_form($block_type, $block_styles, $block_index, $theme_data);
                        $block_index++;
                    }
                }
                ?>
            </div>
            <button type="button" id="add-internal-block" class="button button-secondary">Ajouter un bloc interne</button>
        </div>
        
        <button type="submit" name="utsm_add_section_style" class="button button-primary">
            <?php echo $style_slug ? 'Modifier' : 'Ajouter'; ?>
        </button>
    </form>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let blockIndex = <?php echo isset($style_data['styles']['blocks']) ? count($style_data['styles']['blocks']) : 0; ?>;
        
        // Ajouter un nouveau bloc interne
        document.getElementById('add-internal-block').addEventListener('click', function() {
            const container = document.getElementById('internal-blocks-container');
            const newBlockHtml = `
                <div class="internal-block-form" data-index="${blockIndex}">
                    <div class="internal-block-header">
                        <h4>Bloc interne #${blockIndex + 1}</h4>
                        <button type="button" class="button button-link-delete remove-internal-block">Supprimer</button>
                    </div>
                    
                    <div class="utsm-form-group">
                        <label>Type de bloc</label>
                        <select name="internal_blocks[${blockIndex}][type]" required>
                            <option value="">Sélectionner...</option>
                            <option value="core/button">Bouton (core/button)</option>
                            <option value="core/heading">Titre (core/heading)</option>
                            <option value="core/paragraph">Paragraphe (core/paragraph)</option>
                            <option value="core/list">Liste (core/list)</option>
                            <option value="core/quote">Citation (core/quote)</option>
                            <option value="core/image">Image (core/image)</option>
                            <option value="core/cover">Couverture (core/cover)</option>
                            <option value="core/group">Groupe (core/group)</option>
                        </select>
                    </div>
                    
                    <div class="utsm-form-group">
                        <label>Couleur de fond</label>
                        <select name="internal_blocks[${blockIndex}][background_color]">
                            <option value="">Aucune</option>
                            <?php foreach ($theme_data['colors'] as $color): ?>
                                <option value="var:preset|color|<?php echo $color['slug']; ?>">
                                    <?php echo $color['name']; ?> (<?php echo $color['color']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="utsm-form-group">
                        <label>Couleur du texte</label>
                        <select name="internal_blocks[${blockIndex}][text_color]">
                            <option value="">Aucune</option>
                            <?php foreach ($theme_data['colors'] as $color): ?>
                                <option value="var:preset|color|<?php echo $color['slug']; ?>">
                                    <?php echo $color['name']; ?> (<?php echo $color['color']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="utsm-form-group">
                        <label>Couleur de bordure</label>
                        <select name="internal_blocks[${blockIndex}][border_color]">
                            <option value="">Aucune</option>
                            <?php foreach ($theme_data['colors'] as $color): ?>
                                <option value="var:preset|color|<?php echo $color['slug']; ?>">
                                    <?php echo $color['name']; ?> (<?php echo $color['color']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newBlockHtml);
            blockIndex++;
            updateBlockNumbers();
        });
        
        // Supprimer un bloc interne
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-internal-block')) {
                e.target.closest('.internal-block-form').remove();
                updateBlockNumbers();
            }
        });
        
        function updateBlockNumbers() {
            const blocks = document.querySelectorAll('.internal-block-form');
            blocks.forEach((block, index) => {
                const header = block.querySelector('h4');
                header.textContent = `Bloc interne #${index + 1}`;
            });
        }
    });
    </script>
    
    <style>
    .internal-block-form {
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        background: #f9f9f9;
    }
    .internal-block-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }
    .internal-block-header h4 {
        margin: 0;
    }
    
    /* Styles pour l'aperçu des sections */
    .utsm-style-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        overflow: hidden;
        background: #fff;
    }
    
    .utsm-style-preview {
        padding: 0;
    }
    
    .utsm-style-header {
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .utsm-style-header strong {
        font-size: 16px;
        color: #333;
    }
    
    .utsm-style-header small {
        color: #666;
        font-size: 12px;
    }
    
    .utsm-section-preview {
        padding: 20px;
        margin: 15px;
        border-radius: 6px;
        border: 2px dashed rgba(0,0,0,0.1);
        position: relative;
    }
    
    .utsm-preview-label {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 10px;
        display: block;
        opacity: 0.8;
    }
    
    .utsm-color-info {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 10px;
    }
    
    .utsm-color-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
    }
    
    .utsm-color-preview {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.8);
        box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
        display: inline-block;
    }
    
    .utsm-internal-blocks-preview {
        padding: 15px;
        background: rgba(0,0,0,0.02);
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    
    .utsm-internal-blocks-preview .utsm-preview-label {
        margin-bottom: 15px;
        color: #555;
    }
    
    .utsm-block-preview {
        display: inline-block;
        padding: 10px 15px;
        margin: 5px;
        border-radius: 4px;
        border: 1px solid rgba(255,255,255,0.2);
        min-width: 120px;
        text-align: center;
        vertical-align: top;
    }
    
    .utsm-block-preview strong {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        text-transform: capitalize;
    }
    
    .utsm-block-colors {
        display: flex;
        justify-content: center;
        gap: 8px;
        font-size: 11px;
    }
    
    .utsm-block-colors .utsm-color-preview {
        width: 16px;
        height: 16px;
    }
    
    .utsm-style-meta {
        padding: 10px 15px;
        background: #f8f9fa;
        border-top: 1px solid #ddd;
        font-size: 12px;
        color: #666;
    }
    
    .utsm-style-actions {
        padding: 15px;
        background: #f8f9fa;
        border-top: 1px solid #ddd;
        text-align: right;
    }
    
    .utsm-style-actions .button {
        margin-left: 10px;
    }
    
    /* Styles pour les blocs intégrés dans l'aperçu */
    .utsm-internal-blocks-integrated {
        margin-top: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .utsm-preview-button {
        display: inline-block;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .utsm-preview-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .utsm-preview-heading {
        margin: 10px 0;
        font-size: 24px;
        font-weight: 600;
        line-height: 1.3;
    }
    
    .utsm-preview-paragraph {
        margin: 10px 0;
        line-height: 1.6;
        font-size: 14px;
    }
    
    .utsm-preview-list {
        margin: 10px 0;
        padding-left: 20px;
    }
    
    .utsm-preview-list li {
        margin: 5px 0;
        line-height: 1.5;
    }
    
    .utsm-preview-quote {
        margin: 15px 0;
        padding: 15px 20px;
        border-left: 4px solid currentColor;
        font-style: italic;
        opacity: 0.9;
    }
    
    .utsm-preview-quote p {
        margin: 0;
    }
    
    .utsm-preview-image {
        margin: 10px 0;
        border-radius: 4px;
        overflow: hidden;
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .utsm-image-placeholder {
        font-size: 16px;
        opacity: 0.7;
    }
    
    .utsm-preview-cover {
        margin: 10px 0;
        padding: 30px 20px;
        border-radius: 6px;
        text-align: center;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .utsm-cover-content {
        font-weight: 500;
        font-size: 16px;
    }
    
    .utsm-preview-group {
        margin: 10px 0;
        padding: 15px;
        border-radius: 4px;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .utsm-group-content {
        font-size: 14px;
        opacity: 0.9;
    }
    
    .utsm-preview-default {
        margin: 10px 0;
        padding: 10px 15px;
        border-radius: 4px;
        font-size: 14px;
        text-align: center;
        border: 1px dashed rgba(255,255,255,0.3);
    }
    </style>
    
    <?php if ($style_slug && $style_data): ?>
        <?php utsm_sections_display_apply_buttons($style_slug); ?>
    <?php endif; ?>
    <?php
}

function utsm_sections_display_styles_list() {
    $styles = utsm_sections_get_all_styles();
    
    if (empty($styles)) {
        echo '<p>Aucun style de section créé pour le moment.</p>';
        return;
    }
    
    foreach ($styles as $slug => $style) {
        $block_types_str = implode(',', $style['blockTypes'] ?? []);
        $bg_color = $style['styles']['color']['background'] ?? '';
        $text_color = $style['styles']['color']['text'] ?? '';
        $border_color = $style['styles']['border']['color'] ?? '';
        
        // Convertir les couleurs var:preset vers des couleurs CSS réelles pour l'aperçu
        $theme_data = utsm_file_get_theme_data();
        $bg_color_css = utsm_convert_color_for_preview($bg_color, $theme_data);
        $text_color_css = utsm_convert_color_for_preview($text_color, $theme_data);
        $border_color_css = utsm_convert_color_for_preview($border_color, $theme_data);
        
        // Créer le style pour l'aperçu de la section
        $preview_style = '';
        if ($bg_color_css && $bg_color_css !== 'transparent') {
            $preview_style .= "background-color: {$bg_color_css}; ";
        }
        if ($text_color_css && $text_color_css !== 'transparent') {
            $preview_style .= "color: {$text_color_css}; ";
        }
        if ($border_color_css && $border_color_css !== 'transparent') {
            $preview_style .= "border: 2px solid {$border_color_css}; ";
        }
        ?>
        <div class="utsm-style-item" data-block-types="<?php echo esc_attr($block_types_str); ?>">
            <div class="utsm-style-preview">
                <div class="utsm-style-header">
                    <strong><?php echo esc_html($style['title']); ?></strong>
                    <small>Slug: <?php echo esc_html($slug); ?></small>
                </div>
                
                <!-- Aperçu de la section principale -->
                <div class="utsm-section-preview" style="<?php echo esc_attr($preview_style); ?>">
                    <div class="utsm-preview-content">Aperçu de la section</div>
                    
                    <!-- Blocs internes intégrés dans la section -->
                    <?php if (isset($style['styles']['blocks']) && !empty($style['styles']['blocks'])): ?>
                        <div class="utsm-internal-blocks-integrated">
                            <?php foreach ($style['styles']['blocks'] as $block_type => $block_styles): 
                                $block_bg = $block_styles['color']['background'] ?? '';
                                $block_text = $block_styles['color']['text'] ?? '';
                                $block_border = $block_styles['border']['color'] ?? '';
                                $block_bg_css = utsm_convert_color_for_preview($block_bg, $theme_data);
                                $block_text_css = utsm_convert_color_for_preview($block_text, $theme_data);
                                $block_border_css = utsm_convert_color_for_preview($block_border, $theme_data);
                                
                                echo utsm_render_block_preview($block_type, $block_bg_css, $block_text_css, $block_border_css);
                            endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="utsm-section-colors">
                        <div class="utsm-color-info">
                            <?php if ($bg_color): ?>
                                <span class="utsm-color-item">
                                    Fond: <span class="utsm-color-preview" style="background-color: <?php echo esc_attr($bg_color_css); ?>"></span>
                                    <small><?php echo esc_html($bg_color); ?></small>
                                </span>
                            <?php endif; ?>
                            <?php if ($text_color): ?>
                                <span class="utsm-color-item">
                                    Text: <span class="utsm-color-preview" style="background-color: <?php echo esc_attr($text_color_css); ?>"></span>
                                    <small><?php echo esc_html($text_color); ?></small>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="utsm-style-meta">
                    <small>Blocks: <?php echo implode(', ', $style['blockTypes'] ?? []); ?></small>
                </div>
            </div>
            <div class="utsm-style-actions">
                <a href="?page=up-typo-style-manager-sections&edit=<?php echo $slug; ?>" class="button">Modifier</a>
                <a href="?page=up-typo-style-manager-sections&delete=<?php echo $slug; ?>" class="button button-link-delete" onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
            </div>
        </div>
        <?php
    }
}

function utsm_sections_display_apply_buttons($style_slug) {
    $style_data = utsm_sections_get_style($style_slug);
    if (!$style_data) return;
    
    $blocks = [
        'core/group' => 'Groupe',
        'core/columns' => 'Colonnes',
        'core/column' => 'Colonne',
        'core/cover' => 'Couverture',
        'core/paragraph' => 'Paragraphe',
        'core/heading' => 'Titre',
        'core/button' => 'Bouton',
        'core/list' => 'Liste',
        'core/quote' => 'Citation'
    ];
    ?>
    <div class="utsm-apply-buttons">
        <h4>Appliquer aux éléments globaux (styles->elements)</h4>
        <div class="button-group">
            <?php
            $elements = [
                'h1' => 'Titres H1',
                'h2' => 'Titres H2', 
                'h3' => 'Titres H3',
                'h4' => 'Titres H4',
                'h5' => 'Titres H5',
                'h6' => 'Titres H6',
                'p' => 'Paragraphes',
                'button' => 'Boutons',
                'link' => 'Liens'
            ];
            
            foreach ($elements as $element => $label):
            ?>
            <form method="post" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="style_slug" value="<?php echo $style_slug; ?>">
                <input type="hidden" name="element_type" value="<?php echo $element; ?>">
                <input type="hidden" name="action_type" value="sections">
                <button type="submit" name="apply_section_to_element" class="button button-secondary">
                    <?php echo $label; ?>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
        
        <h4>Appliquer aux blocs (styles->blocks)</h4>
        <div class="button-group">
            <?php foreach ($blocks as $block_type => $block_name): ?>
            <form method="post" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="style_slug" value="<?php echo $style_slug; ?>">
                <input type="hidden" name="block_type" value="<?php echo $block_type; ?>">
                <input type="hidden" name="action_type" value="sections">
                <button type="submit" name="apply_section_to_block" class="button button-secondary">
                    <?php echo $block_name; ?>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>
    
    <style>
    .utsm-apply-buttons {
        background: #f9f9f9;
        padding: 20px;
        margin: 20px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .utsm-apply-buttons h4 {
        margin-top: 0;
        margin-bottom: 10px;
    }
    .button-group {
        margin-bottom: 20px;
    }
    .button-group:last-child {
        margin-bottom: 0;
    }
    </style>
    <?php
}

function utsm_sections_render_internal_block_form($block_type, $block_styles, $index, $theme_data) {
    $bg_color = $block_styles['color']['background'] ?? '';
    $text_color = $block_styles['color']['text'] ?? '';
    
    // Convertir les couleurs pour la sélection
    $bg_converted = '';
    if (preg_match('/var\(--wp--preset--color--(.+)\)/', $bg_color, $matches)) {
        $bg_converted = 'var:preset|color|' . $matches[1];
    } elseif (strpos($bg_color, 'var:preset|color|') === 0) {
        $bg_converted = $bg_color;
    }
    
    $text_converted = '';
    if (preg_match('/var\(--wp--preset--color--(.+)\)/', $text_color, $matches)) {
        $text_converted = 'var:preset|color|' . $matches[1];
    } elseif (strpos($text_color, 'var:preset|color|') === 0) {
        $text_converted = $text_color;
    }
    
    ob_start();
    ?>
    <div class="internal-block-form" data-index="<?php echo $index; ?>">
        <div class="internal-block-header">
            <h4>Bloc interne #<?php echo $index + 1; ?></h4>
            <button type="button" class="button button-link-delete remove-internal-block">Supprimer</button>
        </div>
        
        <div class="utsm-form-group">
            <label>Type de bloc</label>
            <select name="internal_blocks[<?php echo $index; ?>][type]" required>
                <option value="">Sélectionner...</option>
                <option value="core/button" <?php selected($block_type == 'core/button'); ?>>Bouton (core/button)</option>
                <option value="core/heading" <?php selected($block_type == 'core/heading'); ?>>Titre (core/heading)</option>
                <option value="core/paragraph" <?php selected($block_type == 'core/paragraph'); ?>>Paragraphe (core/paragraph)</option>
                <option value="core/list" <?php selected($block_type == 'core/list'); ?>>Liste (core/list)</option>
                <option value="core/quote" <?php selected($block_type == 'core/quote'); ?>>Citation (core/quote)</option>
                <option value="core/image" <?php selected($block_type == 'core/image'); ?>>Image (core/image)</option>
                <option value="core/cover" <?php selected($block_type == 'core/cover'); ?>>Couverture (core/cover)</option>
                <option value="core/group" <?php selected($block_type == 'core/group'); ?>>Groupe (core/group)</option>
            </select>
        </div>
        
        <div class="utsm-form-group">
            <label>Couleur de fond</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <select name="internal_blocks[<?php echo $index; ?>][background_color]" onchange="updateColorPreview(this, 'internal-bg-preview-<?php echo $index; ?>')" style="flex: 1;">
                    <option value="">Aucune</option>
                    <?php foreach ($theme_data['colors'] as $color): 
                        $option_value = 'var:preset|color|' . $color['slug'];
                    ?>
                        <option value="<?php echo $option_value; ?>" data-color="<?php echo esc_attr($color['color']); ?>" <?php selected($bg_converted == $option_value); ?>>
                            <?php echo $color['name']; ?> (<?php echo $color['color']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="internal-bg-preview-<?php echo $index; ?>" class="utsm-color-preview-box" style="width: 40px; height: 40px; border: 2px solid #ccc; border-radius: 4px; background: <?php echo $bg_color ? utsm_convert_color_for_preview($bg_color, $theme_data) : 'transparent'; ?>; background-image: <?php echo !$bg_color || utsm_convert_color_for_preview($bg_color, $theme_data) === 'transparent' ? 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)' : 'none'; ?>; background-size: 8px 8px; background-position: 0 0, 0 4px, 4px -4px, -4px 0px;"></div>
            </div>
        </div>
        
        <div class="utsm-form-group">
            <label>Couleur du texte</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <select name="internal_blocks[<?php echo $index; ?>][text_color]" onchange="updateColorPreview(this, 'internal-text-preview-<?php echo $index; ?>')" style="flex: 1;">
                    <option value="">Aucune</option>
                    <?php foreach ($theme_data['colors'] as $color): 
                        $option_value = 'var:preset|color|' . $color['slug'];
                    ?>
                        <option value="<?php echo $option_value; ?>" data-color="<?php echo esc_attr($color['color']); ?>" <?php selected($text_converted == $option_value); ?>>
                            <?php echo $color['name']; ?> (<?php echo $color['color']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="internal-text-preview-<?php echo $index; ?>" class="utsm-color-preview-box" style="width: 40px; height: 40px; border: 2px solid #ccc; border-radius: 4px; background: <?php echo $text_color ? utsm_convert_color_for_preview($text_color, $theme_data) : 'transparent'; ?>; background-image: <?php echo !$text_color || utsm_convert_color_for_preview($text_color, $theme_data) === 'transparent' ? 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)' : 'none'; ?>; background-size: 8px 8px; background-position: 0 0, 0 4px, 4px -4px, -4px 0px;"></div>
            </div>
        </div>
        
        <div class="utsm-form-group">
            <label>Couleur de bordure</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <select name="internal_blocks[<?php echo $index; ?>][border_color]" onchange="updateColorPreview(this, 'internal-border-preview-<?php echo $index; ?>')" style="flex: 1;">
                    <option value="">Aucune</option>
                    <?php 
                    $border_converted = '';
                    $current_border = $block_styles['border']['color'] ?? '';
                    if (preg_match('/var\(--wp--preset--color--(.+)\)/', $current_border, $matches)) {
                        $border_converted = 'var:preset|color|' . $matches[1];
                    } elseif (strpos($current_border, 'var:preset|color|') === 0) {
                        $border_converted = $current_border;
                    }
                    foreach ($theme_data['colors'] as $color): 
                        $option_value = 'var:preset|color|' . $color['slug'];
                    ?>
                        <option value="<?php echo $option_value; ?>" data-color="<?php echo esc_attr($color['color']); ?>" <?php selected($border_converted == $option_value); ?>>
                            <?php echo $color['name']; ?> (<?php echo $color['color']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="internal-border-preview-<?php echo $index; ?>" class="utsm-color-preview-box" style="width: 40px; height: 40px; border: 2px solid #ccc; border-radius: 4px; background: <?php echo $current_border ? utsm_convert_color_for_preview($current_border, $theme_data) : 'transparent'; ?>; background-image: <?php echo !$current_border || utsm_convert_color_for_preview($current_border, $theme_data) === 'transparent' ? 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)' : 'none'; ?>; background-size: 8px 8px; background-position: 0 0, 0 4px, 4px -4px, -4px 0px;"></div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function utsm_convert_color_for_preview($color_value, $theme_data) {
    if (empty($color_value)) {
        return 'transparent';
    }
    
    // Si c'est déjà une couleur CSS (hex, rgb, etc.)
    if (strpos($color_value, '#') === 0 || strpos($color_value, 'rgb') === 0 || strpos($color_value, 'hsl') === 0) {
        return $color_value;
    }
    
    // Convertir var:preset|color|slug vers la couleur réelle
    if (strpos($color_value, 'var:preset|color|') === 0) {
        $slug = str_replace('var:preset|color|', '', $color_value);
        foreach ($theme_data['colors'] as $color) {
            if ($color['slug'] === $slug) {
                return $color['color'];
            }
        }
    }
    
    // Convertir var(--wp--preset--color--slug) vers la couleur réelle
    if (preg_match('/var\(--wp--preset--color--(.+)\)/', $color_value, $matches)) {
        $slug = $matches[1];
        foreach ($theme_data['colors'] as $color) {
            if ($color['slug'] === $slug) {
                return $color['color'];
            }
        }
    }
    
    // Fallback
    return '#cccccc';
}

function utsm_render_block_preview($block_type, $bg_color, $text_color, $border_color = '') {
    $style = '';
    if ($bg_color && $bg_color !== 'transparent') {
        $style .= "background-color: {$bg_color}; ";
    }
    if ($text_color && $text_color !== 'transparent') {
        $style .= "color: {$text_color}; ";
    }
    if ($border_color && $border_color !== 'transparent') {
        $style .= "border: 2px solid {$border_color}; ";
    }
    
    switch ($block_type) {
        case 'core/button':
            return '<button class="utsm-preview-button" style="' . $style . '">Bouton</button>';
            
        case 'core/heading':
            return '<h2 class="utsm-preview-heading" style="' . $style . '">Titre principal</h2>';
            
        case 'core/paragraph':
            return '<p class="utsm-preview-paragraph" style="' . $style . '">Ceci est un exemple de paragraphe avec du texte pour montrer l\'aperçu des couleurs.</p>';
            
        case 'core/list':
            return '<ul class="utsm-preview-list" style="' . $style . '">
                        <li>Premier élément</li>
                        <li>Deuxième élément</li>
                        <li>Troisième élément</li>
                    </ul>';
                    
        case 'core/quote':
            return '<blockquote class="utsm-preview-quote" style="' . $style . '">
                        <p>« Ceci est un exemple de citation pour montrer l\'aperçu des styles. »</p>
                    </blockquote>';
                    
        case 'core/image':
            return '<div class="utsm-preview-image" style="' . $style . '">
                        <div class="utsm-image-placeholder">🖼️ Image</div>
                    </div>';
                    
        case 'core/cover':
            return '<div class="utsm-preview-cover" style="' . $style . '">
                        <div class="utsm-cover-content">Contenu de couverture</div>
                    </div>';
                    
        case 'core/group':
            return '<div class="utsm-preview-group" style="' . $style . '">Groupe de contenu</div>';
                    
        default:
            $block_name = str_replace('core/', '', $block_type);
            return '<div class="utsm-preview-default" style="' . $style . '">' . ucfirst($block_name) . '</div>';
    }
}

function utsm_sections_display_color_palette($theme_data) {
    if (empty($theme_data['colors'])) {
        return;
    }
    
    ?>
    <div class="utsm-color-palette" style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0; font-size: 16px;">🎨 Palette de couleurs du thème</h3>
            <button type="button" id="toggle-color-palette" style="background: none; border: 1px solid #ccc; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 12px;">Masquer</button>
        </div>
        <div class="utsm-color-swatches" id="color-palette-content" style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php foreach ($theme_data['colors'] as $color): ?>
                <div class="utsm-color-swatch" style="display: flex; align-items: center; background: white; padding: 8px 12px; border-radius: 4px; border: 1px solid #ddd; min-width: 150px;">
                    <div class="utsm-color-preview" style="width: 24px; height: 24px; border-radius: 3px; border: 1px solid #ccc; margin-right: 10px; background-color: <?php echo esc_attr($color['color']); ?>"></div>
                    <div class="utsm-color-info">
                        <div style="font-weight: 500; font-size: 13px;"><?php echo esc_html($color['name']); ?></div>
                        <div style="font-size: 11px; color: #666;"><?php echo esc_html($color['slug']); ?></div>
                        <div style="font-size: 11px; color: #999;"><?php echo esc_html($color['color']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggle-color-palette');
        const paletteContent = document.getElementById('color-palette-content');
        
        if (toggleBtn && paletteContent) {
            toggleBtn.addEventListener('click', function() {
                if (paletteContent.style.display === 'none') {
                    paletteContent.style.display = 'flex';
                    toggleBtn.textContent = 'Masquer';
                } else {
                    paletteContent.style.display = 'none';
                    toggleBtn.textContent = 'Afficher';
                }
            });
        }
    });
    
    function updateColorPreview(selectElement, previewId) {
        const previewBox = document.getElementById(previewId);
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (selectedOption && selectedOption.dataset.color && previewBox) {
            const color = selectedOption.dataset.color;
            
            if (color === 'transparent' || color === '' || !color) {
                // Afficher le damier pour transparent
                previewBox.style.background = 'transparent';
                previewBox.style.backgroundImage = 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)';
                previewBox.style.backgroundSize = '8px 8px';
                previewBox.style.backgroundPosition = '0 0, 0 4px, 4px -4px, -4px 0px';
            } else {
                // Afficher la couleur solide
                previewBox.style.background = color;
                previewBox.style.backgroundImage = 'none';
            }
        } else if (previewBox) {
            // Pas de sélection - afficher le damier
            previewBox.style.background = 'transparent';
            previewBox.style.backgroundImage = 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)';
            previewBox.style.backgroundSize = '8px 8px';
            previewBox.style.backgroundPosition = '0 0, 0 4px, 4px -4px, -4px 0px';
        }
    }
    </script>
    <?php
}
