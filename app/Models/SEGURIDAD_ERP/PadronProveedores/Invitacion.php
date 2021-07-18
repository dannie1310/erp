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
        'id_sucursal_sao',
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
        'observaciones',
        'usuario_invito',
        'usuario_invitado',
        'estado',
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

    public function usuarioInvitado()
    {
        return $this->belongsTo(Usuario::class, "usuario_invitado", "idusuario");
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

    public function scopeCotizacionRealizada($query)
    {
        return $query->whereNotNull("id_cotizacion_generada");
    }

    public function scopeDisponibleCotizar($query)
    {
        return $query->whereNull("id_cotizacion_generada");
    }

    /**
     * Atributos
     */
    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->id);
    }

    public function getFechaHoraFormatAttribute()
    {
        $date = date_create($this->fecha_hora_invitacion);
        return date_format($date,"d/m/Y");
    }

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

    public function getSolicitudes()
    {
        $transacciones = [];
        $solicitudes = self::disponibleCotizar()->get();
        foreach ($solicitudes as $key =>  $solicitud) {
            $transacciones[$key] = $solicitud->transaccionAntecedente;
        }
        dd($transacciones);

        dd($transacciones);
    }
}
