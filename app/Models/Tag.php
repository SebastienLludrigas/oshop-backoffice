<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

class Tag extends CoreModel
{
    protected $name;

    /**
     * Ajout un tag
     *
     * @return void
     */
    protected function insert()
    {

    }

    /**
     * MAJ un tag
     *
     * @return void
     */
    protected function update()
    {

    }

    /**
     * Supprime un tag
     *
     * @return void
     */
    public function delete()
    {

    }

    /**
     * Trouve 1 tag en fonction de son ID
     *
     * @param [int] $tag_id
     * @return Tag
     */
    public static function find($tag_id)
    {

    }

    /**
     * Retrouve tout les tags
     *
     * @return Tag[]
     */
    public static function findAll()
    {

    }

    /**
     * Retrouver les produits associé à mon tag
     *
     * @return Product[]
     */
    public function getProducts()
    {

    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}