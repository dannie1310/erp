<?php


namespace App\Models\CADECO;


use App\Facades\Context;
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
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'id_obra',
        'id_empresa',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'monto',
        'impuesto',
        'referencia',
        'observaciones',
        'fecha_ejecucion',
        'fecha_contable',
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

    public function itemsAvance()
    {
        return $this->hasMany(ItemAvanceSubcontrato::class, 'id_transaccion', 'id_transaccion');
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

    public function getSumaImportePartidasAttribute()
    {
        return (float) $this->itemsAvance->sum('importe');
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        $datos = [];
        try {
            DB::connection('cadeco')->beginTransaction();
            $datos['id_antecedente'] = $data['id_antecedente'];
            $datos['fecha'] = $data['fecha'];
            $datos['cumplimiento'] = $data['cumplimiento'];
            $datos['vencimiento'] = $data['vencimiento'];
            $datos['fecha_ejecucion'] = $data['fecha_ejecucion'];
            $datos['fecha_contable'] = $data['fecha_contable'];
            $datos['observaciones'] = $data['observaciones'];
            $datos['referencia'] = $data['observaciones'];
            $avance = $this->create($datos);
            $avance->agregaConceptos($data['conceptos']);
            $avance->calcularSubtotal();
            DB::connection('cadeco')->commit();
            return $avance ;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public static function calcularFolio()
    {
        $est = Transaccion::withoutGlobalScopes()->where('tipo_transaccion', '=', self::TIPO)->where('id_obra','=', Context::getIdObra())->orderBy('numero_folio', 'DESC')->first();
        return $est ? $est->numero_folio + 1 : 1;
    }

    public function agregaConceptos($conceptos)
    {
        foreach ($conceptos as $concepto)
        {
            if(!array_key_exists('para_estimar', $concepto) && (array_key_exists('cantidad_avance', $concepto) && $concepto['cantidad_avance'] > 0))
            {
                $this->itemsAvance()->create([
                    'id_transaccion' => $this->id_transaccion,
                    'id_antecedente' => $this->id_antecedente,
                    'id_concepto' => $concepto['id_concepto'],
                    'cantidad' => (float) $concepto['cantidad_avance'],
                    'precio_unitario' => (float) $concepto['precio_unitario_subcontrato'],
                    'importe' => (float) $concepto['cantidad_avance'] * (float) $concepto['precio_unitario_subcontrato'],
                ]);
            }
        }
    }

    public function calcularSubtotal()
    {
        $this->refresh('itemsAvance');
        $this->monto = (1 + ($this->obra->iva / 100)) * $this->suma_importe_partidas;
        $this->impuesto = $this->suma_importe_partidas * ($this->obra->iva / 100);
        $this->save();
    }
}
