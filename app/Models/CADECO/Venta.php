<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 07:33 PM
 */

namespace App\Models\CADECO;

use App\PDF\VentaFormato;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;


class Venta extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;

    protected $fillable = [
        'id_concepto',
        'id_empresa',
        'opciones',
        'fecha',
        'observaciones',
        'referencia',
        'monto',
        'saldo',
        'impuesto',
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 38);
        });
    }

    /**
     * Relaciones Eloquent
     */

    public function partidas()
    {
        return $this->hasMany(VentaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function pdfVenta(){
        $venta = new VentaFormato($this);
        return $venta->create();
    }

    public function registrar($data)
    {
        try {
            $fecha_venta = new DateTime($data['fecha']);
            $fecha_venta->setTimezone(new DateTimeZone('America/Mexico_City'));
            DB::connection('cadeco')->beginTransaction();

            $venta = $this->create(
              [
                  'fecha' => $fecha_venta,
                  'id_empresa' => $data['id_empresa'],
                  'id_concepto' => $data['id_concepto'],
                  'referencia' => $data['referencia'],
                  'monto' => $data['monto_total'],
                  'saldo' => $data['saldo_total'],
                  'impuesto' => $data['impuesto_total'],
                  'observaciones' => $data['observaciones']
              ]
            );

            foreach ($data['partidas'] as $partida)
            {
                $venta->partidas()->create(
                    [
                        'id_transaccion' => $venta->id_transaccion,
                        'id_material' => $partida['id_material'],
                        'unidad' => $partida['unidad'],
                        'cantidad' => $partida['cantidad'],
                        ''
                    ]
                );
            }

            DB::connection('cadeco')->commit();
            return $venta;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}