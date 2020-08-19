<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.empresas_contactos';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa_proveedora',
        'nombre',
        'correo_electronico',
        'telefono',
        'puesto',
        'notas',
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class,"id_empresa_proveedora", "id");
    }

}
