<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 27/11/2019
 * Time: 07:04 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class RequisicionEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.requisiciones_eliminadas';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'opciones',
        'id_obra',
        'id_empresa',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'id_usuario',
        'id_area_compradora',
        'id_tipo',
        'id_area_solicitante',
        'folio_compuesto',
        'concepto',
        'registro',
        'timestamp_registro',
        'usuario_elimina',
        'motivo_eliminacion',
        'fecha_eliminacion'
    ];

    public $timestamps = false;
}