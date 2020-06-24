<?php
/**
 * Created by PhpStorm.
 * User: jlopeza
 * Date: 02/06/2020
 * Time: 06:14 PM
 */

namespace App\Models\CADECO\Catalogos;

use Illuminate\Database\Eloquent\Model;

class UnificacionProveedoresCambios extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Catalogos.unificacion_proveedores_cambios';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_unificacion',
        'id_empresa_unificada',
        'tipo_empresa_unificada',
        'id_transaccion',
        'id_solicitud_movimiento',
        'id_cuenta_bancaria_empresa',
    ];
}
