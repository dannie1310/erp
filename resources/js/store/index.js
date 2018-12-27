import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';
import obras from './modules/cadeco/obras';
import cuenta_almacen from './modules/contabilidad/cuenta-almacen';
import poliza from './modules/contabilidad/poliza';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        'cadeco/obras': obras,
        'contabilidad/cuenta-almacen': cuenta_almacen,
        'contabilidad/poliza': poliza,
    },
    strict: process.env.NODE_ENV !== 'production'
})