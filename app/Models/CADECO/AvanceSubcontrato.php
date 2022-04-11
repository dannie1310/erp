<?php


namespace App\Models\CADECO;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AvanceSubcontrato extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;
    public const TIPO = 105;
    public const OPCION = 0;
    public const OPCION_ANTECEDENTE = 2;
    public const NOMBRE = "Avance de Subcontrato";
    public const ICONO = "fa fa-file-contract";

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'id_empresa',
        'id_sucursal',
        'id_moneda',
        'anticipo',
        'anticipo_monto',
        'anticipo_saldo',
        'monto',
        'PorcentajeDescuento',
        'impuesto',
        'impuesto_retenido',
        'id_costo',
        'retencion',
        'referencia',
        'observaciones',
        'id_usuario'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', self::TIPO)
                        ->where('opciones', '=', self::OPCION)
                        ->whereIn('estado', [0, 1, 2]);
        });
    }

    /**
     * Relaciones
     */
    public function subcontrato()
    {
        return $this->belongsTo(Subcontrato::class, 'id_antecedente','id_transaccion');
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */
    public function getTotalAttribute()
    {
        return $this->subtotal + $this->impuesto;
    }

    public function getTotalFormatAttribute()
    {
        return '$' . number_format($this->total, 2, '.', ',');
    }

    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuario->nombre_completo;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getColorEstadoAttribute()
    {
        switch ((int) $this->estado) {
            case 0:
                return '#f39c12';
                break;
            case 1:
                return '#4f9b34';
                break;
            case 2:
                return '#2C6AA4';
                break;
        }
    }

    public function getDescripcionEstadoAttribute()
    {
        switch ((int) $this->estado) {
            case 0:
                return 'Registrada';
                break;
            case 1:
                return 'Aprobada';
                break;
            case 2:
                return 'Cerrada';
                break;
        }
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $subcontrato = Subcontrato::where('id_transaccion', $data['id_antecedente'])->first();
            dd($subcontrato);
            $data['id_obra'] = $subcontrato->id_obra;
            $data['id_empresa'] = $subcontrato->id_empresa;
            $data['id_moneda'] = $subcontrato->id_moneda;
            $data['numero_folio'] = $this->calcularFolio($subcontrato->id_obra);
            $solicitud = $this->create($data);
            $solicitud->estimaConceptos($data['conceptos']);
            $solicitud->recalculaDatosGenerales();
            DB::connection('cadeco')->commit();
            return $solicitud;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}
