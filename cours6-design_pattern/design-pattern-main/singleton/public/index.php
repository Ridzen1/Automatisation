<?php
require('../vendor/autoload.php');

use App\Config;

echo "<h1>Test du pattern Singleton</h1>";

$config1 = Config::getInstance();

echo "<p><strong>Valeur de la clé 'apiKey' :</strong> " . $config1->get('apiKey') . "</p>";
echo "<p><strong>Valeur de la clé 'db' -> 'host' :</strong> " . $config1->get('db')['host'] . "</p>";

$config2 = Config::getInstance();

echo "<p><strong>Les deux instances sont-elles identiques ?</strong> ";
if ($config1 === $config2) {
    echo "Oui ! Le Singleton fonctionne. (bool: true)</p>";
} else {
    echo "Non... Il y a un problème. (bool: false)</p>";
}

echo "<pre>";
var_dump($config1, $config2);
echo "</pre>";