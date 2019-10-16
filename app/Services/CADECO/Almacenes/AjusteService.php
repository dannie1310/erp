<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/09/2019
 * Time: 06:19 PM
 */

namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\Ajuste;
use App\Models\CADECO\AjusteNegativo;
use App\Models\CADECO\AjustePositivo;
use App\Models\CADECO\Almacen;
use App\Models\CADECO\NuevoLote;
use App\Repositories\Repository;

class AjusteService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AjusteService constructor.
     * @param Ajuste $model
     */
    public function __construct(Ajuste $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $ajuste = $this->repository;


        if (isset($data['numero_folio'])) {
            $ajuste = $ajuste->where([['numero_folio', 'LIKE', '%' . $data['numero_folio'] . '%']]);
        }

        if (isset($data['fecha'])) {
            $ajuste = $ajuste->where( [['fecha', '=', request( 'fecha' )]] );
        }

        if (isset($data['id_almacen'])) {
            $almacen = Almacen::query()->where([['descripcion', 'LIKE', '%'.$data['id_almacen'].'%']])->get();
            foreach ($almacen as $a){
                $ajuste = $ajuste->whereOr([['id_almacen', '=', $a->id_almacen]]);
            }
        }

        if (isset($data['referencia'])) {
            $ajuste = $ajuste->where([['referencia', 'LIKE', '%' . $data['referencia'] . '%']]);
        }

        if (isset($data['observaciones'])) {
            $ajuste = $ajuste->where([['observaciones', 'LIKE', '%' . $data['observaciones'] . '%']]);
        }

        return $ajuste->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function delete($data, $id)
    {

        $ajuste = $this->repository->show($id);

        switch ($ajuste->opciones){

            /*Ajuste Positivo*/
            case 0:
                $positivo = AjustePositivo::query()->with('partidas')->find($id);
                $ajuste_positivo = new AjustePositivo();
                $ajuste_positivo->validarPartidasAjusteEliminar($positivo->partidas, $id);

                break;

            /*Ajuste Negativo*/
            case 1:
                $negativo = AjusteNegativo::query()->with('partidas')->find($id);
                $ajuste_negativo = new AjusteNegativo();
                $ajuste_negativo->validarPartidasAjusteEliminar($negativo->partidas, $id);
                break;

             /*Nuevo lotes*/
            case 2:
                $lote = NuevoLote::query()->with('partidas')->find($id);
                $nuevo_lote = new NuevoLote();
                $nuevo_lote->validarPartidasAjusteEliminar($lote->partidas, $id);

                break;
        }

    }



}
