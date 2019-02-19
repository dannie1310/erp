import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';

//CADECO
import obras from './modules/cadeco/obras';
import cuenta from './modules/cadeco/cuenta';

//CONTABILIDAD
import cuentaAlmacen from './modules/contabilidad/cuenta-almacen';
import cuentaEmpresa from './modules/contabilidad/cuenta-empresa';
import cuentaFondo from './modules/contabilidad/cuenta-fondo';
import cuentaGeneral from './modules/contabilidad/cuenta-general'
import estatusPrepoliza from './modules/contabilidad/estatus-prepoliza';
import poliza from './modules/contabilidad/poliza';
import tipoCuentaContable from './modules/contabilidad/tipo-cuenta-contable';
import tipoPolizaContpaq from './modules/contabilidad/tipo-poliza-contpaq';

//TESORERIA
import movimientoBancario from './modules/tesoreria/movimiento-bancario';
import tipoMovimiento from './modules/tesoreria/tipo-movimiento';

//CONTRATOS
import fondoGarantia from './modules/contratos/fondo-garantia';
import solicitudMovimientoFG from './modules/contratos/solicitud-movimiento-fg';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        'cadeco/obras': obras,
        'cadeco/cuenta': cuenta,
        'contabilidad/cuenta-almacen': cuentaAlmacen,
        'contabilidad/cuenta-empresa' : cuentaEmpresa,
        'contabilidad/cuenta-fondo' : cuentaFondo,
        'contabilidad/cuenta-general': cuentaGeneral,
        'contabilidad/estatus-prepoliza': estatusPrepoliza,
        'contabilidad/poliza': poliza,
        'contabilidad/tipo-cuenta-contable': tipoCuentaContable,
        'contabilidad/tipo-poliza-contpaq': tipoPolizaContpaq,

        'tesoreria/movimiento-bancario': movimientoBancario,
        'tesoreria/tipo-movimiento': tipoMovimiento,

        'contratos/fondo-garantia': fondoGarantia,
        'contratos/solicitud-movimiento-fg': solicitudMovimientoFG,
    },
    strict: process.env.NODE_ENV !== 'production'
})