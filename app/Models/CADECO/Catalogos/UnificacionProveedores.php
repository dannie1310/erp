<?php
/**
 * Created by PhpStorm.
 * User: jlopeza
 * Date: 02/06/2020
 * Time: 06:14 PM
 */

namespace App\Models\CADECO\Catalogos;

use Illuminate\Database\Eloquent\Model;

class UnificacionProveedores extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Catalogos.unificacion_proveedores';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
    ];


}
