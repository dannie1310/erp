import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';
import obras from './modules/obras';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        obras,
    },
    strict: process.env.NODE_ENV !== 'production'
})