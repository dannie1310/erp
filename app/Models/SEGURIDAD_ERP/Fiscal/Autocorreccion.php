<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use Illuminate\Database\Eloquent\Model;

class Autocorreccion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.autocorrecciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_proveedor_sat',
        'fecha_hora_registro',
        'usuario_registro',
        'estado'
    ];

    public $timestamps = false;

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }
}
