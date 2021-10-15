<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'telefonos';
    public $primaryKey = 'id';

    protected $fillable = [
        'device_id',
        'updated_at'
    ];

    /**
     * Relaciones Eloquent
     */
    public function impresora()
    {
        return $this->belongsTo(Impresora::class, 'id_impresora', 'id');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estatus',  1);
    }

    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
