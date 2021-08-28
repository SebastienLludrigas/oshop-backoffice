<?php

namespace App\Controllers;

use App\Models\CoreModel;
use App\Models\Product;

class ProductController extends CoreController {

    /**
     * Méthode s'occupant de lister les produits
     *
     * @return void
     */
    public function list()
    {
        $allProducts = Product::findAll();

        // dd($allProducts[0]->getTags());

        $this->show('product/list', [
            'allProducts' => $allProducts
        ]);
    }

    /**
     * Méthode s'occupant d'afficher le formulaire d'ajout d'un nouveau produit
     *
     * @return void
     */
    public function add()
    {
        $this->show('product/create-update', [
            'mode' => 'create'
        ]);
    }

    /**
     * Méthode s'occupant d'afficher le formulaire de modification d'un produit
     *
     * @return void
     */
    public function update($product_id)
    {
        // Je récupere le produit dont l'id est passé dans l'url
        $product = Product::find($product_id);

        // SI le produit n'existe pas en base...
        if (empty($product)) {

            // On envoie le header 404
            header('HTTP/1.0 404 Not Found');

            // Puis on gère l'affichage
            return $this->show('error/err404');
        }

        $this->show('product/create-update', [
            'product' => $product,
            'mode' => 'update'
        ]);
    }

    /**
     * Méthode s'occupant de traiter l'ajout d'un nouveau produit
     *
     * Méthode HTTP: POST
     *
     * @return void
     */
    public function createEdit($product_id = null)
    {
        // Si on ne m'a pas fourni d'id de produit
        if (is_null($product_id)) {

            // C'est qu'on souhaite créer le produit
            $mode = 'create';

        // Sinon...
        } else {

            // C'est qu'on souhaite modifier un produit !
            $mode = 'update';

            // Je récupere le produit dont l'id est passé dans l'url
            $product = Product::find($product_id);

            // SI le produit n'existe pas en base...
            if (empty($product)) {

                // On envoie le header 404
                header('HTTP/1.0 404 Not Found');

                // Puis on gère l'affichage
                return $this->show('error/err404');
            }
        }

        // Comme le formulaire à été soumis en POST
        // PHP me propose d'acceder aux données du formulaire via $_POST
        // dd($_POST);

        // On tente de récuperer les données venant du formulaire
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $picture = filter_input(INPUT_POST, 'picture', FILTER_SANITIZE_URL);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT);
        $brand_id = filter_input(INPUT_POST, 'brandid', FILTER_SANITIZE_NUMBER_INT);
        $category_id = filter_input(INPUT_POST, 'categoryid', FILTER_SANITIZE_NUMBER_INT);
        $type_id = filter_input(INPUT_POST, 'typeid', FILTER_SANITIZE_NUMBER_INT);

        //On va vérifier si les champs du formulaire sont bien renseignée
        $errorList = [];

        // Pour le "name", faut vérifier si la chaîne est présente *et* si elle
        // a passé le filtre de validation.
        if (empty($name)) {
            $errorList[] = 'Le nom est vide';
        }
        if ($name === false) {
            $errorList[] = 'Le nom est invalide';
        }
        // Pareil pour la "description".
        if (empty($description)) {
            $errorList[] = 'La description est vide';
        }
        if ($description === false) {
            $errorList[] = 'La description est invalide';
        }
        // Pour l'URL de l'image "picture", le filtre vérifie forcément sa présence aussi.
        if ($picture === false) {
            $errorList[] = 'L\'URL d\'image est invalide';
        }
        // Etc.
        if ($price === false) {
            $errorList[] = 'Le prix est invalide';
        }
        if ($status === false) {
            $errorList[] = 'Le statut est invalide';
        }
        if (empty($brand_id)) {
            $errorList[] = 'La marque est invalide';
        }
        if (empty($category_id)) {
            $errorList[] = 'La catégorie est invalide';
        }
        if (empty($type_id)) {
            $errorList[] = 'Le type est invalide';
        }
        // NOTE: clairement, ces validations ne sont pas suffisantes
        // (ex. relations par clé étrangère : comment vérifier que les autres ressources
        // existent vraiment ?)

        // TODO continuer les controlles si besoin...



        // Si il n'y a pas d'erreurs dans mon tableau qui les listes...

        if (empty($errorList)) {

            // En mode de création, on souhaite créé un nouveau produit
            if ($mode == 'create') {

                // On instancie donc un nouveau modèle (vide) de type Product.
                $product = new Product();
            }

            // Si je ne suis pas en mode de création, c'est que j'ai déjà une instance
            // du produit que je souhaite modifier.

            // On met à jour les propriétés de l'instance.
            $product->setName($name);
            $product->setDescription($description);
            $product->setPicture($picture);
            $product->setPrice($price);
            $product->setStatus($status);
            $product->setBrandId($brand_id);
            $product->setCategoryId($category_id);
            $product->setTypeId($type_id);

            // On insère ou on met à jour le produit via la méthode save qui permettra de sélectionner la méthode appropriée
            $queryExecuted = $product->save();

            // Si tout c'est bien passé
            if ($queryExecuted) {

                // On va dire à notre serveur WEB de dire au navigateur WEB d'aller sur une autre URL
                // C'est le principe de la "redirection"

                // Pour ce faire il faut que le serveur WEB envoi en réponse de la requete HTTP
                // un header de type "Location:"

                // Un peu crade mais necessaire ici
                global $router;

                if ($mode == 'create') {
                    // A partir là je vais indiquer à mon navigateur d'aller sur cette URL là
                    header('Location: '.$router->generate('product-list'));

                } else {

                    // A partir là je vais indiquer à mon navigateur d'aller sur cette URL là
                    header('Location: '.$router->generate('product-update', ['product_id' => $product->getId()]));
                }

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

            // On réaffiche le formulaire, mais pré-rempli avec les (mauvaises) données
            // proposées par l'utilisateur.
            // Pour ce faire, on instancie un modèle Product, qu'on passe au template.
            $product = new Product();
            $product->setName(filter_input(INPUT_POST, 'name'));
            $product->setDescription(filter_input(INPUT_POST, 'description'));
            $product->setPicture(filter_input(INPUT_POST, 'picture'));
            $product->setPrice(filter_input(INPUT_POST, 'price'));
            $product->setStatus(filter_input(INPUT_POST, 'status'));

            // Je charge ma vue
            $this->show('product/create-update', [
                // Je partage à ma vue ma category
                'product' => $product,
                // Je partage à ma vue la liste des erreurs
                'errorList' => $errorList,
                'mode' => $mode
            ]);
        }
    }

    public function delete($product_id)
    {
        $product = Product::find($product_id);

        // En premier lieu, je vérifie si elle existe bien !
        if (empty($product)) {

            // On envoie le header 404
            header('HTTP/1.0 404 Not Found');

            // Puis on gère l'affichage
            return $this->show('error/err404');
        }

        $product->delete();

        // Un peu crade mais necessaire ici
        global $router;

        // A partir là je vais indiquer à mon navigateur d'aller sur cette URL là
        header('Location: '.$router->generate('product-list'));
    }
}