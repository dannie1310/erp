<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class MovimientoFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.movimientos';
    protected $fillable = ['id_fondo_garantia',
                            'id_tipo_movimiento',
                            'id_movimiento_solicitud',
                            'id_movimiento_retencion',
                            'id_transaccion_generada',
                            'importe',
                            'usuario_registra',
                            'observaciones',
                            ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::creating(function($movimiento_fg)
        {
            $movimiento_fg->created_at = date('Y-m-d h:i:s');
            $movimiento_fg->importe = ($movimiento_fg->tipo->naturaleza == 2)? abs($movimiento_fg->importe) * -1 : $movimiento_fg->importe;
        });
        self::created(function($movimiento_fg)
        {
            $movimiento_fg->fondo_garantia->actualizaSaldo();
        });

    }

    public function fondo_garantia()
    {
        return $this->belongsTo(FondoGarantia::class,'id_fondo_garantia');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoMovimientoFondoGarantia::class,"id_tipo_movimiento");
    }

    public function transaccion_generada()
    {
        return $this->hasOne(Transaccion::class,'id_transaccion', 'id_transaccion_generada');
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

    public function getSaldoAttribute()
    {
        return $this->fondo_garantia->movimientos()->where("id","<=",$this->id)->sum('importe');
    }

}