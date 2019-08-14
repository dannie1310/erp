<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 03:35 PM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Contabilidad\CuentaEmpresa;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
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
    protected $fillable = [
        'tipo_empresa',
        'razon_social',
        'UsuarioRegistro',
        'id_ctg_bancos',
        'rfc'
    ];
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->FechaHoraRegistro = date('Y-m-d h:i:s');
            $model->UsuarioRegistro =  auth()->id();
        });
    }

    public function cuentasEmpresa()
    {
        return $this->hasMany(CuentaEmpresa::class, 'id_empresa')
            ->where('Contabilidad.cuentas_empresas.estatus', '=', 1);
    }

    public function cuentas(){
        return $this->hasMany(Cuenta::class, 'id_empresa', 'id_empresa');
    }

    public function subcontrato(){
        return $this->hasMany(Subcontrato::class, 'id_empresa', 'id_empresa');
    }

    public function estimacion(){
        return $this->hasMany(Estimacion::class, 'id_empresa', 'id_empresa');
    }

    public function cuentasBancarias()
    {
        return $this->hasMany(CuentaBancariaEmpresa::class, 'id_empresa', 'id_empresa');
    }

    public function scopeConCuentas($query)
    {
        return $query->has('cuentasEmpresa');
    }

    public function scopeBancos($query)
    {
        return $query->where('tipo_empresa', '=', 8);
    }

    public function scopeParaSubcontratistas($query)
    {
        return $query->has('subcontrato')->has('estimacion')->distinct('id_empresa')->orderBy('razon_social');
    }

    public function scopeResponsableFondoFijo($query)
    {
        return $query->where('tipo_empresa',32);
    }

    public function scopeProveedorContratista($query)
    {
        return $query->whereIn('tipo_empresa',[1,2,3]);
    }

    public function getTipoAttribute()
    {
        if($this->tipo_empresa == 1){
            return 'Proveedor';
        }
        if($this->tipo_empresa == 2){
            return 'Contratista';
        }
        if($this->tipo_empresa == 3){
            return 'Proveedor y Contratista';
        }
        if($this->tipo_empresa == 32){
            return 'Responsables Fondos Fijos';
        }
    }

    public function scopeBeneficiarioCuentaBancaria($query)
    {
        return $query->has('cuentasBancarias')->toSql;
    }
}
