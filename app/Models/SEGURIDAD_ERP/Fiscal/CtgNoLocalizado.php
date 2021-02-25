<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Fiscal\NoLocalizado;

class CtgNoLocalizado extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.ctg_no_localizados';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_procesamiento',
        'rfc',
        'razon_social',
        'tipo_persona',
        'primera_fecha_publicacion',
        'entidad_federativa',
        'estado'
    ];

    public $timestamps = false;

    public function no_localizados(){
        return $this->belongsTo(NoLocalizado::class, 'id', 'id_procesamiento_registro');
    }

    public function scopeVigente($query){
        return $query->where('estado', '=', 1);
    }
}