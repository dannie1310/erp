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
    public function cancelar($id){
        $distribucion = DistribucionRecursoRemesa::find($id);

        if($distribucion->estado != 0 && $distribucion->estado != 1){
            throw New \Exception('La distribucion de recurso autorizado de remesa no puede ser cancelada, porque no tiene el estatus "generada" ');
        }else{
            $partida = DistribucionRecursoRemesaPartida::where('id_distribucion_recurso',$id)->get();

            foreach($partida as $part){
                if($part->estado != 0){
                    throw New \Exception('La distribucion de recurso autorizado de remesa no puede ser cancelada, porque alguna de sus partidas no tiene el estatus "generada" ');
                }
            }
            $distribucion->estado = -1;
            $distribucion->save();
            return $distribucion;
        }
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
}