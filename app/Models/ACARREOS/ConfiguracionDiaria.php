<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionDiaria extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'configuracion_diaria';
    public $primaryKey = 'id';

    /**
     * Relaciones Eloquent
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    public function origen()
    {
        return $this->belongsTo(Origen::class, 'id_origen', 'IdOrigen');
    }

    public function tiro()
    {
        return $this->belongsTo(Tiro::class, 'id_tiro', 'IdTiro');
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
