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

    public $searchable = [
        'nombre'
    ];

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
    public function getEstadoAttribute()
    {
        if($this->estatus == 1)
        {
            return 'Activo';
        }else if($this->estatus == 0)
        {
            return 'Inactivo';
        }else{
            return '-';
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_inicio_apertura);
        return date_format($date,"d/m/Y");
    }

    /**
     * Métodos
    */
    public function registrar($data)
    {
        $this->validarRegistroNuevo($data);
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
                    'es_empresa_hermes' => $p['es_hermes'] ? 1 : 0,
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

    public function validarRegistroNuevo($data)
    {
        $existe = $this->where('nombre', $data['concurso'])->first();
        if($existe)
        {
            abort(400, "Este concurso ya existe con el nombre: \n" . $data['concurso'] . "\nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }

        foreach($data['participantes'] as $p)
        {
            if($p['monto'] <= 0)
            {
                abort(400, "El participante ".$p['nombre']." no puede tener un monto menor o igual a cero.");
            }
        }
    }

    public function editar($data)
    {
        $this->validarEditar($data);
        try {
            DB::connection('seguridad')->beginTransaction();
            $this->update([
                'nombre' => $data['nombre']
            ]);
            $a = '';
            foreach($data['participantes']['data'] as $p)
            {
                if(array_key_exists('id',$p))
                {
                    $a = $a == '' ? (string) $p['id'] : $a . "," . (string) $p['id'];
                    $participante = $this->participantes()->where('id', $p['id'])->first();
                    $participante->update([
                        'nombre' => $p['nombre'],
                        'monto' => $p['monto'],
                        'es_empresa_hermes' => $p['es_empresa_hermes'] ? 1 : 0,
                        'lugar' => 0
                    ]);
                }else{
                    $participantes = $this->participantes()->create([
                        'id_concurso' => $this->id,
                        'nombre' => $p['nombre'],
                        'monto' => $p['monto'],
                        'es_empresa_hermes' => $p['es_empresa_hermes'] ? 1 : 0,
                        'lugar' => 0
                    ]);
                    $a = $a == '' ? (string) $participantes['id'] : $a . "," . (string) $participantes['id'];
                }
            }
            $participantes = ConcursoParticipante::where('id_concurso', $data['id'])->whereRaw('id not in (' . $a . ')')->get();

            if(count($participantes) > 0)
            {
                foreach($participantes as $p)
                {
                    $p->delete();
                }
            }        
            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    
    public function validarEditar($data)
    {
        $existe = $this->where('nombre', $data['nombre'])->where('id', '!=', $data['id'])->first();
        if($existe)
        {
            abort(400, "Este concurso ya existe con el nombre: \n" . $data['nombre'] . "\nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }

        foreach($data['participantes']['data'] as $p)
        {
            if($p['monto'] <= 0)
            {
                abort(400, "El participante ".$p['nombre']." no puede tener un monto menor o igual a cero.");
            }
        }
    }
}