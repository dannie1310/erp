<?php

namespace App\Exports\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ListaEmpresasExport implements FromCollection, WithHeadings
{
    protected $empresas;

    public function __construct($empresas)
    {
        $this->empresas = $empresas;
    }

    public function collection()
    {
        $i = 1;
        foreach ($this->empresas as $empresa) {
            $lista_empresas[] = array(
                'no'=>$i,
                'nombre'=>$empresa->Nombre,
                'alias'=>$empresa->AliasBDD,
                'visible'=>$empresa->Visible,
                'editable'=>$empresa->Editable,
                'historica'=>$empresa->Historica,
                'consolidadora'=>$empresa->Consolidadora,
                'desarrollo'=>$empresa->Desarrollo,
                'sincronizacion_cfdi'=>$empresa->SincronizacionPolizasCFDI,
                'acceso_bd'=>$empresa->estado_acceso_txt,
                'guid_dsl'=>$empresa->GuidDSL,
                'con_acceso_ct'=>$empresa->con_acceso_ct,
                'con_acceso_other_metadata'=>$empresa->con_acceso_other_metadata,
                'con_acceso_other_content'=>$empresa->con_acceso_other_content,
                'con_acceso_document_content'=>$empresa->con_acceso_document_content,
                'con_acceso_document_metadata'=>$empresa->con_acceso_document_metadata,
                'administrada_hermes'=>$empresa->administrada_hermes,
                'es_catalogo'=>$empresa->es_catalogo,
            );
            $i++;
        }
        return collect($lista_empresas);
    }


    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'Nombre',
            'Alias',
            'Visible',
            'Editable',
            'Histórica',
            'Consolidadora',
            'Desarrollo',
            'Para Sincronización con CFDI',
            "Acceso a BBDD",
            "GuidDSL",
            'Acceso Contpaq',
            "Acceso Other Metadata",
            "Acceso Other Content",
            "Acceso Document Content",
            "Acceso Document Metadata",
            "Administrada Hermes",
            "Es Catálogo"
        ];
    }
}
