<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\RelacionGasto;
use App\Repositories\Repository;
use App\Utils\CFD;

class RelacionGastoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param RelacionGasto $model
     */
    public function __construct(RelacionGasto $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        /*if (isset($data['idserie']))
        {
            $serie = Serie::where([['Descripcion', 'LIKE', '%' . $data['idserie'] . '%']])->pluck('idseries');
            $this->repository->whereIn(['IdSerie', $serie]);
        }

        if (isset($data['IdProveedor']))
        {
            $proveedor = Proveedor::where([['RazonSocial', 'LIKE', '%' . $data['IdProveedor'] . '%']])->pluck('IdProveedor');
            $this->repository->whereIn(['IdProveedor', $proveedor]);
        }

        if (isset($data['idtipodocto']))
        {
            $tipo = TipoDocto::where([['Descripcion', 'LIKE', '%' . $data['idtipodocto'] . '%']])->pluck('IdTipoDocto');
            $this->repository->whereIn(['IdTipoDocto', $tipo]);
        }

        if (isset($data['Fecha']))
        {
            $this->repository->whereBetween( ['Fecha', [ request( 'Fecha' )." 00:00:00",request( 'Fecha' )." 23:59:59"]] );
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

       */
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        dd($data);
        $archivos_xml = json_decode($data["xmls"]);
        $fecha_inicial = New DateTime($data['fecha_inicial']);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_inicial'] = $fecha_inicial->format("Y-m-d H:i:s");
        $fecha_final = New DateTime($data['fecha_final']);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_final'] = $fecha_final->format("Y-m-d H:i:s");

        $datos_relacion = [
            'numero_folio' => $data[''],
            'folio' => $data[''],
            'fecha_inicio' => $data[''],
            'fecha_fin' => $data[''],
            'idempresa' => $data[''],
            'idempleado' => $data[''],
            'idserie' => $data[''],
            'idmoneda' => $data[''],
            'iddepartamento' => $data[''],
            'idproyecto' => $data[''],
            'modifico_estado' => $data[''],
            'idestado' => $data[''],
            'motivo' => $data[''],
            'registro' => $data[''],
        ];

        foreach($archivos_xml as $xml){
            $arreglo_cfd = $this->getArregloCFD($xml);

            $this->validaExistenciaRepositorio($arreglo_cfd);
            $this->validaReceptor($arreglo_cfd);

            $datos_rfactura[] = [
                "xml_file" => $this->repository->getArchivoSQL($xml),
                "hash_file" => hash_file('md5',$xml),
                "uuid" => $arreglo_cfd["complemento"]["uuid"],
                "rfc_emisor" => $arreglo_cfd["emisor"]["rfc"],
                "rfc_receptor" => $arreglo_cfd["receptor"]["rfc"],
                "tipo_comprobante" => $arreglo_cfd["tipo_comprobante"],
                "tipo_transaccion"=> 101
            ];

            $this->validaPresuntoEFO($arreglo_cfd);
        }

        $data["facturas_repositorio"] = $datos_rfactura;

        $transaccion = $this->repository->registrar($data);

        foreach($transaccion->facturasRepositorio as $facturaRepositorio){
            if($facturaRepositorio->cfdiSAT){
                $xml = "data:text/xml;base64," . $facturaRepositorio->cfdiSAT->xml_file;
                $cfd = new CFD($xml);
                try{
                    $logs = $cfd->guardarXmlEnADD();
                }catch (\Exception $e)
                {
                    $logs[] = "Error catch: " . $e->getMessage();
                }

                foreach($logs as $log)
                {
                    if(is_array($log)){
                        $facturaRepositorio->logsADD()->create(
                            [
                                "log_add"=>$log["descripcion"],
                                "tipo"=>$log["tipo"]
                            ]
                        );
                    }else {
                        $facturaRepositorio->logsADD()->create(
                            [
                                "log_add"=>$log
                            ]
                        );
                    }
                }
            }
        }
        return $transaccion;
    }
}
