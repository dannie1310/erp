import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';
import obras from './modules/cadeco/obras';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        'cadeco/obras': obras,
    },
    strict: process.env.NODE_ENV !== 'production'
})