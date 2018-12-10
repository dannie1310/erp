import {getLoggedinUser} from './partials/auth';

const user = getLoggedinUser();

export default {
    state: {
        currentUser: user,
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