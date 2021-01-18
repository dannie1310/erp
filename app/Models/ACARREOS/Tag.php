<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'tags';
    public $primaryKey = 'uid';


    /**
     * Relaciones Eloquent
     */
    public function camion()
    {
        return $this->belongsTo(Camion::class, 'idcamion', 'IdCamion');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estado',  1);
    }

    public function scopeCamionEconomico($query)
    {
        return $query->join('camiones','tags.idcamion', 'camiones.IdCamion');
    }

    public function scopeSinCamionAsignado($query)
    {
        return $query->whereRaw('ISNULL(idcamion)');
    }

    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
