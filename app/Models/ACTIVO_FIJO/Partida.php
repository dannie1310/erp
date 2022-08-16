<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Partida extends Model
{
    protected $connection = 'sci';
    protected $table = 'partidas';
    public $primaryKey = 'idPartida';
    protected $fillable = [
    ];

    /**
     * Relaciones Eloquent
     */
    
    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'idModelo', 'idModelo');
    }
    
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'idMarca', 'idMarca');
    }
    
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'idEquipo', 'idEquipo');
    }
    
    public function grupoActivo()
    {
        return $this->belongsTo(Usuario::class, 'idGrupo', 'idGrupo');
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usr', 'idUsuario');
    }
    
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'idUbicacion', 'idubicacion');
    }
    
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'idFactura', 'idFactura');
    }
    
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'idProveedor', 'idProveedor');
    }
    
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'idEmpresa', 'idEmpresa');
    }
    
}