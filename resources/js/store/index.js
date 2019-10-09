import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';

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

//CADECO

import almacen from './modules/cadeco/almacen';
import banco from './modules/cadeco/banco';
import concepto from './modules/cadeco/concepto';
import costo from './modules/cadeco/costo';
import cuenta from './modules/cadeco/cuenta';
import empresa from './modules/cadeco/empresa';
import familia from './modules/cadeco/familia';
import fondo from './modules/cadeco/fondo';
import material from './modules/cadeco/material';
import moneda from './modules/cadeco/moneda';
import obras from './modules/cadeco/obras';
import sucursal from './modules/cadeco/sucursal';


//COMPRAS
import solicitudCompra from './modules/compras/solicitud-compra';
import ordenCompra from './modules/compras/orden-compra';
import materialFamilia from './modules/compras/material-familia';

//CONFIGURACION
import areaSubcontratante from './modules/configuracion/area-subcontratante';

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

//CONTRATOS
import estimacion from './modules/contratos/estimacion';
import fondoGarantia from './modules/contratos/fondo-garantia';
import solicitudMovimientoFG from './modules/contratos/solicitud-movimiento-fg';
import subcontrato from './modules/contratos/subcontrato';
import contratoProyectado from './modules/contratos/contrato-proyectado';

//FINANZAS
import cargaMasivaPago from './modules/finanzas/carga-masiva-pago';
import ctgTipoFondo from './modules/finanzas/ctg-tipo-fondo';
import cuentaBancariaEmpresa from './modules/finanzas/cuenta-bancaria-empresa';
import datosEstimaciones from './modules/finanzas/estimacion';
import distribuirRecursoRemesa from './modules/finanzas/distribuir-recurso-remesa';
import gestionPago from './modules/finanzas/gestion-pago';
import pago from './modules/finanzas/pago';
import pagoAnticipado from './modules/finanzas/solicitud-pago-anticipado';
import remesa from './modules/finanzas/remesa';
import solicitudAlta from './modules/finanzas/solicitud-alta-cuenta-bancaria';
import solicitudBaja from './modules/finanzas/solicitud-baja-cuenta-bancaria';

//TESORERIA
import movimientoBancario from './modules/tesoreria/movimiento-bancario';
import tipoMovimiento from './modules/tesoreria/tipo-movimiento';
import traspaso from './modules/tesoreria/traspaso-entre-cuentas';

//SEGURIDAD
import configuracionObra from './modules/seguridad/configuracion-obra';
import configuracionRemesa from './modules/seguridad/finanzas/configuracion-remesa';
import ctgbanco from './modules/seguridad/finanzas/ctg-banco';
import ctgplaza from './modules/seguridad/finanzas/ctg-plaza';
import permiso from './modules/seguridad/permiso';
import rol from './modules/seguridad/rol';
import rolPersonalizado from './modules/seguridad/rol-personalizado';
import sistema from './modules/seguridad/sistema';
import sistemaObra from './modules/seguridad/sistema-obra';
import tipoProyecto from './modules/seguridad/tipo-proyecto';

//IGH
import usuario from "./modules/igh/usuario";
import aplicacion from "./modules/igh/aplicacion";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
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
        'cadeco/concepto': concepto,
        'cadeco/costo': costo,
        'cadeco/cuenta': cuenta,
        'cadeco/empresa': empresa,
        'cadeco/familia': familia,
        'cadeco/fondo': fondo,
        'cadeco/material': material,
        'cadeco/moneda': moneda,
        'cadeco/obras': obras,
        'cadeco/sucursal': sucursal,

        'compras/solicitud-compra' : solicitudCompra,
        'compras/orden-compra' : ordenCompra,
        'compras/material-familia' : materialFamilia,

        'configuracion/area-subcontratante' : areaSubcontratante,

        'contratos/estimacion' : estimacion,
        'contratos/fondo-garantia': fondoGarantia,
        'contratos/solicitud-movimiento-fg': solicitudMovimientoFG,
        'contratos/subcontrato': subcontrato,
        'contratos/contrato-proyectado': contratoProyectado,

        'finanzas/carga-masiva-pago' : cargaMasivaPago,
        'finanzas/ctg-tipo-fondo': ctgTipoFondo,
        'finanzas/cuenta-bancaria-empresa': cuentaBancariaEmpresa,
        'finanzas/estimacion' : datosEstimaciones,
        'finanzas/distribuir-recurso-remesa': distribuirRecursoRemesa,
        'finanzas/gestion-pago': gestionPago,
        'finanzas/pago': pago,
        'finanzas/solicitud-pago-anticipado': pagoAnticipado,
        'finanzas/remesa': remesa,
        'finanzas/solicitud-alta-cuenta-bancaria' : solicitudAlta,
        'finanzas/solicitud-baja-cuenta-bancaria' : solicitudBaja,


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

        'igh/usuario': usuario,
        'igh/aplicacion': aplicacion,

        'tesoreria/movimiento-bancario': movimientoBancario,
        'tesoreria/tipo-movimiento': tipoMovimiento,
        'tesoreria/traspaso-entre-cuentas': traspaso,

        'seguridad/finanzas/ctg-banco': ctgbanco,
        'seguridad/finanzas/ctg-plaza': ctgplaza,
        'seguridad/configuracion-obra': configuracionObra,
        'seguridad/finanzas/configuracion-remesa': configuracionRemesa,
        'seguridad/permiso': permiso,
        'seguridad/rol': rol,
        'seguridad/rol-personalizado': rolPersonalizado,
        'seguridad/sistema': sistema,
        'seguridad/sistema-obra': sistemaObra,
        'seguridad/tipo-proyecto': tipoProyecto,
    },
    strict: process.env.NODE_ENV !== 'production'
})
