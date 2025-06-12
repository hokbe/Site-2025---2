Tales & Tape
Tales & Tape est une boutique en ligne spécialisée dans la vente de livres et de films. Ce projet PHP suit une architecture MVC et intègre des fonctionnalités avancées telles que :

Gestion des articles, utilisateurs, commandes et panier.
Interface administrateur (dashboard) pour ajouter, modifier ou supprimer des articles, gérer les utilisateurs et les commandes.
Système de panier avec mise à jour des quantités et suppression via AJAX.
Thème clair/sombre configurable et menu responsive.
API AJAX pour la recherche d'articles et pour la gestion dynamique du panier et des commandes.
Suppression automatique des historiques de commande de plus de 6 mois (via endpoint API / tâche CRON).
Possibilité pour l'utilisateur de supprimer son compte (sauf s'il s'agit d'un administrateur).
Table des Matières
Installation
Configuration
Structure du Projet
Fonctionnalités
Utilisation des API et AJAX

/votre-projet
├─ api/                   # Endpoints API (e.g. search.php, deleteOrderAjax.php, deleteOldOrders.php)
├─ config/
│   └─ config.php         # Configuration de la base de données et des sessions
├─ controllers/           # Contrôleurs pour gérer la logique métier
│   ├─ AdminController.php
│   ├─ ArticlesController.php
│   ├─ CartController.php
│   ├─ ContactController.php
│   ├─ HomeController.php
│   ├─ RegisterController.php
│   └─ UserController.php
├─ models/                # Modèles pour l'accès aux données
│   ├─ Articles.php
│   ├─ Cart.php
│   ├─ Countries.php
│   ├─ Database.php
│   ├─ Order.php
│   └─ Users.php
├─ Public/
│   ├─ css/
│   │   └─ style.css      # Feuille de style principale
│   ├─ images/            # Images du site et produits
│   └─ js/
│       └─ main.js        # Script JavaScript regroupant les interactions AJAX, le thème, etc.
├─ views/                 # Vues en .phtml pour la présentation
│   ├─ adminDashboard.phtml
│   ├─ addArticleForm.phtml
│   ├─ articles.phtml
│   ├─ article.phtml
│   ├─ cart.phtml
│   ├─ contactForm.phtml
│   ├─ home.phtml
│   ├─ login.phtml
│   ├─ profile.phtml
│   ├─ editProfile.phtml
│   └─ userProfile.phtml
├─ index.php              # Front controller, gestion des routes
└─ README.md
Fonctionnalités
Pour l'Utilisateur
Inscription et Connexion :
Inscription sécurisée (hachage des mots de passe via password_hash) et connexion avec validation (utilisation de password_verify).

Gestion du Profil :
Affichage complet des informations utilisateur (username, nom, prénom, email, adresse, code postal, ville, pays par nom et date de création).
Possibilité de modifier ses informations (sauf le mot de passe) via un formulaire d'édition accessible depuis son espace "Mon compte".

Panier et Commandes :
Ajout d'articles au panier et conversion du panier en commande lors du paiement.
Mise à jour des quantités et suppression d'articles dans le panier via AJAX, pour une expérience sans rechargement de page.

Pour l'Administrateur
Dashboard Administrateur :
Gestion complète des articles (ajouter, modifier, supprimer) via des formulaires avec upload d'images.
Consultation et gestion des utilisateurs avec option "Voir profile" (affichage des informations complètes de l'utilisateur, y compris le pays par nom) et la possibilité de supprimer un compte (sauf admin).
Gestion des commandes, modification du statut et suppression de commandes via AJAX.

Suppression Automatique d'Historiques :
Un endpoint API permet de supprimer automatiquement les commandes de plus de 6 mois (exécuté via une tâche CRON).

Interactivité et AJAX
AJAX pour le Panier :

Ajout d’un produit au panier sans rechargement de page, avec affichage d’une notification popup.
Mise à jour des quantités du panier via AJAX avec des boutons d'incrémentation et de décrémentation, évitant le rechargement et la remontée en haut de page.
Suppression d’un article du panier via AJAX, avec synchronisation de la session et de la base de données.
AJAX pour la Suppression des Commandes :
Un bouton dans le dashboard permet de supprimer une commande via une requête AJAX, retirant dynamiquement la ligne correspondante du tableau.

Recherche AJAX (optionnelle) :
Un endpoint API permet d’effectuer des recherches d’articles et de renvoyer les résultats au format JSON pour un affichage dynamique.

Détails Techniques
Utilisation d'AJAX et de l'API JavaScript
Objectifs :
Offrir une interface réactive et moderne sans rechargement complet des pages.
Permettre une mise à jour en temps réel du panier et la suppression de commandes dans le dashboard.

Implémentation :

Le fichier Public/js/main.js centralise toutes les interactions AJAX, y compris l’ajout au panier, la mise à jour des quantités et la suppression d’articles et de commandes.
L'utilisation de fetch() permet d'envoyer des requêtes asynchrones aux endpoints définis dans le front controller (par exemple, updateQuantityAjax, removeFromCartAjax, deleteOrderAjax).
La réponse est traitée en JSON pour afficher des notifications et mettre à jour dynamiquement le DOM.
Sécurité pour les Inscriptions et les Mots de Passe
Hachage des Mots de Passe :
Les mots de passe sont hachés avec password_hash() (BCRYPT), ce qui garantit que même en cas de compromission de la base, les mots de passe restent protégés.

Vérification avec password_verify() :
Lors de la connexion, le mot de passe entré par l'utilisateur est comparé au hash stocké en base à l'aide de password_verify().

Utilisation de requêtes préparées :
Toutes les interactions avec la base de données se font via PDO avec des requêtes préparées, ce qui empêche les injections SQL.

Sanitization des Entrées :
Les données utilisateur sont nettoyées avec htmlspecialchars() et filter_var(), afin de prévenir les attaques XSS et d'assurer l'intégrité des données.

Gestion de Session :
Les sessions sont démarrées et vérifiées dans les contrôleurs pour s'assurer que seules les actions autorisées sont exécutées (par exemple, l'édition de profil ou la gestion du panier).

