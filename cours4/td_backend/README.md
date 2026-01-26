# Projet Vide
## Installation

```bash
docker compose run --rm php composer install
```

## Demarage du projet
```bash
docker compose up
```
=> http://localhost:8080

## Initialisation de la base de données + Peuplement
```bash
## Initialisation de la base de données + Peuplement
Une fois le conteneur lancé, exécutez ces deux commandes pour créer les tables et générer les fausses données de test :

```bash
## 1. Création des Entités et de la Relation
```bash
# Création de l'entité Réalisateur (champs : nom, prenom)
docker compose run --rm php bin/console make:entity Realisateur

# Création de l'entité Film et liaison
docker compose run --rm php bin/console make:entity Film
# Note : La relation ManyToOne vers Realisateur a été ajoutée lors de cette étape (mettre relation comme type de champs)

# 2. Création des tables (Migration)
docker compose run --rm php bin/console doctrine:migrations:migrate

# 3. Création de la fixture
docker compose run --rm php bin/console make:fixture AppFixtures

# 4. Remplissage de la base avec Faker (Fixtures)
docker compose run --rm php bin/console doctrine:fixtures:lo

## Accès aux vues :
Liste des Films : http://localhost:8080/film
Liste des Réalisateurs : http://localhost:8080/realisateur
---



# Note si problemes de docker UNIQUEMENT :
Certain on des problème de performance/lenteur avec docker, vous pourrez utiliser votre composer/php local en gardant bien en tête que ce n'est pas une bonne pratique.

Sur la configuration docker ; vous verez une ligne "user" pour le service php. Elle sert a préciser quel user écrira sur la machine hote, par defaut l'identifiant du user et du groupe est 1000.  
Vous trouverez votre valeur avec la commande suivante, et changer si cela est necessaire.
```bash
echo "UID: ${UID}"
```

Il faut respecter ces conditions:
- `php8.2` avec les extension php`CType`, `iconv`, `session`, `simpleXML` et `Tokenizer`. Et bien sur `composer`

```bash
composer install
```

```bash
php -S 0.0.0.0:8080 -t public
```



