<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/11/18
 * Time: 12:51 PM
 */

namespace App\Repositories;


use App\Contracts\Context;

class ContextSession implements Context
{

    private $auth;

    /**
     * ContextSession constructor.
     */
    public function __construct()
    {
        $this->auth = auth();
    }

    public function setContext(string $database, int $id_obra)
    {
        return $this->auth->claims(['db' => $database, 'obra' => $id_obra])->refresh();
    }

    /**
     * Devuelve el id de la obra sobre la que se está trabajando
     *
     * @return mixed
     */
    public function getIdObra()
    {
        return $this->auth->payload()->get('obra');
    }

    /**
     * Devuelve el nombre de la base de datos sobre la que se está trabajando
     *
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->auth->payload()->get('db');
    }

    /**
     * Nos dice si el contexto esta establecido
     *
     * @return bool
     */
    public function isEstablished(): bool
    {
        return $this->getDatabase() && $this->getIdObra();
    }
}