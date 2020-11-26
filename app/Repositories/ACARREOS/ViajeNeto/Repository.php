<?php


namespace App\Repositories\ACARREOS\ViajeNeto;


use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\ConsultaErronea;
use App\Models\ACARREOS\InicioCamion;
use App\Models\ACARREOS\Json;
use App\Models\ACARREOS\Telefono;
use App\Models\ACARREOS\ViajeNeto;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(ViajeNeto $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function crearJson($json)
    {
        Json::create([
            'json' => json_encode($json)
        ]);
    }

    public function crearLogError($log, $usuario)
    {
        ConsultaErronea::create([
            'consulta' => $log,
            'registro' => $usuario
        ]);
    }

    public function getTelefonoActivo($imei)
    {
        $telefono = Telefono::activo()->where('imei', $imei)->first();
        return is_null($telefono) ? true : false;
    }

    public function viajeNeto($viaje)
    {
        $viaje_neto = $this->model->where('IdCamion', $viaje['IdCamion'])
            ->where('FechaSalida', $viaje['FechaSalida'])
            ->where('HoraSalida', $viaje['HoraSalida'])
            ->where('FechaLlegada', $viaje['FechaLlegada'])
            ->where('HoraLlegada', $viaje['HoraLlegada'])
            ->where('Code', $viaje['Code'])->first();
        return $viaje_neto;
    }

    public function existeViaje($viaje)
    {
        $viaje_neto = $this->viajeNeto($viaje);
        if($viaje_neto)
        {
            return true;
        }
        return false;
    }

    public function getCamion($id_camion)
    {
        return Camion::find($id_camion)->select('IdEmpresa', 'IdSindicato', 'CubicacionParaPago')->first();
    }

    public function existeViajeInicio($inicio)
    {
        if(array_key_exists('Code', $inicio)) {
            $inicio_viaje = InicioCamion::where('IdCamion', $inicio['idcamion'])
                ->where('fecha_origen', $inicio['fecha_origen'])
                ->where('Code', $inicio['Code'])
                ->count('id');
        }else{
            $inicio_viaje = InicioCamion::where('IdCamion', $inicio['idcamion'])
                ->where('fecha_origen', $inicio['fecha_origen'])
                ->count('id');
        }
        if($inicio_viaje > 0)
        {
            return true;
        }
        return false;
    }
}
