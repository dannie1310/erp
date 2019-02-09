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

class FondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.fondos_garantia';
    protected $primaryKey = 'id_subcontrato';
    protected $guarded = 'saldo';
    public $timestamps = false;
    public $usuario_registra = 0;
    public $incrementing = false;
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

        self::created(function($fondo)
        {
            $fondo->generaMovimientoRegistro();
        });
    }

    public function subcontrato()
    {
        return $this->hasOne(Transaccion::class, "id_transaccion");
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


}