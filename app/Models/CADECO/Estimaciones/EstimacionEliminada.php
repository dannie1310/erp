<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 04/11/2019
 * Time: 01:40 PM
 */

namespace App\Models\CADECO\Estimaciones;


use Illuminate\Database\Eloquent\Model;

class EstimacionEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Estimaciones.estimaciones_eliminadas';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'impreso',
        'id_obra',
        'id_empresa',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'monto',
        'saldo',
        'autorizado',
        'impuesto',
        'anticipo',
        'tipo_cambio',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'IVARetenido',
        'id_usuario',
        'usuario_elimina',
        'motivo_eliminacion',
        'fecha_eliminacion'
    ];

    public $timestamps = false;
}