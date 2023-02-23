<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/02/2023
 * Time: 08:05 PM
 */

namespace App\Models\SEGURIDAD_ERP\Concursos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Concurso extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Concursos.concursos';
    protected $primaryKey = 'id';

    protected $fillable =[
        'nombre',
        'fecha_hora_inicio_apertura',
        'id_usuario_inicio_apertura'
    ];

    public $timestamps = false;

    /**
     * Relaciones
     */
    public function participantes()
    {
        return $this->hasMany(ConcursoParticipante::class, 'id_concurso', 'id');
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
    public function registrar($data)
    {
        try {
            DB::connection('seguridad')->beginTransaction();
            $concurso = $this->create([
                'nombre' => $data['concurso'],
                'fecha_hora_inicio_apertura' => date('Y-m-d H:i:s'),
                'id_usuario_inicio_apertura' => auth()->id(),
            ]);

            foreach($data['participantes'] as $p)
            {
                $participantes = $concurso->participantes()->create([
                    'id_concurso' => $concurso->id,
                    'nombre' => $p['nombre'],
                    'monto' => $p['monto'],
                    'es_empresa_hermes' => $p['es_hermes'],
                    'lugar' => 0
                ]);
            }
            DB::connection('seguridad')->commit();
            return $concurso;

        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}