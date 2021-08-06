<?php

namespace App\Models;

// Classe mère de tous les Models
// On centralise ici toutes les propriétés et méthodes utiles pour TOUS les Models
/**
 * Cette classe, CoreModel, n'est pas là pour "représenter" une table
 * en base de donnée.
 * Son seul et unique but est bien d'être étendue (extends...)
 * Il est donc "acté" que cette classe ne sera jamais instanciée
 * Nous n'aurons jamais besoin de faire un new CoreModel.
 * C'est ainsi et PHP accepte bien cette idée là !
 */
abstract class CoreModel {
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $created_at;
    /**
     * @var string
     */
    protected $updated_at;


    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the value of created_at
     *
     * @return  string
     */
    public function getCreatedAt() : string
    {
        return $this->created_at;
    }

    /**
     * Get the value of updated_at
     *
     * @return  string
     */
    public function getUpdatedAt() : string
    {
        return $this->updated_at;
    }

    public function save()
    {
        // Si la propriété id de mon objet est vide
        if (empty($this->id)) {

            // C'est que objet ne provient pas de la BDD

            // il est donc à inserer en base.
            return $this->insert();

        // sinon, comme la propriété id de mon objet n'est pas vide
        } else {

            // c'est que mon objet provient de la BDD

            // il faut donc réaliser une mise à jour de la ligne
            return $this->update();
        }

        // A chaque appel de la méthode save(), celle-ci va "choisir" quel méthode
        // insert ou update elle doit executer sur la classe enfant.
    }

    // Puisque CoreModel est une classe abstraite, elle à la possiblité
    // d'obliger ces enfants à avoir des méthodes...

    // J'oblige mes enfants à avoir une méthode insert
    abstract protected function insert();

    // J'oblige mes enfants à avoir une méthode update
    abstract protected function update();

    // J'oblige mes enfants à avoir une méthode delete
    abstract public function delete();

    // J'oblige mes enfants à avoir une méthode find static (et qui prend un argument)
    abstract public static function find($entity_id);

    // J'oblige mes enfants à avoir une méthode findAll static
    abstract public static function findAll();
}

