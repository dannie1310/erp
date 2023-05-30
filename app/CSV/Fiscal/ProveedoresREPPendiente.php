<?php


namespace App\CSV\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProveedoresREPPendiente implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function query()
    {
        $query = ProveedorREP::query();



        if ($this->data['no_hermes'] === "false" && $this->data['es_hermes'] === "true") {
            $query->where("es_empresa_hermes", "=", "1");
        } else if ($this->data['no_hermes'] === "true" && $this->data['es_hermes'] === "false") {
            $query->where("es_empresa_hermes", "=", "0");
        }

        if ($this->data['con_contactos'] === "false" && $this->data['sin_contactos'] === "true") {
            $query->where("cantidad_contactos", "=", "0");
        } else if ($this->data['con_contactos'] === "true" && $this->data['sin_contactos'] === "false") {
            $query->where("cantidad_contactos", ">", "0");
        }


         if (isset($this->data['rfc_proveedor'])) {
            $query->where('rfc_proveedor', 'LIKE', '%' . $this->data['rfc_proveedor'] . '%');
        }

        if (isset($this->data['proveedor'])) {
            $query->where('proveedor', 'LIKE', '%' . $this->data['proveedor'] . '%');
        }

        if (isset($this->data['ultima_ubicacion_sao'])) {
            $query->where('ultima_ubicacion_sao', 'LIKE', '%' . $this->data['ultima_ubicacion_sao'] . '%');
        }

        if (isset($this->data['ultima_ubicacion_contabilidad'])) {
            $query->where('ultima_ubicacion_contabilidad', 'LIKE', '%' . $this->data['ultima_ubicacion_contabilidad'] . '%');
        }

        if (isset($this->data['cantidad_cfdi'])) {
            if (strpos($this->data['cantidad_cfdi'], ">=") !== false) {
                $cantidad_cfdi = str_replace(">=", "", $this->data['cantidad_cfdi']);
                $query->where('cantidad_cfdi', ">=", $cantidad_cfdi);
            } else if (strpos($this->data['cantidad_cfdi'], ">") !== false) {
                $cantidad_cfdi = str_replace(">", "", $this->data['cantidad_cfdi']);
                $query->where('cantidad_cfdi', ">", $cantidad_cfdi);
            } else if (strpos($this->data['cantidad_cfdi'], "<=") !== false) {
                $cantidad_cfdi = str_replace("<=", "", $this->data['cantidad_cfdi']);
                $query->where('cantidad_cfdi', "<=", $cantidad_cfdi);
            } else if (strpos($this->data['cantidad_cfdi'], "<") !== false) {
                $cantidad_cfdi = str_replace("<", "", $this->data['cantidad_cfdi']);
                $query->where('cantidad_cfdi', "<", $cantidad_cfdi);
            } else if (strpos($this->data['cantidad_cfdi'], "=") !== false) {
                $cantidad_cfdi = str_replace("=", "", $this->data['cantidad_cfdi']);
                $query->where('cantidad_cfdi', "=", $cantidad_cfdi);
            } else {
                $query->where('cantidad_cfdi', "=", $this->data['cantidad_cfdi']);
            }
        }

        if (isset($this->data['total_cfdi'])) {
            if (strpos($this->data['total_cfdi'], ">=") !== false) {
                $total_cfdi = str_replace(">=", "", $this->data['total_cfdi']);
                $query->where('total_cfdi', ">=", $total_cfdi);
            } else if (strpos($this->data['total_cfdi'], ">") !== false) {
                $total_cfdi = str_replace(">", "", $this->data['total_cfdi']);
                $query->where('total_cfdi', ">", $total_cfdi);
            } else if (strpos($this->data['total_cfdi'], "<=") !== false) {
                $total_cfdi = str_replace("<=", "", $this->data['total_cfdi']);
                $query->where('total_cfdi', "<=", $total_cfdi);
            } else if (strpos($this->data['total_cfdi'], "<") !== false) {
                $total_cfdi = str_replace("<", "", $this->data['total_cfdi']);
                $query->where('total_cfdi', "<", $total_cfdi);
            } else if (strpos($this->data['total_cfdi'], "=") !== false) {
                $total_cfdi = str_replace("=", "", $this->data['total_cfdi']);
                $query->where('total_cfdi', "=", $total_cfdi);
            } else {
                $query->where('total_cfdi', "=", $this->data['total_cfdi']);
            }
        }

        if (isset($this->data['total_rep'])) {
            if (strpos($this->data['total_rep'], ">=") !== false) {
                $total_rep = str_replace(">=", "", $this->data['total_rep']);
                $query->where('total_rep', ">=", $total_rep);
            } else if (strpos($this->data['total_rep'], ">") !== false) {
                $total_rep = str_replace(">", "", $this->data['total_rep']);
                $query->where('total_rep', ">", $total_rep);
            } else if (strpos($this->data['total_rep'], "<=") !== false) {
                $total_rep = str_replace("<=", "", $this->data['total_rep']);
                $query->where('total_rep', "<=", $total_rep);
            } else if (strpos($this->data['total_rep'], "<") !== false) {
                $total_rep = str_replace("<", "", $this->data['total_rep']);
                $query->where('total_rep', "<", $total_rep);
            } else if (strpos($this->data['total_rep'], "=") !== false) {
                $total_rep = str_replace("=", "", $this->data['total_rep']);
                $query->where('total_rep', "=", $total_rep);
            } else {
                $query->where('total_rep', "=", $this->data['total_rep']);
            }
        }

        if (isset($this->data['pendiente_rep'])) {
            if (strpos($this->data['pendiente_rep'], ">=") !== false) {
                $pendiente_rep = str_replace(">=", "", $this->data['pendiente_rep']);
                $query->where('pendiente_rep', ">=", $pendiente_rep);
            } else if (strpos($this->data['pendiente_rep'], ">") !== false) {
                $pendiente_rep = str_replace(">", "", $this->data['pendiente_rep']);
                $query->where('pendiente_rep', ">", $pendiente_rep);
            } else if (strpos($this->data['pendiente_rep'], "<=") !== false) {
                $pendiente_rep = str_replace("<=", "", $this->data['pendiente_rep']);
                $query->where('pendiente_rep', "<=", $pendiente_rep);
            } else if (strpos($this->data['pendiente_rep'], "<") !== false) {
                $pendiente_rep = str_replace("<", "", $this->data['pendiente_rep']);
                $query->where('pendiente_rep', "<", $pendiente_rep);
            } else if (strpos($this->data['pendiente_rep'], "=") !== false) {
                $pendiente_rep = str_replace("=", "", $this->data['pendiente_rep']);
                $query->where('pendiente_rep', "=", $pendiente_rep);
            } else {
                $query->where('pendiente_rep', "=", $this->data['pendiente_rep']);
            }
        }



        $query->orderBy("pendiente_rep", "DESC");

        $query->selectRaw("ROW_NUMBER() OVER(ORDER BY pendiente_rep DESC) as no_fila,
        rfc_proveedor, proveedor, cantidad_cfdi, total_cfdi, total_rep, pendiente_rep, ultima_ubicacion_sao
        , ultima_ubicacion_contabilidad, fecha_ultimo_cfdi_con_ubicacion");

        return $query;
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:J1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' => 'arial',
                        'bold' => true
                    ]]);

                $event->sheet->getColumnDimension('C')->setAutoSize(false);
                $event->sheet->getColumnDimension('C')->setWidth(18);

            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function headings(): array
    {
        return array(['#', 'RFC Proveedor', 'Proveedor', '# CFDI Emitidos', 'Monto CFDI', 'Monto REP', 'Pendiente REP'
            , 'Último Proyecto SAO', 'Último Proyecto Contabilidad', 'Fecha Último CFDI Con Ubicación'
        ]);
    }

}
