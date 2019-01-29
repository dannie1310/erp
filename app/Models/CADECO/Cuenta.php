<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 08:00 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.cuentas';
    protected $primaryKey = 'id_cuenta';

    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}