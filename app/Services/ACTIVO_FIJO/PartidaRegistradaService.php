<?php

namespace App\Services\ACTIVO_FIJO;

use App\Models\SCI\VwPartidaRegistrada;
use App\PDF\ActivoFijo\ImpresionEtiquetaFormato;
use App\Repositories\ACTIVO_FIJO\VwPartidaRegistradaRepository as Repository;

class PartidaRegistradaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Resguardo constructor.
     * @param VwPartidaRegistrada $model
     */
    public function __construct(VwPartidaRegistrada $model)
    {
        $this->repository = new Repository($model);
    }

    public function pdfEtiquetas($id, $tipo)
    {
        if($tipo == 1)
        {
            $etiquetas = $this->repository->getPorUsuario($id);
        }
        if($tipo == 2)
        {
            $etiquetas = $this->repository->getPorCodigo($id);
        }
        if($tipo == 3)
        {
            $etiquetas = $this->repository->getPorDepartamento($id);
        }
        if($tipo == 4)
        {
            $etiquetas = $this->repository->getPorReferencia($id);
        }
        if($tipo == 5)
        {
            $etiquetas = $this->repository->getPorProyecto($id);
        }

        $pdf = new ImpresionEtiquetaFormato($etiquetas->toArray());
        return $pdf->create();
    }
}
