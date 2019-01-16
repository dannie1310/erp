<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:18 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\CuentaGeneralRepository;

class CuentaGeneralService
{
    /**
     * @var CuentaGeneralRepository
     */
    protected $cuentaGeneral;

    /**
     * CuentaGeneralService constructor.
     * @param CuentaGeneralRepository $cuentaGeneral
     */
    public function __construct(CuentaGeneralRepository $cuentaGeneral)
    {
        $this->cuentaGeneral = $cuentaGeneral;
    }

    public function paginate($data) {
        return $this->cuentaGeneral->paginate($data);
    }
}