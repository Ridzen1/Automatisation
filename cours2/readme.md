# 1. Démarrer Docker (Base de données, PHP, Nginx)
docker compose up -d

# 2. Démarrer Vite (Compilation des assets à la volée)
# À lancer dans un terminal séparé et laisser tourner
npm run dev

# Exécution de la commande dans le conteneur PHP
docker compose exec php php bin/console app:populate-database

Pour vérifier le code avec les outils configurés :
# PHP (Static analysis & Style)
composer run phpstan
composer run phpcs

# JavaScript (ESLint)
npm run lint