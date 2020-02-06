<?php


namespace App\CSV\seguridad;


use App\Models\SEGURIDAD_ERP\Finanzas\TransaccionesEfos;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransaccionEfo implements FromCollection, WithHeadings
{
    use Exportable;

    public function __construct()
    {

    }

    /**
     * @return Collection
     */
    public function collection()
    {
        // TODO: Implement collection() method.
        $transacciones_efos = array();
        $transacciones = TransaccionesEfos::all();
        foreach ($transacciones as $transaccion)
        {
            $transacciones_efos[] = array(
                'id_transaccion' => $transaccion->getKey(),
                'base_datos' => $transaccion->base_datos,
                'obra' => $transaccion->obra,
                'razon_social' => $transaccion->razon_social,
                'rfc' => $transaccion->rfc,
                'tipo_transaccion' => $transaccion->tipo_transaccion,
                'folio_transaccion' => '#'.$transaccion->folio_transaccion,
                'comentario' => $transaccion->comentario,

            );
        }
        dd($transacciones);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // TODO: Implement headings() method.
        return array([
            'Identificador',
            'Base de Datos',
            'Obra',
            'Razón Social',
            'RFC',
            'Transacción',
            'Folio',
            'Comentario',
            'Usuario',
            'Fecha Hora de Registro',
            'Fecha Transacción',
            'Fecha Presunto',
            'Fecha Definitivo',
            'Monto',
            'Moneda',
            'T.C.',
            'Monto MXP',
            'Grado de Alerta'
        ]);
    }
}
