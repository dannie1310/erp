<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class ViajeNetoImagen extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'viajes_netos_imagenes';
    public $primaryKey = 'id';
    protected $fillable = [
        'idviaje_neto',
        'idtipo_imagen',
        'imagen',
        'estado'
    ];
    public $timestamps = false;

    /**
     * Relaciones Eloquent
     */
    public function viajeNeto()
    {
        return $this->belongsTo(ViajeNeto::class, 'idviaje_neto', 'IdViajeNeto');
    }


    /**
     * Scopes
     */


    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
