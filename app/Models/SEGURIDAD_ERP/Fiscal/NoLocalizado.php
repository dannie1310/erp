<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use Illuminate\Database\Eloquent\Model;

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

    public function ctgNoLocalizado(){
        return $this->belongsTo(CtgNoLocalizado::class, 'rfc', 'rfc');
    }

    public function scopeVigente($query){
        return $query->where('estado', '=', 1);
    }

    public function scopeSinOrigen($query){
        return $query->doesntHave("ctgNoLocalizado");
    }
}
