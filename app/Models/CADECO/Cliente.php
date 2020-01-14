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

    public function validaDuplicidadRfc()
    {
        $cliente = Cliente::whereRaw("(razon_social = '".$this->razon_social."' or rfc ='".$this->rfc."' )")->orderBy('id_empresa', 'desc')->first();

        if(!is_null($cliente))
        {
            if(is_null($this->id_empresa) || ($this->id_empresa != $cliente->id_empresa)) { //creación
                if ($cliente->rfc === $this->rfc) {
                    throw New \Exception('Este rfc se encuentra registrado previamente.');
                }
                if ($cliente->razon_social === $this->razon_social) {
                    throw New \Exception('Está razón social se encuentra registrada previamente.');
                }
            }
        }
    }
}
