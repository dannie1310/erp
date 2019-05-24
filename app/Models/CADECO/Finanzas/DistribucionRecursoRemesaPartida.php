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