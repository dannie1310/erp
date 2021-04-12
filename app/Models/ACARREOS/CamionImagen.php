<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class CamionImagen extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'camiones_imagenes';
    public $primaryKey = 'Id';
    protected $fillable = [
        'IdCamion',
        'TipoC',
        'Imagen',
        'Tipo',
        'Estatus'
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
    public function historico()
    {
        return $this->hasMany(CamionImagenHistorico::class, 'IdCamion', 'IdCamion');
    }


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
    public function buscarImagenesCamion()
    {
        $imagenes = self::where('IdCamion', $this->IdCamion)
            ->where('TipoC', $this->TipoC)->get();
        if(count($imagenes))
        {
            foreach ($imagenes as $imagen) {
                $this->cancelarImagen($imagen);
            }
        }
    }

    /**
     * Elimina en caso de que la imagen tenga al inicio 'data:image/png;base64,'
     */
    public function limpiarStringImagen()
    {
        $img = str_replace('data:image/','',$this->Imagen);
        $index = strpos($img,'base64,');

        if($index != false) {
            return substr($img, $index + 7);
        }
        return $this->Imagen;
    }

    public function historicoImagen()
    {
        $this->historico()->create([
            'Id' => $this->getKey(),
            'IdCamion' => $this->IdCamion,
            'TipoC' => $this->TipoC,
            'Imagen' => $this->Imagen,
            'Tipo' => $this->Tipo

        ]);
    }

    /**
     * Cambiar estatus = 0
     * @param $imagen
     */
    public function cancelarImagen($imagen)
    {
        $imagen->Estatus = 0;
        $imagen->save();
    }
}
