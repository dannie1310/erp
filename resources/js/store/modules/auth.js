import {getLoggedinUser, getObra } from './partials/auth';

const user = getLoggedinUser();
const obra = getObra();

export default {
    namespaced: true,

    state: {
        currentUser: user,
        currentObra: obra,
        sistemas: [],
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
        loginSuccess(state, payload){
            state.auth_error = null;
            state.isLoggedin = true;
            state.loading = false;
            state.currentUser = Object.assign({}, payload.user);
            state.jwt = payload.access_token;

            window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + payload.access_token;
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
        authError(state){
            return state.auth_error;
        },
    },

    actions: {
        login(context){
            context.commit("login");
        },

        fetchPermisos(context, payload) {
            axios.get('/api/auth/permiso', payload)
                .then(res => {
                    context.commit('fetch', res.data)
                })
                .catch(err => {
                    alert(err);
                });
        },

        fetchSistemas(context, payload) {
            axios.get('/api/seguridad_erp/sistema', payload)
                .then(res => {
                    context.commit('fetch', res.data)
                })
                .catch(err => {
                    alert(err);
                });
        }
    },
}