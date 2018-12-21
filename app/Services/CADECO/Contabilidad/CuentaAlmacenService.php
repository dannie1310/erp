<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 11:13 AM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\CuentaAlmacenRepository;

class CuentaAlmacenService
{
    /**
     * @var CuentaAlmacenRepository
     */
    protected $cuentaAlmacen;

    /**
     * CuentaAlmacenService constructor.
     * @param CuentaAlmacenRepository $cuentaAlmacen
     */
    public function __construct(CuentaAlmacenRepository $cuentaAlmacen)
    {
        $this->cuentaAlmacen = $cuentaAlmacen;
    }

    public function index($data) {
        return $this->cuentaAlmacen->all($data);
    }

    public function find($id) {
        return $this->cuentaAlmacen->find($id);
    }

    public function update(array $data, int $id) {
        return $this->cuentaAlmacen->update($data, $id);
    }
}