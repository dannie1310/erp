<?php


namespace App\CSV;


use App\Models\CADECO\CotizacionCompra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CotizacionLayout implements FromCollection, WithHeadings
{
    protected $cotizacion;

    protected $headerFijos = [
        "#" => "#",
        "Id" => "Id",
        "descripcion_span" => "descripcion_span",
        "unidad" => "unidad",
        "cantidad solicitada" => "cantidad solicitada",
        "cantidad_aprobada" => "cantidad_aprobada",
        "Precio Unitario" => "Precio Unitario",
        "% Descuento" => "% Descuento",
        "Precio Total" => "Precio Total",
        "Moneda" => "Moneda",
        "Precio Total Moneda Conversión" => "Precio Total Moneda Conversión",
        "Observaciones" => "Observaciones",
        "material_sao" => "material_sao",
        "idrqctoc_solicitudes_partidas" => "idrqctoc_solicitudes_partidas",
        "idrqctoc_solicitudes" => "idrqctoc_solicitudes",
        "idmoneda" => "idmoneda",
        "separador" => "separador",
    ];

    /**
     * @var array
     */
    protected $headerDinamicos = [
        "Precio Unitario" => "Precio Unitario",
        "% Descuento" => "% Descuento",
//        "Precio Total" => "Precio Total",
//        "Moneda" => "Moneda",
//        "Precio Total Moneda Conversión" => "Precio Total Moneda Conversión",
//        "Observaciones" => "Observaciones",
//        "material_sao" => "material_sao",
//        "idrqctoc_solicitudes_partidas" => "idrqctoc_solicitudes_partidas",
//        "idrqctoc_solicitudes" => "idrqctoc_solicitudes",
//        "idmoneda" => "idmoneda",
//        "separador" => "separador",
    ];
//    protected $headerDinamicos = [
//        "Precio Unitario" => "Precio Unitario",
//        "% Descuento" => "% Descuento",
//        "Precio Total" => "Precio Total",
//        "Moneda" => "Moneda",
//        "Precio Total Moneda Conversión" => "Precio Total Moneda Conversión",
//        "Observaciones" => "Observaciones",
//        "material_sao" => "material_sao",
//        "idrqctoc_solicitudes_partidas" => "idrqctoc_solicitudes_partidas",
//        "idrqctoc_solicitudes" => "idrqctoc_solicitudes",
//        "idmoneda" => "idmoneda",
//        "separador" => "separador",
//    ];

    protected $rowOperacionesExtra = [
        "descuento" => "% Descuento",
        "subtotal_precio_pesos" => "Subtotal Precios PESO MXP",
        "subtotal_precio_dolar" => "Subtotal Precios DOLAR USD",
        "subtotal_precio_euro" => "Subtotal Precios EURO",
        "tc_usd" => "TC USD",
        "tc_eur" => "TC EURO",
        "id_moneda" => "Moneda de Conv",
        "subtotal_moneda_conv" => "Subtotal Moneda Conv",
        "iva" => "IVA",
        "total" => "Total",
        "fecha" => "Fecha de Presupuesto",
        "porcentaje_anticipo_pactado" => "Pago en Parcialdades (%)",
        "anticipo" => "% Anticipo",
        "dias_credito" => "Crédito días",
        "plazo_entrega" => "Tiempo de Entrega (días)",
        "vigencia" => "Vigencia días",
        "observaciones" => "Observaciones Generales",
    ];

    public function __construct(CotizacionCompra $cotizacion)
    {
//        $this->sheet;
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
            );

        }

        return collect($user);
    }

    public function getFile(){
        return $this->headerDinamicos;
    }

    public function headings(): array
    {
        return [$this->headerDinamicos['Precio Unitario']=>'B4'];
    }
}