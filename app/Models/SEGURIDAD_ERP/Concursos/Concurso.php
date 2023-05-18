<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/02/2023
 * Time: 08:05 PM
 */

namespace App\Models\SEGURIDAD_ERP\Concursos;

use App\Models\IGH\Usuario;
use App\Utils\NumberToLetterConverterStatic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Concurso extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Concursos.concursos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'fecha',
        'entidad_licitante',
        'numero_licitacion',
        'nombre',
        'fecha_hora_inicio_apertura',
        'id_usuario_inicio_apertura',
        'fecha_fallo',
        'fecha_hora_registro_fallo',
        'id_usuario_registro_fallo',
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

    public function participantePrimerLugar()
    {
        return $this->hasOne(ConcursoParticipante::class, 'id_concurso', 'id')
            ->primerLugar();
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
        switch ($this->estatus) {
            case 0:
                return 'En Proceso';
            case 1:
                return 'En Proceso';
            case 2:
                return 'Fallo Pendiente';
            case 3:
                return 'Finalizado';
            default:
                return '';
        }
    }

    public function getEstadoAperturaAttribute()
    {
        switch ($this->estatus) {
            case 0:
                return 'En Proceso';
            case 1:
                return 'En Proceso';
            case 2:
                return 'Finalizada';
            case 3:
                return 'Finalizada';
            default:
                return '';
        }
    }

    public function getEstadoColorAttribute()
    {
        switch ($this->estatus) {
            case 1:
                return '#f32c12';
            case 2:
                return '#f39c12';
            case 3:
                return '#00a65a';
            default:
                return '#d2d6de';
        }
    }

    public function getColorEstadoFalloAttribute()
    {
        switch ($this->estatus) {
            //
            case 1:
                return '#f5931c';
            case 0:
                return '#f5931c';
            case 2:
                return '#f5931c';
            case 3:
                return '#7DB646';
            default:
                return '#d1cfd1';
        }
    }

    public function getColorEstadoAperturaAttribute()
    {
        switch ($this->estatus) {
            case 1:
                return '#F00';
            case 2:
                return '#7DB646';
            case 3:
                return '#7DB646';
            case 0:
                return '#F00';
            default:
                return '#d1cfd1';
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date, "d/m/Y");
    }

    public function getFechaFalloFormatAttribute()
    {
        if ($this->fecha_fallo) {
            $date = date_create($this->fecha_fallo);
            return date_format($date, "d/m/Y");
        }
        return '-';
    }

    public function getDivisorAttribute()
    {
        $monto_mayor = number_format($this->participantes()->orderBy("lugar", "desc")->pluck("monto")->first(), 0, "", "");
        $longitud_mayor = strlen($monto_mayor);
        $divisor = (int)str_pad(1, $longitud_mayor - 2, '0', STR_PAD_RIGHT);

        return $divisor;
    }

    public function getPromedioAttribute($key)
    {
        $total = $this->participantes()->sum("monto");
        $cantidad = count($this->participantes);
        if (count($this->participantes) > 0) {
            return $total / $cantidad;
        }
    }

    public function getNombreArchivoAttribute()
    {
        $nombre = $this->nombre;
        return str_replace(" ", "", ucfirst($nombre));
    }

    public function getPromedioFormatAttribute()
    {
        return number_format($this->promedio, 2, ".", ",");
    }

    public function getLabelsParticipantesAttribute()
    {
        $labels = [];
        $i = 0;
        foreach ($this->participantes()->orderBy("lugar")->get() as $participante) {
            $nombre = explode(" ", $participante->nombre);
            $labels[$i] = $participante->lugar . "-" . mb_substr($nombre[0], 0, 1);
            if (key_exists(1, $nombre)) {
                $labels[$i] .= mb_substr($nombre[1], 0, 1);
            } else {
                $labels[$i] .= mb_substr($nombre[0], 1, 2);
            }
            if (key_exists(2, $nombre)) {
                $labels[$i] .= mb_substr($nombre[2], 0, 1);
            }
            if (key_exists(3, $nombre)) {
                $labels[$i] .= mb_substr($nombre[3], 0, 1);
            }
            $labels[$i] = mb_strtoupper($labels[$i]);
            $i++;
        }
        return $labels;
    }

    public function getSaltosGraficaAttribute()
    {
        //$monto_primer_lugar = $this->participantes()->orderBy("monto","asc")->pluck("monto")->first();
        $monto_ultimo_lugar = $this->participantes()->orderBy("monto", "desc")->pluck("monto")->first();
        $monto_ultimo_lugar_round = ceil($monto_ultimo_lugar / $this->divisor);
        //dd($monto_ultimo_lugar_round, $this->divisor);
        $saltos1 = [];
        $saltos2 = [];
        for ($i = 0; $i <= $monto_ultimo_lugar_round + 50; $i += 50) {
            $saltos1[] = $i;
        }
        for ($i = 0; $i <= $monto_ultimo_lugar_round + 25; $i += 25) {
            $saltos2[] = $i;
        }
        return [$saltos1, $saltos2];
    }

    public function getDatosPromedioGraficaAttribute()
    {
        $arreglo_promedio = [];
        for ($i = 0; $i < count($this->participantes); $i++) {
            $arreglo_promedio[] = ceil($this->promedio / $this->divisor);
        }
        return $arreglo_promedio;
    }

    public function getDatosOfertaPrimerLugarGraficaAttribute()
    {
        $arreglo = [];
        for ($i = 0; $i < count($this->participantes); $i++) {
            $arreglo[] = ceil($this->participantes()->primerLugar()->pluck("monto")->first() / $this->divisor);
        }
        return $arreglo;
    }

    public function getDatosIndicadorGanadorAttribute()
    {
        $lugar_ganador = $this->participantes()->ganador()->pluck("lugar")->first();
        return [$lugar_ganador - 1, $lugar_ganador];
    }

    public function getDatosOfertaHermesLineaAttribute()
    {
        $arreglo = [];
        for ($i = 0; $i < count($this->participantes); $i++) {
            $arreglo[] = ceil($this->participantes()->esHermes()->pluck("monto")->first() / $this->divisor);
        }
        return $arreglo;
    }

    public function getDatosOfertaHermesGraficaAttribute()
    {
        $arreglo = [];
        foreach ($this->participantesOrdenados as $participante) {
            if ($participante->esHermes) {
                $arreglo[] = ceil($participante->monto / $this->divisor);
            } else {
                $arreglo[] = 0;
            }

        }
        return $arreglo;
    }

    public function getDatosOfertasGraficaAttribute()
    {
        $arreglo_ofertas = [];
        foreach ($this->participantesOrdenados as $participante) {
            $arreglo_ofertas[] = ceil($participante->monto / $this->divisor);
        }

        return $arreglo_ofertas;
    }

    /**
     * participantes_para_informe
     */

    public function getParticipantesParaInformeAttribute()
    {
        $participantes = $this->participantes()->select(["id", "nombre", "monto", "es_empresa_hermes", "es_ganador"])->get()->toArray();

        $promedio = [[
            "nombre" => "PROMEDIO"
            , "monto_format" => $this->promedio_format
            , "monto" => $this->promedio
            , "es_empresa_hermes" => 0
            , "es_ganador" => 0
            , "porcentaje_vs_primer_lugar" => $this->porcentajePrimerLugar($this->promedio)
            , "porcentaje_vs_promedio" => $this->porcentajePromedio($this->promedio)
            , "porcentaje_vs_hermes" => $this->porcentajeHermes($this->promedio)
            , "porcentaje_vs_ganador" => $this->porcentajeGanador($this->promedio)
            , "i" => ''
        ]];

        $participantes_completos = array_merge($participantes, $promedio);
        $participantesObj = [];
        foreach ($participantes_completos as $participante_completo) {
            $participante_completo["monto_format"] = number_format($participante_completo["monto"], 2);
            $participante_completo["porcentaje_vs_primer_lugar"] = $this->porcentajePrimerLugar($participante_completo["monto"]);
            $participante_completo["porcentaje_vs_promedio"] = $this->porcentajePromedio($participante_completo["monto"]);
            $participante_completo["porcentaje_vs_hermes"] = $this->porcentajeHermes($participante_completo["monto"]);
            $participante_completo["porcentaje_vs_ganador"] = $this->porcentajeGanador($participante_completo["monto"]);
            $participante_completo["es_ganador"] = $participante_completo["es_ganador"] == 1 ? "X" : "";
            $participante_completo["i"] = 0;
            $participantesObj[] = (object)$participante_completo;
        }
        $participantes = collect($participantesObj);
        $participantes_sort = $participantes->sortBy("monto");
        $i = 0;
        foreach ($participantes_sort as $participante_sort)
        {

            if($participante_sort->nombre != "PROMEDIO")
            {
                $i++;
                $participante_sort->i = $i;
            }else{
                $participante_sort->i = '-';
            }
        }
        return $participantes_sort;
    }

    public function getResultadoTxtAttribute()
    {
        if ($this->participanteHermes) {
            return ucfirst(NumberToLetterConverterStatic::Num2Ordinales($this->participanteHermes->lugar)) . " lugar de " . NumberToLetterConverterStatic::num2letras(count($this->participantes)) . " participantes";
        } else {
            return "No hay oferta de Hermes ingresada";
        }
    }

    public function getResultadoFalloTxtAttribute()
    {
        if ($this->estatus == 3) {
            return "Ganador '" . $this->participanteGanador->nombre . "'";
        } else {
            return "Pendiente";
        }

    }

    public function getEstadoFalloTxtAttribute()
    {
        if ($this->estatus == 3) {
            return "Finalizado";
        } else {
            return "Pendiente";
        }

    }


    /**
     * Métodos
     */

    private function porcentajePrimerLugar($monto)
    {
        if ($monto > 0) {
            $monto_primer_lugar = $this->participantePrimerLugar->monto;
            $porcentaje = (($monto / $monto_primer_lugar) - 1) * 100;
            return number_format($porcentaje, 2) . " %";
        }
        return "N/A";
    }

    private function porcentajePromedio($monto)
    {
        if ($monto > 0) {
            $monto_promedio = $this->promedio;
            $porcentaje = (($monto / $monto_promedio) - 1) * 100;
            return number_format($porcentaje, 2) . " %";;
        }
        return "N/A";
    }

    private function porcentajeHermes($monto)
    {
        if ($monto > 0 && $this->participanteHermes) {
            $monto_hermes = $this->participanteHermes->monto;
            $porcentaje = (($monto / $monto_hermes) - 1) * 100;
            return number_format($porcentaje, 2) . " %";;
        }
        return "N/A";
    }

    private function porcentajeGanador($monto)
    {
        if ($monto > 0 && $this->participanteGanador) {
            $monto_ganador = $this->participanteGanador->monto;
            $porcentaje = (($monto / $monto_ganador) - 1) * 100;
            return number_format($porcentaje, 2) . " %";;
        }
        return "N/A";
    }

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

    public function ultimo()
    {
        $ultimo = Concurso::where("estatus", ">=", 1)
            ->orderBy("id", "desc")
            ->first();
        return $ultimo;
    }

    public function validarNombreConcurso($data)
    {
        if ($this->id > 0) {
            $existe = $this->where('nombre', $data['nombre'])
                ->where("id", "!=", $this->id)
                ->first();
        } else {
            $existe = $this->where('nombre', $data['nombre'])->first();
        }

        if ($existe) {
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
                ->where("id", "=", $id_participante)->first()->delete();
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

            foreach ($this->participantesOrdenados as $key => $p) {
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

    public function registrarFallo($data)
    {
        if ($this->estatus == 0 || $this->estatus == 1) {
            abort(400, "Para poder registrar el fallo el estado del concurso debe ser: 'Fallo Pendiente', actualmente el estado es: " . $this->estado);
        }
        if ($this->estatus == 3) {
            abort(400, "Este concurso ya tiene fallo registrado.");
        }
        DB::connection('seguridad')->beginTransaction();
        try {
            $ganadores = $this->participantes()
                ->where("es_ganador", "=", 1)
                ->get();
            if (count($ganadores) == 0) {
                abort(400, "Debe indicar quien es el participante ganador.");
            }
            if (count($ganadores) > 1) {
                abort(400, "Existe mas de un participante registado como ganador, por favor vuelva a dar clic sobre el check del participante ganador.");
            }
            $this->update($data);
            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}
