# Vign8

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)

Vign8 (prononcé "vigneight" ou "vignette") est une plateforme moderne de partage de contenu multimédia inspirée de Pinterest, permettant aux utilisateurs de créer et partager des cartes contenant des vidéos, de la musique et des photos dans un format de type "bento box".

## 🚀 Fonctionnalités

-   **Partage de contenu multimédia** : Support pour les vidéos, la musique et les photos
-   **Système de cartes personnalisables** : Trois tailles disponibles (Petit, Moyen, Grand)
-   **Interface utilisateur moderne** : Design responsive et intuitif
-   **Gestion des catégories** : Organisation du contenu par catégories
-   **Comptes personnels** : Système d'authentification complet
-   **Gestion des médias** : Stockage sécurisé des fichiers multimédias

## 🛠️ Stack technique

-   **Backend** : Laravel 12.x
-   **Frontend** :
    -   Tailwind CSS
    -   Alpine.js
-   **Authentification** : Laravel Breeze
-   **Gestion des médias** : Spatie Media Library
-   **Base de données** : MySQL

## 📋 Prérequis

-   PHP 8.2 ou supérieur
-   Composer
-   Node.js et npm
-   MySQL
-   Configuration d'un serveur mail pour la récupération de mot de passe

## 🔧 Installation

1. Clonez le dépôt :

```bash
git clone https://github.com/votre-username/vign8.git
cd vign8
```

2. Installez les dépendances PHP :

```bash
composer install
```

3. Installez les dépendances JavaScript :

```bash
npm install
```

4. Copiez le fichier d'environnement :

```bash
cp .env.example .env
```

5. Générez la clé d'application :

```bash
php artisan key:generate
```

6. Configurez votre base de données dans le fichier `.env`

7. Exécutez les migrations :

```bash
php artisan migrate
```

8. Compilez les assets :

```bash
npm run build
```

9. Lancez le serveur de développement :

```bash
php artisan serve
```

## 🏗️ Structure du projet

-   `app/Models/` : Modèles de données (Card, Category, CardSize, etc.)
-   `resources/views/` : Templates Blade
-   `public/` : Assets publics
-   `database/migrations/` : Migrations de la base de données

## 🤝 Contribution

Les contributions sont les bienvenues ! Voici comment contribuer :

1. Fork le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👥 Auteurs

-   Votre nom - Travail initial

## 🙏 Remerciements

-   Laravel
-   Tailwind CSS
-   Alpine.js
-   Spatie
-   Tous les contributeurs qui ont aidé à façonner ce projet
