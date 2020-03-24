<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SolicitudEdicionExport implements FromArray, WithHeadings
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
            '#',
            'BD',
            'Folio',
            'Tipo',
            'Concepto Póliza',

        ];
    }
}
