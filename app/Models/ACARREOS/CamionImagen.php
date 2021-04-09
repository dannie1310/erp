<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class CamionImagen extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'camiones_imagenes';
    public $primaryKey = 'Id';
    protected $fillable = [

    ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->activo();
        });
    }


    /**
     * Relaciones Eloquent
     */


    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('Estatus',  1);
    }


    /**
     * Attributes
     */
    public function getDescripcionImagenAttribute()
    {
        switch ($this->TipoC)
        {
            case 't':
                return 'Atras';
                break;

            case 'f':
                return 'Frente';
                break;

            case 'd':
                return 'Derecha';
                break;

            case 'i':
                return 'Izquierda';
                break;
            default:
                return '';
                break;
        }
    }

    public function getImagenCompuestaAttribute()
    {
        if($this->Tipo != 0)
        {
            return "data:image/jpeg;base64,".$this->Imagen;
        }
        return "data:".$this->Tipo.";base64,".$this->Imagen;
    }

    /**
     * Métodos
     */

}
