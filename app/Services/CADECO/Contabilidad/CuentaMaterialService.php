<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 12:18 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\CuentaMaterialRepository;

class CuentaMaterialService
{
    /**
     * @var CuentaMaterialRepository
     */
    protected $cuentaMaterial;

    /**
     * CuentaMaterialService constructor.
     * @param CuentaMaterialRepository $cuentaMaterial
     */
    public function __construct(CuentaMaterialRepository $cuentaMaterial)
    {
        $this->cuentaMaterial = $cuentaMaterial;
    }

    public function paginate($data) {
    return $this->cuentaMaterial->paginate($data);
}

    public function find($id) {
        return $this->cuentaMaterial->find($id);
    }

    public function update(array $data, $id) {
        return $this->cuentaMaterial->update($data, $id);
    }
}