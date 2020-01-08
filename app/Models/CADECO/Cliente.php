<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 03/01/2020
 * Time: 12:49 PM
 */

namespace App\Models\CADECO;


use Illuminate\Support\Facades\DB;

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
        return $this->porcentaje. ' %';
    }

    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validaRegistro($data);
            $cliente = $this->create($data);
            DB::connection('cadeco')->commit();
            return $cliente;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validaRegistro($registro)
    {
        $cliente = Cliente::whereRaw("(razon_social = '".$registro['razon_social']."' or rfc ='".$registro['rfc']."' )")->first();

        if($cliente && $cliente->toArray() != [])
        {
            if ($cliente['rfc'] === $registro['rfc'])
            {
                throw New \Exception('Está rfc se encuentran registrados');
            }
            if ($cliente['razon_social'] === $registro['razon_social'])
            {
                throw New \Exception('Está razón social se encuentran registrados');
            }
        }
    }
}