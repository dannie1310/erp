<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:25 PM
 */

namespace App\Models\CADECO\Tesoreria;


use App\Facades\Context;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Transaccion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoBancario extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Tesoreria.movimientos_bancarios';
    protected $primaryKey = 'id_movimiento_bancario';

    protected $fillable = [
        'fecha',
        'id_cuenta',
        'id_tipo_movimiento',
        'importe',
        'impuesto',
        'observaciones'
    ];

    public $searchable = [
        'observaciones',
        'tipo.descripcion',
        'cuenta.numero'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        self::creating(function ($model) {
            $mov = self::orderBy('numero_folio', 'DESC')->first();
            $folio = $mov ? $mov->numero_folio + 1 : 1;

            $model->estatus = 1;
            $model->registro = auth()->id();
            $model->id_obra = Context::getIdObra();
            $model->numero_folio = $folio;

            $tipo = TipoMovimiento::query()->find($model->id_tipo_movimiento);

            if ($tipo->naturaleza == 2) {
                $model->importe = -1 * abs($model->importe);
                $model->impuesto = -1 * abs($model->impuesto);
            }
        });

        self::updating(function ($model) {
            $tipo = TipoMovimiento::query()->find($model->id_tipo_movimiento);

            if ($tipo->naturaleza == 2) {
                $model->importe = -1 * abs($model->importe);
                $model->impuesto = -1 * abs($model->impuesto);
            }
        });

        self::deleting(function ($model) {
            $model->transacciones()->update(['estado' => '-2']);
        });
    }

    public function tipo()
    {
        return $this->belongsTo(TipoMovimiento::class, 'id_tipo_movimiento');
    }

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'id_cuenta');
    }

    public function transacciones()
    {
        return $this->belongsToMany(Transaccion::class, 'Tesoreria.movimiento_transacciones', 'id_movimiento_bancario', 'id_transaccion')
            ->whereNull('Tesoreria.movimiento_transacciones.deleted_at')
            ->withTimestamps();
    }
}