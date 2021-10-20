<?php


namespace App\Models\CADECO;


use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class AvanceObra extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 98;
    public const OPCION = 0;
    public const NOMBRE = "Avance de Obra";

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'opciones',
        'fecha',
        'id_concepto',
        'observaciones',
        'comentario',
        'FechaHoraRegistro',
        'id_obra',
        'id_usuario',
        'cumplimiento',
        'vencimiento'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query)
        {
            return $query->where('tipo_transaccion', self::TIPO)->where('opciones', self::OPCION);
        });
    }

    /**
     * Relaciones
     */
    public function partidas()
    {
        return $this->hasMany(ItemAvanceObra::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Scope
     */

    /**
     * Attributos
     */

    /**
     * Métodos
     */
    public function registrar(array $data)
    {
        try
        {
            DB::connection('cadeco')->beginTransaction();
            $fecha =New DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $cumplimiento = New DateTime($data['fecha_inicio']);
            $cumplimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
            $vencimiento = New DateTime($data['fecha_termino']);
            $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

            $avance = $this->create([
                'fecha' => $fecha->format("Y-m-d"),
                'cumplimiento' => $cumplimiento->format("Y-m-d"),
                'vencimiento' => $vencimiento->format("Y-m-d"),
                'id_concepto' => $data['id_concepto_padre'],
                'observaciones' => $data['observaciones']
            ]);

            $avance->registrarPartidas($data['conceptos']);
            $avance->actualizarTotales();
            DB::connection('cadeco')->commit();
            return $avance;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e);
        }
    }

    public function registrarPartidas($conceptos)
    {
        foreach ($conceptos as $concepto)
        {
            if($concepto['concepto_medible'] == 3 && (float) $concepto['avance'] != 0)
            {
                $conc = Concepto::where('id_concepto', $concepto['id_concepto'])->first();
                $precio_prod = $conc->precioVenta->precio_produccion;
                $this->partidas()->create([
                    'id_transaccion' => $this->id_transaccion,
                    'id_concepto' => $concepto['id_concepto'],
                    'cantidad' => $concepto ['avance'],
                    'precio_unitario' => $precio_prod,
                    'importe' => $concepto ['avance'] * $precio_prod,
                    'numero' => $concepto['cumplido']
                ]);
            }
        }
    }

    public function actualizarTotales()
    {
        $subtotal = ItemAvanceObra::where('id_transaccion', $this->id_transaccion)->selectRaw('SUM([importe]) as subtotal')->first()->subtotal;
        $iva = ($subtotal * $this->obra->iva) / 100;
        $this->monto = $subtotal + $iva;
        $this->impuesto = $iva;
        $this->save();
    }
}
