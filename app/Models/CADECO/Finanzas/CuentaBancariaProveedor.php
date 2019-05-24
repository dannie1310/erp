<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 09:59 AM
 */

namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Banco;
use Illuminate\Database\Eloquent\Model;

class CuentaBancariaProveedor extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.cuentas_bancarias_proveedores';
    public $timestamps = false;

    public function banco(){
        return $this->hasMany(Banco::class, 'id_empresa', 'id_banco');
    }
}