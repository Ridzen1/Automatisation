<?php

require_once __DIR__ . '/../Entity/Vehicule.php';
require_once __DIR__ . '/../Entity/Bicycle.php';
require_once __DIR__ . '/../Entity/Car.php';
require_once __DIR__ . '/../Entity/Truck.php';

class VehiculeFactory {

    public function getVehicule(string $type){
        switch ($type) {
            case "Bicycle":
                return new Bicycle(0.05,"un gars");
            case "Car":
                return new Car(100,"Diesel");
            case "Truck":
                return new Truck(50,"Sans plomb 95");
            default :
                throw new Exception("Illegal argument");
        }

    }

    public function getVehiculeByTransport($distance,$poids){

        if ($poids > 200) {
            return new Truck(50,"Sans plomb 95");
        }
        if ($poids > 20) {
            return new Car(100,"Diesel");
        }
        
        if ($distance < 20){
            return new Bicycle(0.05,"un gars");
        }

        return new Car(100,"Diesel");
    }

}