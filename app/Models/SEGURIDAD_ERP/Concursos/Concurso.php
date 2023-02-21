<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/02/2023
 * Time: 08:05 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Concurso extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Concursos.concursos';
    protected $primaryKey = 'id';

    /**
     * Relaciones
     */
    public function participantes()
    {
        $this->hasMany(ConcursoParticipante::class, 'id', 'id_concurso');
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