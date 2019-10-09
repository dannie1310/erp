<?php


namespace App\CSV;

use App\Models\CADECO\Solicitud;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Facades\Context;

class PagoLayout implements FromCollection, WithHeadings
{
    public function __construct()
    {
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function collection()
    {
        $user = array();
        $facturas = DB::connection('cadeco')->select(DB::raw("
                select t.id_transaccion, t.fecha, t.referencia, e.razon_social, t.monto, m.nombre from transacciones t
                join empresas e on e.id_empresa = t.id_empresa
                join monedas m on m.id_moneda = t.id_moneda
                where t.id_transaccion not in (select id_referente from transacciones where tipo_transaccion = 68)
                and id_obra = ".Context::getIdObra()."
                and t.tipo_transaccion = 65
                and t.estado = 1
                order by t.id_empresa, t.monto
                "));

        $solicitudes = Solicitud::query()->get();

        foreach ($facturas as $factura){
            $fecha  = new DateTime($factura->fecha);
            $user[]=array(
                'id_transaccion' => $factura->id_transaccion,
                'fecha' => date_format($fecha, 'd/m/Y'),
                'referencia' => str_replace(',',' ', $factura->referencia),
                'razon_social' => str_replace(',',' ', $factura->razon_social),
                'monto' => $factura->monto,
                'moneda' => $factura->nombre,
                '   ',
                '   ',
                '   ',
                '   ',
                '   '
            );
        }

        foreach ($solicitudes as $solicitud){
            if($solicitud->pago == null){
                $fecha  = new DateTime($solicitud->fecha);
                $proveedor = null;
                if($solicitud->id_referente != null){
                    $proveedor = $solicitud->fondo->descripcion;
                }
                if($solicitud->id_empresa != null){
                    $proveedor =  $solicitud->empresa->razon_social;
                }
                $user[]=array(
                    'id_transaccion' => $solicitud->id_transaccion,
                    'fecha' => date_format($fecha, 'd/m/Y'),
                    'referencia' => str_replace(',',' ', $solicitud->referencia),
                    'razon_social' => str_replace(',',' ', $proveedor),
                    'monto' => $solicitud->monto,
                    'moneda' => $solicitud->moneda->nombre,
                    '   ',
                    '   ',
                    '   ',
                    '   ',
                    '   '
                );
            }
        }

        return collect($user);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return array([
            'Identificador Factura',
            'Fecha Factura',
            'Referencia de Factura',
            'Proveedor',
            'Monto de Factura',
            'Moneda de Factura',
            'Cuenta Cargo',
            'Fecha de Pago',
            'Referencia de Pago',
            'Tipo de Cambio (de acuerdo a moneda de factura y moneda de cuenta pagadora)',
            'Monto Pagado (en moneda de la cuenta pagadora)']);
    }
}
