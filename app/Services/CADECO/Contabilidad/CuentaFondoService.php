<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 04:44 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\CuentaFondoRepository;

class CuentaFondoService
{
    /**
     * @var CuentaFondoRepository
     */
    protected $cuentaFondo;

    /**
     * CuentaFondoService constructor.
     * @param CuentaFondoRepository $cuentaFondo
     */
    public function __construct(CuentaFondoRepository $cuentaFondo)
    {
        $this->cuentaFondo = $cuentaFondo;
    }

    public function paginate($data) {
        return $this->cuentaFondo->paginate($data);
    }

    public function find($id) {
        return $this->cuentaFondo->find($id);
    }

    public function update(array $data, $id) {
        return $this->cuentaFondo->update($data, $id);
    }
}