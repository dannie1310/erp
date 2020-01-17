<?php


namespace App\Models\CADECO;


class Destajista extends Empresa
{
    protected $fillable = [
        'razon_social',
        'rfc',
        'dias_credito',
        'tipo_empresa',
        'FechaHoraRegistro',
        'UsuarioRegistro'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_empresa', '=', 4);
        });
    }

    public function validaDuplicidadRfc()
    {
        $destajista = Destajista::whereRaw("(razon_social = '".$this->razon_social."' or rfc ='".$this->rfc."' )")->orderBy('id_empresa', 'desc')->first();

        if(!is_null($destajista))
        {
            if(is_null($this->id_empresa) || ($this->id_empresa != $destajista->id_empresa)) { //creación
                if ($destajista->rfc === $this->rfc) {
                    throw New \Exception('Este rfc se encuentra registrado previamente.');
                }
                if ($destajista->razon_social === $this->razon_social) {
                    throw New \Exception('Esta razón social se encuentra registrada previamente.');
                }
            }
        }
    }
}
