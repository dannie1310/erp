<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.entregas';
    protected $primaryKey = 'id_item';
    public $timestamps = false;
    protected $fillable = [
        'id_item',
        'fecha',
        'cantidad',
        'numero_entrega',
        'id_concepto',
        'id_almacen',
        'surtida',
    ];

    public function itemSolicitudCompra()
    {
        return $this->belongsTo(ItemSolicitudCompra::class, 'id_item');
    }

    public function ordenCompraPartida()
    {
        return $this->belongsTo(OrdenCompraPartida::class, 'id_item','id_item');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date, "d/m/Y");
    }

    public function getPendienteEntregaAttribute()
    {
        return number_format(($this->cantidad - $this->surtida), 3, '.', '');
    }

    public function getDestinoAttribute()
    {
        if ($this->concepto) {
            return $this->concepto;
        } else {
            return $this->almacen;
        }
    }

    public function getDestinoTxtAttribute()
    {
        if ($this->concepto) {
            return $this->concepto->path;
        } else {
            return $this->almacen->descripcion;
        }
    }

    public function setCumplida()
    {
        $this->surtida = $this->cantidad;
        $this->save();
    }

    public function surte($cantidad)
    {
        $this->surtida = $this->surtida + $cantidad;
        $this->save();
    }

    public function recalculaSurtido()
    {
        $surtido = $this->ordenCompraPartida->entradas->sum("cantidad");
        $this->surtida = $surtido;
        $this->save();
    }
}
