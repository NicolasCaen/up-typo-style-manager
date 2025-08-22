# UP Typo Style Manager

Un plugin WordPress pour gérer les styles typographiques des blocs Gutenberg avec application globale aux éléments du thème.

## Description

UP Typo Style Manager permet de créer, modifier et appliquer des styles typographiques personnalisés aux blocs WordPress. Les styles sont sauvegardés dans le dossier du thème actif et peuvent être appliqués globalement à tous les éléments du même type via le fichier `theme.json`.

## Fonctionnalités

- ✅ **Création de styles personnalisés** avec nom, slug, taille de police, famille de police, espacement des lettres et hauteur de ligne
- ✅ **Sauvegarde dans le thème** : les styles sont stockés dans `wp-content/themes/[theme]/styles/blocks/`
- ✅ **Application globale** : possibilité d'appliquer un style à tous les éléments du même type (H1, H2, paragraphes, etc.)
- ✅ **Interface d'administration** intuitive avec formulaires de création/modification
- ✅ **Filtrage par type de bloc** pour organiser les styles
- ✅ **Génération automatique de slug** à partir du nom du style
- ✅ **Pré-remplissage des formulaires** lors de la modification d'un style existant
- ✅ **Intégration theme.json** pour l'application des styles globaux

## Types de blocs supportés

- Titre (core/heading)
- Paragraphe (core/paragraph)
- Liste (core/list)
- Citation (core/quote)

## Installation

1. Téléchargez le plugin depuis GitHub
2. Décompressez l'archive dans le dossier `wp-content/plugins/`
3. Activez le plugin depuis l'administration WordPress
4. Accédez au menu "Style Manager" dans l'administration

## Utilisation

### Créer un nouveau style

1. Allez dans **Style Manager** dans le menu d'administration
2. Remplissez le formulaire avec :
   - **Nom du style** : nom descriptif (le slug sera généré automatiquement)
   - **Taille de police** : sélectionnez parmi les tailles définies dans le thème
   - **Famille de police** : choisissez parmi les polices du thème
   - **Espacement des lettres** : valeur en rem, em ou px
   - **Hauteur de ligne** : valeur numérique
   - **Types de blocs** : sélectionnez les blocs compatibles
3. Cliquez sur **Ajouter le style**

### Modifier un style existant

1. Dans la liste des styles, cliquez sur **Modifier** à côté du style souhaité
2. Le formulaire se pré-remplit avec les valeurs actuelles
3. Modifiez les champs nécessaires
4. Cliquez sur **Mettre à jour le style**


## Structure des fichiers

- `up-typo-style-manager.php` : Fichier principal du plugin
- `admin/admin-page.php` : Interface d'administration
- `includes/file-manager.php` : Gestion des fichiers de styles
- `includes/style-generator.php` : Génération du CSS

## API

### Fonctions principales

- `utsm_file_save_style()` : Sauvegarde un style dans le thème
- `utsm_file_get_style()` : Récupère un style existant
- `utsm_file_apply_to_theme_elements()` : Applique un style aux éléments globaux
- `utsm_generate_css()` : Génère le CSS d'un style

## Changelog

### Version 1.2.0 (2025-08-22)

**Nouvelles fonctionnalités :**
- ✅ **Styles de sections** : Nouveau système complet de gestion des styles pour les sections/groupes
- ✅ **Support border-color** : Ajout de la couleur de bordure pour les sections et blocs internes
- ✅ **Aperçu visuel amélioré** : Prévisualisation réaliste des sections avec blocs internes intégrés
- ✅ **Blocs internes** : Support complet des blocs internes (boutons, titres, paragraphes, listes, etc.)
- ✅ **Normalisation des noms** : Les fichiers de styles utilisent maintenant un format standardisé (style1.json)

**Améliorations :**
- Interface dédiée pour la gestion des styles de sections
- Aperçu en temps réel avec couleurs de fond, texte et bordure
- Rendu conditionnel des titres H2 dans l'aperçu principal si core/heading est défini
- Conversion automatique des couleurs var:preset vers CSS pour l'aperçu
- Gestion intelligente des blocs vides (suppression automatique)

**Structure JSON :**
```json
{
  "styles": {
    "color": { "background": "...", "text": "..." },
    "border": { "color": "..." },
    "blocks": {
      "core/button": {
        "color": { "background": "...", "text": "..." },
        "border": { "color": "..." }
      }
    }
  }
}
```

### Version 1.1.0 (2025-08-22)

**Nouvelles fonctionnalités :**
- Ajout du support pour font-weight (100-900)
- Ajout du support pour font-style (normal, italic, oblique)
- Ajout du support pour text-transform (none, uppercase, lowercase, capitalize)
- Option "Hériter (inherit)" pour toutes les nouvelles propriétés

**Améliorations :**
- Correction du mapping des éléments theme.json selon la documentation WordPress
- Seuls les éléments supportés sont maintenant proposés (h1-h6, link, button, caption, cite)
- Suppression des éléments non supportés (p, ul, li, blockquote)
- Amélioration de la logique d'affichage des boutons d'application
- Ajout de logs de debug pour le diagnostic des problèmes

**Corrections de bugs :**
- Correction de la régression avec l'application des styles aux liens
- Les valeurs "inherit" sont maintenant correctement exclues lors de l'application aux éléments globaux
- Amélioration de la gestion d'erreur lors de l'écriture du theme.json

### Version 1.0.0
- Version initiale du plugin

## Contribution

Les contributions sont les bienvenues ! Merci de créer une issue ou une pull request.

## Licence

Ce plugin est distribué sous licence GPL v2 ou ultérieure.

## Support

Pour signaler un bug ou demander une fonctionnalité, utilisez les [Issues GitHub](https://github.com/NicolasCaen/up-typo-style-manager/issues).

## Auteur

Développé par [Nicolas Caen](https://github.com/NicolasCaen)
