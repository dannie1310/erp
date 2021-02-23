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
        if (config('filesystems.disks.solicitud_cuenta_bancaria.root') == storage_path())
        {
            abort(403,'No existe el directorio destino: STORAGE_SOPORTE_SOLICITUD_CUENTA_BANCARIA. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }
        $proyectos = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();
        $datos = [
            'id_cuenta' => $data['cuenta']['id'],
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

    public function pdf($id){
        $proyectos = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();
        $obra = Context::getIdObra();

        $filename = $proyectos->id.'_'.$obra.'_'.$id.'_baja_cuenta_bancaria.pdf';

        $path = storage_path('Finanzas\solicitudes_cuentas_bancarias/'.$filename);

        if(!file_exists($path)){
            return "El archivo al cual intenta acceder no existe o no se encuentra disponible.";
        }else{
            return response()->file($path);
        }
    }

    public function autorizar($id){
        return $this->repository->show($id)->autorizar();
    }

    public function cancelar($data , $id){
        $observaciones = $data['data'][0];
        return $this->repository->show($id)->cancelar($observaciones);
    }

    public function rechazar($data , $id){
        $observaciones = $data['data'][0];
        return $this->repository->show($id)->rechazar($observaciones);
    }

}
