<?php

namespace App\Controllers;

use App\Models\AppUser;

abstract class CoreController
{
    protected $router;

    // Je créé un constructeur sur mon CoreController
    // Ainsi il sera executé à chaque appel de controller héritant du CoreController
    public function __construct($router)
    {
        // On récupère l'instanciation de AltoRouter depuis index.php
        $this->router = $router;
        
        // Tout ce que je vais écrire ici, sera executé automatiquement
        // pour chaque route de mon application

        // Là je vais pouvoir automatiser l'execution du checkAuthorization.

        // Mais il est necessaire que je connaisse ici, les rôles à authoriser.

        // Pour cela, je doit réaliser un listing... Autrement dit: un ACL

        // La variable $match, contient les infos sur la route courante
        $match = $this->router->match();

        // On récupere le nom de la route courante
        $routeName = $match['name'];

        // On vérifie les ACL
        $this->checkAcl($routeName);

        // On vérifie les token
        $this->checkCSRF($routeName);
    }

    /**
     * Méthode permettant d'afficher du code HTML en se basant sur les views
     *
     * @param string $viewName Nom du fichier de vue
     * @param array $viewVars Tableau des données à transmettre aux vues
     * @return void
     */
    protected function show(string $viewName, $viewVars = [])
    {
        // J'ajoute une clé isConnected pour mes vues
        // Celle-ci va contenir un booléan qui sera à true si l'utilisateur est bien connecté
        // Pour cela, je me base sur la clé 'connectedUser' présente en session
        $viewVars['isConnected'] = isset($_SESSION['connectedUser']) ? true : false;
        $viewVars['isAdmin'] = false;
        $viewVars['connectedUser'] = null;

        // Si l'utilisateur est connecté
        if ($viewVars['isConnected']) {

            // Alors on récupère l'utilisateur connecté
            $user = AppUser::find($_SESSION['connectedUser']);
            

            $viewVars['connectedUser'] = $user;

            // Todo, vérifier que l'utilisateur existe toujours en BDD

            // Puis on récupère son rôle
            $viewVars['isAdmin'] = $user->getRole() == 'admin' ? true : false;
        }

        // On récupère l'instanciation de AltoRouter dans $viewVars pour rendre ses informations
        // disponibles dans toutes nos views
        $viewVars['router'] = $this->router;

        // Comme $viewVars est déclarée comme paramètre de la méthode show()
        // les vues y ont accès
        // ici une valeur dont on a besoin sur TOUTES les vues
        // donc on la définit dans show()
        $viewVars['currentPage'] = $viewName;

        // définir l'url absolue pour nos assets
        $viewVars['assetsBaseUri'] = $_SERVER['BASE_URI'] . 'assets/';
        // définir l'url absolue pour la racine du site
        // /!\ != racine projet, ici on parle du répertoire public/
        $viewVars['baseUri'] = $_SERVER['BASE_URI'];

        // Je définit un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        // J'ajoute mon token dans ma vue
        $viewVars['token'] = $_SESSION['token'];

        // On veut désormais accéder aux données de $viewVars, mais sans accéder au tableau
        // La fonction extract permet de créer une variable pour chaque élément du tableau passé en argument
        extract($viewVars);
        // => la variable $currentPage existe désormais, et sa valeur est $viewName
        // => la variable $assetsBaseUri existe désormais, et sa valeur est $_SERVER['BASE_URI'] . '/assets/'
        // => la variable $baseUri existe désormais, et sa valeur est $_SERVER['BASE_URI']
        // => il en va de même pour chaque élément du tableau

        // $viewVars est disponible dans chaque fichier de vue
        require_once __DIR__.'/../views/layout/header.tpl.php';
        require_once __DIR__.'/../views/'.$viewName.'.tpl.php';
        require_once __DIR__.'/../views/layout/footer.tpl.php';
    }

    /**
     * Méthode qui va vérifier si l'utilisateur authentifié à bien le bon role
     *
     * @param array $authorizedRolesList
     * @return bool
     */
    public function checkAuthorization($authorizedRolesList=[])
    {
        // Si l'utilisateur est connecté
        if (isset($_SESSION['connectedUser'])) {

            // Alors on récupère l'utilisateur connecté
            $user = AppUser::find($_SESSION['connectedUser']);

            // Todo, vérifier que l'utilisateur existe toujours en BDD

            // Puis on récupère son rôle
            $user_role = $user->getRole();

            // Si le rôle de l'utilisateur fait partie des rôles autorisés
            // (fournis en paramètres ($authorizedRolesList))
            // https://www.php.net/manual/fr/function.in-array.php
            if (in_array($user_role, $authorizedRolesList)) {

                // Alors return true
                return true;

            // Sinon c'est que l'utilisateur courant n'a pas la permission d'accéder à la page
            } else {

                // => On envoie le header "403 Forbidden"
                header('HTTP/1.0 403 Forbidden');

                // Puis on affiche la page d'erreur (le template) erreur 403
                $this->show('error/err403');

                // Pour finir, on arrête le script pour que la page demandée ne s'affiche pas
                die();
            }

        // Sinon, si l'utilisateur n'est pas connecté à un compte
        } else {

            // Alors on le redirige vers la page de connexion
            global $router;

            header('Location: '.$router->generate('user-login'));
        }
    }

    public function checkAcl($routeName)
    {
        // Listing des routes & pour chaque route liste des roles autorisés
        $acl = [
            'main-home' => ['admin', 'catalog-manager'],
            'category-list' => ['admin', 'catalog-manager'],
            'category-add' => ['admin', 'catalog-manager'],
            'category-add-post' => ['admin', 'catalog-manager'],
            'category-update' => ['admin', 'catalog-manager'],
            'category-update-post' => ['admin', 'catalog-manager'],
            'category-delete' => ['admin', 'catalog-manager'],
            'product-list' => ['admin', 'catalog-manager'],
            'product-add' => ['admin', 'catalog-manager'],
            'product-add-post' => ['admin', 'catalog-manager'],
            'product-update' => ['admin', 'catalog-manager'],
            'product-update-post' => ['admin', 'catalog-manager'],
            'product-delete' => ['admin', 'catalog-manager'],
            'gestion-home' => ['admin', 'catalog-manager'],
            'gestion-home-post' => ['admin', 'catalog-manager'],
            // 'user-login' => [],          La route est en libre accès
            // 'user-login-post' => [],     La route est en libre accès
            'user-logout' => ['admin', 'catalog-manager'],
            'user-list' => ['admin'],
            'user-add' => ['admin'],
            'user-add-post' => ['admin'],
            'user-update' => ['admin'],
            'user-update-post' => ['admin'],
            'user-delete' => ['admin']
        ];

        // Je commence par vérifier si la route a un ACL de défini
        if (array_key_exists($routeName, $acl)) {

            // Si c'est le cas, je récupere la liste des rôles associés
            $authorizedRolesList = $acl[$routeName];

            // Puis on utilise la méthode checkAuthorization pour vérifier...
            $this->checkAuthorization($authorizedRolesList);
        }
    }

    public function checkCSRF($routeName)
    {
        $csrfTokenToCheckInPost = [
            // 'category-add-post',
            // 'category-update-post',
            'user-login-post',
            'gestion-home-post',
            'category-add-post',
            // etc.
        ];

        $csrfTokenToCheckInGet = [
            'category-delete',
            'product-delete',
            // etc.
        ];

        // Si ma route actuelle necessite la vérif d'un token CSRF en POST
        if (in_array($routeName, $csrfTokenToCheckInPost)) {

            // On récupere le token envoyé en POST
            $token = isset($_POST['token']) ? $_POST['token'] : null;

            // On récupere le token en session
            $sessionToken = isset($_SESSION['token']) ? $_SESSION['token'] : null;

            // On vérifie si les tokens existent bien
            // et si ceux-ci ont bien la même valeur !
            if (empty($token) || empty($sessionToken) || $token !== $sessionToken) {

                // => On envoie le header "403 Forbidden"
                header('HTTP/1.0 403 Forbidden');

                // Puis on affiche la page d'erreur (le template) erreur 403
                $this->show('error/err403');

                // Pour finir, on arrête le script pour que la page demandée ne s'affiche pas
                die();

            } else {

                // On supprime le token en session.
                // Ainsi, on ne pourra pas soumettre le même formulaire plusieurs fois ni même réutiliser
                // le token...
                unset($_SESSION['token']);
            }
        }

        // Si ma route actuelle necessite la vérif d'un token CSRF en GET
        if (in_array($routeName, $csrfTokenToCheckInGet)) {

            // On récupere le token envoyé en GET
            $token = isset($_GET['token']) ? $_GET['token'] : null;

            // On récupere le token en session
            $sessionToken = isset($_SESSION['token']) ? $_SESSION['token'] : null;

            // On vérifie si les tokens existent bien
            // et si ceux-ci ont bien la même valeur !
            if (empty($token) || empty($sessionToken) || $token !== $sessionToken) {

                // => On envoie le header "403 Forbidden"
                header('HTTP/1.0 403 Forbidden');

                // Puis on affiche la page d'erreur (le template) erreur 403
                $this->show('error/err403');

                // Pour finir, on arrête le script pour que la page demandée ne s'affiche pas
                die();

            } else {

                // On supprime le token en session.
                // Ainsi, on ne pourra pas soumettre le même formulaire plusieurs fois ni même réutiliser
                // le token...
                unset($_SESSION['token']);
            }
        }
    }
}
