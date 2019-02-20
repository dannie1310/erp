<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 08:00 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Contabilidad\CuentaBanco;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.cuentas';
    protected $primaryKey = 'id_cuenta';

    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function scopeParaTraspaso($query)
    {
        return $query->whereHas('empresa', function ($q) {
            $q->where('tipo_empresa', '=', 8);
        })
            ->whereRaw('ISNUMERIC(numero) = 1');
    }

    public function cuentaBanco(){
        return $this->hasMany(CuentaBanco::class, 'id_cuenta', 'id_cuenta');
    }
}