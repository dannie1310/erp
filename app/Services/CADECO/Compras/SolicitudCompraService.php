<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\SolicitudCompra;
use App\PDF\CADECO\Compras\SolicitudCompraFormato;
use App\PDF\Compras\CotizacionTablaComparativaFormato;
use App\Repositories\CADECO\Compras\Solicitud\Repository;


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
        try {
            /*Validación de Partidas*/
            foreach ($data['partidas'] as $key => $item){
                $validacion= $this->validarPartidas($data['partidas'], $item, $key);
                if($validacion===true){
                    abort(400, 'No pueden existir dos partidas con el mismo material y mismo destino... Material en Fila: '.strval($key+1)." - ".strval($item['material']['label']));
                }
            }
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
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
        return $this->repository->show($id)->eliminar($data['data']);
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
