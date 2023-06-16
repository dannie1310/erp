<?php
namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\Cuenta;
use Illuminate\Database\Eloquent\Model;

class TipoCuenta extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.tipos_cuentas_contpaq';
    protected $primaryKey = 'Id';
    public $timestamps = false;


    public function cuentas()
    {
        return $this->hasMany(Cuenta::class, 'Tipo', 'tipo');
    }
}
