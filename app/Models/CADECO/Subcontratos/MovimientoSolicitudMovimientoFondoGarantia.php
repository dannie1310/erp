<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\Subcontratos;

use Illuminate\Database\Eloquent\Model;

class MovimientoSolicitudMovimientoFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.solicitud';
    protected $fillable = ['id_solicitud',
                            'id_movimieto_antecedente',
                            'fecha',
                            'referencia',
                            'importe',
                            'observaciones',
                            'usuario_registra',
                            'created_at'
                            ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

    }



}