<?php

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaEmpresa extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cuentas_empresas';
    protected $fillable = [
        'id_empresa',
        'id_tipo_cuenta_empresa',
        'cuenta',
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        self::creating(function ($model) {
            $model->estatus = 1;
            $model->registro = auth()->id();
            $model->id_obra = Context::getIdObra();
        });
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function tipoCuentaEmpresa()
    {
        return $this->belongsTo(TipoCuentaEmpresa::class, 'id_tipo_cuenta_empresa', 'id');
    }
}