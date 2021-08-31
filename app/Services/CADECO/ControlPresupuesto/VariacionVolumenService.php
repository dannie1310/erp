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
use Illuminate\Support\Facades\DB;
use App\PDF\ControlPresupuesto\VariacionVolumenFormato;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioPartidaHistorico;
use App\Repositories\CADECO\ControlPresupuesto\VariacionVolumenRepository;

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
        $this->repository = new VariacionVolumenRepository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }


    public function delete($data, $id)
    {
        return $this->repository->show($id)->rechazar($data['data'][0]);
    }

    public function store(array $data)
    {

        $solicitud_variacion_volumen = $this->repository->create($data);

        return $solicitud_variacion_volumen;
    }

    public function autorizar($id){
        try {
            DB::connection('cadeco')->beginTransaction();
            $variacion_volumen = $this->repository->show($id);
            foreach($variacion_volumen->variacionVolumenPartidas as $partida){
                $concepto = $partida->concepto;

                $monto_presupuestado_original = $concepto->monto_presupuestado;
                $cantidad_presupuestada_original = $concepto->cantidad_presupuestada;

                $cantidad_presupuestada_actualizada = $cantidad_presupuestada_original + $partida->variacion_volumen;
                $dividendo = $cantidad_presupuestada_original > 0?$cantidad_presupuestada_original:1;
                $factor = $cantidad_presupuestada_actualizada / $dividendo;

                $conceptos_afectables = Concepto::where('nivel', 'like', $concepto->nivel . '%')->where('id_obra', '=', Context::getIdObra())->orderBy('nivel', 'ASC')->get();
                foreach($conceptos_afectables as $concepto_afectable){
                    SolicitudCambioPartidaHistorico::create([
                        'id_solicitud_cambio_partida' => $partida->id,
                        'nivel' => $concepto_afectable->nivel,
                        'cantidad_presupuestada_original' => $concepto_afectable->cantidad_presupuestada,
                        'cantidad_presupuestada_actualizada' => $concepto_afectable->cantidad_presupuestada * $factor,
                        'monto_presupuestado_original' => $concepto_afectable->monto_presupuestado,
                        'monto_presupuestado_actualizado' => $concepto_afectable->monto_presupuestado * $factor
                    ]);

                    $concepto_afectable->cantidad_presupuestada = $concepto_afectable->cantidad_presupuestada * $factor;
                    $concepto_afectable->monto_presupuestado = $concepto_afectable->monto_presupuestado * $factor;
                    $concepto_afectable->save();
                }

                $len_nivel = strlen($concepto->nivel) - 4;
                while ($len_nivel > 0) {
                    $concepto_propagado = Concepto::where('id_obra', '=', Context::getIdObra())->where('nivel', '=', substr($concepto->nivel, 0, $len_nivel))->first();
                    $cantidadMonto = ($concepto_propagado->monto_presupuestado - $monto_presupuestado_original) + $concepto->monto_presupuestado;

                    SolicitudCambioPartidaHistorico::create([
                        'id_solicitud_cambio_partida' => $partida->id,
                        'nivel' => $concepto_propagado->nivel,
                        'monto_presupuestado_original' => $concepto_propagado->monto_presupuestado,
                        'monto_presupuestado_actualizado' => $cantidadMonto
                    ]);

                    $concepto_propagado->update(['monto_presupuestado' => $cantidadMonto]);
                    $concepto_propagado->save();
                    $len_nivel -= 4;
                }
            }
            $variacion_volumen->id_estatus = 2;
            $variacion_volumen->id_autoriza = auth()->id();
            $variacion_volumen->fecha_autorizacion = date('Y-m-d h:i:s');
            $variacion_volumen->save();
            DB::connection('cadeco')->commit();

            return $variacion_volumen ;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function pdfVariacionVolumen($id)
    {
        $pdf = new VariacionVolumenFormato($id);
        return $pdf;
    }
}
