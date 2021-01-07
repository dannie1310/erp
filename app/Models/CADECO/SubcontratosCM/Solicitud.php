<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Transaccion;
use Illuminate\Support\Facades\DB;

class Solicitud extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;
    public const OPCION_ANTECEDENTE = 2;
    public const TIPO = 53;
    public const OPCION = 1;
    public const NOMBRE = "Solicitud de Cambio";
    public const ICONO = "fa fa-stack-exchange";

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'impuesto',
        'monto',
        'id_usuario',
    ];

    public function subcontrato()
    {
        return $this->belongsTo(Subcontrato::class, 'id_antecedente', 'id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(Partida::class, 'id_solicitud', 'id_transaccion');
    }

    public function registrar($solicitud, $archivo, $conceptos, $conceptos_extraordinarios, $conceptos_conceptos_cambios_precio){
        dd($solicitud, $conceptos, $conceptos_extraordinarios, $conceptos_conceptos_cambios_precio, $archivo);
        DB::connection('cadeco')->beginTransaction();
        $solicitud = $this->create($solicitud);
        foreach($paridas as $partida){

        }

        DB::connection('cadeco')->rollBack();
    }

    public static function calcularFolio()
    {
        $fol = Transaccion::where('tipo_transaccion', '=', 53)->orderBy('numero_folio', 'DESC')->first();
        return $fol ? $fol->numero_folio + 1 : 1;
    }

}
