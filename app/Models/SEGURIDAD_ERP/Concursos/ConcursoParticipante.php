<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/02/2023
 * Time: 08:05 PM
 */

namespace App\Models\SEGURIDAD_ERP\Concursos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConcursoParticipante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Concursos.concurso_participantes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_concurso',
        'nombre',
        'monto',
        'es_empresa_hermes',
        'lugar'
    ];

    public $timestamps = false;

    /**
     * Relaciones
     */
    public function concurso()
    {
        return $this->belongsTo(Concurso::class, 'id', 'id_concurso');
    }

    /**
     * Scopes
     */


    /**
     * Atributos 
     */ 
    public function getMontoFormatAttribute()
    {
        return number_format($this->monto,2,".", ",");
    }

    public function getEsHermesAttribute()
    {
        return $this->es_empresa_hermes == 1 ? true : false;
    }

     /**
      * Métodos
      */

}