<?php


namespace App\CSV\Contabilidad;


use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\PolizaCFDIRequerido;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PolizasCFDI implements  FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function query()
    {

        $query = PolizaCFDIRequerido::where("estatus","=",1);

        if (isset($this->data['scope']) && $this->data['scope'] == "deEgresos") {
            $query->where('tipo', '=', "Egresos");
        }

         if (isset($this->data['startDate'])) {
            $query->where('fecha', '>=', $this->data['startDate']);
        }
        if (isset($this->data['endDate'])) {
            $query->where('fecha', '<=', $this->data['endDate']);
        }

        if (isset($this->data['base_datos_ctpq'])) {
            $query->where([['base_datos_contpaq', 'like', '%' .$this->data['base_datos_ctpq']. '%' ]]);
        }
        if (isset($this->data['empresa_ctpq'])) {
            $query->join("Contabilidad.ListaEmpresas as pol_empresa", "pol_empresa.AliasBDD","=","polizas_cfdi_requerido.base_datos_contpaq")
                ->where('pol_empresa.Nombre', 'like', '%' .$this->data['empresa_ctpq']. '%' );
        }
        if (isset($this->data['ejercicio'])) {
            $query->where('ejercicio', '=', $this->data['ejercicio']);
        }
        if (isset($this->data['periodo'])) {
            $query->where('periodo', '=', $this->data['periodo']);
        }
        if (isset($this->data['tipo_poliza'])) {
            $query->where('tipo', 'like', '%' .$this->data['tipo_poliza']. '%');
        }
        if (isset($this->data['folio_poliza'])) {
            $query->where('folio', 'like', '%' .$this->data['folio_poliza']. '%');
        }
        if (isset($this->data['fecha_poliza'])) {
            $query->whereBetween('fecha',  request( 'fecha_poliza' )." 00:00:00",request( 'fecha_poliza' )." 23:59:59" );
        }

        $query->orderBy("base_datos_contpaq", "ASC")
            ->orderBy("ejercicio", "DESC")
            ->orderBy("periodo", "DESC");

        $query->selectRaw("ROW_NUMBER() OVER(ORDER BY polizas_cfdi_requerido.base_datos_contpaq ASC, polizas_cfdi_requerido.ejercicio DESC, polizas_cfdi_requerido.periodo DESC) as no_fila,
        base_datos_contpaq, ejercicio, periodo, folio, fecha, tipo, monto");

        return $query;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $cellRange = 'A1:Y1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' => 'arial',
                        'bold' => true
                    ]]);
                },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'h' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }



    public function headings(): array
    {
        return array(['#', 'BD CONTPAQ', 'EJERCICIO', 'PERIODO', 'FOLIO', 'FECHA', 'TIPO PÓLIZA', 'MONTO']);
    }


}
