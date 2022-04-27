<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 03:51 PM
 */

namespace App\Models\CADECO\Finanzas;

use App\Facades\Context;
use App\Models\CADECO\Cuenta;
use App\Models\IGH\Usuario;
use App\Models\CADECO\Finanzas\CtgEstadoLayoutPago;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Zend\Validator\Date;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\Factura;

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
        'estado',
        'id_obra'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function partidas()
    {
        return $this->hasMany(LayoutPagoPartida::class, 'id_layout_pagos', 'id');
    }

    public function estadoLayout()
    {
        return $this->belongsTo(CtgEstadoLayoutPago::class, 'estado', 'estado');
    }

    public function validarArchivo($archivo)
    {
        $file_fingerprint = hash_file('md5', $archivo);
        if ($this->query()->where('hash_file_layout_pagos', '=', $file_fingerprint)->first()) {
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
            $contador_pagos = 0;
            foreach ($data['pagos'] as $pago) {
                if (array_key_exists('fecha_pago_s', $pago)) {
                    /*
                     * EL front envía la fecha con timezone Z (Zero) (+6 horas), por ello se actualiza el time zone a America/Mexico_City
                     * */
                    $fecha_pago = New DateTime($pago['fecha_pago_s']);
                    $fecha_pago->setTimezone(new DateTimeZone('America/Mexico_City'));
                } else {
                    $fecha_pago = DateTime::createFromFormat('d/m/Y', $pago['fecha_pago']);
                }
                if (($pago['estado']['estado'] == 1 || $pago['estado']['estado'] == 10)) {
                    $contador_pagos++;
                    $layout_pagos->partidas()->create([
                        'id_layout_pagos' => $layout_pagos->id,
                        'id_transaccion' => $pago['id_transaccion'],
                        'monto_transaccion' => $pago['monto_documento'],
                        'id_moneda_transaccion' => $pago['id_moneda_transaccion'],
                        'id_moneda_cuenta_cargo' => $pago["cuenta_cargo_obj"]['id_moneda'],
                        'tipo_cambio' => $pago['tipo_cambio'],
                        'cuenta_cargo' => $pago["cuenta_cargo_obj"]['numero'],
                        'id_cuenta_cargo' => $pago['id_cuenta_cargo'],
                        'fecha_pago' => $fecha_pago->format('Y-m-d'),
                        'monto_pagado' => $pago['monto_pagado'],
                        'referencia_pago' => $pago['referencia_pago'],
                        'id_documento_remesa' => $pago['id_documento_remesa'],
                        'monto_autorizado_remesa' => $pago['monto_autorizado_remesa'],
                        'monto_pagado_transaccion' => $pago['monto_pagado_documento'],
                        'saldo_transaccion' => $pago['saldo_documento'],
                    ]);
                }
            }
            if (count($layout_pagos->partidas) == $contador_pagos && count($layout_pagos->partidas) > 0) {
                DB::connection('cadeco')->commit();
            } else {
                DB::connection('cadeco')->rollBack();
                abort(400, 'Hubo un error durante el registro de las partidas');
            }

            return $layout_pagos;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }


    private function getTC($partida)
    {
        $partida["tipo_cambio"] = $partida["monto_pagado"] / $partida["monto_pagado_transaccion"];
        return $partida;
    }

    public function autorizar()
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $partidas = $this->partidas;
            foreach ($partidas as $partida) {
                $partida_arr = $partida->toArray();
                $partida_arr = $this->getTC($partida_arr);
                if (is_null($partida->id_transaccion_pago)) {
                    $documento_pagable = $partida->documento_pagable;
                    if (abs($documento_pagable->saldo_pagable - $partida->monto_pagado_documento) <= 0.99 || $documento_pagable->saldo_pagable >= $partida->monto_pagado_documento) {
                        $transaccion = $partida->transaccion;

                        if ($transaccion->tipo_transaccion === '65') {
                            $pago = Factura::find($partida->id_transaccion)->generaOrdenPago($partida_arr);
                            $partida->id_transaccion_pago = $pago->id_transaccion;
                            $partida->save();
                        }

                        if ($transaccion->tipo_transaccion === '72') {
                            $pago = Solicitud::find($partida->id_transaccion)->generaPago($partida_arr);
                            if ($pago) {
                                $partida->id_transaccion_pago = $pago->id_transaccion;
                                $partida->save();
                            } else {
                                abort(400, "Hubo un error al generar el pago con referencia: " . $partida->referencia_pago . " tipo de solicitud no soportado");
                            }
                        }
                        $pago->estado = 2;
                        $pago->save();
                    }
                }
            }
            $this->id_usuario_autorizo = auth()->id();
            $this->fecha_hora_autorizado = date('Y-m-d H:i:s');
            $this->estado = 1;
            $this->save();
            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_carga', 'idusuario');
    }

    public function usuarioAutorizo()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_autorizo', 'idusuario');
    }

    public function montoPagado($partidas)
    {
        $monto_total = 0;
        foreach ($partidas as $pago) {
            if (($pago['estado']['estado'] == 1 || $pago['estado']['estado'] == 10)) {
                $monto_total += $pago['monto_pagado'];
            }
        }
        return $monto_total;
    }

    public function validarRegistro()
    {
        if ($this->monto_layout_pagos == 0) {
            abort(403, 'No se encuentra monto total a pagar.');
        }
    }
}
