<?php
/**
 * Created by PhpStorm.
 * User: jlopeza
 * Date: 02/06/2020
 * Time: 06:14 PM
 */

namespace App\Models\CADECO;

use Illuminate\Database\Eloquent\Model;

class UnificacionProveedoresCambios extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Catalogos.unificacion_proveedores_cambios';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
    ];
}
