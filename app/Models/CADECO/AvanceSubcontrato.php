<?php


namespace App\Models\CADECO;


use App\Facades\Context;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;
use App\Models\CADECO\Subcontratos\AvanceSubcontratoEliminado;
use App\Models\CADECO\Subcontratos\AvanceSubcontratoPartidaEliminada;
use DateTime;
use DateTimeZone;
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

    public $searchable = [
        'numero_folio',
        'referencia',
        'observaciones',
        'empresa.razon_social',
        'monto',
        'subcontrato.numero_folio'
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

    public function getFechaEjecucionFormatAttribute()
    {
        $date = date_create($this->fecha_ejecucion);
        return date_format($date,"d/m/Y");
    }

    public function getFechaContableFormatAttribute()
    {
        $date = date_create($this->fecha_contable);
        return date_format($date,"d/m/Y");
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        $datos = [];
        try {
            DB::connection('cadeco')->beginTransaction();
            $fecha =New DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $cumplimiento =New DateTime($data['cumplimiento']);
            $cumplimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
            $vencimiento =New DateTime($data['vencimiento']);
            $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_ejecucion =New DateTime($data['fecha_ejecucion']);
            $fecha_ejecucion->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_contable =New DateTime($data['fecha_contable']);
            $fecha_contable->setTimezone(new DateTimeZone('America/Mexico_City'));

            $datos['id_antecedente'] = $data['id_antecedente'];
            $datos['fecha'] = $fecha->format("Y-m-d");
            $datos['cumplimiento'] = $cumplimiento->format("Y-m-d");
            $datos['vencimiento'] = $vencimiento->format("Y-m-d");
            $datos['fecha_ejecucion'] = $fecha_ejecucion->format("Y-m-d");
            $datos['fecha_contable'] = $fecha_contable->format("Y-m-d");
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
                $this->agregarItem($concepto);
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

    public function partidasOrdenadas()
    {
        return $this->itemsAvance()->leftJoin('dbo.contratos', 'contratos.id_concepto', 'items.id_concepto')
            ->where('items.id_transaccion', '=', $this->id_transaccion)
            ->orderBy('contratos.nivel', 'asc')->select('items.*', 'contratos.nivel');
    }

    public function partidasSubcontrato()
    {
        $respuesta = array();
        $items = array();
        $nivel_ancestros = '';

        foreach ($this->partidasOrdenadas as $partida) {
            $nivel = substr($partida->nivel, 0, strlen($partida->nivel) - 4);
            if ($nivel != $nivel_ancestros) {
                $nivel_ancestros = $nivel;
                foreach ($partida->ancestros as $ancestro) {
                    $items[$ancestro[1]] = ["para_estimar" => 0, "descripcion" => $ancestro[0], "clave" => $ancestro[2], "nivel" => (int)$ancestro[3]];
                }
            }
            $contrato = Contrato::where('id_transaccion', '=', $this->subcontrato->id_antecedente)->where("id_concepto", "=",$partida->id_concepto)->first();
            $items [$partida->nivel] = $partida->partidasAvanceSubcontrato($this->id_transaccion, $contrato);
        }
        $subcontratoTransformer=  new SubcontratoTransformer();
        $respuesta = array(
            'id' => $this->getKey(),
            'id_antecedente' => $this->id_antecedente,
            'razon_social' => $this->empresa->razon_social,
            'numero_folio_format' => $this->numero_folio_format,
            'observaciones' => $this->observaciones,
            'fecha_format' => $this->fecha_format,
            'estado' => (int) $this->estado,
            'color_estado' => $this->color_estado,
            'descripcion_estado' => $this->descripcion_estado,
            'nombre_usuario' => $this->nombre_usuario,
            'cumplimiento' => $this->getOriginal('cumplimiento'),
            'cumplimiento_format' => $this->cumplimiento_format,
            'vencimiento' => $this->vencimiento,
            'vencimiento_format' => $this->vencimiento_format,
            'subtotal_format' => $this->subtotal_format,
            'impuesto_format' => $this->impuesto_format,
            'total_format' => $this->total_format,
            'fecha_ejecucion' => $this->fecha_ejecucion.' 00:00:00',
            'fecha_contable' => $this->fecha_contable.' 00:00:00',
            'fecha_ejecucion_format' => $this->fecha_ejecucion_format,
            'fecha_contable_format' => $this->fecha_contable_format,
            'subcontrato' => $subcontratoTransformer->transform($this->subcontrato),
            'partidas' => $items
        );
        return $respuesta;
    }

    public function editar($data)
    {
        $cumplimiento = New DateTime($data['cumplimiento']);
        $cumplimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
        $vencimiento = New DateTime($data['vencimiento']);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
        $fecha_ejecucion = New DateTime($data['fecha_ejecucion']);
        $fecha_ejecucion->setTimezone(new DateTimeZone('America/Mexico_City'));
        $fecha_contable = New DateTime($data['fecha_contable']);
        $fecha_contable->setTimezone(new DateTimeZone('America/Mexico_City'));

        try {
            DB::connection('cadeco')->beginTransaction();
            $data['cumplimiento'] = $cumplimiento->format("Y-m-d");
            $data['vencimiento'] = $vencimiento->format("Y-m-d");
            $data['fecha_ejecucion'] = $fecha_ejecucion->format("Y-m-d");
            $data['fecha_contable'] = $fecha_contable->format("Y-m-d");
            $this->update(array_except($data, 'conceptos'));
            foreach ($data['conceptos'] as $concepto) {
                if (!array_key_exists('para_estimar', $concepto)) {
                    if ($concepto['cantidad_avance'] == 0 && $concepto['id_item_avance'] != 0) {
                        $item = ItemAvanceSubcontrato::where('id_item', $concepto['id_item_avance'])->first();
                        $item->delete();
                    }
                    if ($concepto['cantidad_avance'] != 0 && $concepto['id_item_avance'] != 0)
                    {
                        $item = ItemAvanceSubcontrato::where('id_item', $concepto['id_item_avance'])->first();
                        if($item)
                        {
                            if($item->cantidad != $concepto['cantidad_avance'])
                            {
                                $item->update([
                                    'cantidad' => (float)$concepto['cantidad_avance'],
                                    'importe' => (float)$concepto['cantidad_avance'] * (float)$concepto['precio_unitario_subcontrato'],
                                ]);
                            }
                        }else{
                            $this->agregarItem($concepto);
                        }
                    }
                    if ($concepto['cantidad_avance'] != 0 && $concepto['id_item_avance'] == 0)
                    {
                        $this->agregarItem($concepto);
                    }
                }
            }
            $this->calcularSubtotal();
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function agregarItem($item)
    {
        $this->itemsAvance()->create([
            'id_transaccion' => $this->id_transaccion,
            'id_antecedente' => $this->id_antecedente,
            'id_concepto' => $item['id_concepto'],
            'cantidad' => (float) $item['cantidad_avance'],
            'precio_unitario' => (float) $item['precio_unitario_subcontrato'],
            'importe' => (float) $item['cantidad_avance'] * (float) $item['precio_unitario_subcontrato'],
        ]);
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validarEliminacion();
            $this->delete();
            $this->revisarRespaldos($motivo);
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    /**
     * Validar la eliminación del avance para poder realizar los cambios.
     */
    private function validarEliminacion()
    {
        if($this->estado != 0)
        {
            abort(500, "Este avance de subcontrato se encuentra ".$this->descripcion_estado.".");
        }
    }

    /**
     * Elimina las partidas
     */
    public function eliminarPartidas()
    {
        foreach ($this->itemsAvance()->get() as $item) {
            $item->respaldar();
            $item->delete();
        }
    }

    public function respaldar()
    {
        AvanceSubcontratoEliminado::create([
            'id_transaccion' => $this->id_transaccion,
            'id_antecedente' => $this->id_antecedente,
            'tipo_transaccion' => $this->tipo_transaccion,
            'numero_folio' => $this->numero_folio,
            'fecha' => $this->fecha,
            'estado' => $this->estado,
            'id_obra' => $this->id_obra,
            'id_empresa' => $this->id_empresa,
            'id_moneda' => $this->id_moneda,
            'cumplimiento' => $this->cumplimiento,
            'vencimiento' => $this->vencimiento,
            'opciones' => $this->opciones,
            'monto' => $this->monto,
            'saldo' => $this->saldo,
            'impuesto' => $this->impuesto,
            'referencia'=> $this->referencia,
            'comentario' => $this->comentario,
            'observaciones' => $this->observaciones,
            'FechaHoraRegistro' => $this->FechaHoraRegistro,
            'fecha_ejecucion' => $this->fecha_ejecucion,
            'fecha_contable' => $this->fecha_contable,
            'id_usuario' => $this->id_usuario,
            'motivo' => '',
            'id_usuario_elimino' => auth()->id(),
            'fecha_eliminacion' => date('Y-m-d H:i:s')
        ]);
    }

    private function revisarRespaldos($motivo)
    {
        if (($avance = AvanceSubcontratoEliminado::where('id_transaccion', $this->id_transaccion)->first()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del avance de subcontrato, no se respaldo el avance correctamente.');
        } else {
            $avance->motivo = $motivo;
            $avance->save();
        }
        if (($item = AvanceSubcontratoPartidaEliminada::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del avance de subcontrato, no se respaldo los items correctamente.');
        }
    }
}
