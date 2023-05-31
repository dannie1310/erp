<?php

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Repositories\SEGURIDAD_ERP\Contabilidad\ContabilidadElectronicaRepository as Repository;

class ContabilidadElectronicaService
{
    /**
     * @var Repository
     */
    protected $repository;


    public function __construct()
    {

    }

    public function getDatosXML(array $data)
    {
        $archivo_xml = $data["xml"];
        $arreglo_cfd = $this->getArregloCFD($archivo_xml);
        dd($arreglo_cfd, $archivo_xml);


        return $arreglo_cfd;
    }

}
