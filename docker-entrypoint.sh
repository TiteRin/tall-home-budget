#!/bin/bash
set -e

echo "Initialisation Laravel..."

# Vérifier que les routes Livewire sont disponibles
echo "Routes Livewire disponibles :"
php artisan route:list | grep livewire || echo "Aucune route Livewire trouvée"

# Publier les assets Livewire si nécessaire
if [ ! -d "/var/www/html/public/vendor/livewire" ]; then
    echo "Publication des assets Livewire..."
    php artisan livewire:publish --assets || true
fi

echo "Laravel initialisé avec succès"

# Démarrer PHP-FPM
exec php-fpm
