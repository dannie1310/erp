<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:26 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class EmpresaSAT extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.ListaEmpresasSAT';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $fillable = [
        'rfc',
        'razon_social'
    ];

    public function cfd()
    {
        return $this->hasMany(CFDSAT::class,"rfc_receptor", "rfc");
    }

    public function empresaContabilidad()
    {
        return $this->hasMany(Empresa::class,"IdEmpresaSAT", "id");
    }

    public function empresaContabilidadConDiferencia()
    {
        return $this->hasMany(Empresa::class,"IdEmpresaSAT", "id")
            ->solicitudes();
    }

    public function scopeSolicitudes($query){
        return $query->whereHas('empresaContabilidadConDiferencia');
    }

}
