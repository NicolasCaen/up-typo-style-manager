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

### Appliquer un style globalement

1. Dans la liste des styles, utilisez les boutons **Appliquer à H1**, **Appliquer à H2**, etc.
2. Le style sera appliqué à tous les éléments de ce type dans le thème
3. Les modifications sont sauvegardées dans le fichier `theme.json`

### Filtrer les styles

Utilisez le menu déroulant **Filtrer par type de bloc** pour afficher uniquement les styles compatibles avec un type de bloc spécifique.

## Structure des fichiers

```
up-typo-style-manager/
├── up-typo-style-manager.php    # Fichier principal du plugin
├── admin/
│   └── admin-page.php           # Interface d'administration
├── includes/
│   ├── file-manager.php         # Gestion des fichiers et styles
│   └── style-generator.php      # Génération CSS
├── assets/
│   └── admin.js                 # Scripts JavaScript
├── README.md                    # Documentation
└── CHANGELOG.md                 # Historique des versions
```

## Développement

### Prérequis

- WordPress 5.0+
- PHP 7.4+
- Thème compatible avec `theme.json`

### Fonctions principales

- `utsm_file_save_style()` : Sauvegarde un style dans le thème
- `utsm_file_get_style()` : Récupère un style existant
- `utsm_file_apply_to_theme_elements()` : Applique un style aux éléments globaux
- `utsm_generate_css()` : Génère le CSS d'un style

## Contribution

1. Forkez le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Committez vos changements (`git commit -am 'Ajout d'une nouvelle fonctionnalité'`)
4. Poussez vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créez une Pull Request

## Licence

Ce plugin est distribué sous licence GPL v2 ou ultérieure.

## Support

Pour signaler un bug ou demander une fonctionnalité, utilisez les [Issues GitHub](https://github.com/NicolasCaen/up-typo-style-manager/issues).

## Auteur

Développé par [Nicolas Caen](https://github.com/NicolasCaen)
