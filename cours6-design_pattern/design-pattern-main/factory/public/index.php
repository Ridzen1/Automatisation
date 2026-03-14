<?php
//require('../vendor/autoload.php');
//die('ici, vous pouvez tester votre factory !');

##test factory##
require_once __DIR__ . '/../app/Factory/VehiculeFactory.php';
require_once __DIR__ . '/../app/Entity/Vehicule.php';
require_once __DIR__ . '/../app/Entity/Bicycle.php';
require_once __DIR__ . '/../app/Entity/Car.php';
require_once __DIR__ . '/../app/Entity/Truck.php';

$factory = new VehiculeFactory();

echo "=== Test par type ===\n";

$v1 = $factory->getVehicule("Bicycle");
echo "Bicycle -> Cost/km: " . $v1->getCostPerKm() . " | Fuel: " . $v1->getFuelType() . "\n";

$v2 = $factory->getVehicule("Car");
echo "Car -> Cost/km: " . $v2->getCostPerKm() . " | Fuel: " . $v2->getFuelType() . "\n";

$v3 = $factory->getVehicule("Truck");
echo "Truck -> Cost/km: " . $v3->getCostPerKm() . " | Fuel: " . $v3->getFuelType() . "\n";


echo "\n=== Test par distance / poids ===\n";

$v4 = $factory->getVehiculeByTransport(10, 5);
echo "Distance 10km / Poids 5kg -> " . get_class($v4) . "\n";

$v5 = $factory->getVehiculeByTransport(50, 50);
echo "Distance 50km / Poids 50kg -> " . get_class($v5) . "\n";

$v6 = $factory->getVehiculeByTransport(100, 300);
echo "Distance 100km / Poids 300kg -> " . get_class($v6) . "\n";