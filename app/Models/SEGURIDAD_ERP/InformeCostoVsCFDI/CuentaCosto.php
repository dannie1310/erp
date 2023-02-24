<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 21/05/2020
 * Time: 02:00 AM
 */

namespace App\Models\SEGURIDAD_ERP\InformeCostoVsCFDI;


use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;

class CuentaCosto extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'InformeCostoVsCFDI.cuentas_costo';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'base_datos_contpaq',
        'id_cuenta',
        'codigo_cuenta',
        'nombre_cuenta',
        'tipo_costo',
        'usuario_registro',
        'usuario_elimino',
        'fecha_hora_eliminacion',
        'clasificacion',
    ];
}
