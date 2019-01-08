<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 04:44 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Tesoreria\TraspasoCuentas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poliza extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_polizas';
    protected $primaryKey = 'id_int_poliza';

    protected $dates = ['fecha'];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra_cadeco', '=', Context::getIdObra());
        });

        static::creating(function ($model) {
            $model->id_obra_cadeco = Context::getIdObra();
        });
    }

    public function estatusPrepoliza()
    {
        return $this->belongsTo(EstatusPrepoliza::class, 'estatus', 'estatus');
    }

    public function transaccionInterfaz()
    {
        return $this->belongsTo(TransaccionInterfaz::class, 'id_tipo_poliza_interfaz', 'id_transaccion_interfaz');
    }

    public function tipoPolizaContpaq()
    {
        return $this->belongsTo(TipoPolizaContpaq::class, 'id_tipo_poliza_contpaq');
    }

    public function historicos()
    {
        return $this->hasMany(HistPoliza::class, 'id_int_poliza', 'id_int_poliza');
    }

    public function movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class, 'id_int_poliza');
    }

    public function getUsuarioSolicitaAttribute()
    {
        $usuarioBase = str_split($this->usuario_registro);
        $usuario = '';
        $auxiliar = 0;
        for ($a = 0; $a < count($usuarioBase); $a++) {
            if ($auxiliar == 1 && $usuarioBase[$a] != '|') {
                $usuario .= $usuarioBase[$a];
            }

            if ($usuarioBase[$a] == '|') {
                $auxiliar++;
            }
        }
        return $usuario;
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_transaccion_sao');
    }

    public function traspaso()
    {
        return $this->belongsTo(TraspasoCuentas::class, 'id_traspaso');
    }
}