<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Sucursal;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
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
        'id_area_compradora',
        'id_area_subcontratante',
        'id_cotizacion_generada',
        'id_obra',
        'nombre_obra',
        'descripcion_obra',
        'fecha_cierre_invitacion',
        'direccion_entrega',
        'ubicacion_entrega_plataforma_digital',
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
        'enviada',
        'cuerpo_correo'
    ];

    //protected $dates = ["fecha_cierre_invitacion"];
    //protected $dateFormat = 'M d Y h:i:s A';
    /*
     * Relaciones*/

    /**
     * Relaciones
     */
    public function transaccionAntecedente()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(Transaccion::class, "id_transaccion_antecedente", "id_transaccion")->withoutGlobalScopes();
    }

    public function solicitudAntecedente()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(SolicitudCompra::class, "id_transaccion_antecedente", "id_transaccion")->withoutGlobalScopes();
    }

    public function solicitud()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(SolicitudCompra::class, "id_transaccion_antecedente", "id_transaccion")->withoutGlobalScopes();
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

    public function empresa()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(\App\Models\CADECO\Empresa::class, "id_proveedor_sao", "id_empresa")->withoutGlobalScopes();
    }

    public function sucursal()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(\App\Models\CADECO\Sucursal::class, "id_sucursal_sao", "id_sucursal")->withoutGlobalScopes();
    }

    public function archivos()
    {
        return $this->hasMany(InvitacionArchivo::class, "id_invitacion", "id");
    }

    public function obra()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(Obra::class, "id_obra", "id_obra");
    }

    public function areaCompradora()
    {
        return $this->belongsTo(CtgAreaCompradora::class, 'id_area_compradora','id');
    }

    public function cartaTerminos()
    {
        return $this->hasOne(InvitacionArchivo::class, "id_invitacion", "id")
            ->where("id_tipo_archivo","=",43);
    }

    public function formatoCotizacion()
    {
        return $this->hasOne(InvitacionArchivo::class, "id_invitacion", "id")
            ->where("id_tipo_archivo","=",44);
    }

    /**
     * Scopes
     */
    public function scopeParaCotizacionCompra($query)
    {
        return $query->where("tipo_transaccion_antecedente","=",17);
    }

    public function scopeParaCotizacionContrato($query)
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
    public function scopePorObra($query)
    {
        if (Context::isEstablished()) {
            return $query->where("base_datos","=", Context::getDatabase())
                ->where("id_obra","=",Context::getIdObra());
        } else
        {
            return $query->where("id","=","0");
        }
    }

    public function scopeAreasCompradorasPorUsuario($query)
    {
        return $query->whereHas('areaCompradora', function ($q1) {
            return $q1->areasPorUsuario();
        });
    }

    public function scopeInvitadoAutenticado($query)
    {
        return $query->where('usuario_invitado',  auth()->id());
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

    public function getFechaCierreInvitacionFormatAttribute()
    {
        $date = date_create($this->fecha_cierre_invitacion);
        return date_format($date,"d/m/Y");
    }

    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuarioInvito->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getAutorizacionRequeridaComprasAttribute()
    {
        try{
            return $this->obra->configuracionCompras->con_autorizacion;
        }catch (\Exception $e)
        {
            return "0";
        }
    }

    public function getDescripcionSucursalAttribute()
    {
        try{
            return $this->sucursal->descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getImporteCotizacionFormatAttribute()
    {
        try{
            return '$ '.number_format($this->cotizacionGenerada->monto,2,'.',',');
        }catch (\Exception $e)
        {
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
        $solicitudes = self::invitadoAutenticado()->disponibleCotizar()->orderBy('id_transaccion_antecedente','desc')->get();
        foreach ($solicitudes as $key =>  $solicitud) {
            $transacciones[$key]['id'] = $solicitud->id;
            $transacciones[$key]['numero_folio_format'] = $solicitud->transaccionAntecedente->numero_folio_format;
            $transacciones[$key]['observaciones'] = $solicitud->transaccionAntecedente->observaciones;
        }
        return $transacciones;
    }

    public function getSolicitud()
    {
        if($this->tipo_transaccion_antecedente == 17) {
            return [
                'id' => $this->solicitud->getKey(),
                'numero_folio' => $this->solicitud->numero_folio,
                'fecha' => $this->solicitud->fecha,
                'estado' => (int)$this->solicitud->estado,
                'estado_solicitud' => $this->solicitud->complemento ? $this->solicitud->complemento->estadoSolicitud->descripcion : '',
                'fecha_format' => $this->solicitud->fecha_format,
                'fecha_registro' => $this->solicitud->fecha_hora_registro_format,
                'observaciones' => $this->solicitud->observaciones,
                'concepto' => $this->solicitud->complemento ? $this->solicitud->complemento->concepto : '',
                'numero_folio_compuesto' => $this->solicitud->complemento ? $this->solicitud->complemento->folio_compuesto : '',
                'area_compradora' => $this->solicitud->area_compradora,
                'area_solcitante' => $this->solicitud->area_solicitante,
                'numero_folio_format' => (string)$this->solicitud->numero_folio_format,
                'cotizaciones' => $this->solicitud->cotizaciones ? $this->solicitud->cotizaciones->count() : null,
                'autorizacion_requerida' => $this->autorizacion_requerida_compras,
                'tipo_transaccion' => $this->tipo_transaccion_antecedente,
                'observaciones' => $this->solicitud->observaciones,
                'id_invitacion' => $this->getKey(),
                'razon_social' => $this->razon_social,
                'rfc' => $this->rfc,
                'base_datos' => $this->base_datos,
                'nombre_usuario_invitado' => $this->usuarioInvitado->nombre_completo_sin_espacios,
                'sucursal' => $this->descripcion_sucursal,
                'partidas' => $this->partidasSolicitud()
            ];
        }
        // colocar el array para contrato proyectado
    }

    public function partidasSolicitud()
    {
        $partidas = [];
        foreach ($this->solicitud->partidas as $key => $partida)
        {
            array_push($partidas,[
                'id' => $partida->getKey(),
                'id_material' => $partida->material->getKey(),
                'numero_parte' => $partida->material->numero_parte,
                'material' => $partida->material->descripcion,
                'unidad' => $partida->unidad,
                'cantidad' => $partida->cantidad,
                'cantidad_format' => $partida->cantidad_format,
                'solicitado_cantidad' => $partida->solicitado_cantidad_format,
                'orden_compra_cantidad' => $partida->cantidad_orden_compra ? $partida->cantidad_orden_compra_format : '0.0',
                'surtido_cantidad' => $partida->cantidad_entrada_material ? $partida->cantidad_entrada_material_format : '0.0',
                'existencia_cantidad' => $partida->suma_inventario_format,
                'cantidad_original' => ($partida->cantidad_original1 > 0) ? $partida->cantidad_original_format : $partida->solicitado_cantidad_format,
                'cantidad_original_num' => ($partida->cantidad_original1 > 0) ? $partida->cantidad_original1 : $partida->cantidad,
                'descuento' => $partida->descuento,
                'observaciones' => $partida->complemento ? $partida->complemento->observaciones : '',
                'enable' => true
            ]);
        }
       return $partidas;
    }
}
