<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'proveedores';
    protected $primaryKey = 'IdProveedor';

    /**
     * Relaciones
     */
    public function cuentasProveedores()
    {
        return $this->hasMany(CuentaProveedor::class, 'IdProveedor', 'IdProveedor');
    }

    public function proveedorXSerie()
    {
        return $this->hasMany(ProveedorXSerie::class, 'IDproveedor', 'IdProveedor');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'IdUsuario', 'idusuario');
    }

    public function proveedoresParaReembolso()
    {
        return $this->hasMany(ProveedorReembolso::class, 'idproveedor', 'IdProveedor');
    }

    /**
     * Scopes
     */
    public function scopePorRFC($query)
    {
        return $query->where('Estatus', 1)->whereIn('TipoProveedor',[1,2]);
    }

    public function scopePorSerie($query, $idserie)
    {
        return $query->whereHas('proveedorXSerie', function ($q) use($idserie){
            return $q->where('IDserie', $idserie);
        });
    }

    public function scopePorTipos($query, $tipos)
    {
        return $query->whereIn('TipoProveedor', explode(",", $tipos));
    }

    public function scopePorEstados($query, $estados)
    {
        return $query->whereIn('Estatus', explode(",",$estados));
    }

    public function scopeEmpleados($query)
    {
        $usuarios = \App\Models\IGH92\Usuario::where('usuario_estado', '=', 2)->pluck('idusuario');
        return $query->where('IdUsuario', '>', 0)->whereIn('IdUsuario', $usuarios);
    }

    public function scopeParaReembolso($query)
    {
        return $query->whereHas('proveedoresParaReembolso');
    }

    public function scopeProveedorDisponibleParaRegistrar($query, $idSerie)
    {
        $serie = Serie::where('idseries', $idSerie)->first();
        $idproveedores = SolChequeComprobacion::withoutGlobalScopes()->whereRaw("Importe>SaldoComprobado AND
                estatus in(24,25,34,35) AND
                IdTipoPago=4 AND
                Serie ='" . $serie->Descripcion . "'")->selectRaw('distinct IdProveedor')->pluck('IdProveedor');
        return $query->whereIn('IdProveedor',$idproveedores);
    }

    /**
     * Atributos
     */
    public function getRfcSinGuionesAttribute()
    {
        return str_replace("-", "", $this->RFC);
    }

    /**
     * Métodos
     */
}
