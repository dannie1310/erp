<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 06:27 PM
 */

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoPolizaContpaq;
use App\Traits\RepositoryTrait;

class TipoPolizaContpaqRepository
{
    use RepositoryTrait;

    /**
     * @var TipoPolizaContpaq
     */
    protected $model;

    /**
     * TipoPolizaContpaqRepository constructor.
     * @param TipoPolizaContpaq $model
     */
    public function __construct(TipoPolizaContpaq $model)
    {
        $this->model = $model;
    }
}