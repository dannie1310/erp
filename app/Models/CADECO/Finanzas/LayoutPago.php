<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 03:51 PM
 */

namespace App\Models\CADECO\Finanzas;

use App\Models\CADECO\Cuenta;
use App\Models\IGH\Usuario;
use App\Models\CADECO\Finanzas\CtgEstadoLayoutPago;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Zend\Validator\Date;

class LayoutPago extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.layout_pagos';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario_carga',
        'fecha_hora_carga',
        'nombre_layout_pagos',
        'monto_layout_pagos',
        'hash_file_layout_pagos',
        'estado'
    ];

    public function partidas()
    {
        return $this->hasMany(LayoutPagoPartida::class,'id_layout_pagos', 'id');
    }

    public function estadoLayout()
    {
        return $this->belongsTo(CtgEstadoLayoutPago::class, 'estado', 'estado');
    }

    public function validarArchivo($archivo)
    {
        $file_fingerprint = hash_file('md5', $archivo);

        if($this->query()->where('hash_file_layout_pagos', '=', $file_fingerprint)->first()){
            abort(403, 'Archivo de carga masiva de pagos procesado previamente');
        }

        return $file_fingerprint;
    }

    public function registar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $monto_pagado = $this->montoPagado($data['pagos']);
            $layout_pagos = $this->create([
                'hash_file_layout_pagos' => $this->validarArchivo($data['file_pagos']),
                'nombre_layout_pagos' => $data['nombre_archivo'],
                'monto_layout_pagos' => $monto_pagado
            ]);

            foreach ($data['pagos'] as $pago)
            {
                if(($pago['estado']['estado'] == 1 || $pago['estado']['estado'] == 2) && $pago['datos_completos_correctos'] == 1) {
                    $layout_pagos->partidas()->create([
                        'id_layout_pagos' => $layout_pagos->id,
                        'id_transaccion' => $pago['id_transaccion'],
                        'monto_transaccion' => $pago['monto_factura'],
                        'id_moneda' => $pago['id_moneda'],
                        'tipo_cambio' => $pago['tipo_cambio'],
                        'cuenta_cargo' => $pago['id_cuenta_cargo'] ? Cuenta::query()->where('id_cuenta', $pago['id_cuenta_cargo'])->pluck('numero')->toArray()['0'] : 0,
                        'id_cuenta_cargo' => $pago['id_cuenta_cargo'] ?  $pago['id_cuenta_cargo'] : 0,
                        'fecha_pago' => $pago['fecha_pago'] ? date_format(new DateTime($pago['fecha_pago']), 'd-m-Y') : date_format(new DateTime($pago['fecha_pago_s']), 'd-m-Y'),
                        'monto_pagado' => $pago['monto_pagado'],
                        'referencia_pago' => $pago['referencia_pago'],
                        'id_documento_remesa' => $pago['id_documento'],
                        'id_transaccion_pago' => $pago['estado']['estado'] == 2 ? $pago['estado']['id'] : NULL

                    ]);
                }
            }
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class,  'id_usuario_carga', 'idusuario');
    }

    public  function usuarioAutorizo()
    {
        return $this->belongsTo(Usuario::class,  'id_usuario_autorizo', 'idusuario');
    }

    public function montoPagado($partidas)
    {
        $monto_total = 0;
        foreach ($partidas as $pago) {
            if(($pago['estado']['estado'] == 1 || $pago['estado']['estado'] == 2) && $pago['datos_completos_correctos'] == 1) {
                $monto_total += $pago['monto_pagado'];
            }
        }
        return $monto_total;
    }

    public function validarRegistro()
    {
        if($this->monto_layout_pagos == 0){
            abort(403, 'No se encuentra monto total a pagar.');
        }
    }
}
