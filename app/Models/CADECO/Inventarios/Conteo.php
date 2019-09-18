<?php


namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;

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

}
