# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Versioning Sémantique](https://semver.org/lang/fr/).

## [1.0.0] - 2025-08-22

### Ajouté
- Interface d'administration pour créer et gérer les styles typographiques
- Sauvegarde des styles dans le dossier `styles/blocks/` du thème actif
- Support des types de blocs : Titre, Paragraphe, Liste, Citation
- Génération automatique de slug à partir du nom du style
- Formulaire avec champs pour :
  - Nom du style
  - Taille de police (dynamique depuis theme.json)
  - Famille de police (dynamique depuis theme.json)
  - Espacement des lettres
  - Hauteur de ligne
  - Types de blocs compatibles
- Fonctionnalité de modification des styles existants avec pré-remplissage
- Application globale des styles aux éléments du thème via theme.json
- Boutons pour appliquer un style à tous les H1, H2, H3, H4, H5, H6 ou paragraphes
- Filtrage des styles par type de bloc dans l'interface d'administration
- Génération CSS automatique des styles
- Sécurisation avec nonces WordPress
- Préfixes de fonctions uniques (utsm_) pour éviter les conflits

### Technique
- Architecture modulaire avec séparation des responsabilités :
  - `up-typo-style-manager.php` : Fichier principal et hooks WordPress
  - `admin/admin-page.php` : Interface d'administration
  - `includes/file-manager.php` : Gestion des fichiers et données
  - `includes/style-generator.php` : Génération CSS
  - `assets/admin.js` : Interactions JavaScript
- Intégration complète avec l'écosystème WordPress
- Support des thèmes utilisant theme.json
- Gestion des erreurs et validation des données
- Code documenté et structuré selon les standards WordPress

### Sécurité
- Vérification des nonces pour tous les formulaires
- Validation et échappement des données utilisateur
- Vérification des permissions d'administration
- Protection contre les injections et attaques XSS
