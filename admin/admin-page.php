<?php
function utsm_admin_page() {
    ?>
    <div class="wrap">
        <h1>Gestionnaire de Styles</h1>
        
        <?php
        // Gestion des actions (ajout, modification, suppression)
        if (isset($_POST['wsm_add_style'])) {
            $result = utsm_file_add_or_update_style($_POST);
            if ($result) {
                echo '<div class="notice notice-success"><p>Style ajouté/modifié avec succès!</p></div>';
            }
        }
        
        if (isset($_GET['delete'])) {
            $result = utsm_file_delete_style($_GET['delete']);
            if ($result) {
                echo '<div class="notice notice-success"><p>Style supprimé avec succès!</p></div>';
            }
        }
        
        // Gestion de l'application aux éléments du thème
        if (isset($_POST['apply_to_theme']) && isset($_POST['style_slug']) && isset($_POST['element_type'])) {
            $result = utsm_file_apply_to_theme_elements($_POST['style_slug'], $_POST['element_type']);
            if ($result) {
                echo '<div class="notice notice-success"><p>Style appliqué aux éléments du thème avec succès!</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Erreur lors de l\'application du style au thème.</p></div>';
            }
        }
        
        // Récupérer le style à modifier
        $edit_style = isset($_GET['edit']) ? $_GET['edit'] : null;
        ?>
        
        <div class="wsm-admin-container">
            <div class="wsm-form-container">
                <h2>Ajouter/Modifier un style</h2>
                <?php utsm_admin_display_form($edit_style); ?>
            </div>
            
            <div class="wsm-list-container">
                <h2>Styles existants</h2>
                <div class="wsm-filter-group">
                    <label>Filtrer par type de bloc :</label>
                    <select id="block-type-filter">
                        <option value="">Tous les types</option>
                        <option value="core/heading">Heading</option>
                        <option value="core/paragraph">Paragraph</option>
                        <option value="core/list">List</option>
                        <option value="core/quote">Blockquote</option>
                    </select>
                </div>
                <?php utsm_admin_display_styles_list(); ?>
            </div>
        </div>
    </div>
    
    <style>
    .wsm-admin-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }
    
    .wsm-form-container, .wsm-list-container {
        background: #fff;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    
    .wsm-form-group {
        margin-bottom: 15px;
    }
    
    .wsm-form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .wsm-form-group input, .wsm-form-group select {
        width: 100%;
    }
    
    .wsm-style-item {
        padding: 10px;
        border: 1px solid #eee;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .wsm-style-actions a {
        margin-left: 10px;
    }
    
    .wsm-filter-group {
        margin-bottom: 15px;
    }
    
    .wsm-filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .wsm-style-item[data-block-types] {
        display: block;
    }
    
    .wsm-style-item.hidden {
        display: none !important;
    }
    
    .wsm-apply-to-theme {
        margin-top: 20px;
        padding: 15px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    
    .wsm-apply-to-theme h3 {
        margin-top: 0;
        color: #333;
    }
    
    .wsm-apply-to-theme p {
        color: #666;
        margin-bottom: 15px;
    }
    
    @media (max-width: 768px) {
        .wsm-admin-container {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
}

function utsm_admin_display_form($style_slug = null) {
    $style_data = $style_slug ? utsm_file_get_style($style_slug) : null;
    $theme_data = utsm_file_get_theme_data();
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('wsm_style_nonce', 'wsm_nonce'); ?>
        
        <input type="hidden" name="old_slug" value="<?php echo $style_data['slug'] ?? ''; ?>">
        
        <div class="wsm-form-group">
            <label>Nom du style</label>
            <input type="text" name="name" id="style-name" value="<?php echo $style_data['title'] ?? ''; ?>" required>
        </div>
        
        <div class="wsm-form-group">
            <label>Slug</label>
            <input type="text" name="slug" id="style-slug" value="<?php echo $style_data['slug'] ?? ''; ?>" required>
        </div>
        
        <div class="wsm-form-group">
            <label>Font Size</label>
            <select name="font_size">
                <option value="">Sélectionner...</option>
                <?php foreach ($theme_data['fontSizes'] as $font_size): ?>
                    <option value="var:preset|font-size|<?php echo $font_size['slug']; ?>" 
                        <?php selected(isset($style_data['styles']['typography']['fontSize']) && $style_data['styles']['typography']['fontSize'] == 'var:preset|font-size|' . $font_size['slug']); ?>>
                        <?php echo $font_size['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="wsm-form-group">
            <label>Font Family</label>
            <select name="font_family">
                <option value="">Sélectionner...</option>
                <?php foreach ($theme_data['fontFamilies'] as $font_family): ?>
                    <option value="var:preset|font-family|<?php echo $font_family['slug']; ?>" 
                        <?php selected(isset($style_data['styles']['typography']['fontFamily']) && $style_data['styles']['typography']['fontFamily'] == 'var:preset|font-family|' . $font_family['slug']); ?>>
                        <?php echo $font_family['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="wsm-form-group">
            <label>Letter Spacing</label>
            <input type="text" name="letter_spacing" value="<?php echo $style_data['styles']['typography']['letterSpacing'] ?? ''; ?>">
        </div>
        
        <div class="wsm-form-group">
            <label>Line Height</label>
            <input type="text" name="line_height" value="<?php echo $style_data['styles']['typography']['lineHeight'] ?? ''; ?>">
        </div>
        
        <div class="wsm-form-group">
            <label>Font Weight</label>
            <select name="font_weight">
                <option value="">Hériter (inherit)</option>
                <option value="100" <?php selected(isset($style_data['styles']['typography']['fontWeight']) && $style_data['styles']['typography']['fontWeight'] == '100'); ?>>100 - Thin</option>
                <option value="200" <?php selected(isset($style_data['styles']['typography']['fontWeight']) && $style_data['styles']['typography']['fontWeight'] == '200'); ?>>200 - Extra Light</option>
                <option value="300" <?php selected(isset($style_data['styles']['typography']['fontWeight']) && $style_data['styles']['typography']['fontWeight'] == '300'); ?>>300 - Light</option>
                <option value="400" <?php selected(isset($style_data['styles']['typography']['fontWeight']) && $style_data['styles']['typography']['fontWeight'] == '400'); ?>>400 - Normal</option>
                <option value="500" <?php selected(isset($style_data['styles']['typography']['fontWeight']) && $style_data['styles']['typography']['fontWeight'] == '500'); ?>>500 - Medium</option>
                <option value="600" <?php selected(isset($style_data['styles']['typography']['fontWeight']) && $style_data['styles']['typography']['fontWeight'] == '600'); ?>>600 - Semi Bold</option>
                <option value="700" <?php selected(isset($style_data['styles']['typography']['fontWeight']) && $style_data['styles']['typography']['fontWeight'] == '700'); ?>>700 - Bold</option>
                <option value="800" <?php selected(isset($style_data['styles']['typography']['fontWeight']) && $style_data['styles']['typography']['fontWeight'] == '800'); ?>>800 - Extra Bold</option>
                <option value="900" <?php selected(isset($style_data['styles']['typography']['fontWeight']) && $style_data['styles']['typography']['fontWeight'] == '900'); ?>>900 - Black</option>
            </select>
        </div>
        
        <div class="wsm-form-group">
            <label>Font Style</label>
            <select name="font_style">
                <option value="">Hériter (inherit)</option>
                <option value="normal" <?php selected(isset($style_data['styles']['typography']['fontStyle']) && $style_data['styles']['typography']['fontStyle'] == 'normal'); ?>>Normal</option>
                <option value="italic" <?php selected(isset($style_data['styles']['typography']['fontStyle']) && $style_data['styles']['typography']['fontStyle'] == 'italic'); ?>>Italic</option>
                <option value="oblique" <?php selected(isset($style_data['styles']['typography']['fontStyle']) && $style_data['styles']['typography']['fontStyle'] == 'oblique'); ?>>Oblique</option>
            </select>
        </div>
        
        <div class="wsm-form-group">
            <label>Text Transform (Casse)</label>
            <select name="text_transform">
                <option value="">Hériter (inherit)</option>
                <option value="none" <?php selected(isset($style_data['styles']['typography']['textTransform']) && $style_data['styles']['typography']['textTransform'] == 'none'); ?>>Aucune</option>
                <option value="uppercase" <?php selected(isset($style_data['styles']['typography']['textTransform']) && $style_data['styles']['typography']['textTransform'] == 'uppercase'); ?>>MAJUSCULES</option>
                <option value="lowercase" <?php selected(isset($style_data['styles']['typography']['textTransform']) && $style_data['styles']['typography']['textTransform'] == 'lowercase'); ?>>minuscules</option>
                <option value="capitalize" <?php selected(isset($style_data['styles']['typography']['textTransform']) && $style_data['styles']['typography']['textTransform'] == 'capitalize'); ?>>Première Lettre</option>
            </select>
        </div>
        
        <div class="wsm-form-group">
            <label>Block Types</label>
            <select name="block_types[]" multiple style="height: 120px;">
                <option value="core/heading" <?php echo isset($style_data['blockTypes']) && in_array('core/heading', $style_data['blockTypes']) ? 'selected' : ''; ?>>Heading</option>
                <option value="core/paragraph" <?php echo isset($style_data['blockTypes']) && in_array('core/paragraph', $style_data['blockTypes']) ? 'selected' : ''; ?>>Paragraph</option>
                <option value="core/list" <?php echo isset($style_data['blockTypes']) && in_array('core/list', $style_data['blockTypes']) ? 'selected' : ''; ?>>List</option>
                <option value="core/quote" <?php echo isset($style_data['blockTypes']) && in_array('core/quote', $style_data['blockTypes']) ? 'selected' : ''; ?>>Blockquote</option>
                <option value="core/button" <?php echo isset($style_data['blockTypes']) && in_array('core/button', $style_data['blockTypes']) ? 'selected' : ''; ?>>Button</option>
            </select>
        </div>
        
        <button type="submit" name="wsm_add_style" class="button button-primary">
            <?php echo $style_slug ? 'Modifier' : 'Ajouter'; ?>
        </button>
    </form>
    
    <?php if ($style_slug && $style_data): ?>
    <div class="wsm-apply-to-theme">
        <h3>Appliquer ce style aux éléments globaux du thème</h3>
        <p>Appliquez ce style comme style par défaut pour tous les éléments de ce type dans le thème.</p>
        
        <!-- Bouton pour appliquer aux styles globaux par défaut (body) -->
        <form method="post" style="display: inline-block; margin-right: 10px; margin-bottom: 10px;">
            <input type="hidden" name="style_slug" value="<?php echo $style_slug; ?>">
            <input type="hidden" name="element_type" value="body">
            <button type="submit" name="apply_to_theme" class="button button-primary">
                Appliquer aux styles globaux par défaut (body)
            </button>
        </form>
        
        <?php 
        $block_types = $style_data['blockTypes'] ?? [];
        foreach ($block_types as $block_type):
            $element_buttons = [];
            
            if ($block_type === 'core/heading') {
                $element_buttons = [
                    'heading-h1' => 'Tous les H1',
                    'heading-h2' => 'Tous les H2', 
                    'heading-h3' => 'Tous les H3',
                    'heading-h4' => 'Tous les H4',
                    'heading-h5' => 'Tous les H5',
                    'heading-h6' => 'Tous les H6'
                ];
            } elseif ($block_type === 'core/paragraph') {
                $element_buttons = ['paragraph' => 'Tous les paragraphes'];
            } elseif ($block_type === 'core/list') {
                $element_buttons = [
                    'list' => 'Toutes les listes (ul/ol)',
                    'list-item' => 'Tous les éléments de liste (li)'
                ];
            } elseif ($block_type === 'core/quote') {
                $element_buttons = [
                    'quote' => 'Toutes les citations (blockquote)',
                    'cite' => 'Toutes les sources de citation (cite)'
                ];
            }
            
            // Ajouter les boutons pour les éléments de blocs spécifiques
            if (in_array($block_type, ['core/button', 'core/heading', 'core/paragraph', 'core/list', 'core/quote'])) {
                // Boutons pour les éléments HTML génériques
                $generic_buttons = [
                    'button' => 'Tous les boutons',
                    'link' => 'Tous les liens (a)',
                    'caption' => 'Toutes les légendes'
                ];
                $element_buttons = array_merge($element_buttons ?? [], $generic_buttons);
            }
            
            foreach ($element_buttons as $element_type => $label):
        ?>
        <form method="post" style="display: inline-block; margin-right: 10px; margin-bottom: 10px;">
            <input type="hidden" name="style_slug" value="<?php echo $style_slug; ?>">
            <input type="hidden" name="element_type" value="<?php echo $element_type; ?>">
            <button type="submit" name="apply_to_theme" class="button button-secondary">
                <?php echo $label; ?>
            </button>
        </form>
        <?php 
            endforeach;
        endforeach; 
        ?>
    </div>
    <?php endif; ?>
    
    <?php
}

function utsm_admin_display_styles_list() {
    $styles = utsm_file_get_all_styles();
    
    if (empty($styles)) {
        echo '<p>Aucun style créé pour le moment.</p>';
        return;
    }
    
    foreach ($styles as $slug => $style) {
        $block_types_str = implode(',', $style['blockTypes'] ?? []);
        ?>
        <div class="wsm-style-item" data-block-types="<?php echo esc_attr($block_types_str); ?>">
            <div>
                <strong><?php echo esc_html($style['title']); ?></strong>
                <br>
                <small>Slug: <?php echo esc_html($slug); ?></small>
                <br>
                <small>Blocks: <?php echo implode(', ', $style['blockTypes'] ?? []); ?></small>
            </div>
            <div class="wsm-style-actions">
                <a href="?page=up-typo-style-manager&edit=<?php echo $slug; ?>" class="button">Modifier</a>
                <a href="?page=up-typo-style-manager&delete=<?php echo $slug; ?>" class="button button-link-delete" onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
            </div>
        </div>
        <?php
    }
}