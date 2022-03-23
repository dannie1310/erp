<?php

namespace App\Models\SEGURIDAD_ERP\EsquemaAutorizacion;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'EsquemaAutorizacion.transacciones';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_tipo_transaccion',
        'base_datos',
        'proyecto',
        'opciones',
        'numero_folio',
        'fecha',
        'fecha_registro',
        'razon_social',
        'rfc',
        'observaciones',
        'monto',
        'moneda',
        'hash',
        'usuario_registro',
        'fecha_hora_registro',
        'estado',
    ];


    /**
     * Relaciones
     */

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    /**
     * Scopes
     */

    public function scopeRegistrada($query)
    {
        return $query->where("estado","=",0);
    }
}
