<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 07:17 PM
 */

namespace App\Models\CADECO\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\IGH\Usuario;
use App\Models\MODULOSSAO\ControlRemesas\RemesaLiberada;
use Illuminate\Database\Eloquent\Model;

class DistribucionRecursoRemesa extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.distribucion_recursos_rem';

    protected $fillable = [
            'id_remesa',
            'id_obra',
            'folio',
            'fecha_hora_registro',
            'monto_autorizado',
            'usuario_registro',
            'estado'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
        });

        self::creating(function ($model) {
            $count = DistribucionRecursoRemesa::query()->count('id');

            $model->id_obra = Context::getIdObra();
            $model->folio = $count +1;
            $model->usuario_registro = auth()->id();
            $model->fecha_hora_registro = date('Y-m-d h:i:s');
            $model->estado = 1;
        });

        self::created(function($query)
        {

        });
    }

    public function remesaLiberada(){
        return $this->hasMany(RemesaLiberada::class, 'IDRemesa', 'id_remesa');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro','id_usuario');
    }

    public function usuarioCancelo() {
        return $this->belongsTo(Usuario::class, 'usuario_cancelo', 'id_usuario');
    }

    public function estado(){
        return $this->belongsTo(CtgEstadoDistribucionRecursoRemesa::class, 'estado', 'id');
    }

    public function partida(){
        return $this->belongsTo(DistribucionRecursoRemesaPartida::class, 'id', 'id_distribucion_recurso');
    }

    public function obra(){
        return $this->hasMany(Obra::class, 'id_obra', 'id_obra');
    }
}