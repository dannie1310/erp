<?php

namespace App\Services\ACTIVO_FIJO;

use App\Models\SCI\VwPartidaRegistrada;
use App\PDF\ActivoFijo\ImpresionEtiquetaFormato;
use App\Repositories\Repository as Repository;

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

    public function pdfEtiquetas($id)
    {
        $resguardo = $this->repository->show($id);
        $pdf = new ImpresionEtiquetaFormato($resguardo);
        return $pdf->create();
    }
}
