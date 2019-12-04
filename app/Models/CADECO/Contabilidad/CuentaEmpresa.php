<?php

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaEmpresa extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cuentas_empresas';
    protected $fillable = [
        'id_empresa',
        'id_tipo_cuenta_empresa',
        'cuenta',
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function tipoCuentaEmpresa()
    {
        return $this->belongsTo(TipoCuentaEmpresa::class, 'id_tipo_cuenta_empresa', 'id');
    }

    public function validaCuenta()
    {
        $mensaje = "";
        $cuentas_empresa = $this->where('cuenta', $this->cuenta)->get();
        if($cuentas_empresa != null) {
            foreach ($cuentas_empresa as $cuenta)
            {
                $mensaje .= "-".$cuenta->empresa->razon_social."\n";
            }
        }

        if($mensaje != "")
        {
            throw new \Exception('La cuenta "' . $this->cuenta . '" está asociada a (los) siguiente(s) proveedor(es):'."\n".$mensaje, 400);
        }
    }
}