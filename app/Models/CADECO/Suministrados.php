<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Suministrados extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.suministrados';

    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'id_material',
    ];

    public function material(){
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function eliminarSuministrado($data){
        $suministro = json_decode($data['data']);
        return $this->where('id_empresa', '=', $suministro->id_empresa)->where('id_material', '=', $suministro->id_material)->delete();

        // dd('pardo', );
    }
}