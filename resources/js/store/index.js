import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';

//CADECO
import obras from './modules/cadeco/obras';
import almacen from './modules/cadeco/almacen';
import concepto from './modules/cadeco/concepto';
import costo from './modules/cadeco/costo';
import cuenta from './modules/cadeco/cuenta';
import empresa from './modules/cadeco/empresa';
import fondo from './modules/cadeco/fondo';
import material from './modules/cadeco/material';
import moneda from './modules/cadeco/moneda';

//COMPRAS
import ordenCompra from './modules/compras/orden-compra';

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

//FINANZAS
import cuenta_bancaria_proveedor from './modules/finanzas/cuenta-bancaria-proveedor';
import distribuir_recurso_remesa from './modules/finanzas/distribuir-recurso-remesa';
import pago_anticipado from './modules/finanzas/solicitud-pago-anticipado';
import remesa from './modules/finanzas/remesa';

//TESORERIA
import movimientoBancario from './modules/tesoreria/movimiento-bancario';
import tipoMovimiento from './modules/tesoreria/tipo-movimiento';
import traspaso from './modules/tesoreria/traspaso-entre-cuentas';

//SEGURIDAD
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
        'cadeco/obras': obras,
        'cadeco/almacen': almacen,
        'cadeco/concepto': concepto,
        'cadeco/costo': costo,
        'cadeco/cuenta': cuenta,
        'cadeco/empresa': empresa,
        'cadeco/fondo': fondo,
        'cadeco/material': material,
        'cadeco/moneda': moneda,

        'compras/orden-compra' : ordenCompra,

        'configuracion/area-subcontratante' : areaSubcontratante,

        'contratos/estimacion' : estimacion,
        'contratos/fondo-garantia': fondoGarantia,
        'contratos/solicitud-movimiento-fg': solicitudMovimientoFG,
        'contratos/subcontrato': subcontrato,

        'finanzas/cuenta-bancaria-proveedor': cuenta_bancaria_proveedor,
        'finanzas/distribuir-recurso-remesa': distribuir_recurso_remesa,
        'finanzas/solicitud-pago-anticipado': pago_anticipado,
        'finanzas/remesa': remesa,

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

        'seguridad/permiso': permiso,
        'seguridad/rol': rol,
        'seguridad/rol-personalizado': rolPersonalizado,
        'seguridad/sistema': sistema,
        'seguridad/sistema-obra': sistemaObra,
        'seguridad/tipo-proyecto': tipoProyecto,
    },
    strict: process.env.NODE_ENV !== 'production'
})