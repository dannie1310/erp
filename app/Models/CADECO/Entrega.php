<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.entregas';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'surtida'
    ];

    public $timestamps = false;

    public function ordenCompraPartida()
    {
        return $this->belongsTo(OrdenCompraPartida::class, 'id_item','id_item');
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date, "d/m/Y");
    }

    public function getPendienteEntregaAttribute()
    {
        return number_format(($this->cantidad - $this->surtida), 2, '.', '');
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
}