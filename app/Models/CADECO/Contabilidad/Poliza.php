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
use App\Models\CADECO\Transaccion;
use App\Traits\DateFormatTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poliza extends Model
{
    use SoftDeletes, DateFormatTrait;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_polizas';
    protected $primaryKey = 'id_int_poliza';

    protected $fillable = [
        'concepto',
        'fecha',
        'estatus',
        'lanzable',
        'poliza_contpaq'
    ];

    protected $dates = ['fecha'];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra_cadeco', '=', Context::getIdObra());
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

    public function transaccionAntecedente()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion_sao');
    }

    public function traspaso()
    {
        return $this->belongsTo(TraspasoCuentas::class, 'id_traspaso');
    }

    public function valido() {
        return $this->hasOne(PolizaValido::class, 'id_int_poliza');
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->id_int_poliza);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }
    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->timestamp_regsitro);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function getFechaHoraRegistroOrdenAttribute()
    {
        $date = date_create($this->timestamp_regsitro);
        return date_format($date,"YmdHis");
    }
}