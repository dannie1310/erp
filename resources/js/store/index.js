import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';
import obras from './modules/cadeco/obras';
import cuenta_almacen from './modules/contabilidad/cuenta-almacen';
import poliza from './modules/contabilidad/poliza';
import tiposPolizaContpaq from './modules/contabilidad/tipos-poliza-contpaq';
import estatusPrepoliza from './modules/contabilidad/estatus-prepoliza';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        'cadeco/obras': obras,
        'contabilidad/cuenta-almacen': cuenta_almacen,
        'contabilidad/poliza': poliza,
        'contabilidad/tipos-poliza-contpaq': tiposPolizaContpaq,
        'contabilidad/estatus-prepoliza': estatusPrepoliza,
    },
    strict: process.env.NODE_ENV !== 'production'
})