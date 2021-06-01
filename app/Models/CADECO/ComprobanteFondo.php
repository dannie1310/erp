<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Finanzas\ComprobanteFondoEliminado;
use Illuminate\Support\Facades\DB;

class ComprobanteFondo extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 101;
    public const OPCION = 0;
    public const NOMBRE = "Comprobante de Fondo";

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'opciones',
        'id_moneda',
        'fecha',
        'id_referente',
        'referencia',
        'id_concepto',
        'monto',
        'impuesto',
        'observaciones',
        'comentario',
        'FechaHoraRegistro',
        'id_obra',
        'id_usuario',
        'cumplimiento'
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
     * Relaciones Eloquent
     */
    public function fondo()
    {
        return $this->belongsTo(Fondo::class, 'id_referente', 'id_fondo');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function partidas()
    {
        return $this->hasMany(ItemComprobanteFondo::class, 'id_transaccion', 'id_transaccion');
    }

    public function comprobanteEliminado()
    {
        return $this->belongsTo(ComprobanteFondoEliminado::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Scopes
     */


    /**
     * Attributes
     */
    public function getTotalAttribute()
    {
        return $this->monto + $this->impuesto;
    }

    public function getTotalFormatAttribute()
    {
        return '$ ' . number_format($this->total, 2, '.', ',');
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();

            $comprobante = $this->create([
                'fecha' => $data['fecha'],
                'id_referente' => $data['id_fondo'],
                'referencia' => $data['referencia'],
                'id_concepto' => $data['id_concepto'],
                'monto' => $data['subtotal']+$data['iva'],
                'impuesto' => $data['iva'],
                'observaciones' => $data['observaciones'],
                'cumplimiento' => $data['cumplimiento']
            ]);

            foreach ($data['partidas'] as $partida)
            {
                $comprobante->partidas()->create([
                    'id_transaccion' => $comprobante->id_transaccion,
                    'id_concepto' => $partida['id_concepto'],
                    'importe' => $partida['precio'],
                    'cantidad' => $partida['cantidad'],
                    'referencia' => $partida['referencia']
                ]);
            }
            DB::connection('cadeco')->commit();
            return $comprobante;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function partidasOrdenadas()
    {
        return $this->partidas()->orderBy('id_item', 'asc');
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            foreach ($this->partidas()->get() as $item) {
                $item->delete();
            }
            $this->respaldar($motivo);
            $this->delete();
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function respaldar($motivo)
    {
        ComprobanteFondoEliminado::create([
            'id_transaccion' => $this->getKey(),
            'id_referente' => $this->id_referente,
            'tipo_transaccion' => $this->tipo_transaccion,
            'numero_folio' => $this->numero_folio,
            'fecha' => $this->fecha,
            'estado' => $this->estado,
            'impreso' => $this->impreso,
            'id_obra' => $this->id_obra,
            'id_concepto' => $this->id_concepto,
            'id_moneda' => $this->id_moneda,
            'cumplimiento' => $this->cumplimiento,
            'opciones' => $this->opciones,
            'monto' => $this->monto,
            'impuesto' => $this->impuesto,
            'referencia' => $this->referencia,
            'comentario' => $this->comentario,
            'observaciones' => $this->observaciones,
            'FechaHoraRegistro' => $this->FechaHoraRegistro,
            'id_usuario' => $this->id_usuario,
            'motivo' => $motivo,
            'usuario_elimina' => auth()->id(),
            'fecha_eliminacion' => date('Y-m-d H:i:s')
        ]);
    }
}
