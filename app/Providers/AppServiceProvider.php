<?php

namespace App\Providers;

use App\Models\CADECO\Banco;
use App\Models\CADECO\Compras\EntradaEliminada;
use App\Models\CADECO\Compras\SalidaEliminada;
use App\Models\CADECO\Contabilidad\Apertura;
use App\Models\CADECO\Contabilidad\Cierre;
use App\Models\CADECO\Contabilidad\CuentaAlmacen;
use App\Models\CADECO\Contabilidad\CuentaBanco;
use App\Models\CADECO\Contabilidad\CuentaConcepto;
use App\Models\CADECO\Contabilidad\CuentaContable;
use App\Models\CADECO\Contabilidad\CuentaCosto;
use App\Models\CADECO\Contabilidad\CuentaEmpresa;
use App\Models\CADECO\Contabilidad\CuentaFondo;
use App\Models\CADECO\Contabilidad\CuentaGeneral;
use App\Models\CADECO\Contabilidad\CuentaMaterial;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Contabilidad\PolizaMovimiento;
use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use App\Models\CADECO\Contratos\AreaSubcontratante;
use App\Models\CADECO\Credito;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Debito;
use App\Models\CADECO\DescuentoFondoGarantia;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\EmpresaFondoFijo;
use App\Models\CADECO\EntradaMaterial;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLog;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use App\Models\CADECO\FinanzasCBE\SolicitudAlta;
use App\Models\CADECO\FinanzasCBE\SolicitudBaja;
use App\Models\CADECO\FinanzasCBE\SolicitudMovimiento;
use App\Models\CADECO\Fondo;
use App\Models\CADECO\Inventarios\Conteo;
use App\Models\CADECO\Inventarios\ConteoCancelado;
use App\Models\CADECO\Inventarios\InventarioFisico;
use App\Models\CADECO\Inventarios\LayoutConteo;
use App\Models\CADECO\Inventarios\LayoutConteoPartida;
use App\Models\CADECO\LiberacionFondoGarantia;
use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoACuenta;
use App\Models\CADECO\PagoVario;
use App\Models\CADECO\SalidaAlmacen;
use App\Models\CADECO\Seguridad\AuditoriaPermisoRol;
use App\Models\CADECO\Seguridad\AuditoriaRolUser;
use App\Models\CADECO\Seguridad\Rol;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\SubcontratosEstimaciones\FolioPorSubcontrato;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use App\Models\CADECO\SubcontratosFG\MovimientoFondoGarantia;
use App\Models\CADECO\SubcontratosFG\MovimientoRetencionFondoGarantia;
use App\Models\CADECO\SubcontratosFG\MovimientoSolicitudMovimientoFondoGarantia;
use App\Models\CADECO\SubcontratosFG\RetencionFondoGarantia;
use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;
use App\Models\CADECO\Sucursal;
use App\Models\CADECO\Tesoreria\MovimientoBancario;
use App\Models\CADECO\Tesoreria\TraspasoCuentas;
use App\Models\CADECO\Transaccion;
use App\Models\SEGURIDAD_ERP\AuditoriaRolUsuario;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\UsuarioAreaSubcontratante;
use App\Observers\CADECO\BancoObserver;
use App\Observers\CADECO\Compras\EntradaEliminadaObserver;
use App\Observers\CADECO\Compras\SalidaEliminadaObserver;
use App\Observers\CADECO\Contabilidad\AperturaObserver;
use App\Observers\CADECO\Contabilidad\CierreObserver;
use App\Observers\CADECO\Contabilidad\CuentaAlmacenObserver;
use App\Observers\CADECO\Contabilidad\CuentaBancoObserver;
use App\Observers\CADECO\Contabilidad\CuentaConceptoObserver;
use App\Observers\CADECO\Contabilidad\CuentaContableObserver;
use App\Observers\CADECO\Contabilidad\CuentaCostoObserver;
use App\Observers\CADECO\Contabilidad\CuentaEmpresaObserver;
use App\Observers\CADECO\Contabilidad\CuentaFondoObserver;
use App\Observers\CADECO\Contabilidad\CuentaGeneralObserver;
use App\Observers\CADECO\Contabilidad\CuentaMaterialObserver;
use App\Observers\CADECO\Contabilidad\PolizaMovimientoObserver;
use App\Observers\CADECO\Contabilidad\PolizaObserver;
use App\Observers\CADECO\Contabilidad\TipoCuentaContableObserver;
use App\Observers\CADECO\Contratos\AreaSubcontratanteObserver;
use App\Observers\CADECO\CreditoObserver;
use App\Observers\CADECO\CuentaObserver;
use App\Observers\CADECO\DebitoObserver;
use App\Observers\CADECO\DescuentoFondoGarantiaObserver;
use App\Observers\CADECO\EmpresaFondoFijoObserver;
use App\Observers\CADECO\EmpresaObserver;
use App\Observers\CADECO\EntradaMaterialObserver;
use App\Observers\CADECO\EstimacionObserver;
use App\Observers\CADECO\Finanzas\ConfiguracionEstimacionObserver;
use App\Observers\CADECO\Finanzas\CuentaBancariaEmpresaObserver;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaLogObserver;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaObserver;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaPartidaObserver;
use App\Observers\CADECO\FinanzasCBE\SolicitudAltaObserver;
use App\Observers\CADECO\FinanzasCBE\SolicitudBajaObserver;
use App\Observers\CADECO\FinanzasCBE\SolicitudMovimientoObserver;
use App\Observers\CADECO\FondoObserver;
use App\Observers\CADECO\Inventarios\ConteoObserver;
use App\Observers\CADECO\Inventarios\ConteoCanceladoObserver;
use App\Observers\CADECO\Inventarios\InventarioFisicoObserver;
use App\Observers\CADECO\Inventarios\LayoutConteoObserver;
use App\Observers\CADECO\Inventarios\LayoutConteoPartidaObserver;
use App\Observers\CADECO\LiberacionFondoGarantiaObserver;
use App\Observers\CADECO\OrdenCompraObserver;
use App\Observers\CADECO\OrdenPagoObserver;
use App\Observers\CADECO\PagoACuentaObserver;
use App\Observers\CADECO\PagoObserver;
use App\Observers\CADECO\PagoVarioObserver;
use App\Observers\CADECO\SalidaAlmacenObserver;
use App\Observers\CADECO\Seguridad\AuditoriaPermisoRolObserver;
use App\Observers\CADECO\Seguridad\AuditoriaRolUserObserver;
use App\Observers\CADECO\Seguridad\RolObserver;
use App\Observers\CADECO\SolicitudPagoAnticipadoObserver;
use App\Observers\CADECO\SubcontratoObserver;
use App\Observers\CADECO\SubcontratosEstimaciones\FolioPorSubcontratoObserver;
use App\Observers\CADECO\SubcontratosFG\FondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosFG\MovimientoFondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosFG\MovimientoRetencionFondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosFG\MovimientoSolicitudMovimientoFondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosFG\RetencionFondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantiaObserver;
use App\Observers\CADECO\SucursalObserver;
use App\Observers\CADECO\Tesoreria\MovimientoBancarioObserver;
use App\Observers\CADECO\Tesoreria\TraspasoCuentasObserver;
use App\Observers\CADECO\TransaccionObserver;
use App\Observers\SEGURIDAD_ERP\AuditoriaRolUsuarioObserver;
use App\Observers\SEGURIDAD_ERP\ConfiguracionObraObserver;
use App\Observers\SEGURIDAD_ERP\UsuarioAreaSubcontratanteObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * CADECO
         */

            /**
             * Compras
             */
            EntradaEliminada::observe(EntradaEliminadaObserver::class);
            SalidaEliminada::observe(SalidaEliminadaObserver::class);

            /**
             *Contabilidad
             */
            Apertura::observe(AperturaObserver::class);
            Cierre::observe(CierreObserver::class);
            CuentaAlmacen::observe(CuentaAlmacenObserver::class);
            CuentaBanco::observe(CuentaBancoObserver::class);
            CuentaConcepto::observe(CuentaConceptoObserver::class);
            CuentaContable::observe(CuentaContableObserver::class);
            CuentaCosto::observe(CuentaCostoObserver::class);
            CuentaEmpresa::observe(CuentaEmpresaObserver::class);
            CuentaFondo::observe(CuentaFondoObserver::class);
            CuentaGeneral::observe(CuentaGeneralObserver::class);
            CuentaMaterial::observe(CuentaMaterialObserver::class);
            Poliza::observe(PolizaObserver::class);
            PolizaMovimiento::observe(PolizaMovimientoObserver::class);
            TipoCuentaContable::observe(TipoCuentaContableObserver::class);

            /**
             * Contratos
             */
            AreaSubcontratante::observe(AreaSubcontratanteObserver::class);

            /**
             * Finanzas
             */
            ConfiguracionEstimacion::observe(ConfiguracionEstimacionObserver::class);
            CuentaBancariaEmpresa::observe(CuentaBancariaEmpresaObserver::class);
            DistribucionRecursoRemesa::observe(DistribucionRecursoRemesaObserver::class);
            DistribucionRecursoRemesaLog::observe(DistribucionRecursoRemesaLogObserver::class);
            DistribucionRecursoRemesaPartida::observe(DistribucionRecursoRemesaPartidaObserver::class);

            /**
             * FinanzasCBE
             */
            SolicitudAlta::observe(SolicitudAltaObserver::class);
            SolicitudBaja::observe(SolicitudBajaObserver::class);
            SolicitudMovimiento::observe(SolicitudMovimientoObserver::class);

            /**
             * Inventarios
             */
            InventarioFisico::observe(InventarioFisicoObserver::class);
            LayoutConteo::observe(LayoutConteoObserver::class);
            LayoutConteoPartida::observe(LayoutConteoPartidaObserver::class);
            Conteo::observe(ConteoObserver::class);
            ConteoCancelado::observe(ConteoCanceladoObserver::class);


            /**
             * Seguridad
             */
            AuditoriaPermisoRol::observe(AuditoriaPermisoRolObserver::class);
            AuditoriaRolUser::observe(AuditoriaRolUserObserver::class);
            Rol::observe(RolObserver::class);

            /**
             * SubcontratosEstimaciones
             */
            FolioPorSubcontrato::observe(FolioPorSubcontratoObserver::class);

            /**
             * SubcontratosFG
             */
            FondoGarantia::observe(FondoGarantiaObserver::class);
            MovimientoFondoGarantia::observe(MovimientoFondoGarantiaObserver::class);
            MovimientoRetencionFondoGarantia::observe(MovimientoRetencionFondoGarantiaObserver::class);
            MovimientoSolicitudMovimientoFondoGarantia::observe(MovimientoSolicitudMovimientoFondoGarantiaObserver::class);
            RetencionFondoGarantia::observe(RetencionFondoGarantiaObserver::class);
            SolicitudMovimientoFondoGarantia::observe(SolicitudMovimientoFondoGarantiaObserver::class);

            /**
             * Tesoreria
             */
            MovimientoBancario::observe(MovimientoBancarioObserver::class);
            TraspasoCuentas::observe(TraspasoCuentasObserver::class);

            Banco::observe(BancoObserver::class);
            Credito::observe(CreditoObserver::class);
            Cuenta::observe(CuentaObserver::class);
            Debito::observe(DebitoObserver::class);
            DescuentoFondoGarantia::observe(DescuentoFondoGarantiaObserver::class);
            Empresa::observe(EmpresaObserver::class);
            EmpresaFondoFijo::observe(EmpresaFondoFijoObserver::class);
            EntradaMaterial::observe(EntradaMaterialObserver::class);
            Estimacion::observe(EstimacionObserver::class);
            Fondo::observe(FondoObserver::class);
            LiberacionFondoGarantia::observe(LiberacionFondoGarantiaObserver::class);
            OrdenCompra::observe(OrdenCompraObserver::class);
            OrdenPago::observe(OrdenPagoObserver::class);
            Pago::observe(PagoObserver::class);
            PagoACuenta::observe(PagoACuentaObserver::class);
            PagoVario::observe(PagoVarioObserver::class);
            SalidaAlmacen::observe(SalidaAlmacenObserver::class);
            SolicitudPagoAnticipado::observe(SolicitudPagoAnticipadoObserver::class);
            Subcontrato::observe(SubcontratoObserver::class);
            Sucursal::observe(SucursalObserver::class);
            Transaccion::observe(TransaccionObserver::class);

        /**
         * SEGURIDAD_ERP
         */
        \App\Models\SEGURIDAD_ERP\AuditoriaPermisoRol::observe(\App\Observers\SEGURIDAD_ERP\AuditoriaPermisoRolObserver::class);
        AuditoriaRolUsuario::observe(AuditoriaRolUsuarioObserver::class);
        ConfiguracionObra::observe(ConfiguracionObraObserver::class);
        \App\Models\SEGURIDAD_ERP\Rol::observe(\App\Observers\SEGURIDAD_ERP\RolObserver::class);
        UsuarioAreaSubcontratante::observe(UsuarioAreaSubcontratanteObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
