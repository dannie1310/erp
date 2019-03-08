<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 01:07 PM
 */

namespace App\Models\CADECO\Contabilidad;

use App\Models\CADECO\Fondo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaFondo extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cuentas_fondos';

    protected $fillable = [
        'id_fondo',
        'cuenta',
        'registro',
        'estatus'
    ];

    public $searchable = [
        'cuenta',
        'fondo.descripcion',
        'fondo.nombre',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->registro = auth()->id();
            $model->estatus = 1;
        });
    }

    public function fondo()
    {
        return $this->belongsTo(Fondo::class, 'id_fondo');
    }
}