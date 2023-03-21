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
        'lugar',
        'estatus'
    ];

    public $timestamps = false;

    /**
     * Relaciones
     */
    public function concurso()
    {
        return $this->belongsTo(Concurso::class, 'id_concurso', 'id');
    }

    /**
     * Scopes
     */

    public function scopeEsHermes($query)
    {
        return $query->where("es_empresa_hermes","=",1);
    }

    public function scopeGanador($query)
    {
        return $query->where("lugar","=",1);
    }


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

    public function getDistanciaPrimerLugarAttribute()
    {
        $monto_primer_lugar = $this->concurso->participantes()->orderBy("monto","asc")->pluck("monto")->first();
        $diferencia = $this->monto-$monto_primer_lugar;
        return $diferencia;
    }

    public function getDistanciaPrimerLugarFormatAttribute()
    {
        return number_format($this->distancia_primer_lugar,2,".", ",");
    }

    public function getDistanciaPrimerLugarPorcentajeAttribute()
    {
        $monto_primer_lugar = $this->concurso->participantes()->orderBy("monto","asc")->pluck("monto")->first();
        return number_format($this->distancia_primer_lugar * 100 / $monto_primer_lugar,2) ."%";
    }


    /**
      * Métodos
      */

    public function editar($data)
    {
        $this->validarEditar($data);
        DB::connection('seguridad')->beginTransaction();
        try {
            $this->update([
                'nombre' => $data['nombre'],
                'monto' => $data['monto'],
                'es_empresa_hermes' => $data['es_empresa_hermes']
            ]);

            $this->actualizaParticipantes($this);

            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validarEditar($data)
    {
        if($this->concurso->estatus != 1)
        {
            abort(400, "El concurso ". $this->concurso->nombre . " ya se encuentra cerrado, no es posible editar al participante.");
        }
        $existe = $this->where('nombre',"=", $data['nombre'])
            ->where("id_concurso","=",$this->concurso->id)
            ->where("id","!=",$this->id)
            ->first();
        if($existe)
        {
            abort(400, "Este nombre de participante ya existe \n\nFavor de comunicarse con Soporte a Aplicaciones y/o Coordinación SAO en caso de tener alguna duda.");
        }
    }

    public function registrar($data)
    {
        $this->validarRegistroNuevo($data);
        $concurso = Concurso::find($data["id_concurso"]);
        if($concurso->estatus != 1)
        {
            abort(400, "El concurso ". $concurso->nombre . " ya se encuentra cerrado, no es posible agregar al participante.");
        }
        try {
            DB::connection('seguridad')->beginTransaction();

            if($data["es_empresa_hermes"])
            {
                $data["es_empresa_hermes"] = 1;
            } else {
                $data["es_empresa_hermes"] = 0;
            }
            $participante = $this->create($data);
            $this->actualizaParticipantes($participante);
            DB::connection('seguridad')->commit();
            return $participante;

        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }

    }

    private function actualizaParticipantes($participante_registrado)
    {
        $participantes = $participante_registrado->concurso->participantes()
            ->orderBy("monto","asc")
            ->get();
        $lugar = 1;
        foreach ($participantes as $participante)
        {
            if($participante_registrado->es_empresa_hermes == 1)
            {
                if($participante->es_empresa_hermes == 1 and $participante->id != $participante_registrado->id)
                {
                    $participante->es_empresa_hermes = 0;
                }
            }
            $participante->lugar = $lugar;
            $participante->save();
            $lugar ++;
        }
    }

    public function validarRegistroNuevo($data)
    {
        $existe = $this->where('nombre', $data['nombre'])
            ->where("id_concurso","=",$data["id_concurso"])
            ->first();
        if($existe)
        {
            abort(400, "Este participante ya ha sido registrado. \nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }

        if($data['monto'] <= 0)
        {
            abort(400, "El participante ".$data['nombre']." no puede tener un monto menor o igual a cero.");
        }
    }

    public function eliminar()
    {
        if($this->concurso->estatus != 1)
        {
            abort(400, "El concurso ". $this->concurso->nombre . " ya se encuentra cerrado, no es posible editar al participante.");
        }

        DB::connection('seguridad')->beginTransaction();
        try {
            $this->delete();
            $this->actualizaParticipantes($this);

            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }


    }


}
