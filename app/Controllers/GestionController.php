<?php

namespace App\Controllers;

use App\Models\Category;

class GestionController extends CoreController
{
    public function home()
    {
        // Je récupere toutes les categoriees en base
        $allCategories = Category::findAll();

        $this->show('gestion/home', [
            // Je les partages à ma vue
            'allCategories' => $allCategories
        ]);
    }

    public function homePost()
    {

        // dd($_POST);
        // Pour le moment, aucune erreur...
        $errorList = [];

        // Je récupere tout les emplacements selectionné
        // c'est un tableau sous la forme
        // index (= emplacement - 1) => id de la catégorie associée
        $allEmplacements = $_POST['emplacement'];

        // Si je n'ai pas 5 emplacements c'est qu'il y a un soucis...
        if (count($allEmplacements) != 5) {

            $errorList[] = 'Merci de selectionner les 5 categories';
        }

        // Made by me
        // On vérifie qu'il n'y a pas 2 fois la même catégorie dans le home front
        $tempArray = [];

        foreach ($allEmplacements as $index => $value) {

            if (in_array($value, $tempArray)) {

                $errorList[] = 'Merci de selectionner une catégorie différente pour chaque emplacement';

                // break permet de stoper l'execution d'une boucle.
                // on sort donc de la boucle directement (sans la terminer)
                break;
            }

            $tempArray[] = $value;
        }

        // Si il n'y pas d'erreurs pour le moment...
        if (empty($errorList)) {

            // Puisque je vais redéfinir l'intégralité des emplacements pour mes catégories...
            // Je vais pouvoir commencer par tout réinitialiser.
            Category::resetHomeOrder();

            // Je vais pouvoir parcourir, chaque emplacement
            foreach ($allEmplacements as $index => $categorie_id) {

                // Mon numero d'emplacement correspond à mon index + 1
                // (puisque mes emplacements commencent à 1 et mon index à 0)
                $numero_emplacement = $index + 1;

                // Je trouve la catégorie ciblée pour mon emplacement
                $targetedCategory = Category::find($categorie_id);

                // Si pour une raison un peu louche la catégorie n'existe pas...
                if (empty($targetedCategory)) {

                    $errorList[] = 'Merci de selectionner les 5 categories';

                    // break permet de stoper l'execution d'une boucle.
                    // on sort donc de la boucle directement (sans la terminer)
                    break;
                }

                // var_dump('toto');
                // J'attribut à ma catégorie son home order (= son emplacement sur la home)
                $targetedCategory->setHomeOrder($numero_emplacement);
                // J'enregistre en base
                $targetedCategory->save();
            }
        }

        if (empty($errorList)) {

            global $router;  

            header('Location: '.$router->generate('main-home'));

            return;
        }

        // Je récupere toutes les categoriees en base
        $allCategories = Category::findAll();

        $this->show('gestion/home', [
            'errorList' => $errorList,
            'allCategories' => $allCategories
        ]);
    }

}