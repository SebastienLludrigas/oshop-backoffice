<?php

// POINT D'ENTRÉE UNIQUE :
// FrontController

// inclusion des dépendances via Composer
// autoload.php permet de charger d'un coup toutes les dépendances installées avec composer
// mais aussi d'activer le chargement automatique des classes (convention PSR-4)
require_once '../vendor/autoload.php';

// Démarrage des sessions partout, pour toutes les pages du site
session_start();

/* ------------
--- ROUTAGE ---
-------------*/


// création de l'objet router
// Cet objet va gérer les routes pour nous, et surtout il va
$router = new AltoRouter();

// le répertoire (après le nom de domaine) dans lequel on travaille est celui-ci
// Mais on pourrait travailler sans sous-répertoire
// Si il y a un sous-répertoire
if (array_key_exists('BASE_URI', $_SERVER)) {
    // Alors on définit le basePath d'AltoRouter
    $router->setBasePath($_SERVER['BASE_URI']);
    // ainsi, nos routes correspondront à l'URL, après la suite de sous-répertoire
}
// sinon
else {
    // On donne une valeur par défaut à $_SERVER['BASE_URI'] car c'est utilisé dans le CoreController
    $_SERVER['BASE_URI'] = '/';
}

// On doit déclarer toutes les "routes" à AltoRouter, afin qu'il puisse nous donner LA "route" correspondante à l'URL courante
// On appelle cela "mapper" les routes
// 1. méthode HTTP : GET ou POST (pour résumer)
// 2. La route : la portion d'URL après le basePath
// 3. Target/Cible : informations contenant
//      - le nom de la méthode à utiliser pour répondre à cette route
//      - le nom du controller contenant la méthode
// 4. Le nom de la route : pour identifier la route, on va suivre une convention
//      - "NomDuController-NomDeLaMéthode"
//      - ainsi pour la route /, méthode "home" du MainController => "main-home"
$router->map(
    'GET',
    '/',
    [
        'controller' => 'MainController',
        'method' => 'home',
    ],
    'main-home'
);
$router->map(
    'GET',
    '/categories',
    [
        'controller' => 'CategoryController',
        'method' => 'list',
    ],
    'category-list'
);
$router->map(
    'GET',
    '/category/add',
    [
        'controller' => 'CategoryController',
        'method' => 'add',
    ],
    'category-add'
);
$router->map(
    'POST',
    '/category/add',
    [
        'controller' => 'CategoryController',
        'method' => 'create',
    ],
    'category-add-post'
);
$router->map(
    'GET',
    '/category/update/[i:category_id]',
    [
        'controller' => 'CategoryController',
        'method' => 'update',
    ],
    'category-update'
);
$router->map(
    'POST',
    '/category/update/[i:category_id]',
    [
        'controller' => 'CategoryController',
        'method' => 'edit',
    ],
    'category-update-post'
);
$router->map(
    'GET',
    '/category/delete/[i:category_id]',
    [
        'controller' => 'CategoryController',
        'method' => 'delete',
    ],
    'category-delete'
);
$router->map(
    'GET',
    '/products',
    [
        'controller' => 'ProductController',
        'method' => 'list',
    ],
    'product-list'
);
$router->map(
    'GET',
    '/product/add',
    [
        'controller' => 'ProductController',
        'method' => 'add',
    ],
    'product-add'
);
$router->map(
    'POST',
    '/product/add',
    [
        'controller' => 'ProductController',
        'method' => 'createEdit',
    ],
    'product-add-post'
);
$router->map(
    'GET',
    '/product/update/[i:product_id]',
    [
        'controller' => 'ProductController',
        'method' => 'update',
    ],
    'product-update'
);
$router->map(
    'POST',
    '/product/update/[i:product_id]',
    [
        'controller' => 'ProductController',
        'method' => 'createEdit',
    ],
    'product-update-post'
);
$router->map(
    'GET',
    '/product/delete/[i:product_id]',
    [
        'controller' => 'ProductController',
        'method' => 'delete',
    ],
    'product-delete'
);
$router->map(
    'GET',
    '/login',
    [
        'controller' => 'UserController',
        'method' => 'login'
    ],
    'user-login'
);
$router->map(
    'POST',
    '/login',
    [
        'controller' => 'UserController',
        'method' => 'checkLogin'
    ],
    'user-login-post'
);
$router->map(
    'GET',
    '/logout',
    [
        'controller' => 'UserController',
        'method' => 'logout'
    ],
    'user-logout'
);
$router->map(
    'GET',
    '/users',
    [
        'controller' => 'UserController',
        'method' => 'list'
    ],
    'user-list'
);
$router->map(
    'GET',
    '/user/add',
    [
        'controller' => 'UserController',
        'method' => 'add'
    ],
    'user-add'
);
$router->map(
    'POST',
    '/user/add',
    [
        'controller' => 'UserController',
        'method' => 'createEdit'
    ],
    'user-add-post'
);
$router->map(
    'GET',
    '/user/update/[i:user_id]',
    [
        'controller' => 'UserController',
        'method' => 'update'
    ],
    'user-update'
);
$router->map(
    'POST',
    '/user/update/[i:user_id]',
    [
        'controller' => 'UserController',
        'method' => 'createEdit'
    ],
    'user-update-post'
);
$router->map(
    'GET',
    '/user/delete/[i:user_id]',
    [
        'controller' => 'UserController',
        'method' => 'delete'
    ],
    'user-delete'
);
$router->map(
    'GET',
    '/front/home',
    [
        'controller' => 'GestionController',
        'method' => 'home'
    ],
    'gestion-home'
);
$router->map(
    'POST',
    '/front/home',
    [
        'controller' => 'GestionController',
        'method' => 'homePost'
    ],
    'gestion-home-post'
);

/* -------------
--- DISPATCH ---
--------------*/

// On demande à AltoRouter de trouver une route qui correspond à l'URL courante
$match = $router->match();

// Ensuite, pour dispatcher le code dans la bonne méthode, du bon Controller
// On délègue à une librairie externe : https://packagist.org/packages/benoclock/alto-dispatcher
// 1er argument : la variable $match retournée par AltoRouter
// 2e argument : le "target" (controller & méthode) pour afficher la page 404
$dispatcher = new Dispatcher($match, '\App\Controllers\ErrorController::err404');

// Je viens préciser le namespace pour tout mes controllers
$dispatcher->setControllersNamespace('\App\Controllers\\');

// Je passe à tous mes controllers la variable $router afin de pouvoir utiliser ses données dans
// chaque views sans avoir besoin de créer une variable globale dans mon CoreController
$dispatcher->setControllersArguments($router);

// Une fois le "dispatcher" configuré, on lance le dispatch qui va exécuter la méthode du controller
$dispatcher->dispatch();