import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';
import obras from './modules/cadeco/obras';
import cuenta_almacen from './modules/contabilidad/cuenta-almacen';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        'cadeco/obras': obras,
        'contabilidad/cuenta-almacen': cuenta_almacen
    },
    strict: process.env.NODE_ENV !== 'production'
})