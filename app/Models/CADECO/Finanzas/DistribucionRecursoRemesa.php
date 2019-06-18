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
            'monto_distribuido',
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
            $model->estado = 0;
        });

        self::created(function($query)
        {

        });
    }

    public function remesaLiberada(){
        return $this->belongsTo(RemesaLiberada::class,  'id_remesa', 'IDRemesa');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro','idusuario');
    }

    public function usuarioCancelo() {
        return $this->belongsTo(Usuario::class, 'usuario_cancelo', 'idusuario');
    }

    public function estatus(){
        return $this->belongsTo(CtgEstadoDistribucionRecursoRemesa::class, 'estado', 'estado');
    }

    public function partida(){
        return $this->hasMany(DistribucionRecursoRemesaPartida::class, 'id_distribucion_recurso','id');
    }

    public function obra(){
        return $this->hasMany(Obra::class, 'id_obra', 'id_obra');
    }

    public function remesaValidaEstado(){
        switch ($this->estado){
            case 0:
                abort(400, 'Archivo de distribución de recurso no ha sido descargado.');
                break;
            case 2:
                abort(400, 'Archivo procesado previamente.');
                break;
            case -1:
                abort(400, 'La distribución de recursos esta cancelada');
                break;
        }
        return $this;

    }
}
