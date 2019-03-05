<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 08:56 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Transaccion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.fondos_garantia';
    protected $primaryKey = 'id_subcontrato';
    protected $fillable = ['id_subcontrato'];
    public $timestamps = false;
    public $usuario_registra = 0;
    public $incrementing = false;
    protected $with = array('movimientos', 'subcontrato');
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($fondo) {

            $subcontrato = Subcontrato::find($fondo->id_subcontrato);
            if(!(float) $subcontrato->retencion>0){
                throw New \Exception('La retención de fondo de garantía establecida en el subcontrato no es mayor a 0, el fondo de garantía no puede generarse');
            }
            $fondo->created_at = date('Y-m-d h:i:s');
            $fondo->usuario_registra = $subcontrato->usuario_registra;
        });
        self::updating(function($fondo)
        {
            if($fondo->saldo<0)
            {
                throw New \Exception('El saldo del fondo de garantía no puede ser menor a 0');
            }
        });
        self::created(function($fondo)
        {
            $fondo->generaMovimientoRegistro();
        });
    }

    public function getSaldoFormatAttribute()
    {
        return '$ ' . number_format($this->saldo,2);
    }

    public function getSumaCargosAttribute()
    {
        return $this->movimientos()->join("SubcontratosFG.ctg_tipos_mov","ctg_tipos_mov.id","movimientos.id_tipo_movimiento")
            ->where("naturaleza",1)->sum('importe');
    }

    public function getSumaAbonosAttribute()
    {
        return $this->movimientos()->join("SubcontratosFG.ctg_tipos_mov","ctg_tipos_mov.id","movimientos.id_tipo_movimiento")
            ->where("naturaleza",2)->sum('importe');
    }

    public function getPorcentajeCargosAttribute()
    {
        if($this->suma_cargos>abs($this->suma_abonos)){
            return 100;
        }
        else
        {
            if(abs($this->suma_abonos)>0){
                return number_format($this->suma_cargos * 100 / abs($this->suma_abonos),0);
            }
            else{
                return 0;
            }
        }
    }

    public function getPorcentajeAbonosAttribute()
    {
        if(abs($this->suma_abonos)>$this->suma_cargos){
            return 100;
        }
        else
        {
            if($this->suma_cargos>0)
            {
                return number_format(abs($this->suma_abonos) * 100 / $this->suma_cargos,0);
            }
            else{
                return 0;
            }
        }
    }

    public function subcontrato()
    {
        return $this->hasOne(Subcontrato::class, "id_transaccion","id_subcontrato");
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoFondoGarantia::class,"id_fondo_garantia");

    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudMovimientoFondoGarantia::class,"id_fondo_garantia");

    }


    private function generaMovimientoRegistro()
    {
        MovimientoFondoGarantia::create(
            [
                'id_fondo_garantia'=>$this->id_subcontrato,
                'id_tipo_movimiento'=>1,
                'importe'=>0,
                'usuario_registra'=>$this->usuario_registra,
            ]
        );
        $this->refresh();
    }
    public function generaMovimientoRetencion(MovimientoRetencionFondoGarantia $movimiento_retencion)
    {

        MovimientoFondoGarantia::create(
            [
                'id_fondo_garantia'=>$this->id_subcontrato,
                'id_tipo_movimiento'=>2,
                'importe'=>$movimiento_retencion->retencion->importe,
                'usuario_registra'=>$movimiento_retencion->usuario_registra,
            ]
        );

    }
    /*
     * Función que permite ajustar el saldo del gondo de garantía
     * */
    public function ajustarSaldo($datos)
    {
        if (!$this->validaNoSolicitudesPendientes()) {
            throw New \Exception('Hay una solicitud de movimiento a fondo de garantía pendiente de autorizar, el ajuste actual no puede registrarse');
        }
        DB::connection('cadeco')->transaction(function() use($datos){
            #1) Se genera movimiento de fondo de garantia
            MovimientoFondoGarantia::create([
                'id_fondo_garantia'=>$datos["id_fondo_garantia"],
                'id_tipo_movimiento'=>($datos["importe"]<0)?9:8,
                'importe'=>$datos["importe"],
                'usuario_registra'=>$datos["id_usuario"],
                'observaciones'=>$datos["observaciones"]
            ]);
            #2) Se actualiza saldo después de regsitrar el movimiento de ajuste
            $this->actualizaSaldo();
        });
    }

    public function actualizaSaldo()
    {
        $this->saldo = $this->movimientos()->sum('importe');
        $this->save();

    }

    private function validaNoSolicitudesPendientes()
    {
        $solicitudes = $this->solicitudes->where("estado",0);
        if(count($solicitudes)>0)
        {
            return false;
        }
        return true;
    }

}