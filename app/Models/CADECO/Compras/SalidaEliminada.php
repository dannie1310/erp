<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:58 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class SalidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.salidas_eliminadas';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'id_obra',
        'id_concepto',
        'id_empresa',
        'opciones',
        'diferencia',
        'comentario',
        'FechaHoraRegistro',
        'NumeroFolioAlt',
        'motivo_eliminacion'
    ];
}