<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[WebReinvent](https://webreinvent.com/)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Jump24](https://jump24.co.uk)**
-   **[Redberry](https://redberry.international/laravel/)**
-   **[Active Logic](https://activelogic.com)**
-   **[byte5](https://byte5.de)**
-   **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Pinterest Clone

Cette application est un clone de Pinterest, permettant aux utilisateurs de créer et partager des cartes multimédias.

## Configuration des miniatures (thumbnails)

L'application peut maintenant générer des miniatures optimisées pour différents types de médias (images, vidéos, audio).

### Prérequis

-   **FFmpeg** : Nécessaire pour la génération de miniatures de vidéos. Sans FFmpeg, les conversions de vidéos ne fonctionneront pas.
    -   Sur macOS : `brew install ffmpeg`
    -   Sur Ubuntu : `sudo apt install ffmpeg`
    -   Sur Windows : Téléchargez depuis [ffmpeg.org](https://ffmpeg.org/download.html)

### Images par défaut

Pour que le système d'affichage des miniatures fonctionne correctement, ajoutez les fichiers suivants :

1. Créez un dossier `public/images` s'il n'existe pas déjà
2. Ajoutez les images par défaut suivantes :
    - `public/images/audio-thumbnail.png` - Miniature par défaut pour les fichiers audio
    - `public/images/default-thumbnail.png` - Miniature par défaut générique

### Régénération des miniatures

Les miniatures sont générées automatiquement lors de l'ajout ou de la modification de médias. Si vous avez des médias existants et que vous souhaitez générer leurs miniatures, utilisez la commande Artisan suivante :

```bash
php artisan media:regenerate
```

Options disponibles :

-   `--model=Card` : Spécifier le type de modèle (par défaut : Card)
-   `--id=123` : Régénérer seulement pour un ID spécifique
-   `--collection=images` : Régénérer seulement pour une collection spécifique (images, videos, music)

Exemple :

```bash
# Régénérer toutes les miniatures de vidéos
php artisan media:regenerate --collection=videos

# Régénérer les miniatures pour une carte spécifique
php artisan media:regenerate --id=123
```

## Modifications apportées

Ces nouvelles fonctionnalités ont été ajoutées à plusieurs parties de l'application :

1. **Modèle Card** - Génère automatiquement différentes tailles de miniatures
2. **Vue dashboard** - Affiche désormais les miniatures optimisées
3. **Vue d'accueil** - Utilise les miniatures adaptées à la grille
4. **Vue détaillée** - Affiche les médias avec leurs miniatures correspondantes

## Résolution des problèmes

Si les miniatures ne s'affichent pas correctement :

1. Vérifiez que FFmpeg est installé et accessible dans le PATH
2. Assurez-vous que les images par défaut sont présentes dans le dossier `public/images`
3. Exécutez la commande de régénération des miniatures
