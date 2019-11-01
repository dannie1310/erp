<?php

namespace App\CSV;

use App\Models\CADECO\CotizacionCompra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CotizacionLayout implements FromCollection, WithHeadings
{
    protected $cotizacion;

    public function __construct(CotizacionCompra $cotizacion)
    {
        $this->cotizacion = $cotizacion;
    }

    public function collection()
    {
        $user = array();
        foreach ($this->cotizacion->cotizaciones as $cot){
            $folio = str_pad($cot->folio,6,0,0);
            $user[]=array(
                "folio"=>$this->cotizacion->folio_format. ' '.chunk_split($folio, 3, ' '),
                "id"=>$cot->id_transaccion,
                "panda"=>$this->cotizacion->folio_format. ' '.chunk_split($folio, 3, ' '),
                "pandachou"=>$cot->id_transaccion
            );

        }

        return collect($user);
    }

    public function getFile(){
        $user = array();
        foreach ($this->cotizacion->cotizaciones as $cot){
            $folio = str_pad($cot->folio,6,0,0);
            $user[]=array(
                "folio"=>$this->cotizacion->folio_format. ' '.chunk_split($folio, 3, ' '),
                "id"=>$cot->id_transaccion,
            );

        }

        return collect($user);
    }

    public function panda(){
        return $this->headerDinamicos;
    }

    public function headings(): array
    {
        return array([' ',' ',' ',' ',' ',' ',$this->cotizacion->empresa->razon_social],
        ['#','DESCRIPCION','IDENTIFICADOR','UNIDAD','CANTIDAD_SOLICITADA','CANTIDAD_APROBADA','Precio Unitario','% Descuento','Precio Total','Moneda',
            'Precion Total Moneda Conversión','Observaciones']);
//        [$this->panda()['Precio Unitario'],$this->panda()['% Descuento']],[$this->getFile()]);
    }
}