<?php

namespace App\Controllers;

use App\Models\AppUser;

class UserController extends CoreController
{

    public function login()
    {
        $this->show('user/login');
    }

    public function checkLogin()
    {

        // On récupère les valeurs entrées dans le formulaire et on les filtre
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        //On va vérifier si les champs du formulaire sont bien renseignée
        $errorList = [];

        // Si l'utilisateur n'a pas renseigné un des deux champs
        if (empty($email) || empty($password)) {

            $errorList[] = 'Merci de renseigner tout les champs du formulaire';
        }

        // Si il n'y a pas d'erreur pour le moment...
        if (empty($errorList)) {

            // Je demande à mon AppUser de me trouver l'utilisateur
            // qui à l'email spécifié
            $user = AppUser::findByEmail($email);

            // dd(password_hash($password, PASSWORD_DEFAULT));
            // dd(gettype($user->getStatus()));

            // L'utilisateur n'existe pas
            if ($user === false) {

                $errorList[] = 'Utilisateur n\'existe pas';

                // Là je sais que mon utilisateur est bien présent en base
            } else {

                // Je compare le mot de passe de l'utilisateur en base
                // avec le mot de passe saisi dans le formulaire
                if (!password_verify($password, $user->getPassword())) {

                    $errorList[] = 'Mot de passe incorrect';

                    // Si les mot de passe correspondent

                // Je vérifie que le compte de l'utilisateur ne soit pas suspendu
                } else if ($user->getStatus() !== '1') {

                    $errorList[] = 'Votre compte est actuellement suspendu';

                } else {

                    // Je retient en session l'id de l'utilisateur connecté
                    $_SESSION['connectedUser'] = $user->getId();

                    global $router;

                    header('Location: ' . $router->generate('main-home'));
                }
            }
        }

        // Si j'arrive ici dans mon code c'est qu'il y a eu un soucis:
        // email qui n'existe pas
        // compte utilisateur suspendu
        // mot de passe incorrect

        // Du coup je prépare l'affichage du formulaire avec les messages d'erreurs.
        // De plus je vais réafficher à l'utilisateur l'adresse email qu'il a saisi
        $errorAppUser = new AppUser();
        $errorAppUser->setEmail(filter_input(INPUT_POST, 'email'));

        $this->show('user/login', [
            'errorList' => $errorList,
            'errorAppUser' => $errorAppUser
        ]);
    }

    public function logout()
    {
        // Je supprime la clé "connectedUser" présente en session
        // qui symbolise le fait qu'un utilisateur est connecté sur mon site
        // https://www.php.net/manual/en/function.unset.php
        unset($_SESSION['connectedUser']);

        global $router;

        header('Location: ' . $router->generate('user-login'));
    }

    public function list()
    {
        // Je récupere les users en base
        $userList = AppUser::findAll();

        // Je lance l'affichage de la vue
        $this->show('user/list', [
            'allUsers' => $userList
        ]);
    }

    /**
     * Méthode s'occupant d'afficher le formulaire d'ajout d'un nouvel utilisateur
     *
     * @return void
     */
    public function add()
    {
        $this->show('user/create-update', [
            'mode' => 'create'
        ]);
    }

    public function update($user_id)
    {
        // Récupération de l'id de l'utilisateur passé dans l'url
        $user = AppUser::find($user_id);

        // SI le user n'existe pas en base...
        if (empty($user)) {

            // On envoie le header 404
            header('HTTP/1.0 404 Not Found');

            // Puis on gère l'affichage
            return $this->show('error/err404');
        }

        $this->show('user/create-update', [
            'user' => $user,
            'mode' => 'update'
        ]);
    }

    public function createEdit($user_id = null)
    {
        // Si on ne m'a pas fourni d'id user
        if (is_null($user_id)) {
            // C'est que l'on souhaite créer un nouveau user
            $mode = 'create';
        } else {
            // C'est que l'on souhaite modifier un user
            $mode = 'update';

            // Récupération du user dont l'id est passé dans l'url
            $user = AppUser::find($user_id);

            // Si le user n'existe pas en base
            if (empty($user)) {

                // On envoie le header 404
                header('HTTP/1.0 404 Not Found');

                // Puis on gère l'affichage
                return $this->show('error/err404');
            }
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        // On ne récupère les valeurs des inputs password et conf_password que si l'on 
        // est mode create
        if ($mode === 'create') {
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $conf_password = filter_input(INPUT_POST, 'conf_password', FILTER_SANITIZE_STRING);
        }

        $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT);

        // Pour le moment, je n'ai pas d'erreurs
        $errorList = [];

        if (empty($email)) {

            $errorList[] = 'Merci de renseigner l\'email';
        }

        // Je demande à PHP de tester si mon adresse email est correctement formatée
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $errorList[] = 'Adresse email incorrecte';
        }

        // On ne vérifie les valeurs des inputs password et conf_password que si l'on 
        // est mode create
        if ($mode === 'create') {
            if (empty($password)) {

                $errorList[] = 'Merci de renseigner le mot de passe';
            }

            if (strlen($password) < 8) {

                $errorList[] = 'Le mot de passe doit contenir au moins 8 caractères';
            }

            $nbr_minuscule = 0;
            $nbr_majuscule = 0;
            $nbr_chiffre = 0;
            $nbr_spechar = 0;

            // Je parcour chaque caractères de mon mot de passe pour le tester
            for ($caractere_num = 0; $caractere_num < strlen($password); $caractere_num++) {

                // Je cible chaque caractère l'un après l'autre
                $testedChar = $password[$caractere_num];

                // Si c'est un entier
                if (is_numeric($testedChar)) {

                    $nbr_chiffre++;

                    // Je passe à l'itération suivante (en gros je continue ma boucle)
                    continue;
                }

                // Si mon caractère est dans la liste des caractères spéciaux autorisé
                if (in_array($testedChar, ['_', '-', '|', '%', '&', ' ', '*', '='])) {

                    $nbr_spechar++;
                    continue;
                }

                // Si mon caractères en minuscule est toujours le même
                // c'est qu'il était déjà en minuscule
                if (strtolower($testedChar) == $testedChar) {

                    $nbr_minuscule++;
                    continue;
                }

                // Si mon caractères en majuscule est toujours le même
                // c'est qu'il était déjà en majuscule
                if (strtoupper($testedChar) == $testedChar) {

                    $nbr_majuscule++;
                    continue;
                }
            }

            if (
                $nbr_minuscule == 0
                || $nbr_majuscule == 0
                || $nbr_chiffre == 0
                || $nbr_spechar == 0
            ) {

                $errorList[] = 'Le mot de passe doit contenir au moins 1entier, 1 lettre en majuscule, 1 lettre en minuscule, 1 caractères spécial';
            }

            if (empty($conf_password)) {

                $errorList[] = 'Merci de renseigner la confirmation du mot de passe';
            }

            if ($password !== $conf_password) {

                $errorList[] = 'Les mot de passe ne correspondent pas';
            }
        }

        if (empty($firstname)) {

            $errorList[] = 'Merci de renseigner le prénom de l\'utilisateur';
        }

        if (empty($lastname)) {

            $errorList[] = 'Merci de renseigner le nom de l\'utilisateur';
        }

        if (empty($role)) {

            $errorList[] = 'Merci de selectionner un rôle';
        }

        if (!in_array($role, ['admin', 'catalog-manager'])) {

            $errorList[] = 'Rôle inconnu';
        }

        if (empty($status)) {

            $errorList[] = 'Merci de selectionner un status';
        }

        if (!in_array($status, ['1', '2'])) {

            $errorList[] = 'Status inconnu';
        }

        // Si jusque là je n'ai pas d'erreur...
        if (empty($errorList)) {

            // Si on est en mode création
            if ($mode == 'create') {

                // On instancie un nouveau user
                $user = new AppUser();
            }

            $user->setEmail($email);

            if ($mode === 'create') {
                $user->setPassword($password);
            }

            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setRole($role);
            $user->setStatus($status);

            // On insère ou on met à jour le user via la méthode save qui permettra de sélectionner la méthode appropriée
            $saved = $user->save();

            if ($saved) {

                global $router;

                header('Location: ' . $router->generate('user-list'));

                return;
            } else {

                $errorList[] = 'Une erreur est survenue, merci de reessayer';
            }
        }

        // Si j'ai des erreurs...
        if (!empty($errorList)) {

            // Je créé mon utilisateur en erreur
            $user = new AppUser();

            // Je lui fourni les données en provenance direct du POST (sans filtre)
            $user->setEmail(filter_input(INPUT_POST, 'email'));

            if ($mode === 'create') {
                $user->setPassword(filter_input(INPUT_POST, 'password'));
            }

            $user->setFirstname(filter_input(INPUT_POST, 'firstname'));
            $user->setLastname(filter_input(INPUT_POST, 'lastname'));
            $user->setRole(filter_input(INPUT_POST, 'role'));
            $user->setStatus(filter_input(INPUT_POST, 'status'));

            $this->show('user/create-update', [
                'errorList' => $errorList,
                'errorUser' => $user,
                'mode'      => $mode
            ]);
        }
    }

    public function delete($user_id)
    {
        $user = AppUser::find($user_id);

        // En premier lieu, je vérifie si elle existe bien !
        if (empty($user)) {

            // On envoie le header 404
            header('HTTP/1.0 404 Not Found');

            // Puis on gère l'affichage
            return $this->show('error/err404');
        }

        $user->delete();

        // Un peu crade mais necessaire ici
        global $router;

        // A partir là je vais indiquer à mon navigateur d'aller sur cette URL là
        header('Location: ' . $router->generate('user-list'));
    }
}
