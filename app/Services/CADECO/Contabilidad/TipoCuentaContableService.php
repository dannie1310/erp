<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 11:13 AM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\TipoCuentaContableRepository;

class TipoCuentaContableService
{
    /**
     * @var TipoCuentaContableRepository
     */
    protected $tipoCuentaContable;

    /**
     * TipoCuentaContableService constructor.
     * @param TipoCuentaContableRepository $tipoCuentaContable
     */
    public function __construct(TipoCuentaContableRepository $tipoCuentaContable)
    {
        $this->tipoCuentaContable = $tipoCuentaContable;
    }

    public function index() {
        return $this->tipoCuentaContable->all();
    }
}