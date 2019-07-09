<?php


namespace App\Models\CADECO\Compras;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraComplemento extends Model
{

    protected $connection = 'cadeco';
    protected $table = 'Compras.ordenes_compra_complemento';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;
    protected $fillable = [
        'id_transaccion',
        'estatus',
        'registro',
    ];

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

}