<?php


namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Obra;
use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class DatosEstimaciones extends Model
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
        'desc_otros_prest_antes_iva'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->validar();
            $model->id_obra =  Context::getIdObra();
            $model->usuario_crea = auth()->id();
            $model->usuario_actualiza = auth()->id();
            $model->created_at = date('Y-m-d h:i:s');
            $model->updated_at = date('Y-m-d h:i:s');
        });
    }

    public function validar(){
        if(DatosEstimaciones::query()->where('id_obra',Context::getIdObra())->first() != null)
        {
            abort(400,'La Configuracion Finanzas ya fue registrada anteriormente');
        }
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class, 'id_obra');
    }

}