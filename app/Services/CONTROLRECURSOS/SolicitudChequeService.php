<?php

namespace App\Services\CONTROLRECURSOS;

use App\LAYOUT\LayoutBancario;
use App\Models\CONTROL_RECURSOS\DescargaLayoutBanco;
use App\Models\CONTROL_RECURSOS\SolCheque;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Storage;

class SolicitudChequeService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param SolCheque $model
     */
    public function __construct(SolCheque $model)
    {
        $this->repository = new Repository($model);
    }

    public function layout($data)
    {
        $layout = new LayoutBancario($data['params']);
        return $layout->create();
    }

    public function index(){
        return $this->repository->all();
    }

    public function descarga_layout($id)
    {
        $descarga = DescargaLayoutBanco::where('id', $id)->first();
        return Storage::disk('bancario_recurso_descarga_zip')->download($descarga->nombre_archivo);
    }
}
