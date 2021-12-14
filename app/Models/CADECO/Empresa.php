<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 03:35 PM
 */

namespace App\Models\CADECO;

use App\Models\IGH\Usuario;
use App\Events\IncidenciaCI;
use App\Models\CADECO\Transaccion;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinada;
use App\Views\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaVw;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\FinanzasCBE\Solicitud;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\CADECO\Contabilidad\CuentaEmpresa;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;

class Empresa extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.empresas';
    protected $primaryKey = 'id_empresa';

    public $timestamps = false;
    public $searchable = [
        'razon_social',
        'rfc'
    ];
    protected $fillable = [
        'tipo_empresa',
        'razon_social',
        'UsuarioRegistro',
        'id_ctg_bancos',
        'rfc',
        'dias_credito',
        'no_proveedor_virtual',
        'porcentaje',
        'tipo_cliente',
        'emite_factura',
        'es_nacional',
        'UsuarioRegistro'
    ];

    public function cuentasEmpresa()
    {
        return $this->hasMany(CuentaEmpresa::class, 'id_empresa')
            ->where('Contabilidad.cuentas_empresas.estatus', '=', 1);
    }

    public function cuentas(){
        return $this->hasMany(Cuenta::class, 'id_empresa', 'id_empresa');
    }

    public function compras(){
        return $this->hasMany(OrdenCompra::class, 'id_empresa', 'id_empresa');
    }
    public function subcontrato(){
        return $this->hasMany(Subcontrato::class, 'id_empresa', 'id_empresa');
    }

    public function estimacion(){
        return $this->hasMany(Estimacion::class, 'id_empresa', 'id_empresa');
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'id_empresa');
    }

    public function facturas(){
        return $this->belongsTo(Documento::class, 'id_empresa', 'IDDestinatario');
    }

    public function cuentasBancarias()
    {
        return $this->hasMany(CuentaBancariaEmpresa::class, 'id_empresa', 'id_empresa');
    }

    public function solicitudCBE()
    {
        return $this->hasMany(Solicitud::class, 'id_empresa', 'id_empresa');
    }

    public function efo()
    {
        return $this->belongsTo(CtgEfos::class, 'rfc', 'rfc');
    }

    public function empresaBoletinada()
    {
        return $this->belongsTo(EmpresaBoletinada::class, 'rfc', 'rfc');
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_empresa', 'id_empresa');
    }

    public function transaccionesSgv()
    {
        return $this->hasMany(Transaccion::class, 'id_empresa', 'id_empresa')->withoutGlobalScopes();
    }

    public function usuario ()
    {
        return $this->belongsTo(Usuario::class, 'UsuarioRegistro', 'idusuario');
    }

    public function empresaPadron()
    {
        return $this->belongsTo(\App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa::class, "rfc", "rfc");
    }

    public function empresaSAT()
    {
        return $this->belongsTo(ProveedorSAT::class, "rfc", "rfc");
    }

    public function usuarioIntranet ()
    {
        return $this->hasOne(Usuario::class, 'usuario', 'rfc');
    }

    public function scopeConCuentas($query)
    {
        return $query->has('cuentasEmpresa');
    }

    public function scopeBancos($query)
    {
        return $query->where('tipo_empresa', '=', 8);
    }

    public function scopeNoDeducibles($query)
    {
        return $query->where('emite_factura', '=', 0)
            ->where('es_nacional', '=', 1)
            ->whereIn('tipo_empresa', [1,2,3,4]);
    }

    public function scopeDeducibles($query)
    {
        return $query->where('emite_factura', '=', 1)
            ->where('es_nacional', '=', 1)
            ->whereIn('tipo_empresa', [1,2,3,4]);
    }

    public function scopeExtranjeras($query)
    {
        return $query->where('es_nacional', '=', 0)
            ->whereIn('tipo_empresa', [1,2,3,4]);
    }

    public function scopeFacturasAutorizadas($query){
        return $query->has('facturas')->distinct('id_empresa');
    }

    public function scopeParaSubcontratistas($query)
    {
        return $query->has('subcontrato')->has('estimacion')->distinct('id_empresa')->orderBy('razon_social');
    }

    public function scopeParaOrdenCompra($query)
    {
        return $query->has('compras')->distinct('id_empresa')->orderBy('razon_social');
    }

    public function scopeProveedor($query)
    {
        return $query->whereIn('tipo_empresa',[1,2]);
    }

    public function scopeContratista($query)
    {
        return $query->whereIn('tipo_empresa',[2,3]);
    }

    public function scopeProveedorContratista($query)
    {
        return $query->whereIn('tipo_empresa',[1,2,3]);
    }

    public function scopeTipoEmpresa($query, $tipo)
    {
        return $query->whereIn('tipo_empresa', explode(",", $tipo));
    }

    public function scopeDestajistas($query)
    {
        return $query->where('tipo_empresa',4);
    }

    public function scopeBanco($query)
    {
        return $query->where('tipo_empresa',8);
    }

    public function scopeCliente($query)
    {
        return $query->where('tipo_empresa',16);
    }

    public function scopeClienteComprador($query)
    {
        return $query->where('tipo_empresa',16)
            ->whereIn("tipo_cliente", [1,3]);
    }

    public function scopeClienteInversionista($query)
    {
        return $query->where('tipo_empresa',16)
            ->whereIn("tipo_cliente", [2,3]);
    }

    public function scopeResponsableFondoFijo($query)
    {
        return $query->where('tipo_empresa',32);
    }

    public function scopeConRfc($query){
        return $query->whereNotNull('rfc');
    }

    public function getTipoAttribute()
    {
        if($this->tipo_empresa == 1){
            return 'Proveedor';
        }
        if($this->tipo_empresa == 2){
            return 'Contratista';
        }
        if($this->tipo_empresa == 3){
            return 'Proveedor y Contratista';
        }
        if($this->tipo_empresa == 4){
            return 'Destajistas';
        }
        if($this->tipo_empresa == 8 && $this->tipo_cliente == 0){
            return 'Deudor y/o Acreedor';
        }
        if($this->tipo_empresa == 16 && $this->tipo_cliente == 1){
            return 'Cliente Comprador';
        }
        if($this->tipo_empresa == 16 && $this->tipo_cliente == 2){
            return 'Cliente Inversionista';
        }
        if($this->tipo_empresa == 16 && $this->tipo_cliente == 3){
            return 'Cliente Comprador / Inversionista';
        }
        if($this->tipo_empresa == 32){
            return 'Responsables Fondos Fijos';
        }
    }

    public function getIdPersonalidadRFC()
    {
        $rfc = $this->rfc;
        if($rfc != ''){
            $digito = substr($rfc,3,1);
            if(is_numeric($digito)){
                return 2;
            } else {
                return 1;
            }
        }
    }

    public function getPersonalidadDefinicionAttribute()
    {
        if($this->rfc != ''){
            $id_personalidad = $this->getIdPersonalidadRFC();
        } else {
            $id_personalidad = $this->personalidad;
        }

        switch ($id_personalidad)
        {
            case(1):
                return 'Persona Física';
                break;
            case(2):
                return 'Persona Moral';
                break;
            default:
                return 'Por favor, solicite el registro del RFC';
        }
    }

    public function scopeBeneficiarioCuentaBancaria($query)
    {
        return $query->has('cuentasBancarias');
    }

    public function validaRFC($data){
        if(isset($data->rfc) && $data->rfc != 'XXXXXXXXXXXX' && $data->rfc != 'XEXX010101000'){
            if(strlen(str_replace(" ","", $data->rfc))>0){
                $this->rfcValido($data->rfc)?'':abort(403, 'El RFC tiene formato inválido.');
                $this->rfcValidaEfos($data->rfc);
                $this->rfcValidaBoletinados($data->rfc);
            }
        }
    }

    private function rfcValidaBoletinados($rfc)
    {
        $boletinada = EmpresaBoletinadaVw::where("rfc","=",$rfc)->first();
        if($boletinada)
        {
            abort(403, 'Esta empresa esta boletinada para HI por '.$boletinada->motivo_txt.', no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
        }

    }

    private function rfcValidaEfos($rfc)
    {
        if(!is_null($this->efo()->where('rfc', $rfc)->where('estado', 0)->first()))
        {
            event(new IncidenciaCI(
                ["id_tipo_incidencia"=>1,
                    "rfc"=>$rfc,
                    "empresa"=>$this->efo->razon_social,
                ]
            ));
            abort(403, 'Esta empresa esta invalidada por el SAT por ser un EFOS definitivo, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
        }else if(!is_null($this->efo()->where('rfc', $rfc)->where('estado', 2)->first()))
        {
            event(new IncidenciaCI(
                ["id_tipo_incidencia"=>2,
                    "rfc"=>$rfc,
                    "empresa"=>$this->efo->razon_social,
                ]
            ));
            abort(403, 'Esta empresa esta invalidada por el SAT por ser un EFOS presunto, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
        }
    }

    private function rfcValido($rfc)
    {
        if(strlen(str_replace(" ","", $rfc))>0){
            $reg_exp = "/^(([A-ZÑ&]{3,4})[\-]?([0-9]{2})([0][13578]|[1][02])(([0][1-9]|[12][\\d])|[3][01])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))|".
                "(([A-ZÑ&]{3,4})[\-]?([0-9]{2})([0][13456789]|[1][012])(([0][1-9]|[12][\\d])|[3][0])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))|".
                "(([A-ZÑ&]{3,4})[\-]?([02468][048]|[13579][26])[0][2]([0][1-9]|[12][\\d])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))|".
                "(([A-ZÑ&]{3,4})[\-]?([0-9]{2})[0][2]([0][1-9]|[1][0-9]|[2][0-8])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))$/";
            return (bool)preg_match($reg_exp, $rfc);
        }
        return true;
    }

    public function validaEliminacion()
    {
        $cantidad = $this->transacciones()->withoutGlobalScopes()->count('id_empresa');
        if($cantidad > 0){
            abort(403, 'La empresa "'. $this->razon_social.'" no puede ser eliminada porque tiene ' . $cantidad . ' transaccion(es) asociada(s).');
        }
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->FechaHoraRegistro);
        return date_format($date,"d/m/Y");
    }

    public function getTransaccionesAfectacion(){
        $transacciones = $this->transacciones()->select('tipo_transaccion', DB::raw('count(1) as total'))->groupBy('tipo_transaccion')->get();
        $data = [];
        $data['data'] = [];
        $total = 0;
        foreach($transacciones as $transaccion){
            $total += $transaccion->total;
            $data['data'][] = [
                'tipo_transaccion' => $transaccion->tipo_transaccion,
                'tipo' => $transaccion->tipo,
                'cantidad' => $transaccion->total,
            ];
        }
        $data['total_afectacion'] = $total;
        return $data;
    }

    public function getSolicitudesCBEAfectacion(){
        $solicitudes = $this->solicitudCBE;
        return $solicitudes->count();
    }

    public function getCuentaBancariaEmresaAfectacion(){
        $cuentas = $this->cuentasBancarias;
        return $cuentas->count();
    }

    public function getEmpresaPorRFC($rfc)
    {
        return Empresa::where('rfc', '=', $rfc)->withoutGlobalScopes()->first();
    }
}
