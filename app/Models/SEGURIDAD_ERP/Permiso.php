<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 17/12/18
 * Time: 07:02 PM
 */

namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'permissions';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sistema()
    {
        return $this->belongsTo(Sistema::class, 'sistema_id');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeReservados($query)
    {
        return $query->where('reservado', '=', true);
    }
}