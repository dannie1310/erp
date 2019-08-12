<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/08/2019
 * Time: 05:11 PM
 */

namespace App\Services\CADECO\Finanzas;

use App\Facades\Context;
use App\Models\CADECO\FinanzasCBE\SolicitudAlta;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Storage;

class SolicitudAltaCuentaBancariaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudAltaCuentaBancariaService constructor.
     * @param SolicitudAlta $model
     */
    public function __construct(SolicitudAlta $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        $datos = [
            'id_empresa' => $data['id_empresa'],
            'id_banco' => $data['id_banco'],
            'id_moneda' =>  $data['id_moneda'],
            'cuenta_clabe' => $data['cuenta'],
            'id_plaza' => $data['id_plaza'],
            'sucursal' => $data['sucursal'],
            'tipo_cuenta' => $data['id_tipo'],
            'observaciones' => $data['observaciones']
        ];
        $registro = $this->repository->create($datos);
        if($data['archivo'] != null) {
            Storage::disk('alta_cuenta_bancaria')->put($registro->id . '_' . $registro->numero_folio . '_' . Context::getDatabase() . '_alta_cuenta_bancaria' . '.pdf', fopen($data['archivo'], 'r'));
        }
        return $registro;
    }
}