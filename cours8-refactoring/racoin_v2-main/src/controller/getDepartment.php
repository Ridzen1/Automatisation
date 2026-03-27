<?php

namespace racoin\controller;

use racoin\model\Departement;

class getDepartment {

    protected $departments = array();

    public function getAllDepartments() {
        return Departement::orderBy('nom_departement')->get()->toArray();
    }
}