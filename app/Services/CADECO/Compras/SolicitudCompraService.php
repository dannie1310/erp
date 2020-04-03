<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\Compras\ActivoFijo;
use App\Models\CADECO\Compras\MaterialMarcaModelo;
use App\Models\CADECO\Compras\SolicitudComplemento;
use App\Models\CADECO\Compras\SolicitudPartidaComplemento;
use App\Models\CADECO\Entrega;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\SolicitudCompraPartida;
use App\PDF\CADECO\Compras\SolicitudCompraFormato;
use App\PDF\Compras\CotizacionTablaComparativaFormato;
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
        /*Validación de Partidas*/
        foreach ($data['items'] as $key => $item){
        $validacion= $this->validarPartidas($data['items'], $item, $key);
            if($validacion===true){
                abort(400, 'No pueden existir dos partidas con el mismo material y mismo destino... Material en Fila: '.strval($key+1)." - ".strval($item['material']['label']));
            }
        }


        //Crear Solicitud de compra
        $datos = [
            'observaciones'=> $data['observaciones'],
        ];
        $solicitud = $this->repository->create($datos);


        //Crear el complemento

            $datos_complemento = [
                'id_transaccion' => $solicitud->id_transaccion,
                'id_area_compradora'=> $data['id_area_compradora'],
                'id_tipo' => $data['id_tipo'],
                'concepto' => $data['concepto'],
                'id_area_solicitante' => $data['id_area_solicitante'],
                'fecha_requisicion_origen' => $data['fecha_requisicion'],
                'requisicion_origen' => $data['folio_requisicion'],

                ];
            $complemento = SolicitudComplemento::create($datos_complemento);


        /*Si es Activo Fijo*/
        if($data['id_tipo']===3)
        {
            $data_af = [
                'id_transaccion' => $solicitud->id_transaccion
            ];
            $activo = ActivoFijo::create($data_af);

        }



            /*Registro de partidas*/

        foreach ($data['items'] as $key => $item){

            $partida_datos = [
                'id_transaccion'=>$solicitud->id_transaccion,
                'id_material'=>$item['id_material'],
                'unidad'=>$item['unidad']
            ];



            if($item['destino_concepto'] === true)
            {
                $partida_datos['id_concepto'] = $item['id_destino'];
            }

            if($item['destino_almacen'] === true)
            {
                $partida_datos['id_almacen'] = $item['id_destino'];
            }

           $partida= SolicitudCompraPartida::create($partida_datos);

            /*Registro de Partida Complemento*/
            $partida_complemento_datos = [
                'id_item' => $partida->id_item,
                'observaciones' => $item['observaciones']
            ];
            $partida_complemento = SolicitudPartidaComplemento::create($partida_complemento_datos);


//DESCOMENTAR
            /*Registro en Materiales-Marcas-Modelos*/

//            $datos_material = [
//                'idmaterial' => $item['id_material'],
//                'idMarca' => $item['marca'],
//                'idModelo' => $item['modelo'],
//            ];
//
//
//            $mat_reg = MaterialMarcaModelo::query()->create($datos_material);




            /*Registro de Entregas*/
            $datos_entrega = [
                'id_item' => $partida->id_item,
                'fecha' => $item['fecha'],
                'numero_entrega'=> 1,
                'cantidad' => $item['cantidad'],
                'id_concepto' => $partida->id_concepto,
                'id_almacen' => $partida->id_almacen
            ];

            $entrega = Entrega::create($datos_entrega);
        }



        return $solicitud;


    }


    public function validarPartidas($items, $item, $i){
        foreach ($items as $key => $value){

            if($key!=$i) {
                if ($value['id_material'] === $item['id_material']) {

                    if ($value['id_destino'] === $item['id_destino']) {
                        if ($value['destino_concepto'] === true && $item['destino_concepto'] === true) {
                            return true;
                        }
                        if ($value['destino_almacen'] === true && $item['destino_almacen'] === true) {
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

    public function delete($data, $id)
    {

    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function aprobar($data, $id)
    {
        $solicitud = $this->repository->show($id);
        return $solicitud->aprobarSolicitud($data);
    }

    public function update(array $data, $id)
    {
//        return $this->repository->update($data, $id);
    }

    public function pdfSolicitudCompra($id)
    {
        $pdf = new SolicitudCompraFormato($id);
        return $pdf;
    }

    public function pdfCotizacion($id)
    {
        $pdf = new CotizacionTablaComparativaFormato($id);
        return $pdf;
    }
}
