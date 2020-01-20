<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.sucursales';
    protected $primaryKey = 'id_sucursal';

    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'descripcion',
        'direccion',
        'ciudad',
        'codigo_postal',
        'estado',
        'telefono',
        'fax',
        'telefono_movil',
        'contacto',
        'cargo',
        'email',
        'casa_central',
        'observaciones'

    ];

    public function transacciones(){
        return $this->hasmany(Transaccion::class, 'id_sucursal', 'id_sucursal');
    }

    public function getCodigoPostalFormatAttribute(){
        return str_pad($this->codigo_postal, 5, 0, STR_PAD_LEFT);
    }

    public function validarEliminacionSucursal(){
        $cantidad = $this->transacciones()->count();
        if($cantidad > 0){
            abort(403, 'La Sucursal '.$this->descripcion.' no puede ser eliminada porque tiene ' . $cantidad . ' transaccion(es) asociada(s).');
        }
    }
}
