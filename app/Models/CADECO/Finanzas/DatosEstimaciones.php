<?php


namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Obra;
use Illuminate\Database\Eloquent\Model;

class DatosEstimaciones extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.configuracion_estimaciones';

    protected $fillable = [
        'id_obra',
        'amort_anticipo_antes_iva',
        'penalizacion_antes_iva',
        'retenciones_antes_iva',
        'desc_pres_mat_antes_iva',
        'ret_fon_gar_antes_iva',
        'desc_otros_prest_antes_iva',
        'usuario_crea',
        'usuario_actualiza',
        'amortizacion_antes_iva',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class, 'id_obra');
    }

}