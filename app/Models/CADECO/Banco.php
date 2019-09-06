<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:04 AM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Finanzas\BancoComplemento;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgBanco;
use App\Models\CADECO\Sucursal;


class Banco extends Empresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_empresa', '=', 8);
        });
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class,  'UsuarioRegistro', 'idusuario');
    }

    public function ctg_banco()
    {
        return $this->belongsTo(CtgBanco::class, 'id_ctg_bancos', 'id');
    }

    public function sucursal()
    {
        return $this->hasMany(Sucursal::class, 'id_empresa', 'id_empresa');
    }

    public function scopeBancoGlobal($query)
    {
        return $query->where('id_ctg_bancos', '!=', null);
    }
}