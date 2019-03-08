<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 12:15 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Models\CADECO\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoCuentaEmpresa extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.tipos_cuentas_empresas';

    public function cuentas()
    {
        return $this->hasMany(CuentaEmpresa::class, 'id_tipo_cuenta_empresa');
    }

    public function scopeDisponiblesParaEmpresa($query, $id_empresa){

        $existentes = self::query()->whereHas('cuentas', function ($q) use ($id_empresa) {
            return $q->whereHas('empresa', function ($q) use ($id_empresa) {
                return $q->where('id_empresa', '=', $id_empresa);
            });
        })->pluck('id');

        return $query->whereNotIn('id', $existentes);
    }
}