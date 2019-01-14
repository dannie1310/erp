<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 08:56 AM
 */

namespace App\Models\CADECO\Subcontratos;

use App\Models\CADECO\Transaccion;
use Illuminate\Database\Eloquent\Model;

class FondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.fondos_garantia';
    protected $primaryKey = 'id_subcontrato';
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

    }

    public function subcontrato()
    {
        return $this->hasOne(Transaccion::class, "id_subcontrato");
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoFondoGarantia::class,"id_fondo_garantia");

    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudMovimientoFondoGarantia::class,"id_fondo_garantia");

    }
}