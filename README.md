# Home Budget

Outil de configuration et visualisation des charges du foyer.

L'idée derrière cet outil est d'automatiser le fonctionnement financier d'un foyer. 
Qui paie quoi ? Qui doit combien, à qui ? Oui, comme un tricount, mais où les dépenses sont fixes, et les revenus peuvent varier. 

Ici, on configure une bonne fois pour toutes les charges du foyer :
- loyer
- factures énergétiques
- abonnements
- etc.

Ces dépenses peuvent être réparties à égalité entre les membres du foyer, ou au prorata des revenus. 

Et chaque mois, chaque membre du foyer indique quels sont ses revenus. 
Ainsi, chaque mois, chacun sait combien il doit verser sur le compte commun, ou combien il doit rembourser à qui. 

# Installation

## Prérequis

- Docker
- Docker Compose
- PHP 8.4
- Composer
- Node.js et NPM

## Configuration

1. Cloner le projet :
```bash
git clone https://github.com/votre-username/tall-home-budget.git
cd tall-home-budget
```

2. Installer les dépendances PHP :
```bash
composer install
```

3. Copier le fichier d'environnement :
```bash
cp .env.example .env
```

4. Configurer le fichier `.env` :
```env
APP_URL=http://localhost:8080
APP_PORT=8080
VITE_PORT=5174

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Démarrer les conteneurs Docker :
```bash
./vendor/bin/sail up -d
```

6. Exécuter les migrations :
```bash
./vendor/bin/sail artisan migrate
```

7. Installer les dépendances JavaScript et compiler les assets :
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

L'application sera accessible à l'adresse : http://localhost:8080

## Commandes utiles

- Démarrer l'application : `./vendor/bin/sail up -d`
- Arrêter l'application : `./vendor/bin/sail down`
- Voir les logs : `./vendor/bin/sail logs`
- Exécuter une commande artisan : `./vendor/bin/sail artisan <commande>`
- Exécuter une commande npm : `./vendor/bin/sail npm <commande>`

# Structure
## Le foyer

Un foyer est un ensemble de personnes. 
La configuration du foyer permet de créer une à plusieurs personnes, et de définir s'il y a un compte joint ou non.

## Les charges

Une charge est une dépense fixe, mensuelle. 
Elle est caractérisée par un nom, une description, un montant, qui paie / sur quel compte c'est prélevé et le type de répartition

## Le solde
Chaque mois, chaque membre du foyer renseigne son salaire/ses revenus. 
Une fois que tout le monde a renseigné ses revenus, l'application donne le montant que chacun doit payer en tenant compte des répartitions et des éléments déjà payés. 

Le solde se réinitialise au début de chaque mois. 

# Planning
## Infrastructure
- Installation TALL
- Docker

## MVP 
- Configuration du foyer
- Configuration des dépenses récurrentes
- Saisie des revenus mensuels
- Affichage des soldes

## Phase 2
- Authentification
- Ajout des dépenses ponctuelles

## Plus tard…
- Historique des dépenses
- 

# Stack 
**TALL** : Tailwind, Alpine.js, Laravel et Livewire 
https://tallstack.dev/


---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
