# APLA [![CI/CD](https://github.com/TiteRin/tall-home-budget/actions/workflows/ci-cd.yml/badge.svg?branch=main)](https://github.com/TiteRin/tall-home-budget/actions/workflows/ci-cd.yml)
Un outil pour visualiser les charges du foyer et calculer la part de chacun

## Pourquoi ?
Il y a longtemps, j’ai créé une feuille excel pour lister les dépenses communes du foyer, et calculer le montant à payer au prorata des revenus de chaque membre (on ne croit pas au 50/50 dans cette maison). 
Un peu à la manière d’un Tricount, mais orienté "qui met combien sur le compte joint ce mois-ci". 
Histoire d’avoir une interface un peu plus sympa qu’une feuille excel, et dans le but de me mettre au TDD, j’ai eu l’idée de transformer ma feuille de calcul en application.

## Objectif
- Créer des dépenses, les affecter à quelqu’un, décider du mode de répartition
- Afficher les mouvements financiers nécessaires pour équilibrer la balance.

## MVP

- Application destinée à être hébergée en local sur un serveur privé, car aucune authentification
- Création des membres du foyer
- CRUD des dépenses
- Calcul des mouvements

## Roadmap

- [x] Ajouter l’authentification
- [x] Ajouter la possibilité de créer plusieurs foyers
- [x] Ajouter des dépenses ponctuelles (e.g. les courses)
- [x] Version mobile
- [ ] Meilleure UI

## Stack
- [TALL](https://tallstack.dev/)
- [DaisyUI](https://daisyui.com/)

## Installation
### Prérequis

- Docker
- Docker Compose
- PHP 8.4
- Composer
- Node.js et NPM

### Configuration 
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

# pgAdmin Configuration
PGADMIN_PORT=5050
PGADMIN_DEFAULT_EMAIL=admin@example.com
PGADMIN_DEFAULT_PASSWORD=admin
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

8. Générer la clef privée
```bash
./vendor/bin/sail artisan key:generate
```

L'application sera accessible à l'adresse : http://localhost:8080
