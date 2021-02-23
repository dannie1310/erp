<?php

namespace App\Models\CADECO\Contabilidad;

use App\Models\CADECO\Obra;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatosContables extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.datos_contables_obra';

    protected $fillable = [
        'id_obra',
        'BDContPaq',
        'NumobraContPaq',
        'FormatoCuenta',
        'FormatoCuentaRegExp',
        'manejo_almacenes',
        'costo_en_tipo_gasto',
        'retencion_antes_iva',
        'deductiva_antes_iva',
        'amortizacion_antes_iva',
    ];

    protected $appends = ['mask'];
    //protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Obra
     */
    public function obra()
    {
        return $this->belongsTo(Obra::class, 'id_obra');
    }

    public function getMaskAttribute()
    {
        return str_replace('#', '0', $this->FormatoCuenta);
    }
}
