<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 10/12/18
 * Time: 06:27 PM
 */

namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;


class Proyecto extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'proyectos';
    public $timestamps = false;

    public function sistemas()
    {
        return $this->belongsToMany( Sistema::class, 'dbo.proyectos_sistemas', 'id_proyecto', 'id_sistema' );
    }

    public function configuracionObra()
    {
        return $this->belongsTo(ConfiguracionObra::class, 'id_proyecto');
    }

    public function configuracionObras()
    {
        return $this->hasMany(ConfiguracionObra::class, 'id_proyecto');
    }

    public function obras()
    {
        return $this->hasMany(Obra::class, 'id_proyecto',"id");
    }
}
