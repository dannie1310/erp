<?php


namespace App\Models\CADECO\SubcontratosCM;


use Illuminate\Database\Eloquent\Model;

class CtgTipo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.ctg_tipos';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];

    public function partidasSolicitud()
    {
        return $this->hasMany(Partida::class, 'id_tipo_modificacion', 'id');
    }

}
