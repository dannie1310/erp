<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/02/2023
 * Time: 08:05 PM
 */

namespace App\Models\SEGURIDAD_ERP\Concursos;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Concurso extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Concursos.concursos';
    protected $primaryKey = 'id';

    protected $fillable =[
        'fecha',
        'entidad_licitante',
        'numero_licitacion',
        'nombre',
        'fecha_hora_inicio_apertura',
        'id_usuario_inicio_apertura',
        'estatus'
    ];

    public $timestamps = false;

    public $searchable = [
        'fecha',
        'entidad_licitante',
        'numero_licitacion',
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

    public function participanteGanador()
    {
        return $this->hasOne(ConcursoParticipante::class, 'id_concurso', 'id')
            ->ganador();
    }

    public function usuarioFinalizoApertura()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_finalizo_apertura', 'idusuario');
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
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getDivisorAttribute()
    {
        $monto_mayor = number_format($this->participantes()->orderBy("lugar","desc")->pluck("monto")->first(),0,"","");
        $longitud_mayor = strlen($monto_mayor);
        $divisor =  (int) str_pad(1, $longitud_mayor-2, '0', STR_PAD_RIGHT);

        return $divisor;
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

    public function getNombreArchivoAttribute()
    {
        $nombre = $this->nombre;
        return str_replace(" ","",ucfirst($nombre));
    }

    public function getPromedioFormatAttribute()
    {
        return number_format($this->promedio,2,".", ",");
    }

    public function getLabelsParticipantesAttribute()
    {
        $labels = [];
        $i = 0;
        foreach ($this->participantes()->orderBy("lugar")->get() as $participante) {
            $nombre = explode(" ", $participante->nombre);
            $labels[$i] = $participante->lugar."-".mb_substr($nombre[0],0,1);
            if(key_exists(1, $nombre))
            {
                $labels[$i] .= mb_substr($nombre[1],0,1);
            }
            else{
                $labels[$i] .= mb_substr($nombre[0],1,2);
            }
            if(key_exists(2, $nombre))
            {
                $labels[$i] .= mb_substr($nombre[2],0,1);
            }
            if(key_exists(3, $nombre))
            {
                $labels[$i] .= mb_substr($nombre[3],0,1);
            }
            $labels[$i] = mb_strtoupper($labels[$i]);
            $i++;
        }
        return $labels;
    }

    public function getSaltosGraficaAttribute()
    {
        //$monto_primer_lugar = $this->participantes()->orderBy("monto","asc")->pluck("monto")->first();
        $monto_ultimo_lugar = $this->participantes()->orderBy("monto","desc")->pluck("monto")->first();
        $monto_ultimo_lugar_round = ceil($monto_ultimo_lugar / $this->divisor);
        //dd($monto_ultimo_lugar_round, $this->divisor);
        $saltos1 = [];
        $saltos2 = [];
        for($i= 0;$i<=$monto_ultimo_lugar_round+50;$i+=50)
        {
            $saltos1[] = $i;
        }
        for($i= 0;$i<=$monto_ultimo_lugar_round+25;$i+=25)
        {
            $saltos2[] = $i;
        }
        return [$saltos1, $saltos2];
    }

    public function getDatosPromedioGraficaAttribute()
    {
        $arreglo_promedio = [];
        for($i = 0; $i<count($this->participantes);$i++)
        {
            $arreglo_promedio[] = ceil($this->promedio / $this->divisor);
        }
        return $arreglo_promedio;
    }

    public function getDatosOfertaGanadoraGraficaAttribute()
    {
        $arreglo = [];
        for($i = 0; $i<count($this->participantes);$i++)
        {
            $arreglo[] = ceil($this->participantes()->ganador()->pluck("monto")->first() / $this->divisor);
        }
        return $arreglo;
    }

    public function getDatosIndicadorHermesAttribute()
    {
        $lugar_hermes = $this->participantes()->esHermes()->pluck("lugar")->first();
        return [$lugar_hermes-1, $lugar_hermes];
    }

    public function getDatosOfertaHermesGraficaAttribute()
    {
        $arreglo = [];
        for($i = 0; $i<count($this->participantes);$i++)
        {
            $arreglo[] = ceil($this->participantes()->esHermes()->pluck("monto")->first() / $this->divisor);
        }
        return $arreglo;
    }

    public function getDatosOfertasGraficaAttribute()
    {
        $arreglo_ofertas = [];
        foreach ($this->participantes as $participante)
        {
            $arreglo_ofertas[] = ceil($participante->monto / $this->divisor);
        }

        return $arreglo_ofertas;
    }

    /**
     * Métodos
    */
    public function registrar($data)
    {
        $this->validarNombreConcurso($data);
        try {
            DB::connection('seguridad')->beginTransaction();
            $concurso = $this->create($data);

            DB::connection('seguridad')->commit();
            return $concurso;

        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function validarNombreConcurso($data)
    {
        if($this->id > 0){
            $existe = $this->where('nombre', $data['nombre'])
                ->where("id","!=",$this->id)
                ->first();
        } else{
            $existe = $this->where('nombre', $data['nombre'])->first();
        }

        if($existe)
        {
            abort(400, "Ya existe un concurso con el nombre: \n'" . $data['nombre'] . "'\n\nFavor de modificarlo.");
        }
    }

    public function editar($data)
    {
        $this->validarNombreConcurso($data);
        DB::connection('seguridad')->beginTransaction();
        try {
            $this->update($data);
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
