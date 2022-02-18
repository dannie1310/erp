<?php

namespace App\Exports\FinanzasGlobal;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SolicitudesPagoAplicadasExport implements FromArray, WithHeadings
{
    protected $solicitudes;

    public function __construct(array $solicitudes)
    {
        $this->solicitudes = $solicitudes;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->solicitudes;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Base de Datos',
            'Obra',
            'Fecha de Solicitud',
            'Folio de Solicitud',
            'Proveedor / Contratista',
            'Monto de Solicitud',
            'Usuario Registró',
            'Observaciones',
            'Remesa Relacionada',
            'Monto Autorizado en Remesa',
            'Monto Pagado',
            'Monto Aplicado',
            'Saldo',
        ];
    }
}
