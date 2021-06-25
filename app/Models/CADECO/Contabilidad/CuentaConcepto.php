<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 01:48 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Models\CADECO\Concepto;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaConcepto extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cuentas_conceptos';
    protected $fillable = [
        'cuenta',
        'id_concepto'
    ];
    public $searchable = [
        'cuenta',
        'concepto.descripcion',
        'concepto.clave_concepto',
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->whereHas('concepto');
        });
    }

    /**
     * Relaciones
     */
    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro');
    }
}
