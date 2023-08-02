<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\Factura;
use App\Models\CONTROL_RECURSOS\Serie;
use App\Models\CONTROL_RECURSOS\TipoDocto;
use App\Repositories\CONTROLRECURSOS\FacturaRepository as Repository;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Factura $model
     */
    public function __construct(Factura $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['idserie']))
        {
            $serie = Serie::where([['Descripcion', 'LIKE', '%' . $data['idserie'] . '%']])->pluck('idseries');
            $this->repository->whereIn(['IdSerie', $serie]);
        }

        if (isset($data['idtipodocto']))
        {
            $tipo = TipoDocto::where([['Descripcion', 'LIKE', '%' . $data['idtipodocto'] . '%']])->pluck('IdTipoDocto');
            $this->repository->whereIn(['IdTipoDocto', $tipo]);
        }

        if (isset($data['fecha']))
        {
            $this->repository->whereBetween( ['Fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if (isset($data['foliodocto']))
        {
            $this->repository->where([['FolioDocto', 'LIKE', '%'.$data['foliodocto'].'%']]);
        }

        if (isset($data['concepto']))
        {
            $this->repository->where([['Concepto', 'LIKE', '%'.$data['concepto'].'%']]);
        }

        if (isset($data['total']))
        {
            $this->repository->where([['Total', 'LIKE', '%'.$data['total'].'%']]);
        }

        if (isset($data['idmoneda']))
        {
            $tipo = CtgMoneda::where([['moneda', 'LIKE', '%' . $data['idmoneda'] . '%']])->pluck('id');
            $this->repository->whereIn(['IdMoneda', $tipo]);
        }

        return $this->repository->paginate($data);
    }

    public function cargaXML(array $data)
    {dd($data);
        $archivo_xml = $data["xml"];
        $tipo = $data["tipo"];
        $id_empresa = $data["id_empresa"];
        $arreglo_cfd = $this->getArregloCFD($archivo_xml);
        if(is_numeric($id_empresa)){
            $empresa = $this->repository->getEmpresaPorId($id_empresa);
            if($empresa["rfc"] != $arreglo_cfd["emisor"]["rfc"]){
                if($arreglo_cfd["tipo_comprobante"] == "E"){
                    abort(500, "El emisor de los CFDI no coincide, favor de verificar");
                }
            }
        }
        if($arreglo_cfd["tipo_comprobante"] == "I" && $tipo == 2)
        {
            abort(500, "Se ingresó un CFDI de tipo erróneo, favor de ingresar un CFDI de tipo egreso (Nota de Crédito)");
        }
        elseif($arreglo_cfd["tipo_comprobante"] == "E" && $tipo == 1)
        {
            abort(500, "Se ingresó un CFDI de tipo erróneo, favor de ingresar un CFDI de tipo ingreso (Factura)");
        }
        return $arreglo_cfd;
    }

}
