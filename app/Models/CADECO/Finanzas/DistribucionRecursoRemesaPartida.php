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
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function distribucionRecurso(){
        return $this->hasMany(DistribucionRecursoRemesa::class, 'id', 'id_distribucion_recurso');
    }

    public function documentos(){
        return $this->belongsTo(DocumentoLiberado::class, 'id_documento', 'IDDocumento');
    }
}