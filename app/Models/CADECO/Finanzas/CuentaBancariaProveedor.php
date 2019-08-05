<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 09:59 AM
 */

namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Banco;
use App\Models\CADECO\Empresa;
use Illuminate\Database\Eloquent\Model;

class CuentaBancariaProveedor extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.cuentas_bancarias_proveedores';
    public $timestamps = false;
    protected $fillable = [
        'id_empresa',
        'id_banco',
        'cuenta_clabe',
        'sucursal',
        'tipo',
        'plaza',
        'id_moneda'
    ];
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->fecha_hora_registro = date('Y-m-d h:i:s');
            $model->registro =  auth()->id();
        });
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id_banco', 'id_empresa');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function complemento(){
        return $this->hasOne(BancoComplemento::class, 'id_empresa', 'id_banco');
    }
}
