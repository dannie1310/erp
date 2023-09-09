<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\Documento;
use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Models\CONTROL_RECURSOS\Serie;
use App\Models\CONTROL_RECURSOS\TipoDocto;
use App\Repositories\CONTROLRECURSOS\DocumentoRepository as Repository;
use DateTime;
use DateTimeZone;

class DocumentoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Documento $model
     */
    public function __construct(Documento $model)
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

        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        $vencimiento = new DateTime($data["vencimiento"]);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

        $fecha = new DateTime($data["fecha"]);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $this->validaFechas($fecha, $vencimiento);
        $data['vencimiento'] = $vencimiento->format('Y-m-d');
        $data['fecha'] = $fecha->format('Y-m-d');
        return $this->repository->registrar($data);
    }

    private function validaFechas($emision, $vencimiento)
    {
        if ($emision > $vencimiento) {
            abort(500, "La fecha de facturación no puede ser mayor a la fecha de vencimiento");
        }
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->editar($data);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar();
    }
}
