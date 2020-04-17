<?php


namespace App\Models\CADECO;


use App\CSV\CotizacionLayout;
use App\Models\CADECO\Compras\CotizacionComplemento;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CotizacionCompra  extends Transaccion
{
    public const TIPO_ANTECEDENTE = 17;
    public const OPCION_ANTECEDENTE = 1;

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'id_obra',
        'comentario',
        'observaciones',
        'FechaHoraRegistro' 
    ];

    protected $searchable = [
        'id_transaccion'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 18)
            ->where('opciones','=', 1)->where('estado', '!=', 2);
        });
    }

    public function cotizaciones() {
        return $this->hasMany(Cotizacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function complemento()
    {
        return $this->belongsTo(CotizacionComplemento::class, 'id_transaccion', 'id_transaccion');
    }

    public function descargaLayout($id)
    {
        return Excel::download(new CotizacionLayout(CotizacionCompra::find($id)), 'LayoutCotizacion.xlsx');
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCompra::class, 'id_antecedente', 'id_transaccion');
    }

    public function crear($data)
    {
        try
        {
            DB::connection('cadeco')->beginTransaction();

            $fecha =New DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $cotizacion = $this->create([
            'id_antecedente' => $data['id_solicitud'],
            'id_empresa' => $data['id_proveedor'],
            'id_sucursal' => ($data['sucursal']) ? $data['id_sucursal'] : null,
            'id_moneda' => 1,
            'observaciones' => $data['observacion'],
            'fecha' => $fecha->format("Y-m-d")
        ]);


            DB::connection('cadeco')->commit();
            return $cotizacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}