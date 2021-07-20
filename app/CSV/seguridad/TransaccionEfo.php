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
        foreach ($transacciones as $transaccion) {
            $transacciones_efos[] = array(
                'id_transaccion' => $transaccion->getKey(),
                'base_datos' => $transaccion->base_datos,
                'obra' => $transaccion->obra,
                'razon_social' => $transaccion->razon_social,
                'rfc' => $transaccion->rfc,
                'tipo_transaccion' => $this->limpiaCadena($transaccion->tipo_transaccion),
                'folio_transaccion' => '#' . $transaccion->folio_transaccion,
                'comentario' => $transaccion->comentario,
                'usuario' => $transaccion->usuario ? $transaccion->usuario->nombre_completo : '',
                'fecha_hora_registro' => date('d/m/Y H:i', strtotime($transaccion->fecha_hora_registro)),
                'fecha_transaccion' => date('d/m/Y', strtotime($transaccion->fecha_transaccion)),
                'fecha_presunto' => date('d/m/Y', strtotime($transaccion->fecha_presunto)),
                'fecha_definitivo' => ($transaccion->fecha_definitivo != NULL) ? date("d/m/Y", strtotime($transaccion->fecha_definitivo)) : '',
                'monto' => $transaccion->monto_format,
                'moneda' => $transaccion->moneda,
                'tipo_cambio' => (int)$transaccion->tipo_cambio,
                'monto_mxp' => $transaccion->monto_format_mxp,
                'grado_alerta' => $transaccion->alerta_estado_descripcion
            );
        }
        return collect($transacciones_efos);
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
            'Razon Social',
            'RFC',
            'Tipo Transaccion',
            'Folio',
            'Comentario',
            'Usuario',
            'Fecha Hora de Registro',
            'Fecha Transaccion',
            'Fecha Presunto',
            'Fecha Definitivo',
            'Monto',
            'Moneda',
            'T.C.',
            'Monto MXN',
            'Grado de Alerta'
        ]);
    }

    private function limpiaCadena($string)
    {
        $string = preg_replace("/[^0-9a-zA-Z\s]+/", "", $string);
        return strtoupper($string);
    }
}
