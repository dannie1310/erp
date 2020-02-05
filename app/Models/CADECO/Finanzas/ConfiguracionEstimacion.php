<?php


namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Obra;
use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionEstimacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.configuracion_estimaciones';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_obra',
        'penalizacion_antes_iva',
        'retenciones_antes_iva',
        'ret_fon_gar_antes_iva',
        'desc_pres_mat_antes_iva',
        'desc_otros_prest_antes_iva',
        'ret_fon_gar_con_iva'
    ];

    public function validar()
    {
        if(ConfiguracionEstimacion::query()->where('id_obra',Context::getIdObra())->first() != null)
        {
            abort(400,'La Configuracion Finanzas ya fue registrada anteriormente');
        }
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class, 'id_obra');
    }
}