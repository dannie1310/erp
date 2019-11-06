<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\SolicitudComplemento;
use App\Models\CADECO\Transaccion;
use App\Models\CADECO\SolicitudCompraPartida;
use App\Models\IGH\Usuario;
use Illuminate\Http\Request;

class SolicitudCompra extends Transaccion
{

    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 17);
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

    public function getRegistroAttribute()
    {
        $comentario = explode('|', $this->comentario);
        return $comentario[1];
    }

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




}
