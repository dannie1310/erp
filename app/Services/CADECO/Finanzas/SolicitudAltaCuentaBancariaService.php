<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/08/2019
 * Time: 05:11 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Models\CADECO\FinanzasCBE\SolicitudAlta;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Repositories\Repository;

class SolicitudAltaCuentaBancariaService
{
    /**
     * @var Repository
     */
    protected $repository;
    private $files_global;

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

    public function pdf($id){
        $proyectos = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();
        $obra = Context::getIdObra();

        $filename = $proyectos->id.'_'.$obra.'_'.$id.'.pdf';

        $path = storage_path($this->files_global . 'finanzas\solicitudes_cuentas_bancarias/'.$filename);

        if(!file_exists($path)){
            return "El archivo al cual intenta acceder no existe o no se encuentra disponible.";
        }else{
            return response()->file($path);
        }
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
        return $this->repository->create($datos);
    }

    public function autorizar($id){
        return $this->repository->show($id)->autorizar();
    }
}