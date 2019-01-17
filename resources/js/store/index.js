import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';
import obras from './modules/cadeco/obras';
import cuentaAlmacen from './modules/contabilidad/cuenta-almacen';
import cuentaFondo from './modules/contabilidad/cuenta-fondo';
import cuentaGeneral from './modules/contabilidad/cuenta-general'
import estatusPrepoliza from './modules/contabilidad/estatus-prepoliza';
import poliza from './modules/contabilidad/poliza';
import tipoCuentaContable from './modules/contabilidad/tipo-cuenta-contable';
import tipoMovimiento from './modules/contabilidad/tipo-movimiento';
import tipoPolizaContpaq from './modules/contabilidad/tipo-poliza-contpaq';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        'cadeco/obras': obras,
        'contabilidad/cuenta-almacen': cuentaAlmacen,
        'contabilidad/cuenta-fondo' : cuentaFondo,
        'contabilidad/cuenta-general': cuentaGeneral,
        'contabilidad/estatus-prepoliza': estatusPrepoliza,
        'contabilidad/poliza': poliza,
        'contabilidad/tipo-cuenta-contable': tipoCuentaContable,
        'contabilidad/tipo-movimiento': tipoMovimiento,
        'contabilidad/tipo-poliza-contpaq': tipoPolizaContpaq,
    },
    strict: process.env.NODE_ENV !== 'production'
})