<?php


namespace App\Models\CADECO;


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

            $this->create([
                'fecha' => $data['fecha'],
                'cumplimiento' => $data['fechaInicio'],
                'vencimiento' => $data['fechaFin'],
                'id_concepto' => $data['id_concepto_padre']
            ]);

            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e);
        }

    }

    public function registrarPartidas()
    {

    }
}
