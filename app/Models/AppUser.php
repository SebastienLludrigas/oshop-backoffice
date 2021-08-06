<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

class AppUser extends CoreModel
{
    protected $email;

    protected $password;

    protected $firstname;

    protected $lastname;

    protected $role;

    protected $status;

    public function insert()
    {
        $pdo = Database::getPDO();

        $sql = 'INSERT INTO `app_user` (
            `email`,
            `password`,
            `firstname`,
            `lastname`,
            `role`,
            `status`
        ) VALUES (
            :email,
            :password,
            :firstname,
            :lastname,
            :role,
            :status
        )';

        // Je prépare ma requete
        $pdoStatement = $pdo->prepare($sql);

        // Je fait la correspondance entre mes champs & les valeurs
        $pdoStatement->bindValue(':email', $this->email, PDO::PARAM_STR);
        $pdoStatement->bindValue(':password', $this->password, PDO::PARAM_STR);
        $pdoStatement->bindValue(':firstname', $this->firstname, PDO::PARAM_STR);
        $pdoStatement->bindValue(':lastname', $this->lastname, PDO::PARAM_STR);
        $pdoStatement->bindValue(':role', $this->role, PDO::PARAM_STR);
        $pdoStatement->bindValue(':status', $this->status, PDO::PARAM_INT);

        // J'execute la requete
        $executed = $pdoStatement->execute();

        // Puis je compte le nombre de lignes inserée
        $insertedRows = $pdoStatement->rowCount();

        // Si tout c'est bien passé
        if ($executed && $insertedRows === 1) {

            // Je récupere l'id de l'utilisateur nouvellement inséré
            $this->id = $pdo->lastInsertId();

            // Puis je return true
            return true;

        }

        // Il y a eu un soucis: je return false !
        return false;
    }

    public function delete()
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête DELETE
        $sql = 'DELETE FROM app_user WHERE id = :id';

        // Préparation de la requete SQL
        $pdoStatement = $pdo->prepare($sql);

        // J'indique à pdoStatement la correspondance entre mes :truc et la bonne valeur
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);

        // Est ce que ma requete c'est bien executée
        return $pdoStatement->execute();
    }

    public function update()
    {
       // Récupération de l'objet PDO représentant la connexion à la DB
       $pdo = Database::getPDO();

       // Ecriture de la requête UPDATE
       $sql = 'UPDATE `app_user`
               SET
                   email = :email,
                   password = :password,
                   firstname = :firstname,
                   lastname = :lastname,
                   role = :role,
                   status = :status,
                   updated_at = NOW()
               WHERE
                   id = :id';

       // Préparation de la requete SQL
       $pdoStatement = $pdo->prepare($sql);

       // J'indique à pdoStatement la correspondance entre mes :truc et la bonne valeur
       $pdoStatement->bindValue(':email', $this->email, PDO::PARAM_STR);
       $pdoStatement->bindValue(':password', $this->password, PDO::PARAM_STR);
       $pdoStatement->bindValue(':firstname', $this->firstname, PDO::PARAM_STR);
       $pdoStatement->bindValue(':lastname', $this->lastname, PDO::PARAM_STR);
       $pdoStatement->bindValue(':role', $this->role, PDO::PARAM_STR);
       $pdoStatement->bindValue(':status', $this->status, PDO::PARAM_INT);
       $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);

       // Est ce que ma requete c'est bien executée
       $executed = $pdoStatement->execute();

       // Combien de lignes à elle modifié ?
       $updatedRows = $pdoStatement->rowCount();

       // Si mon utilisateur à bien été modifié en base
       if ($executed && $updatedRows === 1) {

           // On retourne VRAI car l'ajout a parfaitement fonctionné
           return true;
           // => l'interpréteur PHP sort de cette fonction car on a retourné une donnée
       }

       // Si on arrive ici, c'est que quelque chose n'a pas bien fonctionné => FAUX
       return false;
    }

    /**
     * Méthode qui permet de récuperer un AppUser en fonction de son ID
     *
     * @param [int] $user_id
     * @return AppUser
     */
    public static function find($user_id)
    {
        // récupérer un objet PDO = connexion à la BDD
        $pdo = Database::getPDO();

        // Mise en palce de la requete SQL
        $sql = 'SELECT * FROM app_user WHERE id = :id';

        // Je prépare ma requête sql
        $pdoStatement = $pdo->prepare($sql);

        // Je fait la correspondance entre mes clé & la bonne valeur
        $pdoStatement->bindValue(':id', $user_id, \PDO::PARAM_STR);

        // J'execute la requete
        $pdoStatement->execute();

        // Je récupere le résultat
        $result = $pdoStatement->fetchObject('App\Models\AppUser');

        return $result;
    }

    /**
     * Méthode qui permet de récuperer tous les AppUser
     *
     * @return AppUser[]
     */
    public static function findAll()
    {
        // Je récupere PDO
        $pdo = Database::getPDO();

        // Je construit ma requete SQL
        $sql = 'SELECT * FROM `app_user`;';

        // Je la "donne" à PDO
        $pdoStatement = $pdo->query($sql);

        // Je demande à pdoStatement de me fournir le résultat sous la forme
        // d'une liste d'instance de AppUser
        $results = $pdoStatement->fetchAll(\PDO::FETCH_CLASS, 'App\Models\AppUser');

        // Je retourne le résultat
        return $results;
    }

    /**
     * Méthode qui permet de récuperer un user en fonction de son email
     *
     * @param [string] $email
     * @return AppUser
     */
    public static function findByEmail($email)
    {
        // récupérer un objet PDO = connexion à la BDD
        $pdo = Database::getPDO();

        // Mise en palce de la requete SQL
        $sql = 'SELECT * FROM app_user WHERE email = :email';

        // Je prépare ma requête sql
        $pdoStatement = $pdo->prepare($sql);

        // Je fait la correspondance entre mes clé & la bonne valeur
        $pdoStatement->bindValue(':email', $email, \PDO::PARAM_STR);

        // J'execute la requete
        $pdoStatement->execute();

        // Je récupere le résultat
        $result = $pdoStatement->fetchObject('App\Models\AppUser');

        return $result;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        // Je m'assure ainsi que à chaque fois qu'on me définit un mot de passe
        // celui-ci sera stocké de manière hashé
        $this->password = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    /**
     * Get the value of firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return  self
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastname
     *
     * @return  self
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @return  self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}