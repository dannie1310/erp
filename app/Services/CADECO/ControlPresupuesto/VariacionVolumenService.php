<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:45 PM
 */

namespace App\Services\CADECO\ControlPresupuesto;

use App\Facades\Context;
use App\Models\CADECO\Concepto;
use App\Repositories\Repository;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;

class VariacionVolumenService{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * VariacionVolumen constructor.
     *
     * @param VariacionVolumen $model
     */
    public function __construct(VariacionVolumen $model)
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
        $Solicitud_variacion_volumen = $this->repository->create([
            'area_solicitante' => $data['area_solicitante'],
            'motivo' => $data['motivo'],
            'id_tipo_orden' => 4,
            'importe_afectacion' => $data['variacion_volumen'] * $data['precio_unitario'],
            'numero_folio' => $this->repository->all()->count() + 1,
            'id_solicita' => auth()->id(),
            'id_obra' => Context::getIdObra(),
            'id_estatus' => 1,
        ]);
        $Solicitud_variacion_volumen->solicitudPartidas()->create([
            'id_tipo_orden' => 4,
            'id_concepto' => $data['id'],
            'nivel' => $data['nivel'],
            'unidad' => $data['unidad'],
            'cantidad_presupuestada_original' => $data['cantidad_presupuestada'],
            'cantidad_presupuestada_nueva' => $data['cantidad_presupuestada'] + $data['variacion_volumen'],
            'precio_unitario_original' => $data['precio_unitario'],
            'monto_presupuestado' => $data['monto_presupuestado'],
            'variacion_volumen' => $data['variacion_volumen'],
        ]);
        
        return $Solicitud_variacion_volumen;
    }

    public function autorizar($id){
        $variacion_volumen = $this->repository->show($id);
        foreach($variacion_volumen->variacionVolumenPartidas as $partida){
            $concepto = $partida->concepto;
            
            $monto_presupuestado_original = $concepto->monto_presupuestado;
            $cantidad_presupuestada_original = $concepto->cantidad_presupuestada;

            $cantidad_presupuestada_actualizada = $cantidad_presupuestada_original + $partida->variacion_volumen;
            $factor = $cantidad_presupuestada_actualizada / $cantidad_presupuestada_original;

            $conceptos_afectables = Concepto::where('nivel', 'like', $concepto->nivel . '_%')->where('id_obra', '=', Context::getIdObra())->orderBy('nivel', 'ASC')->get();
            foreach($conceptos_afectables as $concepto){
                $concepto->update(['cantidad_presupuestada' => $conceptos_afectables->cantidad_presupuestada * $factor, 'monto_presupuestado' => $conceptos_afectables->monto_presupuestado * $factor]);
                $concepto->save();
            }            

        }
        dd('pardo');
    }
}