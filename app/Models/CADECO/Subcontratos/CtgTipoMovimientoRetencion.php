<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\Subcontratos;

use Illuminate\Database\Eloquent\Model;

class CtgTipoMovimientoRetencion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.fg_ctg_tipos_mov_ret';
    protected $fillable = ['descripcion'];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

    }



}