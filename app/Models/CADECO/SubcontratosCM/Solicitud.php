<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\ItemSubcontrato;
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

    public function subcontratoOriginal()
    {
        return $this->hasOne(SubcontratoOriginal::class, "id_solicitud", "id_transaccion");
    }

    public function itemsSubcontratoOriginal()
    {
        return $this->hasMany(ItemSubcontratoOriginal::class, "id_solicitud", "id_transaccion");
    }

    public function contratosOriginal()
    {
        return $this->hasMany(ContratoOriginal::class, "id_solicitud", "id_transaccion");
    }

    public function registrar($solicitud, $archivo, $partidas){
        DB::connection('cadeco')->beginTransaction();
        try{
            $solicitud = $this->create($solicitud);

            $solicitud->subcontratoOriginal()->create([
                "id_subcontrato"=> $solicitud->subcontrato->id_transaccion,
                "impuesto"=> $solicitud->subcontrato->impuesto,
                "monto"=> $solicitud->subcontrato->monto,
            ]);
            foreach($partidas as $partida){
                if(key_exists("id_item_subcontrato", $partida) && $partida["id_tipo_modificacion"] != 3 ){
                    $itemSubcontrato = ItemSubcontrato::find($partida["id_item_subcontrato"]);
                    $solicitud->itemsSubcontratoOriginal()->create([
                        "id_item"=> $partida["id_item_subcontrato"],
                        "cantidad"=> $itemSubcontrato->cantidad,
                        "precio_unitario"=> $itemSubcontrato->precio_unitario,
                    ]);

                    $solicitud->contratosOriginal()->create([
                        "id_contrato"=> $itemSubcontrato->contrato->id_concepto,
                        "cantidad_presupuestada"=> $itemSubcontrato->contrato->cantidad_presupuestada,
                    ]);
                }

                if($partida["id_tipo_modificacion"] == 1) {
                    $solicitud->partidas()->create([
                        'id_solicitud' => $this->id_transaccion,
                        'id_item_subcontrato' => $partida["id_item_subcontrato"],
                        'id_tipo_modificacion' => $partida['id_tipo_modificacion'],
                        'cantidad' => $partida['cantidad'],
                        'importe' => $partida['importe'],
                        'precio' => $partida['precio'],
                    ]);
                }
                if($partida["id_tipo_modificacion"] == 2) {
                    $solicitud->partidas()->create([
                        'id_solicitud' => $this->id_transaccion,
                        'id_item_subcontrato' => $partida["id_item_subcontrato"],
                        'id_tipo_modificacion' => $partida['id_tipo_modificacion'],
                        'cantidad' => $partida['cantidad'],
                        'importe' => $partida['importe'],
                        'precio' => $partida['precio'],
                    ]);
                }
                if($partida["id_tipo_modificacion"] == 3) {
                    $solicitud->partidas()->create([
                        'id_solicitud' => $this->id_transaccion,
                        'id_item_subcontrato' => $partida["id_item_subcontrato"],
                        'id_tipo_modificacion' => $partida['id_tipo_modificacion'],
                        'cantidad' => $partida['cantidad'],
                        'importe' => $partida['importe'],
                        'precio' => $partida['precio'],
                    ]);
                }
                if($partida["id_tipo_modificacion"] == 4) {
                    $solicitud->partidas()->create([
                        'id_solicitud' => $this->id_transaccion,
                        'id_tipo_modificacion' => $partida['id_tipo_modificacion'],
                        'clave' => $partida['clave'],
                        'descripcion' => $partida['descripcion'],
                        'unidad' => $partida['unidad'],
                        'cantidad' => $partida['cantidad'],
                        'importe' => $partida['importe'],
                        'precio' => $partida['precio'],
                        'id_concepto' => $partida['id_concepto'],
                    ]);
                }
            }
            DB::connection('cadeco')->commit();
            return $solicitud;
        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
        }
    }


    public static function calcularFolio()
    {
        $fol = Transaccion::where('tipo_transaccion', '=', 53)->orderBy('numero_folio', 'DESC')->first();
        return $fol ? $fol->numero_folio + 1 : 1;
    }

}
