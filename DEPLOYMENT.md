# Guide de Déploiement

Ce guide explique comment configurer le déploiement automatique de l'application sur un VPS.

## 1. Configuration du VPS

### Installation des dépendances système

```bash
# Mise à jour du système
sudo apt update && sudo apt upgrade -y

# Installation de PHP 8.4 et extensions nécessaires
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.4-fpm php8.4-cli php8.4-common php8.4-mysql php8.4-zip php8.4-gd php8.4-mbstring php8.4-curl php8.4-xml php8.4-bcmath

# Installation de Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Installation de Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Installation de Nginx
sudo apt install -y nginx

# Installation de MySQL
sudo apt install -y mysql-server
```

### Configuration de Nginx

Créez un fichier de configuration pour votre site :

```bash
sudo nano /etc/nginx/sites-available/laravel
```

Contenu du fichier :
```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Activez la configuration :
```bash
sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Configuration des permissions

```bash
sudo mkdir -p /var/www/html
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
```

## 2. Configuration de GitHub Actions

### Génération de la clé SSH

1. Sur votre machine locale, générez une paire de clés SSH :
```bash
ssh-keygen -t ed25519 -C "votre-email@example.com"
```

2. Affichez la clé publique :
```bash
cat ~/.ssh/id_ed25519.pub
```

3. Copiez cette clé et ajoutez-la dans le fichier `~/.ssh/authorized_keys` sur votre VPS :
```bash
echo "votre-clé-publique" >> ~/.ssh/authorized_keys
```

### Configuration des secrets GitHub

Dans les paramètres de votre dépôt GitHub (Settings > Secrets and variables > Actions), ajoutez les secrets suivants :

| Nom du Secret | Description |
|---------------|-------------|
| `VPS_HOST` | L'adresse IP de votre VPS |
| `VPS_USERNAME` | Votre nom d'utilisateur SSH |
| `VPS_SSH_KEY` | Le contenu de votre clé privée SSH (`~/.ssh/id_ed25519`) |

## 3. Premier déploiement

1. Clonez le dépôt sur le VPS :
```bash
cd /var/www/html
git clone votre-repo-github .
```

2. Configurez le fichier .env :
```bash
cp .env.example .env
nano .env
```

3. Installez les dépendances et configurez l'application :
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. Configurez les permissions :
```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chmod -R 775 /var/www/html/storage
```

## 4. Déploiement automatique

Le déploiement se fera automatiquement à chaque push sur la branche main grâce au workflow GitHub Actions configuré dans `.github/workflows/deploy.yml`.

## Dépannage

Si vous rencontrez des problèmes :

1. Vérifiez les logs GitHub Actions pour des messages d'erreur détaillés
2. Vérifiez que tous les secrets sont correctement configurés
3. Assurez-vous que la clé SSH a les bonnes permissions
4. Vérifiez les logs Nginx : `sudo tail -f /var/log/nginx/error.log`
5. Vérifiez les logs PHP-FPM : `sudo tail -f /var/log/php8.4-fpm.log` 