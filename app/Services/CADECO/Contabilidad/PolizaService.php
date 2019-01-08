<?php

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\PolizaRepository;

class PolizaService
{
    /**
     * @var PolizaRepository
     */
    protected $poliza;

    /**
     * PolizaService constructor.
     * @param PolizaRepository $poliza
     */
    public function __construct(PolizaRepository $poliza)
    {
        $this->poliza = $poliza;
    }

    public function paginate($data)
    {
        $poliza = $this->poliza;

        if(isset($data['startDate'])) {
            $poliza = $poliza->where([['fecha', '>=', $data['startDate']]]);
        }

        if(isset($data['endDate'])) {
            $poliza = $poliza->where([['fecha', '<=', $data['endDate']]]);
        }

        if(isset($data['id_tipo_poliza_contpaq'])) {
            $poliza = $poliza->where([['id_tipo_poliza_contpaq', '=', $data['id_tipo_poliza_contpaq']]]);
        }

        if(isset($data['estatus'])) {
            $poliza = $poliza->where([['estatus', '=', $data['estatus']]]);
        }
        if(isset($data['concepto'])) {
            $poliza = $poliza->where([['concepto', 'LIKE', '%'.$data['concepto'].'%']]);
        }

        return $poliza->paginate($data);
    }

    public function find($id) {
        return $this->poliza->find($id);
    }
}