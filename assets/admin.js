jQuery(document).ready(function($) {
    // Auto-génération du slug depuis le nom
    $('#style-name').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Supprimer les accents
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#style-slug').val(slug);
    });
    
    // Filtrage des styles par type de bloc
    $('#block-type-filter').on('change', function() {
        const selectedType = $(this).val();
        
        $('.wsm-style-item').each(function() {
            const blockTypes = $(this).data('block-types');
            
            if (!selectedType || blockTypes.includes(selectedType)) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    });
});