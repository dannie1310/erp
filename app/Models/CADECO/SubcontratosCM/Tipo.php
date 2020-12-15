<?php


namespace App\Models\CADECO\SubcontratosCM;


use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.ctg_tipos';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];

    public function itemsConvenioModificatorio()
    {
        return $this->hasMany(Item::class, 'id_tipo_modificacion', 'id');
    }

}
