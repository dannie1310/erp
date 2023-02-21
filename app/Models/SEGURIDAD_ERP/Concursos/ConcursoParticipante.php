<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/02/2023
 * Time: 08:05 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConcursoParticipante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Concursos.concurso_participantes';
    protected $primaryKey = 'id';

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

     /**
      * Métodos
      */

}