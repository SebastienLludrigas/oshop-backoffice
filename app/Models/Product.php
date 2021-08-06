<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

/**
 * Une instance de Product = un produit dans la base de données
 * Product hérite de CoreModel
 */
class Product extends CoreModel {

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $picture;
    /**
     * @var float
     */
    private $price = 0.0;
    /**
     * @var int
     */
    private $rate = 0;
    /**
     * @var int
     */
    private $status = 0;
    /**
     * @var int
     */
    private $brand_id;
    /**
     * @var int
     */
    private $category_id;
    /**
     * @var int
     */
    private $type_id;

    /**
     * Méthode permettant de récupérer un enregistrement de la table Product en fonction d'un id donné
     *
     * @param int $productId ID du produit
     * @return Product
     */
    public static function find($productId)
    {
        // récupérer un objet PDO = connexion à la BDD
        $pdo = Database::getPDO();

        // Mise en palce de la requete SQL
        $sql = 'SELECT * FROM product WHERE id = :id';

        // Je prépare ma requête sql
        $pdoStatement = $pdo->prepare($sql);

        // Je fait la correspondance entre mes clé & la bonne valeur
        $pdoStatement->bindValue(':id', $productId, \PDO::PARAM_STR);

        // J'execute la requete
        $pdoStatement->execute();

        // Je récupere le résultat
        $result = $pdoStatement->fetchObject('App\Models\Product');

        return $result;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table product
     *
     * @return Product[]
     */
    public static function findAll()
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM `product`';
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Product');

        return $results;
    }

    /**
     * Méthode qui permet de récuperer les 5 derniers produits ajouté
     *
     * @return Product[]
     */
    public static function findLastFive()
    {
        // récupérer un objet PDO = connexion à la BDD
        $pdo = Database::getPDO();

        // on écrit la requête SQL pour récupérer les produits
        $sql = '
            SELECT *
            FROM product
            ORDER BY id DESC
            LIMIT 5
            ';

        // query ? exec ?
        // On fait de la LECTURE = une récupration => query()
        // si on avait fait une modification, suppression, ou un ajout => exec
        $pdoStatement = $pdo->query($sql);

        // J'ai plusieurs résultats donc fetchAll avec un FETCH_CLASS pour
        // récuperer des instances...
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Product');

        return $results;
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
        $sql = "INSERT INTO `product` (
                    name,
                    description,
                    picture,
                    price,
                    rate,
                    status,
                    brand_id,
                    category_id,
                    type_id
                ) VALUES (
                    :name,
                    :description,
                    :picture,
                    :price,
                    :rate,
                    :status,
                    :brand_id,
                    :category_id,
                    :type_id
                )";

        // Préparation de la requete SQL
        $pdoStatement = $pdo->prepare($sql);

        // J'indique à pdoStatement la correspondance entre mes :truc et la bonne valeur
        $pdoStatement->bindValue(':name', $this->name, PDO::PARAM_STR);
        $pdoStatement->bindValue(':description', $this->description, PDO::PARAM_STR);
        $pdoStatement->bindValue(':picture', $this->picture, PDO::PARAM_STR);
        $pdoStatement->bindValue(':price', $this->price, PDO::PARAM_INT);
        $pdoStatement->bindValue(':rate', $this->rate, PDO::PARAM_INT);
        $pdoStatement->bindValue(':status', $this->status, PDO::PARAM_INT);
        $pdoStatement->bindValue(':brand_id', $this->brand_id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':type_id', $this->type_id, PDO::PARAM_INT);


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
     * Méthode qui permet de enregistrer mon instance en tant que nouvelle entrée dans ma table
     *
     * @return bool
     */
    public function update()
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête INSERT INTO
        $sql = 'UPDATE `product`
                SET
                    name = :name,
                    description = :description,
                    picture = :picture,
                    price = :price,
                    rate = :rate,
                    status = :status,
                    brand_id = :brand_id,
                    category_id = :category_id,
                    type_id = :type_id,
                    updated_at = NOW()
                WHERE
                    id = :id';

        // Préparation de la requete SQL
        $pdoStatement = $pdo->prepare($sql);

        // J'indique à pdoStatement la correspondance entre mes :truc et la bonne valeur
        $pdoStatement->bindValue(':name', $this->name, PDO::PARAM_STR);
        $pdoStatement->bindValue(':description', $this->description, PDO::PARAM_STR);
        $pdoStatement->bindValue(':picture', $this->picture, PDO::PARAM_STR);
        $pdoStatement->bindValue(':price', $this->price, PDO::PARAM_INT);
        $pdoStatement->bindValue(':rate', $this->rate, PDO::PARAM_INT);
        $pdoStatement->bindValue(':status', $this->status, PDO::PARAM_INT);
        $pdoStatement->bindValue(':brand_id', $this->brand_id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':type_id', $this->type_id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);

        // Est ce que ma requete c'est bien executée
        $executed = $pdoStatement->execute();

        // Combien de lignes à elle modifié ?
        $updatedRows = $pdoStatement->rowCount();

        // Si mon produit à bien été modifié en base
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
        $sql = 'DELETE FROM product WHERE id = :id';

        // Préparation de la requete SQL
        $pdoStatement = $pdo->prepare($sql);

        // J'indique à pdoStatement la correspondance entre mes :truc et la bonne valeur
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);

        // Est ce que ma requete c'est bien executée
        return $pdoStatement->execute();
    }

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
     * Get the value of description
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string  $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the value of picture
     *
     * @return  string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set the value of picture
     *
     * @param  string  $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * Get the value of price
     *
     * @return  float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @param  float  $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get the value of rate
     *
     * @return  int
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set the value of rate
     *
     * @param  int  $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * Get the value of status
     *
     * @return  int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  int  $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get the value of brand_id
     *
     * @return  int
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * Set the value of brand_id
     *
     * @param  int  $brand_id
     */
    public function setBrandId($brand_id)
    {
        $this->brand_id = $brand_id;
    }

    /**
     * Get the value of category_id
     *
     * @return  int
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Set the value of category_id
     *
     * @param  int  $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * Get the value of type_id
     *
     * @return  int
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * Set the value of type_id
     *
     * @param  int  $type_id
     */
    public function setTypeId($type_id)
    {
        $this->type_id = $type_id;
    }

    /**
     * Retrouver les tags associés à mon produit
     *
     * @return Tag[]
     */
    public function getTags()
    {
        // PDO
        $pdo = Database::getPDO();

        $sql = 'SELECT tag.* FROM tag
        INNER JOIN product_has_tag
        ON product_has_tag.tag_id = tag.id
        WHERE product_has_tag.product_id = :product_id;';

        // Préparation de la requete SQL
        $pdoStatement = $pdo->prepare($sql);

        // J'indique à pdoStatement la correspondance entre mes :truc et la bonne valeur
        $pdoStatement->bindValue(':product_id', $this->id, PDO::PARAM_INT);

        // J'execute la requete
        $pdoStatement->execute();

        $result = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Tag');

        return $result;
    }
}
