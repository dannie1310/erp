<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\SolicitudComplemento;
use App\Models\CADECO\SolicitudCompraPartida;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;

class SolicitudCompra extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 17)
            ->where('opciones', '=', 1)
            ->where('estado', '!=', 2);
        });
    }



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


    public $searchable = [
        'numero_folio',
        'observaciones',
        'fecha'
    ];

    public function complemento(){
        return $this->belongsTo(SolicitudComplemento::class,'id_transaccion', 'id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(SolicitudCompraPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'usuario');
    }
    public function cotizaciones()
    {
        return $this->hasMany(CotizacionCompra::class, 'id_antecedente', 'id_transaccion');
    }

    public function getRegistroAttribute()
    {
        $comentario = explode('|', $this->comentario);
        return $comentario[1];
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");

    }

    public function aprobarSolicitud($data)
    {
        $x = 0;
        $partidas = $data['partidas'];
        $cantidades = $data['cantidad'];
        $res = array();

        foreach($partidas as $partida)
        {
            if($partida['solicitado_cantidad'] != $cantidades[$x])
            {
                $items = SolicitudCompraPartida::find($partida['id']);
                $items->cantidad_original1 = $partida['solicitado_cantidad'];
                $items->cantidad = $cantidades[$x];
                $items->save();
            }
            $x ++;
        }

        $this->estado = 1;
        $this->save();
        return $this->partidas;
    }

}
