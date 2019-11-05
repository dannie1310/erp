<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\SolicitudCompra;
use App\PDF\CADECO\Compras\SolicitudCompraFormato;
use App\Repositories\Repository;


class SolicitudCompraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudCompraService constructor.
     * @param SolicitudCompra $model
     */
    public function __construct(SolicitudCompra $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $solicitudes = $this->repository;

        if(isset($data['numero_folio']))
        {
            $solicitudes = $solicitudes->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['fecha']))
        {
            $solicitudes = $solicitudes->where( [['fecha', '=', $data['fecha']]] );
        }




        return $solicitudes->paginate($data);

    }

    public function store($data)
    {

        /*Validamos Partidas*/
        foreach ($data['items'] as $key => $item){

        $validacion= $this->validarPartidas($data['items'], $item, $key);

            if($validacion===true){
                abort(400, 'No pueden existir dos partidas con el mismo material y mismo destino... Material: '.strval($key+1)." - ".strval($item['material']['label']));
            }
        }


           //Crear Solicitud de compra

    }


    public function validarPartidas($items, $item, $i){
        foreach ($items as $key => $value){

            if($key!=$i) {
                if ($value['id_material'] === $item['id_material']) {

                    if ($value['id_destino'] === $item['id_destino']) {
                        if ($value['destino_concepto'] === true && $item['destino_concepto'] === true) {
//                       dd("aca",$value['destino_concepto']===$item['destino_concepto'] );
//                            dd("aca1");
                            return true;
                        }
                        if ($value['destino_almacen'] === true && $item['destino_almacen'] === true) {
//                            dd("aca2", $value['destino_almacen'], $item['destino_almacen']);
                            return true;

                        }
                        return false;
                    }
                    return false;
                }
            }



            return false;

        }
    }


    public function pdfSolicitudCompra($id)
    {
        $pdf = new SolicitudCompraFormato($id);
        return $pdf;
    }
}
