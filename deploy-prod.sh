#!/bin/bash
set -e

echo "Déploiement en production..."

# Construire les images
docker compose -f docker-compose.prod.yml build --no-cache

# Démarrer les services
docker compose -f docker-compose.prod.yml up -d

# Attendre que Laravel soit prêt
echo "Attendre que Laravel soit prêt..."
sleep 10

# Exécuter les migrations
docker compose -f docker-compose.prod.yml exec laravel-prod php artisan config:clear
docker compose -f docker-compose.prod.yml exec laravel-prod php artisan migrate --force

# Tester que tout fonctionne
echo "Test des assets Vite..."
curl -f http://localhost/build/manifest.json > /dev/null && echo "✅ Vite OK" || echo "❌ Vite KO"

echo "Test des routes Livewire..."
curl -f http://localhost/livewire/livewire.min.js?id=test > /dev/null && echo "✅ Livewire OK" || echo "❌ Livewire KO"

echo "Déploiement terminé !"
