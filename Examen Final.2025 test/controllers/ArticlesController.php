<?php
namespace Controllers;

class ArticlesController {
    public function display() {
        $model = new \Models\Articles();
        $articles = $model->getAllArticles();
        $template = "articles.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function displayOne($id) {
        $model = new \Models\Articles();
        $article = $model->getOneArticle($id);
        $template = "article.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function displayAllByCategorie($category_id) {
        $model = new \Models\Articles();
        $articles = $model->getAllByCategorie($category_id);
        $template = "articles.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function search($query) {
        if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
            $query = trim($_GET['query']);
            $model = new \Models\Articles();
            $articles = $model->searchResult($query);
            $template = "searchResult.phtml";
            include_once 'views/layout.phtml';
        } else {
            header('Location: index.php?route=home');
            exit();
        }
    }
}
?>
