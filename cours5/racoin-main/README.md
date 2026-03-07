## Racoin
# Application Racoin - Projet de TD

Cette application est une plateforme de petites annonces développée en PHP avec Slim Framework (v2), Twig et Eloquent.

## Prérequis
- Docker et Docker Compose installés sur votre machine.
- Composer installé localement.
- Un script SQL (fourni en cours) pour initialiser la base de données.

## Processus de démarrage

1. **Installer les dépendances PHP :**
   À la racine du projet, exécutez la commande suivante pour télécharger les librairies requises par le projet :
   `composer install`
   *(Note : Les anciennes versions de Twig et Illuminate étant bloquées par l'audit de sécurité de Composer, l'audit a été désactivé dans le fichier composer.json pour permettre l'installation initiale).*

2. **Démarrer les conteneurs Docker :**
   Lancez l'environnement de développement (serveur web PHP et base de données MariaDB) en arrière-plan :
   `docker-compose up -d`

3. **Initialiser la base de données :**
   Connectez-vous à la base de données via votre client SQL sur `localhost:3306` avec les identifiants suivants (visibles dans `config/config.ini`) :
   - User : `racoin_user`
   - Password : `racoin_password`
   - Database : `racoin`
   Importez ensuite le script SQL du projet pour créer les tables (annonces, annonceurs, etc.).

4. **Accéder à l'application :**
   Ouvrez votre navigateur et rendez-vous sur : [http://localhost:8080](http://localhost:8080)

