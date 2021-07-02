<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;

class FacturasRepositorioLogADD extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Finanzas.repositorio_facturas_log_ADD';
    public $timestamps = false;

    protected $fillable =[
        "id_factura_repositorio",
        "log_add"
    ];

}
