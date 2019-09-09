<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 10:26 AM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Models\CADECO\Almacen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaAlmacen extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $connection = 'cadeco';

    /**
     * @var string
     */
    protected $table = 'Contabilidad.cuentas_almacenes';

    /**
     * @var array
     */
    protected $fillable = ['id_almacen', 'cuenta'];

    /**
     * @var array
     */
    public $searchable = [
        'cuenta',
        'almacen.descripcion'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }
}