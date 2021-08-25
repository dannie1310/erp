require('./bootstrap');
import Vue from 'vue';
import VueSession from 'vue-session';
import VeeValidate, { Validator } from 'vee-validate';
import es from 'vee-validate/dist/locale/es';
import Datatable from 'vue2-datatable-component';
import './utils';

const VueInputMask = require('vue-inputmask').default;

Vue.use(VueInputMask);

import store from './store';
import MainApp from './components/MainApp.vue';
import {es as datatableEs} from "./datatable.es";
import VueProgressBar from 'vue-progressbar';
import router from './router';


Vue.use(VueSession, {persist: true});
Vue.use(VeeValidate);
Vue.component('treeselect', VueTreeselect.Treeselect);
import vue2Dropzone from 'vue2-dropzone';
Vue.component('vueDropzone', vue2Dropzone);
Validator.localize('es', es);
Vue.use(Datatable, { locale: datatableEs });

Vue.use(VueProgressBar, {
    color: '#68a34d',
    failedColor: 'red',
    height: '10px'
});
import CKEditor from 'ckeditor4-vue';
Vue.use(CKEditor);

const app = new Vue({
    el: '#app',
    router,
    store,
    components: {
        MainApp
    },
    methods: {
        can(permiso, general) {
            let permisos = general ? this.$store.getters['auth/permisosGenerales'] : this.$store.getters['auth/permisos'];
            if (permisos) {
                if (Array.isArray(permiso)) {
                    let result = false;
                    permiso.forEach(perm => {
                        let search = permisos.find(p => {
                            return p == perm;
                        });
                        if (search) {
                            result = true;
                        }
                    });
                    return result;
                }  else {
                    return permisos.find(perm => {
                        return perm == permiso;
                    })
                }
            }
            return false;
        }
    }
});
