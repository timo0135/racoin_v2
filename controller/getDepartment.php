<?php

namespace controller;

use model\Departement;

class DepartmentController {

    protected array $departments = [];

    public function getAllDepartments(): array {
        return Departement::orderBy('nom_departement')->get()->toArray();
    }
}