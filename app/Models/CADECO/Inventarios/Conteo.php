<?php


namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Conteo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.conteos';
    protected $primaryKey = 'id';

    public $timestamps = false;
    protected $fillable = [
        'id_marbete',
        'tipo_conteo',
        'id_layout_conteos_partida',
        'cantidad_usados',
        'cantidad_nuevo',
        'cantidad_inservible',
        'total',
        'iniciales',
        'observaciones'
    ];


    public function marbete()
    {
        return $this->belongsTo(Marbete::class,'id_marbete','id');
    }

    public function getTipoConteoFormatAttribute()
    {
        if($this->tipo_conteo == 1){
            return 'Primero';
        }else if($this->tipo_conteo == 2){
            return 'Segundo';
        }else if($this->tipo_conteo == 3){
            return 'Tercero';
        }else{
            return 'Extra';
        }

    }
    public function getFolioMarbeteAttribute(){
        return $this->marbete->invetarioFisico->numero_folio_format."-".$this->marbete->folio_format;
    }

    public function cancelar($motivo){
        try {
            DB::connection('cadeco')->beginTransaction();
            ConteoCancelado::query()->create([
                'id_conteo' => $this->id,
                'id_marbete' => $this->id_marbete,
                'tipo_conteo' => $this->tipo_conteo,
                'id_layout_conteos_partida' => $this->id_layout_conteos_partida,
                'cantidad_usados' => $this->cantidad_usados,
                'cantidad_nuevo' => $this->cantidad_nuevo,
                'cantidad_inservible' => $this->cantidad_inservible,
                'total' => $this->total,
                'iniciales' => $this->iniciales,
                'id_usuario' => $this->id_usuario,
                'fecha_hora_registro' => $this->fecha_hora_registro,
                'observaciones' => $this->observaciones,
                'motivo_cancelacion' => $motivo,
            ]);
            $this->delete();
            DB::connection('cadeco')->commit();
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }

    }

}
