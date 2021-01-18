<?php


namespace App\Models\ACARREOS\SCA_CONFIGURACION;


use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $connection = 'scaconf';
    protected $table = 'sca_configuracion.tags';
    public $primaryKey = 'uid';
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function tags()
    {
        return $this->hasMany(\App\Models\ACARREOS\Tag::class, 'uid','uid');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estado',  1);
    }

    public function scopeDisponibles($query, $id_proyecto)
    {
        $tags = \App\Models\ACARREOS\Tag::activo()->selectRaw('UID')->get()->toArray();
        return $query->activo()->where('id_proyecto', $id_proyecto)->whereNotIn('utid', $tags);
    }

    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
