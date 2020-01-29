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

    public function validaDuplicidadRfc($nuevos_datos)
    {
        $destajista = Destajista::whereRaw("(razon_social = '".$nuevos_datos->razon_social."' or rfc ='".$nuevos_datos->rfc."' )")
            ->where('id_empresa', '!=', $nuevos_datos->id_empresa)->first();

        if(!is_null($destajista)) {
            if (strcasecmp($destajista->rfc, $nuevos_datos->rfc) == 0) {
                throw New \Exception('Este rfc se encuentra registrado previamente.');
            }
            if (strcasecmp($destajista->razon_social, $nuevos_datos->razon_social) == 0) {
                throw New \Exception('Esta razón social se encuentra registrada previamente.');
            }
        }
    }
}
