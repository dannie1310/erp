<?php

namespace App\Exports\Contratos;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LayoutCambioVolumenPrecioSubcontratoExport implements FromArray, WithHeadings
{
    protected $subcontrato;

    public function __construct(array $solicitudes)
    {
        $this->subcontrato = $solicitudes;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->subcontrato;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'Id',
            'Clave',
            'Concepto',
            'Unidad',
            'Volumen Contratado',
            'Precio Unitario',
            'Saldo',
            'Importe del Saldo',
            'Aditiva/Deductiva',
            'Nuevo Precio',
            'Destino',
        ];
    }
}
