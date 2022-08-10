import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';

//ACARREOS
import camion from "./modules/acarreos/camion";
import checador from "./modules/acarreos/checador";
import empresaAcarreo from "./modules/acarreos/empresa";
import impresora from "./modules/acarreos/impresora";
import marcaAcarreo from "./modules/acarreos/marca";
import materialAcarreo from "./modules/acarreos/material";
import operador from "./modules/acarreos/operador";
import origen from './modules/acarreos/origen';
import sindicato from "./modules/acarreos/sindicato";
import telefono from './modules/acarreos/telefono';
import tipoOrigen from './modules/acarreos/tipo-origen';
import tiro from './modules/acarreos/tiro';

//ACTIVO FIJO
import resguardo from "./modules/activoFijo/resguardo"

//ALMACENES
import ajusteInventario from './modules/almacenes/ajuste-inventario';
import ajusteNegativo from './modules/almacenes/ajuste-negativo';
import ajustePositivo from './modules/almacenes/ajuste-positivo';
import conteo from './modules/almacenes/conteo';
import entradaAlmacen from './modules/almacenes/entrada-almacen';
import inventarioFisico from './modules/almacenes/inventario-fisico';
import marbete from './modules/almacenes/marbete';
import nuevoLote from  './modules/almacenes/nuevo-lote';
import tipoConteo from './modules/almacenes/ctg-tipo-conteo';
import salidaAlmacen from './modules/almacenes/salida-almacen';

//ARCHIVOS

import archivo from './modules/archivos/archivo'

//CADECO

import almacen from './modules/cadeco/almacen';
import banco from './modules/cadeco/banco';
import cliente from './modules/cadeco/cliente';
import concepto from './modules/cadeco/concepto';
import costo from './modules/cadeco/costo';
import cuenta from './modules/cadeco/cuenta';
import destajista from "./modules/cadeco/destajista";
import empresa from './modules/cadeco/empresa';
import familia from './modules/cadeco/familia';
import fondo from './modules/cadeco/fondo';
import inventario from './modules/cadeco/inventario';
import material from './modules/cadeco/material';
import moneda from './modules/cadeco/moneda';
import obras from './modules/cadeco/obras';
import proveedorContratista from './modules/cadeco/proveedor-contratista';
import proveedorContratistaSucursal from './modules/cadeco/proveedor-contratista-sucursal';
import sucursal from './modules/cadeco/sucursal';
import suministrado from './modules/cadeco/suministrado';
import unidad from './modules/cadeco/unidad'

//CATALOGOS
import unificacionProveedores from './modules/catalogos/unificacion-proveedores';
import proyecto from './modules/catalogos/proyecto';

//COMPRAS
import asignacion from "./modules/compras/asignacion";
import invitacionCotizarCompra from "./modules/compras/invitacion-cotizar";
import cotizacion from './modules/compras/cotizacion';
import itemContratista from './modules/compras/item-contratista';
import ordenCompra from './modules/compras/orden-compra';
import requisicion from './modules/compras/requisicion';
import solicitudCompra from './modules/compras/solicitud-compra';
import formaPagoCredito from './modules/compras/forma-pago-credito';

//CONFIGURACION
import areaCompradora from './modules/configuracion/area-compradora';
import areaSolicitante from './modules/configuracion/area-solicitante';
import areaSubcontratante from './modules/configuracion/area-subcontratante';

// CONFIGURACION CADECO
import nodoTipo from './modules/configuracion/nodo-tipo';
import nodoProyecto from './modules/configuracion/nodo-proyecto';

//CONTABILIDAD
import cierrePeriodo from './modules/contabilidad/cierre-periodo';
import cuentaAlmacen from './modules/contabilidad/cuenta-almacen';
import cuentaBanco from './modules/contabilidad/cuenta-banco';
import cuentaConcepto from './modules/contabilidad/cuenta-concepto';
import cuentaCosto from './modules/contabilidad/cuenta-costo';
import cuentaEmpresa from './modules/contabilidad/cuenta-empresa';
import cuentaFondo from './modules/contabilidad/cuenta-fondo';
import cuentaGeneral from './modules/contabilidad/cuenta-general';
import cuentaMaterial from './modules/contabilidad/cuenta-material';
import datosContables from './modules/contabilidad/datos-contables';
import estatusPrepoliza from './modules/contabilidad/estatus-prepoliza';
import naturalezaPoliza from './modules/contabilidad/naturaleza-poliza';
import poliza from './modules/contabilidad/poliza';
import tipoCuentaContable from './modules/contabilidad/tipo-cuenta-contable';
import tipoCuentaEmpresa from './modules/contabilidad/tipo-cuenta-empresa';
import tipoCuentaMaterial from './modules/contabilidad/tipo-cuenta-material';
import tipoPolizaContpaq from './modules/contabilidad/tipo-poliza-contpaq';
import transaccionInterfaz from './modules/contabilidad/transaccion-interfaz';
import polizaCFDIProyecto from './modules/contabilidad/poliza-cfdi';
import CFDIPolizaProyecto from './modules/contabilidad/cfdi-poliza';

//CONTABILIDAD GRAL
import cuentaCTPQ from './modules/contabilidadGeneral/cuenta';
import empresaContabilidad from './modules/contabilidadGeneral/empresa';
import empresaContpaq from './modules/contabilidadGeneral/empresa-contpaq';
import empresaSAT from './modules/contabilidadGeneral/empresa-sat';
import incidentePoliza from './modules/contabilidadGeneral/incidente-poliza';
import polizaGeneral from './modules/contabilidadGeneral/poliza';
import proveedoresSat from './modules/contabilidadGeneral/proveedor-sat';
import solicitudEdicionPoliza from './modules/contabilidadGeneral/solicitud-edicion-poliza';
import tipoPoliza from './modules/contabilidadGeneral/tipo-poliza';
import polizaCFDI from './modules/contabilidadGeneral/poliza-cfdi';
import cuentaSaldoNegativo from './modules/contabilidadGeneral/cuenta-saldo-negativo';

//CONTRATOS
import asigacionContratista from './modules/contratos/asignacion-contratista';
import avanceSubcontrato from './modules/contratos/avance-subcontrato';
import contratoConcepto from './modules/contratos/contrato-concepto';
import contratoProyectado from './modules/contratos/contrato-proyectado';
import estimacion from './modules/contratos/estimacion';
import fondoGarantia from './modules/contratos/fondo-garantia';
import presupuesto from './modules/contratos/presupuesto';
import solicitudMovimientoFG from './modules/contratos/solicitud-movimiento-fg';
import subcontrato from './modules/contratos/subcontrato';
import tipoContrato from './modules/contratos/tipo-contrato';
import solicitudCambio from './modules/contratos/solicitud-cambio';
import invitacionCotizarContrato from "./modules/contratos/invitacion-cotizar";

//CONTROL DE OBRA
import avanceObra from './modules/controlObra/avance-obra';

//CONTROL PRESUPUESTO
import solicitudCambioPresupuesto from './modules/controlPresupuesto/solicitud-cambio';
import variacionVolumen from './modules/controlPresupuesto/variacion-volumen';
import tipoOrden from './modules/controlPresupuesto/tipo-orden';
import tarjeta from './modules/controlPresupuesto/tarjeta';
import conceptoTarjeta from './modules/controlPresupuesto/concepto-tarjeta';
import extraordinario from './modules/controlPresupuesto/extraordinario';

//ENTREGA CFDI
import solicitudRecepcionCFDI from './modules/entregaCFDI/solicitud-recepcion-cfdi';
import tipoArchivo from './modules/entregaCFDI/ctg-tipo-archivo';
import archivoCFDI from './modules/entregaCFDI/archivo';
import tipoTransaccion from './modules/entregaCFDI/ctg-tipo-transaccion';

//RECEPCIÓN CFDI
import solicitudRecepcionCFDIProyecto from './modules/recepcionCFDI/solicitud-recepcion-cfdi';

//FINANZAS
import cargaMasivaPago from './modules/finanzas/carga-masiva-pago';
import ctgTipoFondo from './modules/finanzas/ctg-tipo-fondo';
import comprobanteFondo from './modules/finanzas/comprobante-fondo';
import cuentaBancariaEmpresa from './modules/finanzas/cuenta-bancaria-empresa';
import datosEstimaciones from './modules/finanzas/estimacion';
import distribuirRecursoRemesa from './modules/finanzas/distribuir-recurso-remesa';
import factura from './modules/finanzas/factura';
import gestionPago from './modules/finanzas/gestion-pago';
import movimientoBancario from './modules/finanzas/movimiento-bancario';
import pago from './modules/finanzas/pago';
import pagoAnticipado from './modules/finanzas/solicitud-pago-anticipado';
import remesa from './modules/finanzas/remesa';
import solicitudAlta from './modules/finanzas/solicitud-alta-cuenta-bancaria';
import solicitudBaja from './modules/finanzas/solicitud-baja-cuenta-bancaria';
import tipoMovimiento from './modules/finanzas/tipo-movimiento';
import traspaso from './modules/finanzas/traspaso-entre-cuentas';
import rubro from './modules/finanzas/rubro';
import CFDSATObra from './modules/finanzas/cfd-sat';

//FINANZAS GENERAL

import solicitudPagoAplicada from './modules/finanzasGeneral/solicitud-pago-aplicada';
import solicitudPago from './modules/finanzasGeneral/solicitud-pago';

//PADRON PROVEEDORES
import archivoPrestadoraExpediente from './modules/padronProveedores/archivo-prestadora';
import archivoExpediente from './modules/padronProveedores/archivo';
import ctgSeccion from './modules/padronProveedores/ctg-seccion';
import ctgArea from './modules/padronProveedores/ctg-area';
import empresaProveedor from './modules/padronProveedores/empresa';
import especialidad from './modules/padronProveedores/especialidad';
import giro from './modules/padronProveedores/giro';
import invitacion  from "./modules/padronProveedores/invitacion";
import empresaBoletinada from './modules/padronProveedores/empresa-boletinada';

//PORTAL PROVEEDOR
import solicitudAutorizacionAvance from './modules/portalProveedor/solicitud-autorizacion-avance';

//SEGURIDAD
import configuracionObra from './modules/seguridad/configuracion-obra';
import incidencia from './modules/seguridad/control-interno/incidencia'
import configuracionRemesa from './modules/seguridad/finanzas/configuracion-remesa';
import ctgbanco from './modules/seguridad/finanzas/ctg-banco';
import ctgEfos from './modules/seguridad/finanzas/ctg-efos';
import ctgplaza from './modules/seguridad/finanzas/ctg-plaza';
import ctgtipo from './modules/configuracion/ctg-tipo';
import listaEmpresas from './modules/seguridad/contabilidad/lista-empresa';
import permiso from './modules/seguridad/permiso';
import rol from './modules/seguridad/rol';
import rolPersonalizado from './modules/seguridad/rol-personalizado';
import sistema from './modules/seguridad/sistema';
import transaccionEfo from './modules/seguridad/finanzas/transaccion-efo';
import sistemaObra from './modules/seguridad/sistema-obra';
import tipoProyecto from './modules/seguridad/tipo-proyecto';

//SEGURIDAD/FISCAL
import autocorreccion from './modules/fiscal/autocorreccion';
import CFDSAT from './modules/fiscal/cfd-sat';
import ctgNoLocalizado from './modules/fiscal/ctg-no-localizado';
import efos from './modules/fiscal/efos';
import empresaFacturera from './modules/fiscal/empresa-facturera';
import fechaInhabilSat from './modules/fiscal/fecha-inhabil-sat';
import noDeducido from './modules/fiscal/no-deducido';
import noLocalizado from './modules/fiscal/no-localizado';
import tipoFechaSat from './modules/fiscal/tipo-fecha-sat';

//SUBCONTRATOSESTIMACIONES
import descuento from './modules/subcontratosEstimaciones/descuento';
import penalizacion from './modules/subcontratosEstimaciones/penalizacion';
import penalizacionLiberacion from './modules/subcontratosEstimaciones/penalizacion-liberacion';
import retencion from './modules/subcontratosEstimaciones/retencion';
import retencionLiberacion from './modules/subcontratosEstimaciones/retencion-liberacion';
import retencionTipo from './modules/subcontratosEstimaciones/retencion-tipo';

//IGH
import usuario from "./modules/igh/usuario";
import aplicacion from "./modules/igh/aplicacion";

//PRESUPUESTO
import conceptoPresupuesto from './modules/presupuesto/concepto';

//REMESAS
import documentoNoLocalizado from './modules/remesas/documento-no-localizado';
import folio from './modules/remesas/remesa-folio';
import proyectoMSAO from './modules/remesas/proyecto';

//REPORTES
import reporte from './modules/reportes/reporte';

//SCI
import marca from "./modules/sci/marca";
import modelo from "./modules/sci/modelo";

// VENTAS
import venta from "./modules/ventas/venta";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        'acarreos/camion' : camion,
        'acarreos/checador' : checador,
        'acarreos/empresa' : empresaAcarreo,
        'acarreos/impresora' : impresora,
        'acarreos/marca' : marcaAcarreo,
        'acarreos/material' : materialAcarreo,
        'acarreos/operador' : operador,
        'acarreos/origen' : origen,
        'acarreos/sindicato' : sindicato,
        'acarreos/telefono' : telefono,
        'acarreos/tipo-origen' : tipoOrigen,
        'acarreos/tiro' : tiro,

        'activo-fijo/resguardo' : resguardo,

        'almacenes/conteo' : conteo,
        'almacenes/ctg-tipo-conteo' : tipoConteo,
        'almacenes/ajuste-inventario' : ajusteInventario,
        'almacenes/ajuste-negativo' : ajusteNegativo,
        'almacenes/ajuste-positivo' : ajustePositivo,
        'almacenes/inventario-fisico' : inventarioFisico,
        'almacenes/marbete' : marbete,
        'almacenes/nuevo-lote' : nuevoLote,
        'almacenes/entrada-almacen' : entradaAlmacen,
        'almacenes/salida-almacen' : salidaAlmacen,

        'cadeco/almacen': almacen,
        'cadeco/banco': banco,
        'cadeco/cliente': cliente,
        'cadeco/concepto': concepto,
        'cadeco/costo': costo,
        'cadeco/cuenta': cuenta,
        'cadeco/destajista' : destajista,
        'cadeco/empresa': empresa,
        'cadeco/familia': familia,
        'cadeco/fondo': fondo,
        'cadeco/inventario': inventario,
        'cadeco/material': material,
        'cadeco/moneda': moneda,
        'cadeco/obras': obras,
        'cadeco/proveedor-contratista': proveedorContratista,
        'cadeco/proveedor-contratista-sucursal': proveedorContratistaSucursal,
        'cadeco/sucursal': sucursal,
        'cadeco/suministrado': suministrado,
        'cadeco/unidad': unidad,

        'documentacion/archivo' : archivo,

        'catalogos/unificacion-proveedores' : unificacionProveedores,
        'catalogos/proyecto' : proyecto,

        'compras/asignacion' : asignacion,
        'compras/cotizacion' : cotizacion,
        'compras/item-contratista' : itemContratista,
        'compras/orden-compra' : ordenCompra,
        'compras/requisicion' : requisicion,
        'compras/solicitud-compra' : solicitudCompra,
        'compras/forma-pago-credito' : formaPagoCredito,
        'compras/invitacion' : invitacionCotizarCompra,


        'configuracion/area-compradora' : areaCompradora,
        'configuracion/area-solicitante' : areaSolicitante,
        'configuracion/area-subcontratante' : areaSubcontratante,
        'configuracion/ctg-tipo': ctgtipo,

        'configuracion/nodo-tipo' : nodoTipo,
        'configuracion/nodo-proyecto' : nodoProyecto,

        'contratos/asignacion-contratista' : asigacionContratista,
        'contratos/avance-subcontrato' : avanceSubcontrato,
        'contratos/contrato-concepto' : contratoConcepto,
        'contratos/contrato-proyectado': contratoProyectado,
        'contratos/estimacion' : estimacion,
        'contratos/fondo-garantia': fondoGarantia,
        'contratos/presupuesto' : presupuesto,
        'contratos/solicitud-movimiento-fg': solicitudMovimientoFG,
        'contratos/subcontrato': subcontrato,
        'contratos/tipo-contrato': tipoContrato,
        'contratos/solicitud-cambio': solicitudCambio,
        'contratos/invitacion' : invitacionCotizarContrato,

        'controlObra/avance-obra' : avanceObra,

        'control-presupuesto/solicitud-cambio': solicitudCambioPresupuesto,
        'control-presupuesto/variacion-volumen': variacionVolumen,
        'control-presupuesto/tipo-orden': tipoOrden,
        'control-presupuesto/tarjeta': tarjeta,
        'control-presupuesto/concepto-tarjeta': conceptoTarjeta,
        'control-presupuesto/extraordinario': extraordinario,

        'finanzas/carga-masiva-pago' : cargaMasivaPago,
        'finanzas/comprobante-fondo' : comprobanteFondo,
        'finanzas/ctg-tipo-fondo': ctgTipoFondo,
        'finanzas/cuenta-bancaria-empresa': cuentaBancariaEmpresa,
        'finanzas/distribuir-recurso-remesa': distribuirRecursoRemesa,
        'finanzas/estimacion' : datosEstimaciones,
        'finanzas/factura': factura,
        'finanzas/gestion-pago': gestionPago,
        'finanzas/movimiento-bancario': movimientoBancario,
        'finanzas/pago': pago,
        'finanzas/remesa': remesa,
        'finanzas/solicitud-alta-cuenta-bancaria' : solicitudAlta,
        'finanzas/solicitud-baja-cuenta-bancaria' : solicitudBaja,
        'finanzas/solicitud-pago-anticipado': pagoAnticipado,
        'finanzas/tipo-movimiento': tipoMovimiento,
        'finanzas/traspaso-entre-cuentas': traspaso,
        'finanzas/rubro': rubro,
        'finanzas/cfdi-sat': CFDSATObra,

        'finanzas-general/solicitud-pago': solicitudPago,
        'finanzas-general/solicitud-pago-aplicada': solicitudPagoAplicada,

        'contabilidad/cierre-periodo': cierrePeriodo,
        'contabilidad/cuenta-almacen': cuentaAlmacen,
        'contabilidad/cuenta-banco' : cuentaBanco,
        'contabilidad/cuenta-costo' : cuentaCosto,
        'contabilidad/cuenta-concepto' : cuentaConcepto,
        'contabilidad/cuenta-empresa' : cuentaEmpresa,
        'contabilidad/cuenta-fondo' : cuentaFondo,
        'contabilidad/cuenta-general': cuentaGeneral,
        'contabilidad/cuenta-material' : cuentaMaterial,
        'contabilidad/datos-contables' : datosContables,
        'contabilidad/estatus-prepoliza': estatusPrepoliza,
        'contabilidad/naturaleza-poliza' : naturalezaPoliza,
        'contabilidad/poliza': poliza,
        'contabilidad/tipo-cuenta-contable': tipoCuentaContable,
        'contabilidad/tipo-cuenta-empresa': tipoCuentaEmpresa,
        'contabilidad/tipo-cuenta-material': tipoCuentaMaterial,
        'contabilidad/tipo-poliza-contpaq': tipoPolizaContpaq,
        'contabilidad/transaccion-interfaz': transaccionInterfaz,
        'contabilidad/poliza-cfdi' : polizaCFDIProyecto,
        'contabilidad/cfdi-poliza' : CFDIPolizaProyecto,

        'contabilidadGeneral/cuenta' : cuentaCTPQ,
        'contabilidadGeneral/empresa' :empresaContabilidad,
        'contabilidadGeneral/empresa-sat': empresaSAT,
        'contabilidadGeneral/empresa-contpaq': empresaContpaq,
        'contabilidadGeneral/incidente-poliza' : incidentePoliza,
        'contabilidadGeneral/poliza' :polizaGeneral,
        'contabilidadGeneral/proveedor-sat' :proveedoresSat,
        'contabilidadGeneral/solicitud-edicion-poliza':solicitudEdicionPoliza,
        'contabilidadGeneral/tipo-poliza' : tipoPoliza,
        'contabilidadGeneral/poliza-cfdi' : polizaCFDI,
        'contabilidadGeneral/cuenta-saldo-negativo' : cuentaSaldoNegativo,

        'reportes/reporte': reporte,

        'igh/usuario': usuario,
        'igh/aplicacion': aplicacion,


        'sci/marca': marca,
        'sci/modelo': modelo,

        'presupuesto/concepto': conceptoPresupuesto,


        'seguridad/control-interno/incidencia': incidencia,
        'seguridad/finanzas/ctg-banco': ctgbanco,
        'seguridad/finanzas/ctg-efos': ctgEfos,
        'seguridad/finanzas/ctg-plaza': ctgplaza,
        'seguridad/finanzas/configuracion-remesa': configuracionRemesa,
        'seguridad/finanzas/transaccion-efo': transaccionEfo,
        'seguridad/configuracion-obra': configuracionObra,
        'seguridad/permiso': permiso,
        'seguridad/rol': rol,
        'seguridad/rol-personalizado': rolPersonalizado,
        'seguridad/sistema': sistema,
        'seguridad/sistema-obra': sistemaObra,
        'seguridad/tipo-proyecto': tipoProyecto,
        'seguridad/lista-empresas': listaEmpresas,

        'fiscal/empresa-facturera': empresaFacturera,
        'fiscal/autocorreccion' : autocorreccion,
        'fiscal/cfd-sat': CFDSAT,
        'fiscal/ctg-no-localizado': ctgNoLocalizado,
        'fiscal/efos' : efos,
        'fiscal/fecha-inhabil-sat' : fechaInhabilSat,
        'fiscal/no-deducido' : noDeducido,
        'fiscal/no-localizado' : noLocalizado,
        'fiscal/tipo-fecha-sat' : tipoFechaSat,

        'entrega-cfdi/solicitud-recepcion-cfdi' : solicitudRecepcionCFDI,
        'entrega-cfdi/tipo-archivo' : tipoArchivo,
        'entrega-cfdi/archivo' : archivoCFDI,
        'entrega-cfdi/tipo-transaccion' : tipoTransaccion,

        'padronProveedores/archivo' : archivoExpediente,
        'padronProveedores/archivo-prestadora' : archivoPrestadoraExpediente,
        'padronProveedores/ctg-area' : ctgArea,
        'padronProveedores/ctg-seccion' : ctgSeccion,
        'padronProveedores/empresa' : empresaProveedor,
        'padronProveedores/especialidad' : especialidad,
        'padronProveedores/giro' : giro,
        'padronProveedores/invitacion' : invitacion,
        'padronProveedores/empresa-boletinada' : empresaBoletinada,

        'portalProveedor/solicitud-autorizacion-avance' : solicitudAutorizacionAvance,

        'recepcion-cfdi/solicitud-recepcion-cfdi' : solicitudRecepcionCFDIProyecto,

        'remesas/documento-no-localizado' : documentoNoLocalizado,
        'remesas/remesa-folio' : folio,
        'remesas/proyecto' : proyectoMSAO,

        'subcontratosEstimaciones/descuento': descuento,
        'subcontratosEstimaciones/penalizacion': penalizacion,
        'subcontratosEstimaciones/penalizacion-liberacion': penalizacionLiberacion,
        'subcontratosEstimaciones/retencion': retencion,
        'subcontratosEstimaciones/retencion-liberacion': retencionLiberacion,
        'subcontratosEstimaciones/retencion-tipo': retencionTipo,

        'ventas/venta': venta,
    },
    strict: process.env.NODE_ENV !== 'production'
})
