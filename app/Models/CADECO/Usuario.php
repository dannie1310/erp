<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 6/12/18
 * Time: 03:40 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'usuarios';
    protected $primaryKey = 'usuario';

    public $timestamps = false;
    public $incrementing = false;


    /**
     * Indica si el usuario tiene acceso a todas las obras
     *
     * @return bool
     */
    public function tieneAccesoATodasLasObras()
    {
        return is_null($this->id_obra);
    }

    /**
     * Obras asociadas con este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function obras()
    {
        return $this->belongsToMany(Obra::class, 'usuarios_obras', 'usuario', 'id_obra');
    }
}