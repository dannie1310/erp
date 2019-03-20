<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 6/12/18
 * Time: 04:23 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Contabilidad\DatosContables;
use App\Models\CADECO\Seguridad\ConfiguracionObra;
use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'obras';
    protected $primaryKey = 'id_obra';

    public $searchable = [
        'nombre',
        'descripcion'
    ];

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'constructora',
        'cliente',
        'facturar',
        'responsable',
        'rfc',
        'id_moneda',
        'iva',
        'fecha_inicial',
        'fecha_final',
        'tipo_obra',
        'descripcion',
        'estado',
        'direccion',
        'ciudad',
        'codigo_postal',
        'valor_contrato'
    ];

    protected $dates = [
        'fecha_inicial',
        'fecha_final'
    ];

    public function datosContables()
    {
        return $this->hasOne(DatosContables::class, 'id_obra');
    }

    public function configuracion()
    {
        return $this->hasOne(ConfiguracionObra::class, 'id_obra');
    }
}