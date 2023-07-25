<?php

namespace App\Models\CADECO\Contabilidad;

use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;

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
        'estado'
    ];

    protected $appends = ['mask'];
    //protected $dateFormat = 'Y-m-d H:i:s';


    public function configuracion()
    {
        return $this->belongsTo(ConfiguracionObra::class, 'id_obra', 'id_obra');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Obra
     */
    public function obra()
    {
        return $this->belongsTo(Obra::class, 'id_obra');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, "BDContPaq", "AliasBDD");
    }

    public function getMaskAttribute()
    {
        return str_replace('#', '0', $this->FormatoCuenta);
    }
}
