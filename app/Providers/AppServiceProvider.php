<?php

namespace App\Providers;

use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\CamionImagen;
use App\Models\ACARREOS\Impresora;
use App\Models\ACARREOS\InicioCamion;
use App\Models\ACARREOS\Marca;
use App\Models\ACARREOS\Material as MaterialAcarreo;
use App\Models\ACARREOS\Operador;
use App\Models\ACARREOS\Origen;
use App\Models\ACARREOS\Tag;
use App\Models\ACARREOS\Telefono;
use App\Models\ACARREOS\Tiro;
use App\Models\ACARREOS\TiroConcepto;
use App\Models\ACARREOS\ViajeNeto;
use App\Models\ACARREOS\VolumenDetalle;
use App\Models\CADECO\AjusteNegativo;
use App\Models\CADECO\AjusteNegativoPartida;
use App\Models\CADECO\AjustePositivo;
use App\Models\CADECO\AjustePositivoPartida;
use App\Models\CADECO\Almacen;
use App\Models\CADECO\Almacenes\AjusteEliminado;
use App\Models\CADECO\Almacenes\EntregaContratista;
use App\Models\CADECO\Anticipo;
use App\Models\CADECO\AplicacionManual;
use App\Models\CADECO\AvanceObra;
use App\Models\CADECO\AvanceSubcontrato;
use App\Models\CADECO\Banco;
use App\Models\CADECO\Cliente;
use App\Models\CADECO\Catalogos\UnificacionProveedores;
use App\Models\CADECO\Compras\AsignacionProveedor;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Models\CADECO\Compras\CotizacionComplemento;
use App\Models\CADECO\Compras\EntradaEliminada;
use App\Models\CADECO\Compras\RequisicionComplemento;
use App\Models\CADECO\Compras\RequisicionEliminada;
use App\Models\CADECO\Compras\RequisicionPartidaComplemento;
use App\Models\CADECO\Compras\MovimientoEliminado;
use App\Models\CADECO\Compras\OrdenCompraComplemento;
use App\Models\CADECO\Compras\SalidaEliminada;
use App\Models\CADECO\Compras\SolicitudComplemento;
use App\Models\CADECO\ComprobanteFondo;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\Configuracion\NodoTipo;
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
use App\Models\CADECO\Contabilidad\DatosContables;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Contabilidad\PolizaMovimiento;
use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use App\Models\CADECO\ContraRecibo;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Contratos\AreaSubcontratante;
use App\Models\CADECO\ControlPresupuesto\Extraordinario;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioPartidas;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioRechazada;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\CotizacionCompraPartida;
use App\Models\CADECO\Credito;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Debito;
use App\Models\CADECO\DepositoCliente;
use App\Models\CADECO\DescuentoFondoGarantia;
use App\Models\CADECO\Destajista;
use App\Models\CADECO\Destino;
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
use App\Models\CADECO\Finanzas\PagoEliminadoLog;
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
use App\Models\CADECO\ItemAvanceObra;
use App\Models\CADECO\ItemComprobanteFondo;
use App\Models\CADECO\ItemEstimacion;
use App\Models\CADECO\ItemSolicitudAutorizacionAvance;
use App\Models\CADECO\ItemSolicitudCompra;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\LiberacionFondoGarantia;
use App\Models\CADECO\Material;
use App\Models\CADECO\Movimiento;
use App\Models\CADECO\NotaCredito;
use App\Models\CADECO\NuevoLote;
use App\Models\CADECO\NuevoLotePartida;
use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\OrdenCompraPartida;
use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoACuenta;
use App\Models\CADECO\PagoACuentaPorAplicar;
use App\Models\CADECO\PagoAnticipoDestajo;
use App\Models\CADECO\PagoListaRaya;
use App\Models\CADECO\PagoVario;
use App\Models\CADECO\PresupuestoObra\Responsable;
use App\Models\CADECO\ProveedorContratista;
use App\Models\CADECO\Requisicion;
use App\Models\CADECO\RequisicionPartida;
use App\Models\CADECO\SalidaAlmacen;
use App\Models\CADECO\SalidaAlmacenPartida;
use App\Models\CADECO\Seguridad\AuditoriaPermisoRol;
use App\Models\CADECO\Seguridad\AuditoriaRolUser;
use App\Models\CADECO\Seguridad\Rol;
use App\Models\CADECO\SolicitudAnticipoDestajo;
use App\Models\CADECO\SolicitudAutorizacionAvance;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\SolicitudReposicionFF;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Subcontratos\AsignacionContratista;
use App\Models\CADECO\Subcontratos\AsignacionContratistaEliminada;
use App\Models\CADECO\Subcontratos\AsignacionContratistaPartida;
use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;
use App\Models\CADECO\Subcontratos\AsignacionSubcontratoEliminado;
use App\Models\CADECO\Subcontratos\Subcontratos;
use App\Models\CADECO\Subcontratos\ClasificacionSubcontrato;
use App\Models\CADECO\SolicitudCambioSubcontrato;
use App\Models\CADECO\SubcontratosCM\SolicitudCambioSubcontratoComplemento;
use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use App\Models\CADECO\SubcontratosEstimaciones\FolioPorSubcontrato;
use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;
use App\Models\CADECO\SubcontratosEstimaciones\Retencion;
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
use App\Models\CADECO\Ventas\VentaCancelacion;
use App\Models\CADECO\VentaPartida;
use App\Models\CTPQ\OtherMetadata\Documento;
use App\Models\MODULOSSAO\ControlRemesas\RemesaFolio;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use App\Models\REPSEG\FinDimIngresoPartida;
use App\Models\REPSEG\FinDimTipoIngreso;
use App\Models\REPSEG\FinFacIngresoFactura;
use App\Models\REPSEG\FinFacIngresoFacturaConcepto;
use App\Models\REPSEG\FinFacIngresoFacturaDetalle;
use App\Models\SEGURIDAD_ERP\AuditoriaRolUsuario;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Compras\AreaCompradoraUsuario;
use App\Models\SEGURIDAD_ERP\Compras\AreaSolicitanteUsuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfosLog;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Fiscal\Autocorreccion;
use App\Models\SEGURIDAD_ERP\Fiscal\CFDAutocorreccion;
use App\Models\SEGURIDAD_ERP\Fiscal\CFDNoDeducido;
use App\Models\SEGURIDAD_ERP\Fiscal\FechaInhabilSat;
use App\Models\SEGURIDAD_ERP\Fiscal\NoDeducido;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaEfos;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinada;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaExcluidaDocumentacion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaPrestadora;
use App\Models\SEGURIDAD_ERP\PadronProveedores\RepresentanteLegal;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Models\SEGURIDAD_ERP\UsuarioAreaSubcontratante;
use App\Observers\ACARREOS\CamionImagenObserver;
use App\Observers\ACARREOS\CamionObserver;
use App\Observers\ACARREOS\ImpresoraObserver;
use App\Observers\ACARREOS\InicioCamionObserver;
use App\Observers\ACARREOS\MaterialObserver as MaterialAcarreoObserver;
use App\Observers\ACARREOS\MarcaObserver;
use App\Observers\ACARREOS\OperadorObserver;
use App\Observers\ACARREOS\OrigenObserver;
use App\Observers\ACARREOS\TagObserver;
use App\Observers\ACARREOS\TelefonoObserver;
use App\Observers\ACARREOS\TiroConceptoObserver;
use App\Observers\ACARREOS\TiroObserver;
use App\Observers\ACARREOS\ViajeNetoObserver;
use App\Observers\ACARREOS\VolumenDetalleObserver;
use App\Observers\CADECO\AjusteNegativoObserver;
use App\Observers\CADECO\AjusteNegativoPartidaObserver;
use App\Observers\CADECO\AjustePositivoObserver;
use App\Observers\CADECO\AjustePositivoPartidaObserver;
use App\Observers\CADECO\Almacenes\AjusteEliminadoObserver;
use App\Observers\CADECO\Almacenes\EntregaContratistaObserver;
use App\Observers\CADECO\AlmacenObserver;
use App\Observers\CADECO\AnticipoObserver;
use App\Observers\CADECO\AplicacionManualObserver;
use App\Observers\CADECO\AvanceObraObserver;
use App\Observers\CADECO\AvanceObraPartidaObserver;
use App\Observers\CADECO\AvanceSubcontratoObserver;
use App\Observers\CADECO\BancoObserver;
use App\Observers\CADECO\ClienteObserver;
use App\Observers\CADECO\Compras\AsignacionProveedorObserver;
use App\Observers\CADECO\Compras\AsignacionProveedorPartidaObserver;
use App\Observers\CADECO\Catalogos\UnificacionProveedoresObserver;
use App\Observers\CADECO\Compras\EntradaEliminadaObserver;
use App\Observers\CADECO\Compras\OrdenCompraComplementoObserver;
use App\Observers\CADECO\Compras\RequisicionComplementoObserver;
use App\Observers\CADECO\Compras\RequisicionEliminadaObserver;
use App\Observers\CADECO\Compras\RequisicionPartidaComplementoObserver;
use App\Observers\CADECO\Compras\RequisicionPartidaObserver;
use App\Observers\CADECO\Compras\SalidaEliminadaObserver;
use App\Observers\CADECO\Compras\SolicitudComplementoObserver;
use App\Observers\CADECO\ComprobanteFondoObserver;
use App\Observers\CADECO\ComprobanteFondoPartidaObserver;
use App\Observers\CADECO\ConceptoObserver;
use App\Observers\CADECO\Configuracion\NodotipoObserver;
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
use App\Observers\CADECO\Contabilidad\DatosContablesObserver;
use App\Observers\CADECO\Contabilidad\PolizaMovimientoObserver;
use App\Observers\CADECO\Contabilidad\PolizaObserver;
use App\Observers\CADECO\Contabilidad\TipoCuentaContableObserver;
use App\Observers\CADECO\ContrareciboObserver;
use App\Observers\CADECO\ContratoObserver;
use App\Observers\CADECO\ContratoProyectadoObserver;
use App\Observers\CADECO\Contratos\AreaSubcontratanteObserver;
use App\Observers\CADECO\ControlPresupuesto\ExtraordinarioObserver;
use App\Observers\CADECO\ControlPresupuesto\SolicitudCambioObserver;
use App\Observers\CADECO\ControlPresupuesto\SolicitudCambioPartidasObserver;
use App\Observers\CADECO\ControlPresupuesto\SolicitudCambioRechazadaObserver;
use App\Observers\CADECO\ControlPresupuesto\VariacionVolumenObserver;
use App\Observers\CADECO\CotizacionCompraPartidaObserver;
use App\Observers\CADECO\CreditoObserver;
use App\Observers\CADECO\CuentaObserver;
use App\Observers\CADECO\DebitoObserver;
use App\Observers\CADECO\DepositoClienteObserver;
use App\Observers\CADECO\DescuentoFondoGarantiaObserver;
use App\Observers\CADECO\DestajistaObserver;
use App\Observers\CADECO\DestinoObserver;
use App\Observers\CADECO\EmpresaFondoFijoObserver;
use App\Observers\CADECO\EmpresaObserver;
use App\Observers\CADECO\EntradaMaterialObserver;
use App\Observers\CADECO\EntradaMaterialPartidaObserver;
use App\Observers\CADECO\EntregaObserver;
use App\Observers\CADECO\Estimaciones\EstimacionEliminadaObserver;
use App\Observers\CADECO\EstimacionObserver;
use App\Observers\CADECO\EstimacionPartidaObserver;
use App\Observers\CADECO\FacturaObserver;
use App\Observers\CADECO\FamiliaObserver;
use App\Observers\CADECO\Finanzas\ConfiguracionEstimacionObserver;
use App\Observers\CADECO\Finanzas\CuentaBancariaEmpresaObserver;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaLogObserver;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaObserver;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Finanzas\FacturaEliminada;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaPartidaObserver;
use App\Observers\CADECO\Finanzas\LayoutPagoObserver;
use App\Observers\CADECO\Finanzas\LayoutPagoPartidaObserver;
use App\Observers\CADECO\Finanzas\PagoEliminadoLogObserver;
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
use App\Observers\CADECO\ItemSubcontratoObserver;
use App\Observers\CADECO\LiberacionFondoGarantiaObserver;
use App\Observers\CADECO\MaterialObserver;
use App\Observers\CADECO\MovimientoObserver;
use App\Observers\CADECO\NotaCreditoObserver;
use App\Observers\CADECO\NuevoLoteObserver;
use App\Observers\CADECO\NuevoLotePartidaObserver;
use App\Observers\CADECO\OrdenCompraObserver;
use App\Observers\CADECO\OrdenCompraPartidaObserver;
use App\Observers\CADECO\OrdenPagoObserver;
use App\Observers\CADECO\PagoACuentaObserver;
use App\Observers\CADECO\PagoACuentaPorAplicarObserver;
use App\Observers\CADECO\PagoAnticipoDestajoObserver;
use App\Observers\CADECO\PagoListaRayaObserver;
use App\Observers\CADECO\PagoObserver;
use App\Observers\CADECO\PagoVarioObserver;
use App\Observers\CADECO\PresupuestoObra\ResponsableObserver;
use App\Observers\CADECO\ProveedorContratistaObserver;
use App\Observers\CADECO\RequisicionObserver;
use App\Observers\CADECO\SalidaAlmacenObserver;
use App\Observers\CADECO\SalidaAlmacenPartidaObserver;
use App\Observers\CADECO\Seguridad\AuditoriaPermisoRolObserver;
use App\Observers\CADECO\Seguridad\AuditoriaRolUserObserver;
use App\Observers\CADECO\Seguridad\RolObserver;
use App\Observers\CADECO\SolicitudAnticipoDestajoObserver;
use App\Observers\CADECO\SolicitudAutorizacionAvanceObserver;
use App\Observers\CADECO\SolicitudAutorizacionAvancePartidaObserver;
use App\Observers\CADECO\SolicitudCompraObserver;
use App\Observers\CADECO\SolicitudCompraPartidaObserver;
use App\Observers\CADECO\SolicitudCambioSubcontratoObserver;
use App\Observers\CADECO\SolicitudPagoAnticipadoObserver;
use App\Observers\CADECO\SolicitudReposicionFFObserver;
use App\Observers\CADECO\SubcontratoObserver;
use App\Observers\CADECO\Subcontratos\AsignacionContratistaEliminadaObserver;
use App\Observers\CADECO\Subcontratos\AsignacionContratistaObserver;
use App\Observers\CADECO\Subcontratos\AsignacionContratistaPartidaObserver;
use App\Observers\CADECO\Subcontratos\AsignacionSubcontratoEliminadoObserver;
use App\Observers\CADECO\Subcontratos\AsignacionSubcontratoObserver;
use App\Observers\CADECO\Subcontratos\SubcontratosObserver;
use App\Observers\CADECO\Subcontratos\ClasificacionSubcontratoObserver;
use App\Observers\CADECO\SubcontratosEstimaciones\DescuentoObserver;
use App\Observers\CADECO\SubcontratosEstimaciones\FolioPorSubcontratoObserver;
use App\Observers\CADECO\SubcontratosEstimaciones\LiberacionObserver;
use App\Observers\CADECO\SubcontratosEstimaciones\RetencionObserver;
use App\Observers\CADECO\SubcontratosFG\FondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosFG\MovimientoFondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosFG\MovimientoRetencionFondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosFG\MovimientoSolicitudMovimientoFondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosFG\RetencionFondoGarantiaObserver;
use App\Observers\CADECO\SubcontratosCM\SolicitudCambioSubcontratoComplementoObserver;
use App\Observers\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantiaObserver;
use App\Observers\CADECO\SucursalObserver;
use App\Observers\CADECO\Tesoreria\MovimientoBancarioObserver;
use App\Observers\CADECO\Tesoreria\TraspasoCuentasObserver;
use App\Observers\CADECO\TransaccionObserver;
use App\Observers\CADECO\VentaObserver;
use App\Observers\CADECO\Ventas\VentaCancelacionObserver;
use App\Observers\CADECO\VentaPartidaObserver;
use App\Observers\CTPQ\DocumentoObserver;
use App\Observers\MODULOSSAO\Proyectos\ProyectoObserver;
use App\Observers\REPSEG\FinDimIngresoPartidaObserver;
use App\Observers\REPSEG\FinDimTipoIngresoObserver;
use App\Observers\REPSEG\FinFacIngresoFacturaConceptoObserver;
use App\Observers\REPSEG\FinFacIngresoFacturaDetalleObserver;
use App\Observers\REPSEG\FinFacIngresoFacturaObserver;
use App\Observers\SEGURIDAD_ERP\Contabilidad\CargaCFDSATObserver;
use App\Observers\SEGURIDAD_ERP\Contabilidad\LogEdicionObserver;
use App\Observers\SEGURIDAD_ERP\AuditoriaRolUsuarioObserver;
use App\Observers\SEGURIDAD_ERP\ConfiguracionObraObserver;
use App\Observers\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionObserver;
use App\Observers\SEGURIDAD_ERP\ControlInterno\IncidenciaObserver;
use App\Observers\SEGURIDAD_ERP\CtgEfosObserver;
use App\Observers\SEGURIDAD_ERP\CtgEfosLogObserver;
use App\Observers\SEGURIDAD_ERP\FacturaRepositorioObserver;
use App\Observers\SEGURIDAD_ERP\Fiscal\CFDNoDeducidoObserver;
use App\Observers\SEGURIDAD_ERP\Fiscal\FechaInhabilSatObserver;
use App\Observers\SEGURIDAD_ERP\Fiscal\NoDeducidoObserver;
use App\Observers\SEGURIDAD_ERP\Fiscal\ProcesamientoListaEfosObserver;
use App\Observers\SEGURIDAD_ERP\Fiscal\AutocorreccionObserver;
use App\Observers\SEGURIDAD_ERP\Fiscal\CFDAutocorreccionObserver;
use App\Observers\SEGURIDAD_ERP\Fiscal\EFOSObserver;
use App\Observers\SEGURIDAD_ERP\Fiscal\ProcesamientoNoLocalizadosObserver;
use App\Observers\SEGURIDAD_ERP\PadronProveedores\ArchivoObserver;
use App\Observers\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaObserver;
use App\Observers\SEGURIDAD_ERP\PadronProveedores\EmpresaExcluidaDocumentacionObserver;
use App\Observers\SEGURIDAD_ERP\PadronProveedores\EmpresaPrestadoraObserver;
use App\Observers\SEGURIDAD_ERP\PadronProveedores\RepresentanteLegalObserver;
use App\Observers\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaObserver;
use App\Observers\SEGURIDAD_ERP\UsuarioAreaCompradoraObserver;
use App\Observers\SEGURIDAD_ERP\UsuarioAreaSolicitanteObserver;
use App\Observers\SEGURIDAD_ERP\UsuarioAreaSubcontratanteObserver;
use App\Observers\CADECO\PagoReposicionFFObserver;
use App\Models\CADECO\PagoReposicionFF;
use App\Models\CADECO\PagoFactura;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\CADECO\SubcontratosEstimaciones\Penalizacion;
use App\Models\CADECO\SubcontratosEstimaciones\PenalizacionLiberacion;
use App\Models\CADECO\Unidad;
use App\Models\CADECO\UnidadComplemento;
use App\Observers\CADECO\Compras\CotizacionComplementoObserver;
use App\Observers\CADECO\CotizacionCompraObserver;
use App\Observers\CADECO\Finanzas\FacturaEliminadaObserver;
use App\Observers\CADECO\PagoFacturaObserver;
use App\Observers\CADECO\PresupuestoContratistaObserver;
use App\Observers\CADECO\SubcontratosEstimaciones\PenalizacionLiberacionObserver;
use App\Observers\CADECO\SubcontratosEstimaciones\PenalizacionObserver;
use App\Observers\CADECO\UnidadComplementoObserver;
use App\Observers\CADECO\UnidadObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Observers\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIObserver;

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
         * ACARREOS
         */
        CamionImagen::observe(CamionImagenObserver::class);
        Camion::observe(CamionObserver::class);
        \App\Models\ACARREOS\Empresa::observe(\App\Observers\ACARREOS\EmpresaObserver::class);
        Impresora::observe(ImpresoraObserver::class);
        InicioCamion::observe(InicioCamionObserver::class);
        Marca::observe(MarcaObserver::class);
        Operador::observe(OperadorObserver::class);
        Origen::observe(OrigenObserver::class);
        MaterialAcarreo::observe(MaterialAcarreoObserver::class);
        Tag::observe(TagObserver::class);
        Telefono::observe(TelefonoObserver::class);
        Tiro::observe(TiroObserver::class);
        TiroConcepto::observe(TiroConceptoObserver::class);
        ViajeNeto::observe(ViajeNetoObserver::class);
        VolumenDetalle::observe(VolumenDetalleObserver::class);
            /**
             * SCA CONFIGURACION
             */
            \App\Models\ACARREOS\SCA_CONFIGURACION\Tag::observe(\App\Observers\ACARREOS\SCA_CONFIGURACION\TagObserver::class);

        /**
         * CTPQ
         * */
        \App\Models\CTPQ\Cuenta::observe(\App\Observers\CTPQ\CuentaObserver::class);
        Documento::observe(DocumentoObserver::class);
        \App\Models\CTPQ\Poliza::observe(\App\Observers\CTPQ\PolizaObserver::class);
        \App\Models\CTPQ\PolizaMovimiento::observe(\App\Observers\CTPQ\PolizaMovimientoObserver::class);

        /**
         * MODULOSSAO
         */
        Proyecto::observe(ProyectoObserver::class);

        /**
        *RECEPCIÓN CFDI
         */
        SolicitudRecepcionCFDI::observe(SolicitudRecepcionCFDIObserver::class);


        /**
         * CADECO
         */
            /**
             * Archivos
             * */
            \App\Models\CADECO\Documentacion\Archivo::observe(\App\Observers\CADECO\Documentacion\ArchivoObserver::class);

            /**
             * Almacenes
             */
            AjusteEliminado::observe(AjusteEliminadoObserver::class);
            EntregaContratista::observe(EntregaContratistaObserver::class);

            /**
             * Catalogos
             */
            UnificacionProveedores::observe(UnificacionProveedoresObserver::class);

            /**
             * Compras
             */
            AsignacionProveedor::observe(AsignacionProveedorObserver::class);
            AsignacionProveedorPartida::observe(AsignacionProveedorPartidaObserver::class);
            CotizacionComplemento::observe(CotizacionComplementoObserver::class);
            EntradaEliminada::observe(EntradaEliminadaObserver::class);
            RequisicionComplemento::observe(RequisicionComplementoObserver::class);
            RequisicionEliminada::observe(RequisicionEliminadaObserver::class);
            RequisicionPartidaComplemento::observe(RequisicionPartidaComplementoObserver::class);
            OrdenCompraComplemento::observe(OrdenCompraComplementoObserver::class);
            SalidaEliminada::observe(SalidaEliminadaObserver::class);
            SolicitudComplemento::observe(SolicitudComplementoObserver::class);

            /**
             * Configuracion
             */
            NodoTipo::observe(NodoTipoObserver::class);


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
            DatosContables::observe(DatosContablesObserver::class);
            PolizaMovimiento::observe(PolizaMovimientoObserver::class);
            Poliza::observe(PolizaObserver::class);
            TipoCuentaContable::observe(TipoCuentaContableObserver::class);

            /**
             * Contratos
             */
            AreaSubcontratante::observe(AreaSubcontratanteObserver::class);

            /**
             * Control Presupuesto
             */
            SolicitudCambio::observe(SolicitudCambioObserver::class);
            SolicitudCambioPartidas::observe(SolicitudCambioPartidasObserver::class);
            SolicitudCambioRechazada::observe(SolicitudCambioRechazadaObserver::class);
            VariacionVolumen::observe(VariacionVolumenObserver::class);
            Extraordinario::observe(ExtraordinarioObserver::class);


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
            FacturaEliminada::observe(FacturaEliminadaObserver::class);
            LayoutPago::observe(LayoutPagoObserver::class);
            LayoutPagoPartida::observe(LayoutPagoPartidaObserver::class);
            PagoEliminadoLog::observe(PagoEliminadoLogObserver::class);

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
             *Presupuesto
             */

            Concepto::observe(ConceptoObserver::class);
            Responsable::observe(ResponsableObserver::class);

            /**
             * Subcontratos
             */
            AsignacionContratista::observe(AsignacionContratistaObserver::class);
            AsignacionContratistaEliminada::observe(AsignacionContratistaEliminadaObserver::class);
            AsignacionContratistaPartida::observe(AsignacionContratistaPartidaObserver::class);
            AsignacionSubcontratoEliminado::observe(AsignacionSubcontratoEliminadoObserver::class);
            AsignacionSubcontrato::observe(AsignacionSubcontratoObserver::class);
            Subcontratos::observe(SubcontratosObserver::class);
            ClasificacionSubcontrato::observe(ClasificacionSubcontratoObserver::class);


            /**
             * SubcontratosEstimaciones
             */
            Descuento::observe(DescuentoObserver::class);
            FolioPorSubcontrato::observe(FolioPorSubcontratoObserver::class);
            Liberacion::observe(LiberacionObserver::class);
            Penalizacion::observe(PenalizacionObserver::class);
            PenalizacionLiberacion::observe(PenalizacionLiberacionObserver::class);
            Retencion::observe(RetencionObserver::class);

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
            Almacen::observe(AlmacenObserver::class);
            Anticipo::observe(AnticipoObserver::class);
            AplicacionManual::observe(AplicacionManualObserver::class);
            AvanceObra::observe(AvanceObraObserver::class);
            ItemAvanceObra::observe(AvanceObraPartidaObserver::class);
            AvanceSubcontrato::observe(AvanceSubcontratoObserver::class);
            Banco::observe(BancoObserver::class);
            Cliente::observe(ClienteObserver::class);
            ComprobanteFondo::observe(ComprobanteFondoObserver::class);
            ItemComprobanteFondo::observe(ComprobanteFondoPartidaObserver::class);
            ContraRecibo::observe(ContrareciboObserver::class);
            Contrato::observe(ContratoObserver::class);
            ContratoProyectado::observe(ContratoProyectadoObserver::class);
            CotizacionCompra::observe(CotizacionCompraObserver::class);
            CotizacionCompraPartida::observe(CotizacionCompraPartidaObserver::class);
            Credito::observe(CreditoObserver::class);
            Cuenta::observe(CuentaObserver::class);
            Debito::observe(DebitoObserver::class);
            DepositoCliente::observe(DepositoClienteObserver::class);
            DescuentoFondoGarantia::observe(DescuentoFondoGarantiaObserver::class);
            Destajista::observe(DestajistaObserver::class);
            Destino::observe(DestinoObserver::class);
            Empresa::observe(EmpresaObserver::class);
            EmpresaFondoFijo::observe(EmpresaFondoFijoObserver::class);
            Entrega::observe(EntregaObserver::class);
            EntradaMaterial::observe(EntradaMaterialObserver::class);
            EntradaMaterialPartida::observe(EntradaMaterialPartidaObserver::class);
            Estimacion::observe(EstimacionObserver::class);
            ItemEstimacion::observe(EstimacionPartidaObserver::class);
            ItemSolicitudCompra::observe(SolicitudCompraPartidaObserver::class);
            Factura::observe(FacturaObserver::class);
            NotaCredito::observe(NotaCreditoObserver::class);
            Familia::observe(FamiliaObserver::class);
            Fondo::observe(FondoObserver::class);
            Inventario::observe(InventarioObserver::class);
            ItemSubcontrato::observe(ItemSubcontratoObserver::class);
            LiberacionFondoGarantia::observe(LiberacionFondoGarantiaObserver::class);
            Material::observe(MaterialObserver::class);
            Movimiento::observe(MovimientoObserver::class);
            NuevoLote::observe(NuevoLoteObserver::class);
            NuevoLotePartida::observe(NuevoLotePartidaObserver::class);
            OrdenCompra::observe(OrdenCompraObserver::class);
            OrdenPago::observe(OrdenPagoObserver::class);
            OrdenCompraPartida::observe(OrdenCompraPartidaObserver::class);
            Pago::observe(PagoObserver::class);
            PagoACuenta::observe(PagoACuentaObserver::class);
            PagoACuentaPorAplicar::observe(PagoACuentaPorAplicarObserver::class);
            PagoAnticipoDestajo::observe(PagoAnticipoDestajoObserver::class);
            PagoFactura::observe(PagoFacturaObserver::class);
            PagoListaRaya::observe(PagoListaRayaObserver::class);
            PagoReposicionFF::observe(PagoReposicionFFObserver::class);
            PagoVario::observe(PagoVarioObserver::class);
            PresupuestoContratista::observe(PresupuestoContratistaObserver::class);
            ProveedorContratista::observe(ProveedorContratistaObserver::class);
            Requisicion::observe(RequisicionObserver::class);
            RequisicionPartida::observe(RequisicionPartidaObserver::class);
            SalidaAlmacen::observe(SalidaAlmacenObserver::class);
            SalidaAlmacenPartida::observe(SalidaAlmacenPartidaObserver::class);
            SolicitudAutorizacionAvance::observe(SolicitudAutorizacionAvanceObserver::class);
            ItemSolicitudAutorizacionAvance::observe(SolicitudAutorizacionAvancePartidaObserver::class);
            SolicitudCambioSubcontrato::observe(SolicitudCambioSubcontratoObserver::class);
            SolicitudCambioSubcontratoComplemento::observe(SolicitudCambioSubcontratoComplementoObserver::class);
            SolicitudCompra::observe(SolicitudCompraObserver::class);
            SolicitudAnticipoDestajo::observe(SolicitudAnticipoDestajoObserver::class);
            SolicitudPagoAnticipado::observe(SolicitudPagoAnticipadoObserver::class);
            SolicitudReposicionFF::observe(SolicitudReposicionFFObserver::class);
            Subcontrato::observe(SubcontratoObserver::class);
            Sucursal::observe(SucursalObserver::class);
            Transaccion::observe(TransaccionObserver::class);
            Unidad::observe(UnidadObserver::class);
            UnidadComplemento::observe(UnidadComplementoObserver::class);
            Venta::observe(VentaObserver::class);
            VentaCancelacion::observe(VentaCancelacionObserver::class);
            VentaPartida::observe(VentaPartidaObserver::class);

        /**
         * REPSEG
         */
         FinDimIngresoPartida::observe(FinDimIngresoPartidaObserver::class);
         FinDimTipoIngreso::observe(FinDimTipoIngresoObserver::class);
         FinFacIngresoFacturaDetalle::observe(FinFacIngresoFacturaDetalleObserver::class);
         FinFacIngresoFactura::observe(FinFacIngresoFacturaObserver::class);


        /**
         * SEGURIDAD_ERP
         */
            /**
             * Contabilidad
             */
            LogEdicion::observe(LogEdicionObserver::class);
            CargaCFDSAT::observe(CargaCFDSATObserver::class);
            SolicitudEdicion::observe(SolicitudEdicionObserver::class);

            /**
             * ControlInterno
             */
            Incidencia::observe(IncidenciaObserver::class);

            /**
             * Finanzas
             */
            CtgEfosLog::observe(CtgEfosLogObserver::class);
            CtgEfos::observe(CtgEfosObserver::class);
            FacturaRepositorio::observe(FacturaRepositorioObserver::class);

            /**
             * Fiscal
             */
            Autocorreccion::observe(AutocorreccionObserver::class);
            CFDAutocorreccion::observe(CFDAutocorreccionObserver::class);
            CFDNoDeducido::observe(CFDNoDeducidoObserver::class);
            EFOS::observe(EFOSObserver::class);
            FechaInhabilSat::observe(FechaInhabilSatObserver::class);
            NoDeducido::observe(NoDeducidoObserver::class);
            ProcesamientoListaEfos::observe(ProcesamientoListaEfosObserver::class);
            ProcesamientoListaNoLocalizados::observe(ProcesamientoNoLocalizadosObserver::class);

            /**
             *  PadronProveedores
             */
            Archivo::observe(ArchivoObserver::class);
            EmpresaExcluidaDocumentacion::observe(EmpresaExcluidaDocumentacionObserver::class);
            \App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa::observe(\App\Observers\SEGURIDAD_ERP\PadronProveedores\EmpresaObserver::class);
            EmpresaPrestadora::observe(EmpresaPrestadoraObserver::class);
            RepresentanteLegal::observe(RepresentanteLegalObserver::class);
            EmpresaBoletinada::observe(EmpresaBoletinadaObserver::class);

            /**
             * PolizasCtpqIncidentes
             */
            Diferencia::observe(DiferenciaObserver::class);

        \App\Models\SEGURIDAD_ERP\AuditoriaPermisoRol::observe(\App\Observers\SEGURIDAD_ERP\AuditoriaPermisoRolObserver::class);
        AuditoriaPermisoRol::observe(AuditoriaPermisoRolObserver::class);
        AuditoriaRolUsuario::observe(AuditoriaRolUsuarioObserver::class);
        AuditoriaRolUser::observe(AuditoriaRolUserObserver::class);
        ConfiguracionObra::observe(ConfiguracionObraObserver::class);
        \App\Models\SEGURIDAD_ERP\Rol::observe(\App\Observers\SEGURIDAD_ERP\RolObserver::class);
        Rol::observe(RolObserver::class);
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
