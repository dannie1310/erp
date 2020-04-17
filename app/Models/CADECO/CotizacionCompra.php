<?php


namespace App\Models\CADECO;


use App\CSV\CotizacionLayout;
use App\Models\CADECO\Compras\CotizacionComplemento;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CotizacionCompra  extends Transaccion
{
    public const TIPO_ANTECEDENTE = 17;

    protected $fillable = [
        'id_transaccion',
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
        $cotizacion = $this->create([
            'fecha' => '2020-02-03',
            'observaciones' => $data['observacion']
        ]);
        dd('fin', $cotizacion);

        
        try
        {
            DB::connection('cadeco')->beginTransaction();
            $cotizacion = $this->create([
                'fecha' => '2020-02-03',
                'observaciones' => 'jorge'
            ]);
            dd('fin', $cotizacion);


            DB::connection('cadeco')->commit();
            return $cotizacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}