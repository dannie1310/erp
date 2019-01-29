<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 12:28 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\CuentaEmpresaRepository;

class CuentaEmpresaService
{
    /**
     * @var CuentaEmpresaRepository
     */
    protected $cuentaEmpresa;

    /**
     * CuentaEmpresaService constructor.
     * @param CuentaEmpresaRepository $cuentaEmpresa
     */
    public function __construct(CuentaEmpresaRepository $cuentaEmpresa)
    {
        $this->cuentaEmpresa = $cuentaEmpresa;
    }

    public function paginate($data) {
        return $this->cuentaAlmacen->paginate($data);
    }

    public function find($id) {
        return $this->cuentaAlmacen->find($id);
    }

    public function update(array $data, $id) {
        return $this->cuentaAlmacen->update($data, $id);
    }
}