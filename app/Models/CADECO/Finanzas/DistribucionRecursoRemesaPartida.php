<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 07:17 PM
 */

namespace App\Models\CADECO\Finanzas;


use App\Models\MODULOSSAO\ControlRemesas\DocumentoLiberado;
use Illuminate\Database\Eloquent\Model;

class DistribucionRecursoRemesaPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.distribucion_recursos_rem_partidas';
    public $timestamps = false;

    protected $fillable = [
        'id_distribucion_recurso',
        'id_documento',
        'fecha_registro',
        'id_cuenta_abono',
        'id_cuenta_cargo',
        'id_moneda',
        'estado'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
        });

        self::creating(function ($model) {
            $model->fecha_registro = date('Y-m-d h:i:s');
            $model->estado = 1;
        });

        self::created(function($query)
        {

        });
    }

    public function distribucionRecurso(){
        return $this->hasMany(DistribucionRecursoRemesa::class, 'id', 'id_distribucion_recurso');
    }

    public function documentoLiberado(){
        return $this->belongsTo(DocumentoLiberado::class, 'id_documento', 'IDDocumento');
    }

    public function estado(){
        return $this->belongsTo(CtgEstadoDistribucionRecursoRemesaPartida::class, 'estado', 'id');
    }
}