<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 01:15 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaFondo;
use App\Models\CADECO\Finanzas\CtgTipoFondo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Fondo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'fondos';
    protected $primaryKey = 'id_fondo';

    public $timestamps = false;
    public $searchable = [
        'descripcion',
        'nombre',
        'saldo',
        'cuentaFondo.cuenta'
    ];
    protected $fillable = [
        'id_obra',
        'id_tipo',
        'id_responsable',
        'descripcion',
        'nombre',
        'fecha',
        'fondo_obra',
        'id_costo',
        'saldo'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function cuentaFondo()
    {
        return $this->hasOne(CuentaFondo::class, 'id_fondo', 'id_fondo')
            ->where('Contabilidad.cuentas_fondos.estatus', '=', 1);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_responsable', 'id_empresa');
    }

    public function tipoFondo()
    {
        return $this->belongsTo(CtgTipoFondo::class, 'id_tipo', 'id');
    }
    public function costo()
    {
        return$this->belongsTo(Costo::class, 'id_costo','id_costo');
    }

    public function scopeSinCuenta($query)
    {
        return $query->doesntHave('cuentaFondo');
    }

    public function scopeConResponsable($query)
    {
        return $query->where('id_responsable', '>', 0);
    }

    public function aumentaSaldo(PagoReposicionFF $pago){
        $this->saldo = $this->saldo + ($pago->monto * (1/$pago->tipo_cambio) * -1);
        $this->save();
    }

    /**
     * Disminuye al saldo la cantidad enviada.
     * @param $cantidad
     */
    public function disminuyeSaldo($cantidad)
    {
        $this->saldo = $this->saldo - $cantidad;
        $this->save();
    }
}
