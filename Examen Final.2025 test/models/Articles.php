<?php
namespace Models;

class Articles extends Database {
    public function getAllArticles() {
       $req = "SELECT articles.id, articles.title, articles.author, articles.price, articles.description, articles.image, articles.alt, articles.type, articles.stock, articles.trailer_url, categories.name AS category, articles.id_categories
                FROM articles
                INNER JOIN categories ON categories.id = articles.id_categories
                ORDER BY articles.title ASC";
        return $this->findAll($req);
    }
    
    public function getOneArticle($id) {
        $req = "SELECT articles.id, articles.title, articles.author, articles.price, articles.description, articles.image, articles.alt, articles.type, articles.stock, articles.trailer_url, categories.name AS category, articles.id_categories
                FROM articles
                INNER JOIN categories ON categories.id = articles.id_categories
                WHERE articles.id = ?";
        return $this->findOne($req, [$id]);
    }
    
    public function deleteArticle($id) {
        $req = "DELETE FROM articles WHERE id = ?";
        $stmt = $this->bdd->prepare($req);
        $stmt->execute([$id]);
    }
    public function createArticle($data) {
         $query = "INSERT INTO articles
                  (title, author, price, description, image, alt, type, stock, trailer_url, id_categories)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->bdd->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['author'],
            $data['price'],
            $data['description'],
            $data['image'],
            $data['alt'],
            $data['type'],
            $data['stock'],
            $data['trailer_url'],
            $data['id_categories']
        ]);
    }
    public function getAllByCategorie($category_id) {
        $req = "SELECT articles.id, articles.title, articles.price, articles.description, articles.author, articles.image, articles.alt, articles.type, articles.stock, articles.trailer_url, categories.name AS category
                FROM articles
                INNER JOIN categories ON categories.id = articles.id_categories
                WHERE articles.type = ?";
        return $this->findAll($req, [$category_id]);
    }
    
    public function updateArticle($id, $data) {
       $query = "UPDATE articles
                  SET title = ?, author = ?, price = ?, description = ?, image = ?, alt = ?, type = ?, stock = ?, trailer_url = ?, id_categories = ?
                  WHERE id = ?";
        $stmt = $this->bdd->prepare($query);
        $stmt->execute([
            $data['title'],
            $data['author'],
            $data['price'],
            $data['description'],
            $data['image'],
            $data['alt'],
            $data['type'],
            $data['stock'],
            $data['trailer_url'],
            $data['id_categories'],
            $id
        ]);
    }
    
    public function searchResult($query) {
        $searchTerm = '%' . $query . '%';
       $req = "SELECT articles.id, articles.title, articles.price, articles.description, articles.author, articles.image, articles.alt, articles.type, articles.trailer_url, categories.name AS category
                FROM articles
                INNER JOIN categories ON categories.id = articles.id_categories
                WHERE articles.title LIKE ? OR articles.author LIKE ? OR categories.name LIKE ? OR articles.type LIKE ?
                ORDER BY articles.title ASC";
        return $this->findAll($req, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
}
?>
