<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\ContraRecibo;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Repositories\CADECO\Finanzas\RegistrarPago\Repository;
use http\Env\Request;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FacturaService constructor.
     * @param Factura $model
     */
    public function __construct(Factura $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {

        $facturas = $this->repository;


       if(isset($data['id_transaccion']))
       {
           $facturas = $facturas->where([['numero_folio', 'LIKE', '%'.$data['id_transaccion'].'%']]);
       }

       if(isset($data['numero_folio']))
       {
           $contraRecibos = ContraRecibo::query()->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']])->get();
           foreach ($contraRecibos as $e){
               $facturas = $facturas->whereOr([['id_antecedente','=',$e->id_transaccion]]);
           }
       }


       if(isset($data['referencia']))
       {
           $facturas = $facturas->where([['referencia', 'LIKE', '%'.$data['referencia'].'%']]);
       }


        if(isset($data['observaciones']))
        {
            $contraRecibos = ContraRecibo::query()->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']])->get();
            foreach ($contraRecibos as $e){
                $facturas = $facturas->whereOr([['id_antecedente','=',$e->id_transaccion]]);
            }
        }

        if (isset($data['id_empresa']))
        {
            $empresas = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['id_empresa'].'%']])->get();
            foreach ($empresas as $e){
                $facturas = $facturas->whereOr([['id_empresa','=', $e->id_empresa]]);
            }
        }

        if(isset($data['estado']))
        {
            if(strcmp(strtoupper($data['estado']),'REGISTRADA')==0){
                $facturas = $facturas->where([['estado', '=', 0]]);
            }

            if(strcmp(strtoupper($data['estado']),'REVISADA')==0){
                $facturas = $facturas->where([['estado', '=', 1]]);
            }

            if(strcmp(strtoupper($data['estado']),'PAGADA')==0){
                $facturas = $facturas->where([['estado', '=', 2]]);
            }
        }


        if(isset($data['opciones']))
        {
            if(strpos('FACTURA',strtoupper($data['opciones'])) !== FALSE ){
                $facturas = $facturas->where([['opciones', '=', 0]]);
            }

            if(strpos('GASTOS VARIOS',strtoupper($data['opciones'])) !== FALSE ){
                $facturas = $facturas->where([['opciones', '=', 1]]);
            }

            if(strpos('MATERIALES / SERVICIOS',strtoupper($data['opciones'])) !== FALSE ){
                $facturas = $facturas->where([['opciones', '=', 65537]]);
            }
        }

        if(isset($data['fecha'])) {
            $facturas = $facturas->where( [['fecha', '=', $data['fecha']]] );
        }






        return $facturas->paginate($data);
    }

    public function autorizadas(){
        return $this->repository->autorizadas();
    }

    public function pendientesPago($id){
        return $this->repository->pendientesPago($id);
    }
}

