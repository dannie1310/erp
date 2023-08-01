<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/12/18
 * Time: 05:14 PM
 */

namespace App\Contracts;


interface Context
{
    /**
     * Guarda la información de la base de datos y la obra en la sesión
     *
     * @param string $database
     * @param int $id_obra
     */
    public function setContext(string $database, int $id_obra, string $databaseContpaq);

    /**
     * Borra la información del contexto guardado en la sesión
     * @return mixed
     */
    public function clearContext();
    /**
     * Devuelve el id de la obra sobre la que se está trabajando
     *
     * @return mixed
     */
    public function getIdObra();

    /**
     * Devuelve el nombre de la base de datos sobre la que se está trabajando
     *
     * @return mixed
     */
    public function getDatabase();

    /**
     * Nos dice si el contexto esta establecido
     *
     * @return bool
     */
    public function isEstablished();

    public function getDatabaseContpaq();
}
