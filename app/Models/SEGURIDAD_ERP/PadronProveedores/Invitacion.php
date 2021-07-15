<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\CADECO\Obra;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Invitacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.invitaciones';
    public $timestamps = false;

    protected $fillable = [
        'id_proveedor_padron',
        'id_proveedor_sat',
        'base_datos',
        'id_proveedor_sao',
        'id_transaccion_antecedente',
        'id_cotizacion_generada',
        'id_obra',
        'tipo_transaccion_antecedente',
        'opcion_transaccion_antecedente',
        'razon_social',
        'rfc',
        'domicilio_fiscal',
        'email',
        'nombre_contacto',
        'usuario_invito',
        'estado'
    ];

    /**
     * Relaciones
     */
    public function transaccionAntecedente()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(Transaccion::class, "id_transaccion_antecedente", "id_transaccion")->withoutGlobalScopes();
    }

    public function cotizacionGenerada()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(Transaccion::class, "id_cotizacion_generada", "id_transaccion")->withoutGlobalScopes();
    }

    public function usuarioInvito()
    {
        return $this->belongsTo(Usuario::class, "usuario_invito", "idusuario");
    }

    public function obra()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(Obra::class, "id_obra", "id_obra");
    }

    /**
     * Scopes
     */
    public function scopeParaCotizacionCompra($query)
    {
        return $query->where("tipo_transaccion_antecedente","=",17);
    }

    public function scopeParaCotizacionContraro($query)
    {
        return $query->where("tipo_transaccion_antecedente","=",49);
    }

    /**
     * Atributos
     */
    public function getNombreObraAttribute()
    {
        try{
            return $this->obra->nombre;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuarioInvito->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        $invitacion = Invitacion::create($data);
        return $invitacion;
    }

}
