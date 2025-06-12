<?php
namespace Models;

class Categories extends Database {
    public function getAllCategories() {
        $req = "SELECT id, name FROM categories ORDER BY name ASC";
        return $this->findAll($req);
    }
}
?>
