<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;

class NoLocalizado extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.no_localizados';
    protected $primaryKey = 'id';

    protected $fillable = [
        'rfc',
        'razon_social',
        'id_procesamiento_registro',
        'id_procesamiento_baja',
        'id_carga_cfd',
        'estado'
    ];

    public $timestamps = false;

    public function ctg_no_localizados_registro(){
        return $this->belongsTo(CtgNoLocalizado::class, 'rfc', 'rfc');
    }

    public function scopeVigente($query){
        return $query->where('estado', '=', 1);
    }

}