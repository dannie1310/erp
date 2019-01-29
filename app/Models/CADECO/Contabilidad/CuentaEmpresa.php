<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 11:45 AM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Models\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaEmpresa extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cuentas_empresas';
    protected $fillable = ['id_obra',
                            'id_empresa',
                            'id_tipo_cuenta_empresa',
                            'cuenta',
                            'registro',
                            'estatus'
                            ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->registro = auth()->id();
            $model->estatus = 1;
            $model->obra = Context::getIdObra();
        });
    }

    public function empresa(){
        $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function tipoCuentaEmpresa(){
        $this->belongsTo(TipoCuentaEmpresa::class, 'id_tipo_cuenta_empresa', 'id');
    }
}
