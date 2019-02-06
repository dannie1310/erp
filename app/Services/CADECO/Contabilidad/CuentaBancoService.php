<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/02/2019
 * Time: 04:20 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\CuentaBancoRepository;

class CuentaBancoService
{
    /**
     * @var CuentaBancoRepository
     */
    protected $cuenta;

    /**
     * CuentaBancoService constructor.
     * @param CuentaBancoRepository $cuenta
     */
    public function __construct(CuentaBancoRepository $cuenta)
    {
        $this->cuenta = $cuenta;
    }

    public function paginate($data) {
        return $this->cuenta->paginate($data);
    }

    public function find($id) {
        return $this->cuenta->find($id);
    }

    public function update(array $data, $id) {
        return $this->cuenta->update($data, $id);
    }
}