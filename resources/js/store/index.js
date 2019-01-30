import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';
import obras from './modules/cadeco/obras';

//CONTABILIDAD
import cuentaAlmacen from './modules/contabilidad/cuenta-almacen';
import cuentaFondo from './modules/contabilidad/cuenta-fondo';
import cuentaGeneral from './modules/contabilidad/cuenta-general'
import estatusPrepoliza from './modules/contabilidad/estatus-prepoliza';
import poliza from './modules/contabilidad/poliza';
import tipoCuentaContable from './modules/contabilidad/tipo-cuenta-contable';
import tipoPolizaContpaq from './modules/contabilidad/tipo-poliza-contpaq';

//TESORERIA
import movimientoBancario from './modules/tesoreria/movimiento-bancario';

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
        'contabilidad/tipo-poliza-contpaq': tipoPolizaContpaq,

        'tesoreria/movimiento-bancario': movimientoBancario,
    },
    strict: process.env.NODE_ENV !== 'production'
})