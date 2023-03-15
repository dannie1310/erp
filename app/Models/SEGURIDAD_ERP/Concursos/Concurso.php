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
        'id_usuario_inicio_apertura',
        'estatus'
    ];

    public $timestamps = false;

    public $searchable = [
        'nombre',
        'fecha_hora_inicio_apertura',
        'estatus'
    ];

    /**
     * Relaciones
     */
    public function participantes()
    {
        return $this->hasMany(ConcursoParticipante::class, 'id_concurso', 'id');
    }

    public function participantesOrdenados()
    {
        return $this->hasMany(ConcursoParticipante::class, 'id_concurso', 'id')
                    ->orderBy('monto', 'ASC');
    }

    public function participanteHermes()
    {
        return $this->hasOne(ConcursoParticipante::class, 'id_concurso', 'id')
            ->esHermes();
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
        }else if($this->estatus == 2)
        {
            return 'Cerrado';
        }else{
            return '-';
        }
    }

    public function getEstadoColorAttribute()
    {
        switch ($this->estatus) {
            case 1:
                return '#f39c12';
                break;

            case 2:
                return '#00a65a';
                break;

            default:
                return '#d2d6de';
                break;
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_inicio_apertura);
        return date_format($date,"d/m/Y");
    }

    public function getPromedioAttribute($key)
    {
        $total = $this->participantes()->sum("monto");
        $cantidad = count($this->participantes);
        if(count($this->participantes)>0)
        {
            return $total / $cantidad;
        }
    }

    public function getPromedioFormatAttribute()
    {
        return number_format($this->promedio,2,".", ",");
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
        DB::connection('seguridad')->beginTransaction();
        try {
            $this->update([
                'nombre' => $data['nombre']
            ]);
            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function agregaParticipante($data)
    {
        DB::connection('seguridad')->beginTransaction();
        try {
            $participante = $this->participantes()->create([
                'nombre' => $data['nombre'],
                'monto' => $data['monto'],
                'es_empresa_hermes' => $data['es_empresa_hermes'] ? 1 : 0,
                'lugar' => 0
            ]);
            DB::connection('seguridad')->commit();
            return $participante;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function eliminaParticipante($id_participante)
    {
        DB::connection('seguridad')->beginTransaction();
        try {
            $this->participantes()
            ->where("id","=",$id_participante)->first()->delete();
            DB::connection('seguridad')->commit();
            return null;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validarEditar($data)
    {
        $existe = $this->where('nombre', $data['nombre'])->where('id', '!=', $this->id)->first();
        if($existe)
        {
            abort(400, "Este concurso ya existe con el nombre: \n" . $data['nombre'] . "\nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }
    }

    public function cerrar()
    {
        try {
            DB::connection('seguridad')->beginTransaction();

            $this->update([
                'estatus' => 2
            ]);

            if(!count($this->participantes()->esHermes()->get())>0)
            {
                abort("500","Debe especificar un participante como empresa del grupo Hermes");
            }

            foreach($this->participantesOrdenados as $key => $p)
            {
                $p->update([
                    'lugar' => $key + 1,
                    'estatus' => 2
                ]);
            }
            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }

    }
}
