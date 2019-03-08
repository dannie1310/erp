<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 03:35 PM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Contabilidad\CuentaEmpresa;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.empresas';
    protected $primaryKey = 'id_empresa';

    public $timestamps = false;
    public $searchable = [
        'razon_social',
        'rfc'
    ];

    public function cuentasEmpresa()
    {
        return $this->hasMany(CuentaEmpresa::class, 'id_empresa')
            ->where('Contabilidad.cuentas_empresas.estatus', '=', 1);
    }

    public function cuentas(){
        return $this->hasMany(Cuenta::class, 'id_empresa', 'id_empresa');
    }

    public function subcontrato(){
        return $this->belongsTo(Subcontrato::class, 'id_empresa', 'id_empresa')
                    ->whereIn('tipo_transaccion', [51, 52]);
    }

    public function scopeConCuentas($query)
    {
        return $query->has('cuentasEmpresa');
    }

    public function scopeParaBancos($query)
    {
        return $query->where('tipo_empresa', '=', 8);
    }

    public function scopeParaSubcontratistas($query)
    {
        return $query->has('subcontrato')->distinct('id_empresa')->orderBy('razon_social');
    }
}