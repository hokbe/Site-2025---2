<?php
namespace Controllers;

class ContactController {
    public function displayForm() {
        $template = "contactForm.phtml";
        include_once 'views/layout.phtml';
    }
}
?>
