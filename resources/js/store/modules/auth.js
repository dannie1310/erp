import {getLoggedinUser, getObra } from './partials/auth';

const user = getLoggedinUser();
const obra = getObra();

export default {
    namespaced: true,

    state: {
        currentUser: user,
        currentObra: obra,
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
        loginFailed(state, payload){
            state.loading = false;
            state.auth_error = payload.error;
        },
        logout(state){
            state.isLoggedin = false;
            state.currentUser = null;
            state.currentObra = null;
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
        }
    },
}