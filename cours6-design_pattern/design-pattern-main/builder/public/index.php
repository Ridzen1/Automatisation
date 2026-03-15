<?php
require('../vendor/autoload.php');

use App\MySqlQueryBuilder;

$builder = new MySqlQueryBuilder();

$query1 = $builder
    ->select(['id', 'name', 'email'])
    ->from('users')
    ->where('age >= 18')
    ->where('status = "active"')
    ->getSQL();

$builder2 = new MySqlQueryBuilder();
$query2 = $builder2
    ->select(['title', 'content'])
    ->from('posts')
    ->getSQL();

echo "<h1>Test du Query Builder (Design Pattern)</h1>";

echo "<h2>Requête 1 :</h2>";
echo "<code>" . $query1 . "</code>"; 

echo "<h2>Requête 2 :</h2>";
echo "<code>" . $query2 . "</code>";