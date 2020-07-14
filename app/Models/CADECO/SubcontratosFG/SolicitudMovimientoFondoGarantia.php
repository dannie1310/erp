<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use App\Models\CADECO\DescuentoFondoGarantia;
use App\Models\CADECO\LiberacionFondoGarantia;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SolicitudMovimientoFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.solicitudes';
    protected $fillable = ['id_fondo_garantia',
                            'id_tipo_solicitud',
                            'fecha',
                            'referencia',
                            'importe',
                            'observaciones',
                            'usuario_registra'
                            ];
    public $timestamps = false;
    protected $with = array('movimientos');
    protected $appends = array('ultimo_movimiento', 'movimiento_autorizacion');

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope('subcontrato_fondo', function ($query) {
            return $query->whereHas('fondo_garantia');
        });
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->id);

    }

    public function getImporteFormatAttribute()
    {
        return '$ ' . number_format($this->importe,2);

    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");

    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoSolicitudMovimientoFondoGarantia::class,"id_solicitud");

    }

    public function fondo_garantia()
    {
        return $this->belongsTo(FondoGarantia::class,'id_fondo_garantia');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoSolicitud::class,"id_tipo_solicitud");
    }


    public function getMovimientoAutorizacionAttribute()
    {
        $movimiento_autorizacion = $this->movimientos()->where('id_tipo_movimiento',2)->first();
        return $movimiento_autorizacion;
    }

    public function getUltimoMovimientoAttribute()
    {
        return $this->movimientos()->orderBy('id','desc')->first();
    }

    public function getEstadoDescAttribute()
    {
        $tipo_movimiento = CtgTipoMovimientoSolicitud::where('estado_resultante',$this->estado)->first();
        return $tipo_movimiento->estado_resultante_desc;
    }

    public function getUsuarioRegistraDescAttribute()
    {
        $usuario = Usuario::where('idusuario',$this->usuario_registra)->first();
        if($usuario)
        {
            return $usuario->usuario;
        }
        return null;
    }

    public function getUsuarioCompletoRegistraDescAttribute()
    {
        $usuario = Usuario::where('idusuario',$this->usuario_registra)->first();
        if($usuario)
        {
            return $usuario->nombre.' '.$usuario->apaterno.' '.$usuario->amaterno;
        }
        return null;
    }


    /**
     * @throws \Throwable
     * Mètodo para cancelar solicitud de movimiento a fondo de garantía
     */
    public function cancelar($datos)
    {
        DB::connection('cadeco')->transaction(function() use($datos){
            #1) Se genera movimiento de solicitud
            $movimiento_solicitud = $this->generaMovimientoSolicitud(3, $datos["id_usuario"], $datos["observaciones"]);
            #2) Se actualiza estado de solicitud
            $this->actualizarEstado();
        });
    }

    /**
     * @throws \Throwable
     * Método para autorizar solicitud de movimiento a fondo de garantía
     */
    public function autorizar($datos)
    {
        DB::connection('cadeco')->transaction(function() use($datos){
            #1) Se genera movimiento de solicitud
            $movimiento_solicitud = $this->generaMovimientoSolicitud(2, $datos['id_usuario']);
            #2) Se actualiza estado de solicitud
            $this->actualizarEstado();
            #3) Se genera transacción de movimiento a fondo de garantía
            $transaccion_movimiento_fg = $this->generaTransaccionMovimientoFG();
            #4) Se genera movimiento de fondo de garantia
            $this->generaMovimientoFondoDeGarantia($movimiento_solicitud, $transaccion_movimiento_fg);
        });
    }

    /**
     * @throws \Throwable
     * Mètodo para rechazar solicitud de movimiento a fondo de garantía
     */
    public function rechazar($datos)
    {
        DB::connection('cadeco')->transaction(function() use($datos){
            #1) Se genera movimiento de solicitud
            $movimiento_solicitud = $this->generaMovimientoSolicitud(4, $datos["id_usuario"], $datos["observaciones"]);
            #2) Se actualiza estado de solicitud
            $this->actualizarEstado();
        });
    }

    /**
     * @throws \Throwable
     * Método para revertir autorización de solicitud de movimiento a fondo de garantía
     */
    public function revertirAutorizacion($datos)
    {
        DB::connection('cadeco')->transaction(function() use($datos){
            #1) Se genera movimiento de solicitud
            $movimiento_solicitud = $this->generaMovimientoSolicitud(5, $datos["id_usuario"], $datos["observaciones"]);
            #2) Se actualiza estado de solicitud
            $this->actualizarEstado();
            #3) Se cancela transacción de movimiento a fondo de garantía
            $transaccion_movimiento_fg = $this->cancelaTransaccionMovimientoFG();
            #4) Se genera movimiento de fondo de garantia
            $this->generaMovimientoFondoDeGarantia($movimiento_solicitud, $transaccion_movimiento_fg);
        });
    }

    /**
     * @param $tipo_movimiento
     * @return mixed
     */
    public function generaMovimientoSolicitud($tipo_movimiento, $usuario, $observaciones = null)
    {
        $movimiento_solicitud = MovimientoSolicitudMovimientoFondoGarantia::create([
                'id_solicitud'=>$this->id,
                'id_tipo_movimiento'=>$tipo_movimiento,
                'usuario_registra'=>$usuario,
                'observaciones'=>$observaciones
            ]
        );
        $this->fresh();
        $this->refresh();
        return $movimiento_solicitud;
    }

    /**
     * Se actualiza el estado de la solicitud de acuerdo a su último movimiento registrado
     */
    public function actualizarEstado()
    {
        $this->estado = $this->ultimo_movimiento->tipo->estado_resultante;
        $this->save();
    }


    /**
     * @return mixed
     * Se genera la transacción de movimiento a fondo de garantía tomando los datos de la solicitud autorizada
     */
    public function generaTransaccionMovimientoFG()
    {
        if($this->id_tipo_solicitud == 1)
        {
            $transaccion_movimiento_fg = LiberacionFondoGarantia::create([
                'id_antecedente'=>$this->fondo_garantia->id_subcontrato,
                'fecha'=>date('Y-m-d'),
                'id_obra'=>$this->fondo_garantia->subcontrato->id_obra,
                'monto'=>$this->importe,
                'referencia'=>$this->referencia,
                'observaciones'=>$this->observaciones,
            ]);

        }
        else if($this->id_tipo_solicitud == 2)
        {
            $transaccion_movimiento_fg= DescuentoFondoGarantia::create([
                'id_antecedente'=>$this->fondo_garantia->id_subcontrato,
                'fecha'=>date('Y-m-d'),
                'id_obra'=>$this->fondo_garantia->subcontrato->id_obra,
                'monto'=>$this->importe,
                'referencia'=>$this->referencia,
                'observaciones'=>$this->observaciones,
            ]);
        }

        return $transaccion_movimiento_fg;
    }

    /**
     * @return mixed
     * se busca la transacción generada por la autorización de la solicitud y se cambia el estatus a -2 y se pone el saldo en 0
     */
    private function cancelaTransaccionMovimientoFG()
    {
        $transaccion_movimiento_fg = $this->movimiento_autorizacion->movimiento_fondo_garantia->transaccion_generada;
        $transaccion_movimiento_fg->estado = -2;
        $transaccion_movimiento_fg->saldo= 0;
        $transaccion_movimiento_fg->save();
        return $transaccion_movimiento_fg;
    }

    /**
     * @param MovimientoSolicitudMovimientoFondoGarantia $movimiento_solicitud
     * @param $transaccion_movimiento_fg
     * Generación de movimiento a fondo de garantía
     */
    private function generaMovimientoFondoDeGarantia(MovimientoSolicitudMovimientoFondoGarantia $movimiento_solicitud, $transaccion_movimiento_fg)
    {
        MovimientoFondoGarantia::create([
            'id_fondo_garantia'=>$this->id_fondo_garantia,
            'id_tipo_movimiento'=>$this->obtieneTipoMovimientoFondoGarantia($movimiento_solicitud),
            'id_movimiento_solicitud'=>$movimiento_solicitud->id,
            'id_transaccion_generada'=>$transaccion_movimiento_fg->id_transaccion,
            'importe'=>$this->importe,
            'usuario_registra'=>$this->usuario_registra
        ]);

    }

    /**
     * @param MovimientoSolicitudMovimientoFondoGarantia $movimiento_solicitud
     * @return int
     * Obtiene el tipo de movimiento a fondo de garantía que corresponde de acuerdo al tipo de solicitud y tipo de
     * movimiento registrado
     */
    private function obtieneTipoMovimientoFondoGarantia(MovimientoSolicitudMovimientoFondoGarantia $movimiento_solicitud)
    {
        $tipo_movimiento_solicitud = $movimiento_solicitud->tipo->id;
        if($this->id_tipo_solicitud == 1 && $tipo_movimiento_solicitud == 2)
        {
            #Movimiento de liberación de fondo de garantía
            return 6;
        } else if($this->id_tipo_solicitud == 1 && $tipo_movimiento_solicitud == 5)
        {
            #Movimiento de cancelación de liberación de fondo de garantía
            return 7;
        } else if($this->id_tipo_solicitud == 2 && $tipo_movimiento_solicitud == 2)
        {
            #Movimiento de descuento a fondo de garantía
            return 4;
        }else if($this->id_tipo_solicitud == 2 && $tipo_movimiento_solicitud == 5)
        {
            #Movimiento de cancelación de descuento a fondo de garantía
            return 5;
        }
    }


    /**
     * No puede haber más de una solicitud de movimiento a fondo de garantía con estado 0 (Generada)
     * @return bool
     */

    public function validaNoSolicitudesPendientes()
    {
        $solicitudes = SolicitudMovimientoFondoGarantia::where("id_fondo_garantia",$this->id_fondo_garantia)->where("estado",0)->get();
        if(count($solicitudes)>0)
        {
            return false;
        }
        return true;
    }

    /**
     * El  monto de la solicitud no puede ser mayor al monto disponible del fondo de garantía
     * @return bool
     */
    public function validaMontoSolicitud()
    {

        $this->refresh();
        $monto_disponible = $this->fondo_garantia->saldo;
        if($monto_disponible < $this->importe){
            return false;
        }
        return true;
    }

}
