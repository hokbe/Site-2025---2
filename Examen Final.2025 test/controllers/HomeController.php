<?php
namespace Controllers;

class HomeController {
    public function display() {
        $template = "home.phtml";
        include_once 'views/layout.phtml';
    }
}
?>
