<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 03/01/2020
 * Time: 12:49 PM
 */

namespace App\Models\CADECO;


class Cliente extends Empresa
{
    protected $fillable = [
        'razon_social',
        'rfc',
        'tipo_empresa',
        'tipo_cliente',
        'porcentaje',
        'FechaHoraRegistro',
        'UsuarioRegistro'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_empresa', '=', 16);
        });
    }

    public function getTipoAttribute()
    {
        if($this->tipo_cliente == 1){
            return 'Comprador';
        }
        if($this->tipo_cliente == 2){
            return 'Inversionista';
        }
        if($this->tipo_cliente == 3){
            return 'Comprador / Inversionista';
        }
    }

    public function getPorcentajeFormatAttribute()
    {
       return number_format((float)$this->porcentaje, 2, '.', '');
    }

    public function getPorcentajeConSignoFormatAttribute()
    {
        return  $this->porcentaje_format. ' %';
    }

    public function validaDuplicidadRfc($nuevos_datos)
    {
        $cliente = Cliente::whereRaw("(razon_social = '".$nuevos_datos->razon_social."' or rfc ='".$nuevos_datos->rfc."' )")
            ->where('id_empresa', '!=', $nuevos_datos->id_empresa)->first();

        if(!is_null($cliente)) {
            if (strcasecmp($cliente->rfc, $this->rfc) == 0) {
                throw New \Exception('Este rfc se encuentra registrado previamente.');
            }
            if (strcasecmp($cliente->razon_social, $this->razon_social) == 0) {
                throw New \Exception('Esta razón social se encuentra registrada previamente.');
            }
        }
    }
}
