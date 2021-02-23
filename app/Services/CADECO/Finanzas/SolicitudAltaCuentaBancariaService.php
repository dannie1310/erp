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

    public function pdf($id){
        $proyectos = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();
        $obras = Obra::all('id_obra');
        foreach ($obras as $obra)
        {
            $filename = $proyectos->id.'_'.$obra->id_obra.'_'.$id.'_alta_cuenta_bancaria.pdf';
            $path = storage_path('Finanzas\solicitudes_cuentas_bancarias/'.$filename);
            if(file_exists($path)){
                break;
            }
        }

        if(!file_exists($path))
        {
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
        if (config('filesystems.disks.solicitud_cuenta_bancaria.root') == storage_path())
        {
            abort(403,'No existe el directorio destino: STORAGE_SOPORTE_SOLICITUD_CUENTA_BANCARIA. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }
        $proyectos = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();
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
            Storage::disk('solicitud_cuenta_bancaria')->put($proyectos->id.'_'.Context::getIdObra().'_'.$registro->id.'_alta_cuenta_bancaria'.'.pdf', fopen($data['archivo'], 'r'));
        }
        return $registro;
    }

    public function autorizar($id){
        return $this->repository->show($id)->autorizar();
    }

    public function rechazar($data , $id){
        $observaciones = $data['data'][0];
        return $this->repository->show($id)->rechazar($observaciones);
    }

    public function cancelar($data , $id){
        $observaciones = $data['data'][0];
        return $this->repository->show($id)->cancelar($observaciones);
    }
}
