<?php

namespace App\Controllers;

use App\Models\Category;

class CategoryController extends CoreController {

    /**
     * Méthode s'occupant de lister les catégories
     *
     * @return void
     */
    public function list()
    {
        /*

        Ancien fonctionnement avec findAll lié à l'instance

        // Création d'une nouvelle instance (donc vide)
        // de la classe Category
        // Il est également possible de dire que $emptyCategory
        // est un objet (ici vide) de type Category
        $emptyCategory = new Category();

        // Appel de la méthode findAll() sur notre instance
        // $emptyCategory de la classe Category.
        // Le résultat est ensuite stocké dans ma variable
        // $allCategories.
        $allCategories = $emptyCategory->findAll();

        */

        /*

        Nouveau fonctionnement avec findAll en static

        Pour info:
        static signifie que la méthode n'est pas liée
        à l'instance mais directement à la classe.

        Lorsque nous réalisons des appels de méthode, nos appels
        sont executé sur des instances. En effet les méthodes
        sont là pour retourner des informations concernant l'instance.
        Ex:
            $myCategory->getName(); // Donne moi le nom de ma catégorie instanciée
            $myCategory->getId(); // Donne moi l'id de ma catégorie instanciée

        Maintenant il est parfois necessaire d'avoir des méthodes qui ne dépendent pas
        d'une instance en particulier.
        Typiquement les méthodes find() et findAll() peuvent fonctionner quelque soit l'instance.
        En effet je n'ai pas besoin de connaitre une information spécifique pour retourner un produit
        ou plusieurs.
        Ex:
            -je n'ai pas besoin de connaitre le nom d'une catégorie en particulier pour toutes les récupérer.

        Du coup la solution est de passer nos méthodes "générique" (autrement dit où une instance n'est pas necessaire)
        en static.

        Ainsi il sera possible d'executer la méthode directement depuis le nom de la classe
        et non plus uniquement depuis l'instance de la classe.

        Ex:
            Category::findAll();

        Il est possible de généraliser ainsi:

            Une méthode qui utilise $this est donc liée à l'instance de la classe: IMPOSSIBLE de la déclarer en static

            Une méthode qui n'UTILISE PAS $this n'est pas liée à l'instance de la classe: il est donc possible (mais pas obligatoire) de la déclarer en static

            Résumé by gregory:
                Toutes les fonctions génériques (indépendantes) peuvent être static

        L'interet final étant qu'on est plus obliger d'instancier une classe vide pour executer une méthode.

        Pour info:
            -> Est l'opérateur de résolution de portée d'une instance
            :: Est l'opérateur de résolution de portée static

        */

        // Récupération de toutes les categories sous la forme d'un tableau indexé
        // A chaque clé de mon tableau se trouve une instance de la classe Category avec des valeurs (propriétés)
        // renseignées en provenance de la table category en base de donnée
        $allCategories = Category::findAll();

        // Execution de la méthode show afin de générer la construction du HTML
        $this->show('category/list', [
            // Je partage à ma vue ma variable $allCategories afin de pouvoir l'exploiter
            // dans ma vue pour construire le HTML correspondant
            'allCategories' => $allCategories
        ]);
    }

    /**
     * Méthode s'occupant d'afficher le formulaire d'ajout d'une nouvelle catégorie
     *
     * Méthode HTTP: GET
     *
     * @return void
     */
    public function add()
    {
        $this->show('category/add');
    }

    /**
     * Méthode s'occupant de traiter l'ajout d'une nouvelle catégorie
     *
     * * Méthode HTTP: POST
     *
     * @return void
     */
    public function create()
    {
        // Puiseque le formulaire à été soumis en POST
        // PHP me propose d'acceder aux données du formulaire via $_POST
        // dd($_POST);

        // On tente de récuperer les données venant du formulaire
        $name = filter_input(INPUT_POST, 'catname', FILTER_SANITIZE_STRING);
        $subtitle = filter_input(INPUT_POST, 'subtitle', FILTER_SANITIZE_STRING);
        $picture = filter_input(INPUT_POST, 'picture', FILTER_SANITIZE_URL);

        //On va vérifier si les champs du formulaire sont bien renseignée
        $errorList = [];

        // Si le nom de la categorie est vide...
        if (empty($name)) {

            $errorList[] = 'Le nom de la catégorie est vide';
        }

        // Si le nom de la categorie est trop petit...
        if (strlen($name) < 3) {

            $errorList[] = 'Le nom de la catégorie doit comporter 3 caractères minimum';
        }

        // Si je n'ai pas passé le filtre
        if ($subtitle === false) {

            $errorList[] = 'Le sous-titre est invalide';
        }

        // Si je n'ai pas passé le filtre
        if ($picture === false) {

            $errorList[] = 'L\'url de l\'image est invalide';
        }

        // TODO continuer les controlles si besoin...

        // Si il n'y a pas d'erreurs dans mon tableau qui les listes...

        if (empty($errorList)) {

            // Je me créé une nouvelle instance de la classe Category
            // (En gros une nouvelle catégorie vide)
            $newCategory = new Category();

            // Je lui ajoute des données
            $newCategory->setName($name);
            $newCategory->setSubtitle($subtitle);
            $newCategory->setPicture($picture);

            // Maintenant que mon instance de la classe Category
            // à bien des données dans ces propriétés
            // Il ne me reste plus qu'a inserer le contenu dans la bonne table en BDD
            $inserted = $newCategory->insert();

            // Si tout c'est bien passé
            if ($inserted) {

                // On va dire à notre serveur WEB de dire au navigateur WEB d'aller sur une autre URL
                // C'est le principe de la "redirection"

                // Pour ce faire il faut que le serveur WEB envoi en réponse de la requete HTTP
                // un header de type "Location:"

                // Un peu crade mais necessaire ici
                global $router;

                // A partir là je vais indiquer à mon navigateur d'aller sur cette URL là
                header('Location: '.$router->generate('category-list'));

                // J'arrete l'execution de ma méthode là
                return;


            // Si au contraire il y a eu un soucis
            } else {

                // On ajoute un message d'erreur.
                $errorList[] = 'La sauvegarde a échoué, merci de retenter';
            }
        }

        // Si au contraire j'ai des erreurs dans mon tableau de listing des erreurs...
        if (!empty($errorList)) {

            // On créé une instance de la classe Category
            $errorCategory = new Category();

            // Je lui ajoute des données en provenance directe du formulaire
            // (donc sans filtre ni test)
            $errorCategory->setName(filter_input(INPUT_POST, 'catname'));
            $errorCategory->setSubtitle(filter_input(INPUT_POST, 'subtitle'));
            $errorCategory->setPicture(filter_input(INPUT_POST, 'picture'));

            // Je charge ma vue
            $this->show('category/add', [
                // Je partage à ma vue ma category
                'errorCategory' => $errorCategory,
                // Je partage à ma vue la liste des erreurs
                'errorList' => $errorList
            ]);
        }
    }

    /**
     * Méthode qui permet d'afficher le formulaire de modification d'une catégorie
     *
     * Méthode HTTP: GET
     *
     * @return void
     */
    public function update($category_id)
    {
        // Je cherche à récuperer la catégorie que l'on souhaite modifier
        $category = Category::find($category_id);

        // En premier lieu, je vérifie si elle existe bien !
        if (empty($category)) {

            // On envoie le header 404
            header('HTTP/1.0 404 Not Found');

            // Puis on gère l'affichage
            return $this->show('error/err404');
        }

        // A partir de maintenant, je sais que ma catégorie existe bien..

        $this->show('category/update', [
            'category' => $category
        ]);
    }

    /**
     * Méthode qui permet de traiter la soumission du formulaire de modification d'une catégorie
     *
     * Méthode HTTP: POST
     *
     * @return void
     */
    public function edit($category_id)
    {
        // Je cherche à récuperer la catégorie que l'on souhaite modifier
        $category = Category::find($category_id);

        // En premier lieu, je vérifie si elle existe bien !
        if (empty($category)) {

            // On envoie le header 404
            header('HTTP/1.0 404 Not Found');

            // Puis on gère l'affichage
            return $this->show('error/err404');
        }

        // On tente de récuperer les données venant du formulaire
        $name = filter_input(INPUT_POST, 'catname', FILTER_SANITIZE_STRING);
        $subtitle = filter_input(INPUT_POST, 'subtitle', FILTER_SANITIZE_STRING);
        $picture = filter_input(INPUT_POST, 'picture', FILTER_SANITIZE_URL);

        //On va vérifier si les champs du formulaire sont bien renseignée
        $errorList = [];

        // Si le nom de la categorie est vide...
        if (empty($name)) {

            $errorList[] = 'Le nom de la catégorie est vide';
        }

        // Si le nom de la categorie est trop petit...
        if (strlen($name) < 3) {

            $errorList[] = 'Le nom de la catégorie doit comporter 3 caractères minimum';
        }

        // Si je n'ai pas passé le filtre
        if ($subtitle === false) {

            $errorList[] = 'Le sous-titre est invalide';
        }

        // Si je n'ai pas passé le filtre
        if ($picture === false) {

            $errorList[] = 'L\'url de l\'image est invalide';
        }

        // TODO continuer les controlles si besoin...

        // Si il n'y a pas d'erreurs dans mon tableau qui les listes...

        if (empty($errorList)) {

            // Je met à jour les données de la catégorie avec celle en provenance du formulaire
            $category->setName($name);
            $category->setSubtitle($subtitle);
            $category->setPicture($picture);

            // J'execute la méthode update de mon instance de la classe
            // category.
            // Celle-ci va devoir executer du SQL pour mettre à jour
            // les données dans ma table category
            $updated = $category->update();

            // Si la mise à jour c'est bien passée...
            if ($updated) {

                // Un peu crade mais necessaire ici
                global $router;

                // A partir là je vais indiquer à mon navigateur d'aller sur cette URL là
                header('Location: '.$router->generate('category-update', ['category_id' => $category->getId()]));

                // J'arrete l'execution de ma méthode là
                return;

            // Si au contraire il y a eu un soucis
            } else {

                // On ajoute un message d'erreur.
                $errorList[] = 'La sauvegarde a échoué, merci de retenter';
            }
        }

        // Si au contraire j'ai des erreurs dans mon tableau de listing des erreurs...
        if (!empty($errorList)) {

            // Je lui ajoute des données en provenance directe du formulaire
            // (donc sans filtre ni test)
            $category->setName(filter_input(INPUT_POST, 'catname'));
            $category->setSubtitle(filter_input(INPUT_POST, 'subtitle'));
            $category->setPicture(filter_input(INPUT_POST, 'picture'));

            // Je charge ma vue
            $this->show('category/update', [
                // Je partage à ma vue ma category
                'category' => $category,
                // Je partage à ma vue la liste des erreurs
                'errorList' => $errorList
            ]);
        }
    }

    public function delete($category_id)
    {
        $category = Category::find($category_id);

        // En premier lieu, je vérifie si elle existe bien !
        if (empty($category)) {

            // On envoie le header 404
            header('HTTP/1.0 404 Not Found');

            // Puis on gère l'affichage
            return $this->show('error/err404');
        }

        $category->delete();

        // Un peu crade mais necessaire ici
        global $router;

        // A partir là je vais indiquer à mon navigateur d'aller sur cette URL là
        header('Location: '.$router->generate('category-list'));
    }
}