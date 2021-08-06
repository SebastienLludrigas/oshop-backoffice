<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

class Category extends CoreModel {

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $subtitle;
    /**
     * @var string
     */
    private $picture;
    /**
     * @var int
     */
    private $home_order;

    /**
     * Get the value of name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the value of subtitle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set the value of subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * Get the value of picture
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set the value of picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * Get the value of home_order
     */
    public function getHomeOrder()
    {
        return $this->home_order;
    }

    /**
     * Set the value of home_order
     */
    public function setHomeOrder($home_order)
    {
        $this->home_order = $home_order;
    }

    /**
     * Méthode permettant de récupérer un enregistrement de la table Category en fonction d'un id donné
     *
     * @param int $categoryId ID de la catégorie
     * @return Category
     */
    public static function find($categoryId)
    {
        // récupérer un objet PDO = connexion à la BDD
        $pdo = Database::getPDO();

        // Mise en palce de la requete SQL
        $sql = 'SELECT * FROM category WHERE id = :id';

        // Je prépare ma requête sql
        $pdoStatement = $pdo->prepare($sql);

        // Je fait la correspondance entre mes clé & la bonne valeur
        $pdoStatement->bindValue(':id', $categoryId, \PDO::PARAM_STR);

        // J'execute la requete
        $pdoStatement->execute();

        // Je récupere le résultat
        $result = $pdoStatement->fetchObject('App\Models\Category');

        return $result;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table category
     *
     * @return Category[]
     */
    public static function findAll()
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM `category`';
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Category');

        return $results;
    }

    /**
     * Récupérer les 5 catégories mises en avant sur la home
     *
     * @return Category[]
     */
    public static function findAllHomepage()
    {
        $pdo = Database::getPDO();
        $sql = '
            SELECT *
            FROM category
            WHERE home_order > 0
            ORDER BY home_order ASC
        ';
        $pdoStatement = $pdo->query($sql);
        $categories = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Category');

        return $categories;
    }

    public static function resetHomeOrder()
    {
        $pdo = Database::getPDO();

        // Ma requete vient remettre à 0 tout les champs home_order de ma table
        $sql = 'UPDATE category SET home_order = 0;';

        return $pdo->exec($sql);
    }

    /**
     * Méthode qui permet de enregistrer mon instance en tant que nouvelle entrée dans ma table
     *
     * @return bool
     */
    public function insert()
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête INSERT INTO
        $sql = "INSERT INTO `category` (name, subtitle, picture)
            VALUES (:name, :subtitle, :picture)";

        // Préparation de la requete SQL
        $pdoStatement = $pdo->prepare($sql);

        // J'indique à pdoStatement la correspondance entre mes :truc et la bonne valeur
        $pdoStatement->bindValue(':name', $this->name, PDO::PARAM_STR);
        $pdoStatement->bindValue(':subtitle', $this->subtitle, PDO::PARAM_STR);
        $pdoStatement->bindValue(':picture', $this->picture, PDO::PARAM_STR);

        // Est ce que ma requete c'est bien executée
        $executed = $pdoStatement->execute();

        // Combien de lignes à elle inserée ?
        $insertedRows = $pdoStatement->rowCount();

        // Si ma catégorie à bien été inserée en base
        if ($executed && $insertedRows === 1) {

            // Alors on récupère l'id auto-incrémenté généré par MySQL
            // Ainsi je peut completer l'instance de ma classe Category
            $this->id = $pdo->lastInsertId();

            // On retourne VRAI car l'ajout a parfaitement fonctionné
            return true;
            // => l'interpréteur PHP sort de cette fonction car on a retourné une donnée
        }

        // Si on arrive ici, c'est que quelque chose n'a pas bien fonctionné => FAUX
        return false;
    }

    /**
     * Méthode permettant de mettre à jour un enregistrement dans la table category
     * L'objet courant doit contenir l'id, et toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function update()
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête UPDATE
        $sql = 'UPDATE `category`
            SET
                name = :name,
                subtitle = :subtitle,
                picture = :picture,
                home_order = :home_order,
                updated_at = NOW()
            WHERE id = :id';

        // Préparation de la requete SQL
        $pdoStatement = $pdo->prepare($sql);

        // J'indique à pdoStatement la correspondance entre mes :truc et la bonne valeur
        $pdoStatement->bindValue(':name', $this->name, PDO::PARAM_STR);
        $pdoStatement->bindValue(':subtitle', $this->subtitle, PDO::PARAM_STR);
        $pdoStatement->bindValue(':picture', $this->picture, PDO::PARAM_STR);
        $pdoStatement->bindValue(':home_order', $this->home_order, PDO::PARAM_STR);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);

        // Est ce que ma requete c'est bien executée
        $executed = $pdoStatement->execute();

        // Combien de lignes à elle modifié ?
        $updatedRows = $pdoStatement->rowCount();

        // Si ma catégorie à bien été mise à jour en base
        if ($executed && $updatedRows === 1) {

            // On retourne VRAI car l'ajout a parfaitement fonctionné
            return true;
            // => l'interpréteur PHP sort de cette fonction car on a retourné une donnée
        }

        // Si on arrive ici, c'est que quelque chose n'a pas bien fonctionné => FAUX
        return false;
    }

    public function delete()
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête DELETE
        $sql = 'DELETE FROM category WHERE id = :id';

        // Préparation de la requete SQL
        $pdoStatement = $pdo->prepare($sql);

        // J'indique à pdoStatement la correspondance entre mes :truc et la bonne valeur
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);

        // Est ce que ma requete c'est bien executée
        return $pdoStatement->execute();
    }
}
