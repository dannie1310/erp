<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/08/2019
 * Time: 10:39 AM
 */

namespace App\Services\CADECO\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\FinanzasCBE\SolicitudBaja;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Storage;

class SolicitudBajaCuentaBancariaService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudCambioCuentaBancariaService constructor.
     * @param SolicitudBaja $model
     */
    public function __construct(SolicitudBaja $model)
    {
        $this->repository = $model;
    }

    public function store(array $data)
    {
        $proyectos = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();
        $datos = [
            'id_empresa' => $data['id_empresa'],
            'id_banco' =>$data['cuenta']['banco']['id'],
            'id_moneda' => $data['cuenta']['moneda']['id'],
            'cuenta_clabe' => $data['cuenta']['cuenta'],
            'id_plaza' => $data['cuenta']['plaza']['id'],
            'sucursal' => $data['cuenta']['sucursal'],
            'tipo_cuenta' => $data['cuenta']['id_tipo'],
            'observaciones' => $data['observaciones']
        ];
        $registro = $this->repository->create($datos);
        if($data['archivo'] != null) {
            Storage::disk('solicitud_cuenta_bancaria')->put($proyectos->id.'_'.Context::getIdObra().'_'.$registro->id.'_baja_cuenta_bancaria'.'.pdf', fopen($data['archivo'], 'r'));
        }
        return $registro;
    }
}