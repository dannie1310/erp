import {getLoggedinUser, getObra, getPermisos, getPermisosGenerales, getEmpresa } from './partials/auth';

const user = getLoggedinUser();
const obra = getObra();
const perms = getPermisos();
const permsGenerales = getPermisosGenerales();
const empresa = getEmpresa();

export default {
    namespaced: true,

    state: {
        currentUser: user,
        currentObra: obra,
        currentEmpresa : empresa,
        permisos: perms,
        permisosGenerales: permsGenerales,
        jwt: null,
        isLoggedIn: false,
        loading: false,
        auth_error: null,
    },
    mutations: {
        login(state){
            state.loading = true;
            state.auth_error = null;
        },
        loginSuccess(state, payload) {
            state.auth_error = null;
            state.isLoggedin = true;
            state.loading = false;
            state.currentUser = Object.assign({}, payload.user);
            state.jwt = payload.access_token;
        },
        loginFailed(state, payload) {
            state.loading = false;
            state.auth_error = payload.error;
        },
        logout(state, payload = {}) {
            state.isLoggedin = false;
            state.currentUser = null;
            state.currentObra = null;
            state.auth_error = payload.error;
        },
        setObra(state, payload) {
            state.currentObra = Object.assign({}, payload.obra);
            this._vm.$session.set('obra', payload.obra);
        },
        setEmpresa(state, payload) {
            state.currentEmpresa = Object.assign({}, payload);
            this._vm.$session.set('empresa', payload);
        },
        setPermisos(state, payload) {
                state.permisos = payload.permisos;
        }
    },

    getters: {
        isLoading(state){
            return state.loading;
        },
        isLoggedin(state){
            return state.isLoggedin;
        },
        currentUser(state){
            return state.currentUser;
        },
        currentObra(state){
            return state.currentObra;
        },
        currentEmpresa(state){
            return state.currentEmpresa;
        },
        datosContables(state){
            return state.currentObra ? (state.currentObra.datos_contables ? state.currentObra.datos_contables.FormatoCuentaRegExp : null) : null
        },
        idEmpresaContabilidad(state){
            return state.currentObra && state.currentObra.datos_contables && state.currentObra.datos_contables.empresa  ? state.currentObra.datos_contables.empresa.Id : null
        },
        authError(state){
            return state.auth_error;
        },
        permisos(state){
            return state.permisos;
        },
        permisosGenerales(state){
            return state.permisosGenerales;
        }
    },

    actions: {
        login(context){
            context.commit("login");
        },

        logout(context, payload) {
            return new Promise((resolve, reject) => {
                axios.post('/api/auth/logout')
                    .then(r => r.data)
                    .then(() => {
                        context.commit('logout', payload)
                        resolve();
                    })
                    .catch((error) => {
                        reject(error);
                    })
            })
        }
    },
}
