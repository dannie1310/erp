<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 01:46 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Compras\ActivoFijo;
use App\Models\CADECO\Compras\RequisicionComplemento;
use Illuminate\Support\Facades\DB;

class Requisicion extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'observaciones'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 16);
        });
    }

    public function partidas()
    {
        return $this->hasMany(RequisicionPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function complemento()
    {
        return $this->belongsTo(RequisicionComplemento::class,'id_transaccion', 'id_transaccion');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function registrar($datos)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $requisicion = $this->create([
                'fecha' => date('Y-m-d H:i:s'),
               'observaciones' => $datos['observaciones']
            ]);

            $requisicion_complemento = $this->complemento()->create([
                'id_transaccion' => $requisicion->id_transaccion,
                'id_area_compradora' => $datos['id_area_compradora'],
                'id_tipo' => $datos['id_tipo'],
                'id_area_solicitante' => $datos['id_area_solicitante'],
                'concepto' => $datos['concepto']
            ]);

            $requisicion_complemento->registrarActivoFijo($requisicion->id_transaccion);

            foreach ($datos['partidas'] as $partida)
            {
                $item = $requisicion->partidas()->create([
                    'id_transaccion' => $requisicion->id_transaccion,
                    'id_material' => $partida['material'] ? $partida['material']['id'] : NULL,
                    'unidad' => $partida['material'] ? $partida['material']['unidad'] : NULL,
                    'cantidad' => $partida['cantidad']
                ]);

                $complemento =$item->complemento()->create([
                    'id_item' => $item->id_item,
                    'descripcion_material' => $partida['material'] ? NULL : $partida['descripcion'],
                    'observaciones' => $partida['observaciones'],
                    'fecha_entrega' => $partida['fecha']
                ]);

            }
            DB::connection('cadeco')->commit();
            return $requisicion;
        }
        catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}