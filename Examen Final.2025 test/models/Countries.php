<?php

namespace Models;

class Countries extends Database {

    public function getAllCountries() {
        $req = "SELECT id, name FROM countries ORDER BY name ASC";
        return $this->findAll($req);
    }
    public function getCountryById($id) {
        $req = "SELECT id, name FROM countries WHERE id = ?";
        return $this->findOne($req, [$id]);
}
}
?>
