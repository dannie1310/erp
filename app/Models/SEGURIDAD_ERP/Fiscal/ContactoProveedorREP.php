<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Scopes\EstatusMayorACeroScope;
use Illuminate\Database\Eloquent\Model;

class ContactoProveedorREP extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.contactos_proveedores';
    public $timestamps = false;

    public $fillable = [
        "id_proveedor_sat"
        , "correo"
        , "nombre"
        , "puesto"
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstatusMayorACeroScope);
    }

    public function desactivar()
    {
        $this->estatus = 0;
        $this->save();
    }

}
