<?php

namespace App\Providers;

use App\Models\CADECO\AjusteNegativo;
use App\Models\CADECO\AjusteNegativoPartida;
use App\Models\CADECO\AjustePositivo;
use App\Models\CADECO\AjustePositivoPartida;
use App\Models\CADECO\Almacenes\AjusteEliminado;
use App\Models\CADECO\Almacenes\EntregaContratista;
use App\Models\CADECO\Anticipo;
use App\Models\CADECO\Banco;
use App\Models\CADECO\Compras\EntradaEliminada;
use App\Models\CADECO\Compras\MovimientoEliminado;
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
use App\Models\CADECO\EntradaMaterialPartida;
use App\Models\CADECO\Entrega;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Estimaciones\EstimacionEliminada;
use App\Models\CADECO\Factura;
use App\Models\CADECO\Familia;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLog;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use App\Models\CADECO\Finanzas\LayoutPago;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
use App\Models\CADECO\Finanzas\Servicio;
use App\Models\CADECO\FinanzasCBE\SolicitudAlta;
use App\Models\CADECO\FinanzasCBE\SolicitudBaja;
use App\Models\CADECO\FinanzasCBE\SolicitudMovimiento;
use App\Models\CADECO\Fondo;
use App\Models\CADECO\Inventario;
use App\Models\CADECO\Inventarios\Conteo;
use App\Models\CADECO\Inventarios\ConteoCancelado;
use App\Models\CADECO\Inventarios\InventarioFisico;
use App\Models\CADECO\Inventarios\LayoutConteo;
use App\Models\CADECO\Inventarios\LayoutConteoPartida;
use App\Models\CADECO\Inventarios\Marbete;
use App\Models\CADECO\Inventarios\MarbeteLog;
use App\Models\CADECO\LiberacionFondoGarantia;
use App\Models\CADECO\Material;
use App\Models\CADECO\Movimiento;
use App\Models\CADECO\NuevoLote;
use App\Models\CADECO\NuevoLotePartida;
use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\OrdenCompraPartida;
use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoACuenta;
use App\Models\CADECO\PagoACuentaPorAplicar;
use App\Models\CADECO\PagoAnticipoDestajo;
use App\Models\CADECO\PagoVario;
use App\Models\CADECO\SalidaAlmacen;
use App\Models\CADECO\SalidaAlmacenPartida;
use App\Models\CADECO\Seguridad\AuditoriaPermisoRol;
use App\Models\CADECO\Seguridad\AuditoriaRolUser;
use App\Models\CADECO\Seguridad\Rol;
use App\Models\CADECO\SolicitudAnticipoDestajo;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\SolicitudReposicionFF;
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
use App\Models\CADECO\Venta;
use App\Models\CADECO\VentaPartida;
use App\Models\SEGURIDAD_ERP\AuditoriaRolUsuario;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\TipoAreaCompradora;
use App\Models\SEGURIDAD_ERP\TipoAreaSolicitante;
use App\Models\SEGURIDAD_ERP\UsuarioAreaCompradora;
use App\Models\SEGURIDAD_ERP\UsuarioAreaSolicitante;
use App\Models\SEGURIDAD_ERP\UsuarioAreaSubcontratante;
use App\Observers\CADECO\AjusteNegativoObserver;
use App\Observers\CADECO\AjusteNegativoPartidaObserver;
use App\Observers\CADECO\AjustePositivoObserver;
use App\Observers\CADECO\AjustePositivoPartidaObserver;
use App\Observers\CADECO\Almacenes\AjusteEliminadoObserver;
use App\Observers\CADECO\Almacenes\EntregaContratistaObserver;
use App\Observers\CADECO\AnticipoObserver;
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
use App\Observers\CADECO\EntradaMaterialPartidaObserver;
use App\Observers\CADECO\EntregaObserver;
use App\Observers\CADECO\Estimaciones\EstimacionEliminadaObserver;
use App\Observers\CADECO\EstimacionObserver;
use App\Observers\CADECO\FacturaObserver;
use App\Observers\CADECO\FamiliaObserver;
use App\Observers\CADECO\Finanzas\ConfiguracionEstimacionObserver;
use App\Observers\CADECO\Finanzas\CuentaBancariaEmpresaObserver;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaLogObserver;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaObserver;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaPartidaObserver;
use App\Observers\CADECO\Finanzas\LayoutPagoObserver;
use App\Observers\CADECO\Finanzas\LayoutPagoPartidaObserver;
use App\Observers\CADECO\FinanzasCBE\SolicitudAltaObserver;
use App\Observers\CADECO\FinanzasCBE\SolicitudBajaObserver;
use App\Observers\CADECO\FinanzasCBE\SolicitudMovimientoObserver;
use App\Observers\CADECO\FondoObserver;
use App\Observers\CADECO\InventarioObserver;
use App\Observers\CADECO\Inventarios\ConteoObserver;
use App\Observers\CADECO\Inventarios\ConteoCanceladoObserver;
use App\Observers\CADECO\Inventarios\InventarioFisicoObserver;
use App\Observers\CADECO\Inventarios\LayoutConteoObserver;
use App\Observers\CADECO\Inventarios\LayoutConteoPartidaObserver;
use App\Observers\CADECO\Inventarios\MarbeteLogObserver;
use App\Observers\CADECO\Inventarios\MarbeteObserver;
use App\Observers\CADECO\LiberacionFondoGarantiaObserver;
use App\Observers\CADECO\MaterialObserver;
use App\Observers\CADECO\MovimientoObserver;
use App\Observers\CADECO\NuevoLoteObserver;
use App\Observers\CADECO\NuevoLotePartidaObserver;
use App\Observers\CADECO\OrdenCompraObserver;
use App\Observers\CADECO\OrdenCompraPartidaObserver;
use App\Observers\CADECO\OrdenPagoObserver;
use App\Observers\CADECO\PagoACuentaObserver;
use App\Observers\CADECO\PagoACuentaPorAplicarObserver;
use App\Observers\CADECO\PagoAnticipoDestajoObserver;
use App\Observers\CADECO\PagoObserver;
use App\Observers\CADECO\PagoVarioObserver;
use App\Observers\CADECO\SalidaAlmacenObserver;
use App\Observers\CADECO\SalidaAlmacenPartidaObserver;
use App\Observers\CADECO\Seguridad\AuditoriaPermisoRolObserver;
use App\Observers\CADECO\Seguridad\AuditoriaRolUserObserver;
use App\Observers\CADECO\Seguridad\RolObserver;
use App\Observers\CADECO\SolicitudAnticipoDestajoObserver;
use App\Observers\CADECO\SolicitudPagoAnticipadoObserver;
use App\Observers\CADECO\SolicitudReposicionFFObserver;
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
use App\Observers\CADECO\VentaObserver;
use App\Observers\CADECO\VentaPartidaObserver;
use App\Observers\SEGURIDAD_ERP\AuditoriaRolUsuarioObserver;
use App\Observers\SEGURIDAD_ERP\ConfiguracionObraObserver;
use App\Observers\SEGURIDAD_ERP\TipoAreaCompradoraObserver;
use App\Observers\SEGURIDAD_ERP\TipoAreaSolicitanteObserver;
use App\Observers\SEGURIDAD_ERP\UsuarioAreaCompradoraObserver;
use App\Observers\SEGURIDAD_ERP\UsuarioAreaSolicitanteObserver;
use App\Observers\SEGURIDAD_ERP\UsuarioAreaSubcontratanteObserver;
use App\Observers\CADECO\PagoReposicionFFObserver;
use App\Models\CADECO\PagoReposicionFF;
use App\Models\CADECO\PagoFactura;
use App\Observers\CADECO\PagoFacturaObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * Ordenado como la ubicación de los Modelos.
     * @return void
     */
    public function boot()
    {
        /**
         * CADECO
         */

            /**
             * Almacenes
             */
            AjusteEliminado::observe(AjusteEliminadoObserver::class);
            EntregaContratista::observe(EntregaContratistaObserver::class);

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
            PolizaMovimiento::observe(PolizaMovimientoObserver::class);
            Poliza::observe(PolizaObserver::class);
            TipoCuentaContable::observe(TipoCuentaContableObserver::class);

            /**
             * Contratos
             */
            AreaSubcontratante::observe(AreaSubcontratanteObserver::class);


            /**
             * Estimaciones
             */
            EstimacionEliminada::observe(EstimacionEliminadaObserver::class);

            /**
             * Finanzas
             */
            ConfiguracionEstimacion::observe(ConfiguracionEstimacionObserver::class);
            CuentaBancariaEmpresa::observe(CuentaBancariaEmpresaObserver::class);
            DistribucionRecursoRemesa::observe(DistribucionRecursoRemesaObserver::class);
            DistribucionRecursoRemesaLog::observe(DistribucionRecursoRemesaLogObserver::class);
            DistribucionRecursoRemesaPartida::observe(DistribucionRecursoRemesaPartidaObserver::class);
            LayoutPago::observe(LayoutPagoObserver::class);
            LayoutPagoPartida::observe(LayoutPagoPartidaObserver::class);

            /**
             * FinanzasCBE
             */
            SolicitudAlta::observe(SolicitudAltaObserver::class);
            SolicitudBaja::observe(SolicitudBajaObserver::class);
            SolicitudMovimiento::observe(SolicitudMovimientoObserver::class);

            /**
             *Inventarios
             */
            Conteo::observe(ConteoObserver::class);
            ConteoCancelado::observe(ConteoCanceladoObserver::class);
            InventarioFisico::observe(InventarioFisicoObserver::class);
            LayoutConteo::observe(LayoutConteoObserver::class);
            LayoutConteoPartida::observe(LayoutConteoPartidaObserver::class);
            Marbete::observe(MarbeteObserver::class);
            MarbeteLog::observe(MarbeteLogObserver::class);

            /**
             * Seguridad
             */
            AuditoriaPermisoRol::observe(AuditoriaPermisoRolObserver::class);
            AuditoriaRolUser::observe(AuditoriaRolUserObserver::class);
            Rol::observe(RolObserver::class);
            TipoAreaCompradora::observe(TipoAreaCompradoraObserver::class);
            TipoAreaSolicitante::observe(TipoAreaSolicitanteObserver::class);

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



            AjusteNegativo::observe(AjusteNegativoObserver::class);
            AjusteNegativoPartida::observe(AjusteNegativoPartidaObserver::class);
            AjustePositivo::observe(AjustePositivoObserver::class);
            AjustePositivoPartida::observe(AjustePositivoPartidaObserver::class);
            Anticipo::observe(AnticipoObserver::class);
            Banco::observe(BancoObserver::class);
            Credito::observe(CreditoObserver::class);
            Cuenta::observe(CuentaObserver::class);
            Debito::observe(DebitoObserver::class);
            DescuentoFondoGarantia::observe(DescuentoFondoGarantiaObserver::class);
            Empresa::observe(EmpresaObserver::class);
            EmpresaFondoFijo::observe(EmpresaFondoFijoObserver::class);
            Entrega::observe(EntregaObserver::class);
            EntradaMaterial::observe(EntradaMaterialObserver::class);
            EntradaMaterialPartida::observe(EntradaMaterialPartidaObserver::class);
            Estimacion::observe(EstimacionObserver::class);
            Factura::observe(FacturaObserver::class);
            Familia::observe(FamiliaObserver::class);
            Fondo::observe(FondoObserver::class);
            Inventario::observe(InventarioObserver::class);
            LiberacionFondoGarantia::observe(LiberacionFondoGarantiaObserver::class);
            Material::observe(MaterialObserver::class);
            Movimiento::observe(MovimientoObserver::class);
            NuevoLote::observe(NuevoLoteObserver::class);
            NuevoLotePartida::observe(NuevoLotePartidaObserver::class);
            OrdenCompra::observe(OrdenCompraObserver::class);
            OrdenPago::observe(OrdenPagoObserver::class);
            OrdenCompraPartida::observe(OrdenCompraPartidaObserver::class);
            PagoACuenta::observe(PagoACuentaObserver::class);
            PagoACuentaPorAplicar::observe(PagoACuentaPorAplicarObserver::class);
            PagoAnticipoDestajo::observe(PagoAnticipoDestajoObserver::class);
            PagoFactura::observe(PagoFacturaObserver::class);
            Pago::observe(PagoObserver::class);
            PagoReposicionFF::observe(PagoReposicionFFObserver::class);
            PagoVario::observe(PagoVarioObserver::class);
            SalidaAlmacen::observe(SalidaAlmacenObserver::class);
            SalidaAlmacenPartida::observe(SalidaAlmacenPartidaObserver::class);
            SolicitudAnticipoDestajo::observe(SolicitudAnticipoDestajoObserver::class);
            SolicitudPagoAnticipado::observe(SolicitudPagoAnticipadoObserver::class);
            SolicitudReposicionFF::observe(SolicitudReposicionFFObserver::class);
            Subcontrato::observe(SubcontratoObserver::class);
            Sucursal::observe(SucursalObserver::class);
            Transaccion::observe(TransaccionObserver::class);
            Venta::observe(VentaObserver::class);
            VentaPartida::observe(VentaPartidaObserver::class);

        /**
         * SEGURIDAD_ERP
         */
        \App\Models\SEGURIDAD_ERP\AuditoriaPermisoRol::observe(\App\Observers\SEGURIDAD_ERP\AuditoriaPermisoRolObserver::class);
        AuditoriaRolUsuario::observe(AuditoriaRolUsuarioObserver::class);
        ConfiguracionObra::observe(ConfiguracionObraObserver::class);
        \App\Models\SEGURIDAD_ERP\Rol::observe(\App\Observers\SEGURIDAD_ERP\RolObserver::class);
        UsuarioAreaCompradora::observe(UsuarioAreaCompradoraObserver::class);
        UsuarioAreaSolicitante::observe(UsuarioAreaSolicitanteObserver::class);
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
