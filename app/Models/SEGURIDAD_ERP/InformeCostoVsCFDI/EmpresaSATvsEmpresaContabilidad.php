<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 21/05/2020
 * Time: 02:00 AM
 */

namespace App\Models\SEGURIDAD_ERP\InformeCostoVsCFDI;


use App\Scopes\EstatusMayorACeroScope;
use Illuminate\Database\Eloquent\Model;

class EmpresaSATvsEmpresaContabilidad extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'InformeCostoVsCFDI.empresas_sat_vs_empresas_contabilidad';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa_sat',
        'id_empresa_contabilidad',
        'usuario_activo',
        'usuario_desactivo',
        'fecha_hora_desactivacion',
        'estatus',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstatusMayorACeroScope());
    }
}
