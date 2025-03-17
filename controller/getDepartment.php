<?php

namespace controller;

use model\Departement;

class DepartmentController {

    protected $departments = [];

    public function getAllDepartments() {
        return Departement::orderBy('nom_departement')->get()->toArray();
    }
}